<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\AI\AICostCalculator;
use Amanah\SEOEngine\AI\AIContextAssembler;
use Amanah\SEOEngine\AI\AIEngine;
use Amanah\SEOEngine\AI\AIExecutionService;
use Amanah\SEOEngine\AI\AIModelCatalogInterface;
use Amanah\SEOEngine\AI\AIModelDiscoveryService;
use Amanah\SEOEngine\AI\AIRequest;
use Amanah\SEOEngine\AI\AIResponse;
use Amanah\SEOEngine\AI\AIRouter;
use Amanah\SEOEngine\AI\AIStructuredValidator;
use Amanah\SEOEngine\AI\AIUsageRecorder;
use Amanah\SEOEngine\AI\Provider\AIProviderInterface;
use Amanah\SEOEngine\AI\Provider\ProviderCapabilities;
use Amanah\SEOEngine\AI\Provider\ProviderRegistry;
use Amanah\SEOEngine\Infrastructure\Database\WordPressDatabase;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressAIModelCatalog;
use PHPUnit\Framework\TestCase;

final class Phase7FullPackageTest extends TestCase {

 public function test_fallback_selects_next_provider(): void {
  $first=$this->createMock(AIProviderInterface::class);
  $first->method('getProviderIdentifier')->willReturn('first');
  $first->method('getCapabilities')->willReturn(array(ProviderCapabilities::TEXT_GENERATION));
  $first->method('validateCredentials')->willReturn(array('valid'=>false,'category'=>'authentication_error'));
  $second=$this->createMock(AIProviderInterface::class);
  $second->method('getProviderIdentifier')->willReturn('second');
  $second->method('getCapabilities')->willReturn(array(ProviderCapabilities::TEXT_GENERATION));
  $second->method('validateCredentials')->willReturn(array('valid'=>true));
  $second->method('generate')->willReturn(new AIResponse('second','model','ok'));
  $registry=new ProviderRegistry(); $registry->register($first); $registry->register($second);
  $response=(new AIEngine(new AIRouter($registry),new AIContextAssembler()))->execute(new AIRequest('text','model','Prompt'),'first',array(),array(),array('second'));
  $this->assertSame('second',$response->getProvider());
 }

 public function test_structured_response_gets_one_revision(): void {
  $provider=$this->createMock(AIProviderInterface::class);
  $provider->method('getProviderIdentifier')->willReturn('test');
  $provider->method('getCapabilities')->willReturn(array(ProviderCapabilities::TEXT_GENERATION,ProviderCapabilities::STRUCTURED_OUTPUT));
  $provider->method('validateCredentials')->willReturn(array('valid'=>true));
  $provider->expects($this->exactly(2))->method('generate')->willReturnOnConsecutiveCalls(new AIResponse('test','model','not-json'),new AIResponse('test','model','{"keyword":"ok"}'));
  $registry=new ProviderRegistry(); $registry->register($provider);
  $response=(new AIEngine(new AIRouter($registry),new AIContextAssembler(),new AIStructuredValidator()))->execute(new AIRequest('structured','model','Prompt',array(),true,array('keyword')),'test',array(),array(ProviderCapabilities::STRUCTURED_OUTPUT));
  $this->assertSame('{"keyword":"ok"}',$response->getContent());
 }

 public function test_task_service_isolates_provider_failure(): void {
  $provider=$this->createMock(AIProviderInterface::class);
  $provider->method('getProviderIdentifier')->willReturn('test');
  $provider->method('getCapabilities')->willReturn(array(ProviderCapabilities::TEXT_GENERATION));
  $provider->method('validateCredentials')->willReturn(array('valid'=>true));
  $provider->method('generate')->willThrowException(new \RuntimeException('network_error'));
  $registry=new ProviderRegistry(); $registry->register($provider);
  $result=(new AIExecutionService(new AIEngine(new AIRouter($registry),new AIContextAssembler()),1))->executeTask(new AIRequest('text','model','Prompt'),'test',array());
  $this->assertFalse($result->isSuccessful());
  $this->assertSame('network_error',$result->getErrorCategory());
 }

 public function test_cost_is_calculated_only_with_complete_pricing_data(): void {
  $result=(new AICostCalculator())->calculate(array('input_tokens'=>1500,'output_tokens'=>500,'input_cost_per_1k'=>2.00,'output_cost_per_1k'=>4.00,'currency'=>'USD','pricing_source'=>'configured'));
  $this->assertSame(5.0,$result['estimated_cost']);
  $this->assertSame('USD',$result['currency']);
  $this->assertSame('configured',$result['pricing_source']);
  $this->assertSame('calculated',$result['cost_status']);
 }

 public function test_cost_remains_unknown_without_sufficient_data(): void {
  $result=(new AICostCalculator())->calculate(array('input_tokens'=>1500,'output_tokens'=>500,'currency'=>'USD'));
  $this->assertNull($result['estimated_cost']);
  $this->assertSame('USD',$result['currency']);
  $this->assertNull($result['pricing_source']);
  $this->assertSame('unknown',$result['cost_status']);
 }

