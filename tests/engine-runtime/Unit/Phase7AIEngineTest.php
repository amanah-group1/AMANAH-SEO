<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\AI\AIContextAssembler;
use Amanah\SEOEngine\AI\AIEngine;
use Amanah\SEOEngine\AI\AIRequest;
use Amanah\SEOEngine\AI\AIStructuredValidator;
use Amanah\SEOEngine\AI\AIRouter;
use Amanah\SEOEngine\AI\AIResponse;
use Amanah\SEOEngine\AI\AIUsageRecorderInterface;
use Amanah\SEOEngine\AI\AIProviderIdentityResolverInterface;
use Amanah\SEOEngine\AI\Provider\CustomAIProvider;
use Amanah\SEOEngine\AI\Provider\ProviderCapabilities;
use Amanah\SEOEngine\AI\Provider\ProviderRegistry;
use PHPUnit\Framework\TestCase;

final class Phase7AIEngineTest extends TestCase {

 public function test_registry(): void {
  $r = new ProviderRegistry();
  $p = $this->createMock( \Amanah\SEOEngine\AI\Provider\AIProviderInterface::class );
  $p->method( 'getProviderIdentifier' )->willReturn( 'test' );
  $r->register( $p );
  $this->assertSame( $p, $r->get( 'test' ) );
 }

 public function test_context_is_minimized(): void {
  $a = new AIContextAssembler();
  $this->assertSame(
   array( 'keyword' => 'x' ),
   $a->assemble( array( 'keyword' ), array( 'keyword' => 'x', 'secret' => 'y' ) )
  );
 }

 public function test_router_enforces_capability(): void {
  $r = new ProviderRegistry();
  $p = $this->createMock( \Amanah\SEOEngine\AI\Provider\AIProviderInterface::class );
  $p->method( 'getProviderIdentifier' )->willReturn( 'test' );
  $p->method( 'getCapabilities' )->willReturn( array( ProviderCapabilities::TEXT_GENERATION ) );
  $r->register( $p );
  $this->expectException( \RuntimeException::class );
  ( new AIRouter( $r ) )->select(
   'test',
   'm',
   array( ProviderCapabilities::STRUCTURED_OUTPUT )
  );
 }

 public function test_structured_validation(): void {
  $v = new AIStructuredValidator();
  $this->assertFalse( $v->validate( 'bad' )['valid'] );
  $this->assertTrue( $v->validate( '{"keyword":"x"}', array( 'keyword' ) )['valid'] );
 }

 public function test_custom_endpoint_is_restricted(): void {
  $p = new CustomAIProvider(
   $this->createMock( \Amanah\SEOEngine\Infrastructure\Http\HttpClientInterface::class )
  );
  $this->assertFalse(
   $p->validateCredentials(
    array(
     'endpoint' => 'http://127.0.0.1:1',
     'api_key'  => 'x',
    )
   )['valid']
  );
 }

 public function test_engine_validates_structured_response(): void {
  $provider = $this->createMock( \Amanah\SEOEngine\AI\Provider\AIProviderInterface::class );
  $provider->method( 'getProviderIdentifier' )->willReturn( 'test' );
  $provider->method( 'getCapabilities' )->willReturn(
   array(
    ProviderCapabilities::TEXT_GENERATION,
    ProviderCapabilities::STRUCTURED_OUTPUT,
   )
  );
  $provider->method( 'validateCredentials' )->willReturn( array( 'valid' => true ) );
  $provider->method( 'generate' )->willReturn(
   new AIResponse( 'test', 'model', '{"keyword":"x"}', array( 'usage_status' => 'unknown' ) )
  );

  $registry = new ProviderRegistry();
  $registry->register( $provider );

  $response = ( new AIEngine(
   new AIRouter( $registry ),
   new AIContextAssembler(),
   new AIStructuredValidator()
  ) )->execute(
   new AIRequest( 'keyword_generation', 'model', 'Prompt', array( 'keyword' => 'x' ), true, array( 'keyword' ) ),
   'test',
   array( 'api_key' => 'x' ),
   array( ProviderCapabilities::STRUCTURED_OUTPUT )
  );

  $this->assertSame( 'x', $response->getContent() === '{"keyword":"x"}' ? 'x' : '' );
 }

