<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\Application\Repository\ContentBriefRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentRequirementRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\PageBlueprintRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\RequirementSnapshotRepositoryInterface;
use Amanah\SEOEngine\Application\UseCase\GeneratePageBlueprint;
use Amanah\SEOEngine\Application\Service\PageBlueprintBuilder;
use Amanah\SEOEngine\Domain\ContentIntelligence\ContentRequirement;
use Amanah\SEOEngine\Domain\ContentIntelligence\RequirementSnapshot;
use Amanah\SEOEngine\Domain\ContentBrief\ContentBrief;
use Amanah\SEOEngine\Domain\PageBlueprint\PageBlueprintService;
use PHPUnit\Framework\TestCase;

defined( 'ABSPATH' ) || define( 'ABSPATH', __DIR__ . '/../../' );

if ( ! function_exists( 'sanitize_key' ) ) {
 function sanitize_key( $key ) {
  return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) );
 }
}

final class ContentRequirementTraceabilityTest extends TestCase {

 public function test_active_requirement_and_snapshot_are_frozen_into_blueprint_baseline(): void {
  $requirement = new ContentRequirement(
   7,
   101,
   'support_keyword_kopi_liong',
   'SUPPORT_KEYWORD',
   'Cover kopi liong asli naturally.',
   'HIGH',
   true,
   array( 'contains' => 'kopi liong' ),
   'ACTIVE',
   1,
   77
  );

  $snapshot = new RequirementSnapshot(
   77,
   1,
   array( 'statement' => 'Cover kopi liong asli naturally.' ),
   501
  );

  $brief = $this->createBrief();
  $captured = null;

  $briefRepository = $this->createMock( ContentBriefRepositoryInterface::class );
  $briefRepository->method( 'findByKeywordId' )->willReturn( $brief );

  $blueprintRepository = $this->createMock( PageBlueprintRepositoryInterface::class );
  $blueprintRepository->method( 'findByKeywordId' )->willReturn( null );
  $blueprintRepository->method( 'save' )->willReturnCallback(
   static function ( $blueprint ) use ( &$captured ) {
    $captured = $blueprint;
    return $blueprint;
   }
  );

  $blueprintService = new PageBlueprintService();

  $requirementRepository = $this->createMock( ContentRequirementRepositoryInterface::class );
  $requirementRepository->method( 'findByKeywordId' )->with( 7, 'ACTIVE' )->willReturn( array( $requirement ) );

  $snapshotRepository = $this->createMock( RequirementSnapshotRepositoryInterface::class );
  $snapshotRepository->method( 'findByRequirementVersion' )->with( 77, 1 )->willReturn( $snapshot );

  $useCase = new GeneratePageBlueprint(
   $briefRepository,
   $blueprintRepository,
   $blueprintService,
   new PageBlueprintBuilder( $requirementRepository, $snapshotRepository )
  );

  $useCase->execute( 7 );

  $data = $captured->getData();

  $this->assertCount( 1, $data['requirement_baseline'] );
  $this->assertSame( 77, $data['requirement_baseline'][0]['requirement_id'] );
  $this->assertSame( 'support_keyword_kopi_liong', $data['requirement_baseline'][0]['requirement_key'] );
  $this->assertSame( 1, $data['requirement_baseline'][0]['version'] );
  $this->assertSame( 77, $data['requirement_baseline'][0]['snapshot']['requirement_id'] );
  $this->assertSame( 1, $data['requirement_baseline'][0]['snapshot']['version'] );
  $this->assertSame( 'Cover kopi liong asli naturally.', $data['requirement_baseline'][0]['snapshot']['data']['statement'] );
 }