 public function test_provider_reported_cost_is_preserved_without_local_pricing_assumptions(): void {
  $result=(new AICostCalculator())->calculate(array('estimated_cost'=>0.12345678,'currency'=>'USD','pricing_source'=>'provider_reported'));
  $this->assertSame(0.12345678,$result['estimated_cost']);
  $this->assertSame('provider_reported',$result['pricing_source']);
  $this->assertSame('reported',$result['cost_status']);
 }

 public function test_model_discovery_persists_discovered_metadata_through_catalog(): void {
  $provider=$this->createMock(AIProviderInterface::class);
  $provider->method('getProviderIdentifier')->willReturn('test');
  $provider->method('getProviderName')->willReturn('Test Provider');
  $provider->method('getCapabilities')->willReturn(array(ProviderCapabilities::TEXT_GENERATION,ProviderCapabilities::MODEL_DISCOVERY));
  $provider->method('validateCredentials')->willReturn(array('valid'=>true));
  $provider->method('discoverModels')->willReturn(array(array(
   'model_identifier'=>'test-model',
   'display_name'=>'Test Model',
   'capabilities'=>array(ProviderCapabilities::TEXT_GENERATION),
   'metadata'=>array('family'=>'test'),
  )));
  $catalog=$this->createMock(AIModelCatalogInterface::class);
  $catalog->expects($this->once())->method('syncProvider')->with(
   'test',
   'Test Provider',
   array(ProviderCapabilities::TEXT_GENERATION,ProviderCapabilities::MODEL_DISCOVERY),
   array(array(
    'model_identifier'=>'test-model',
    'display_name'=>'Test Model',
    'capabilities'=>array(ProviderCapabilities::TEXT_GENERATION),
    'metadata'=>array('family'=>'test'),
   ))
  );
  $registry=new ProviderRegistry(); $registry->register($provider);
  $models=(new AIModelDiscoveryService($registry,$catalog))->discover('test',array('api_key'=>'x'));
  $this->assertCount(1,$models);
 }

 public function test_wordpress_model_catalog_writes_provider_and_model_metadata(): void {
  $wpdb=$this->createMock(\wpdb::class);
  $wpdb->prefix='wp_';
  $wpdb->expects($this->exactly(2))->method('prepare')->willReturnArgument(0);
  $wpdb->expects($this->exactly(2))->method('get_var')->willReturnOnConsecutiveCalls(null,null);
  $inserted=array();
  $wpdb->expects($this->exactly(2))->method('insert')->willReturnCallback(function($table,$data)use(&$wpdb,&$inserted){
   $inserted[]=array($table,$data);
   $wpdb->insert_id=count($inserted)===1?11:21;
   return 1;
  });
  $wpdb->expects($this->never())->method('update');
  $wpdb->expects($this->once())->method('query')->with($this->stringContains("availability = 'unavailable'"));
  (new WordPressAIModelCatalog($wpdb))->syncProvider(
   'test',
   'Test Provider',
   array(ProviderCapabilities::TEXT_GENERATION),
   array(array(
    'model_identifier'=>'test-model',
    'display_name'=>'Test Model',
    'capabilities'=>array(ProviderCapabilities::TEXT_GENERATION),
    'metadata'=>array('family'=>'test'),
   ))
  );
  $this->assertSame('wp_amanah_seo_ai_providers',$inserted[0][0]);
  $this->assertSame('test',$inserted[0][1]['provider_key']);
  $this->assertSame('wp_amanah_seo_ai_models',$inserted[1][0]);
  $this->assertSame(11,$inserted[1][1]['provider_id']);
  $this->assertSame('test-model',$inserted[1][1]['model_identifier']);
  $this->assertSame('Test Model',$inserted[1][1]['display_name']);
  $this->assertSame('available',$inserted[1][1]['availability']);
  $this->assertStringContainsString('family',$inserted[1][1]['metadata']);
 }

 public function test_usage_recorder_persists_provider_reported_cost_metadata(): void {
  $wpdb=$this->createMock(\wpdb::class);
  $wpdb->prefix='wp_';
  $wpdb->expects($this->once())->method('insert')->willReturnCallback(function($table,$data){
   $this->assertSame('wp_amanah_seo_ai_usage',$table);
   $this->assertSame(0.12345678,$data['estimated_cost']);
   $this->assertSame('USD',$data['currency']);
   $this->assertSame('provider_reported',$data['pricing_source']);
   $this->assertSame('reported',$data['cost_status']);
   return 1;
  });
  $database=new WordPressDatabase($wpdb);
  $this->assertTrue((new AIUsageRecorder($database))->record(42,7,'test','model',array(
   'input_tokens'=>10,'output_tokens'=>20,'total_tokens'=>30,'usage_status'=>'reported',
   'estimated_cost'=>0.12345678,'currency'=>'USD','pricing_source'=>'provider_reported',
  )));
 }
}