 public function test_engine_records_usage_when_task_id_is_supplied(): void {
  $provider = $this->createMock( \Amanah\SEOEngine\AI\Provider\AIProviderInterface::class );
  $provider->method( 'getProviderIdentifier' )->willReturn( 'test' );
  $provider->method( 'getCapabilities' )->willReturn( array( ProviderCapabilities::TEXT_GENERATION ) );
  $provider->method( 'validateCredentials' )->willReturn( array( 'valid' => true ) );
  $provider->method( 'generate' )->willReturn(
   new AIResponse(
    'test',
    'model',
    'result',
    array(
     'input_tokens' => 10,
     'output_tokens' => 20,
     'total_tokens' => 30,
     'usage_status' => 'reported',
    )
   )
  );

  $recorder = $this->createMock( AIUsageRecorderInterface::class );
  $recorder->expects( $this->once() )
   ->method( 'record' )
   ->with( 42, null, 'test', 'model', array(
    'input_tokens' => 10,
    'output_tokens' => 20,
    'total_tokens' => 30,
    'usage_status' => 'reported',
   ) )
   ->willReturn( true );

  $registry = new ProviderRegistry();
  $registry->register( $provider );

  ( new AIEngine(
   new AIRouter( $registry ),
   new AIContextAssembler(),
   null,
   $recorder
  ) )->execute(
   new AIRequest( 'text_generation', 'model', 'Prompt', array() ),
   'test',
   array( 'api_key' => 'x' ),
   array(),
   array(),
   42
  );
 }

 public function test_engine_records_resolved_provider_id(): void {
  $provider = $this->createMock( \Amanah\SEOEngine\AI\Provider\AIProviderInterface::class );
  $provider->method( 'getProviderIdentifier' )->willReturn( 'test' );
  $provider->method( 'getCapabilities' )->willReturn( array( ProviderCapabilities::TEXT_GENERATION ) );
  $provider->method( 'validateCredentials' )->willReturn( array( 'valid' => true ) );
  $provider->method( 'generate' )->willReturn(
   new AIResponse( 'test', 'model', 'result', array( 'usage_status' => 'reported' ) )
  );

  $resolver = $this->createMock( AIProviderIdentityResolverInterface::class );
  $resolver->expects( $this->once() )->method( 'resolveId' )->with( 'test' )->willReturn( 7 );

  $recorder = $this->createMock( AIUsageRecorderInterface::class );
  $recorder->expects( $this->once() )->method( 'record' )->with(
   42,
   7,
   'test',
   'model',
   array( 'usage_status' => 'reported' )
  )->willReturn( true );

  $registry = new ProviderRegistry();
  $registry->register( $provider );

  ( new AIEngine( new AIRouter( $registry ), new AIContextAssembler(), null, $recorder, $resolver ) )->execute(
   new AIRequest( 'text_generation', 'model', 'Prompt', array() ),
   'test',
   array( 'api_key' => 'x' ),
   array(),
   array(),
   42
  );
 }

 public function test_engine_rejects_invalid_structured_response(): void {
  $provider = $this->createMock( \Amanah\SEOEngine\AI\Provider\AIProviderInterface::class );
  $provider->method( 'getProviderIdentifier' )->willReturn( 'test' );
  $provider->method( 'getCapabilities' )->willReturn(
   array( ProviderCapabilities::TEXT_GENERATION, ProviderCapabilities::STRUCTURED_OUTPUT )
  );
  $provider->method( 'validateCredentials' )->willReturn( array( 'valid' => true ) );
  $provider->method( 'generate' )->willReturn( new AIResponse( 'test', 'model', 'not-json' ) );

  $registry = new ProviderRegistry();
  $registry->register( $provider );

  $this->expectException( \RuntimeException::class );
  ( new AIEngine( new AIRouter( $registry ), new AIContextAssembler() ) )->execute(
   new AIRequest( 'keyword_generation', 'model', 'Prompt', array(), true, array( 'keyword' ) ),
   'test',
   array( 'api_key' => 'x' ),
   array( ProviderCapabilities::STRUCTURED_OUTPUT )
  );
 }
}