 public function test_existing_blueprint_baseline_remains_frozen_when_requirement_moves_to_new_version(): void {
  $requirementV1 = new ContentRequirement(
   7,
   101,
   'support_keyword_kopi_liong',
   'SUPPORT_KEYWORD',
   'Cover kopi liong asli naturally.',
   'HIGH',
   true,
   array(),
   'ACTIVE',
   1,
   77
  );

  $requirementV2 = new ContentRequirement(
   7,
   101,
   'support_keyword_kopi_liong',
   'SUPPORT_KEYWORD',
   'Cover kopi liong asli and its brewing context.',
   'HIGH',
   true,
   array(),
   'ACTIVE',
   2,
   77
  );

  $snapshotV1 = new RequirementSnapshot( 77, 1, array( 'statement' => 'Cover kopi liong asli naturally.' ), 501 );
  $snapshotV2 = new RequirementSnapshot( 77, 2, array( 'statement' => 'Cover kopi liong asli and its brewing context.' ), 502 );
  $currentRequirement = $requirementV1;
  $saved = array();

  $briefRepository = $this->createMock( ContentBriefRepositoryInterface::class );
  $briefRepository->method( 'findByKeywordId' )->willReturn( $this->createBrief() );

  $blueprintRepository = $this->createMock( PageBlueprintRepositoryInterface::class );
  $blueprintRepository->method( 'findByKeywordId' )->willReturn( null );
  $blueprintRepository->method( 'save' )->willReturnCallback(
   static function ( $blueprint ) use ( &$saved ) {
    $saved[] = $blueprint;
    return $blueprint;
   }
  );

  $blueprintService = new PageBlueprintService();
  $requirementRepository = $this->createMock( ContentRequirementRepositoryInterface::class );
  $requirementRepository->method( 'findByKeywordId' )->willReturnCallback(
   static function () use ( &$currentRequirement ) {
    return array( $currentRequirement );
   }
  );

  $snapshotRepository = $this->createMock( RequirementSnapshotRepositoryInterface::class );
  $snapshotRepository->method( 'findByRequirementVersion' )->willReturnCallback(
   static function ( $requirementId, $version ) use ( $snapshotV1, $snapshotV2 ) {
    return 1 === $version ? $snapshotV1 : $snapshotV2;
   }
  );

  $useCase = new GeneratePageBlueprint(
   $briefRepository,
   $blueprintRepository,
   $blueprintService,
   new PageBlueprintBuilder( $requirementRepository, $snapshotRepository )
  );

  $useCase->execute( 7 );
  $currentRequirement = $requirementV2;
  $useCase->execute( 7 );

  $first = $saved[0]->getData()['requirement_baseline'][0];
  $second = $saved[1]->getData()['requirement_baseline'][0];

  $this->assertSame( 1, $first['version'] );
  $this->assertSame( 'Cover kopi liong asli naturally.', $first['snapshot']['data']['statement'] );
  $this->assertSame( 2, $second['version'] );
  $this->assertSame( 'Cover kopi liong asli and its brewing context.', $second['snapshot']['data']['statement'] );
 }

 public function test_active_requirement_without_snapshot_blocks_blueprint_generation(): void {
  $requirement = new ContentRequirement(
   7,
   101,
   'support_keyword_kopi_liong',
   'SUPPORT_KEYWORD',
   'Cover kopi liong asli naturally.',
   'HIGH',
   true,
   array(),
   'ACTIVE',
   1,
   77
  );

  $briefRepository = $this->createMock( ContentBriefRepositoryInterface::class );
  $briefRepository->method( 'findByKeywordId' )->willReturn( $this->createBrief() );

  $blueprintRepository = $this->createMock( PageBlueprintRepositoryInterface::class );
  $blueprintRepository->method( 'findByKeywordId' )->willReturn( null );

  $blueprintService = new PageBlueprintService();

  $requirementRepository = $this->createMock( ContentRequirementRepositoryInterface::class );
  $requirementRepository->method( 'findByKeywordId' )->willReturn( array( $requirement ) );

  $snapshotRepository = $this->createMock( RequirementSnapshotRepositoryInterface::class );
  $snapshotRepository->method( 'findByRequirementVersion' )->willReturn( null );

  $useCase = new GeneratePageBlueprint(
   $briefRepository,
   $blueprintRepository,
   $blueprintService,
   new PageBlueprintBuilder( $requirementRepository, $snapshotRepository )
  );

  $this->expectException( \RuntimeException::class );
  $this->expectExceptionMessage( 'Requirement Snapshot is missing' );

  $useCase->execute( 7 );
 }

 private function createBrief(): ContentBrief {
  return new ContentBrief(
   7,
   array(
    'keyword' => array( 'text' => 'kopi liong' ),
    'support_keywords' => array( 'kopi liong asli' ),
    'target_url' => 'http://amanah-seo-engine.test/kopi-liong/',
    'article_type' => 'pillar',
    'intent_coverage' => array( 'informational' ),
    'article_outline' => array(
     array( 'id' => 'intro', 'level' => 2, 'title' => 'Pengenalan Kopi Liong' ),
    ),
    'support_keyword_mapping' => array(),
    'outline_status' => 'draft',
   ),
   1,
   'draft',
   18,
   17
  );
 }
}
