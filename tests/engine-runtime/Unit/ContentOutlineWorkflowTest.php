<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\Application\Repository\ContentBriefRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\PageBlueprintRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentRequirementRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\RequirementSnapshotRepositoryInterface;
use Amanah\SEOEngine\Application\Service\PageBlueprintBuilder;
use Amanah\SEOEngine\Application\UseCase\SaveContentOutline;
use Amanah\SEOEngine\Domain\ContentBrief\ArticleOutlineBuilder;
use Amanah\SEOEngine\Domain\ContentBrief\ContentBrief;
use Amanah\SEOEngine\Domain\PageBlueprint\PageBlueprint;
use PHPUnit\Framework\TestCase;

final class ContentOutlineWorkflowTest extends TestCase {

 public function test_outline_save_persists_mapped_support_keywords_into_brief_and_blueprint(): void {
  $brief = new ContentBrief(
   4,
   array(
    'keyword' => array( 'phrase' => 'kopi liong' ),
    'support_keywords' => array(
     array( 'id' => 11, 'phrase' => 'kopi liong asli' ),
     array( 'id' => 12, 'phrase' => 'cara menyeduh kopi liong' ),
    ),
   ),
   1,
   'draft',
   7,
   20
  );

  $brief_repo = new class( $brief ) implements ContentBriefRepositoryInterface {
   private $latest;
   public function __construct( ContentBrief $brief ) { $this->latest = $brief; }
   public function findById( int $id ): ?ContentBrief { return $this->latest; }
   public function findByKeywordId( int $keyword_id ): ?ContentBrief { return $this->latest; }
   public function findAll( int $limit = 100 ): array { return array( $this->latest ); }
   public function save( ContentBrief $brief ): ContentBrief {
    $this->latest = new ContentBrief( $brief->getKeywordId(), $brief->getData(), $brief->getVersion(), $brief->getStatus(), $brief->getStrategyId(), 21 );
    return $this->latest;
   }
  };

  $blueprint_repo = new class implements PageBlueprintRepositoryInterface {
   private $latest;
   public function findById( int $id ): ?PageBlueprint { return $this->latest; }
   public function findByKeywordId( int $keyword_id ): ?PageBlueprint { return $this->latest; }
   public function findAll( int $limit = 100 ): array { return null !== $this->latest ? array( $this->latest ) : array(); }
   public function save( PageBlueprint $blueprint ): PageBlueprint {
    $this->latest = new PageBlueprint( $blueprint->getKeywordId(), $blueprint->getData(), $blueprint->getVersion(), $blueprint->getStatus(), $blueprint->getContentBriefId(), 31 );
    return $this->latest;
   }
  };

  $builder = new PageBlueprintBuilder(
   new class implements ContentRequirementRepositoryInterface {
    public function findByKey( string $key ): ?\Amanah\SEOEngine\Domain\ContentIntelligence\ContentRequirement { return null; }
    public function findByKeywordId( int $keyword_id, string $status = '' ): array { return array(); }
    public function save( \Amanah\SEOEngine\Domain\ContentIntelligence\ContentRequirement $requirement ): \Amanah\SEOEngine\Domain\ContentIntelligence\ContentRequirement { return $requirement; }
   },
   new class implements RequirementSnapshotRepositoryInterface {
    public function findByRequirementVersion( int $requirement_id, int $version ): ?\Amanah\SEOEngine\Domain\ContentIntelligence\RequirementSnapshot { return null; }
    public function save( \Amanah\SEOEngine\Domain\ContentIntelligence\RequirementSnapshot $snapshot ): \Amanah\SEOEngine\Domain\ContentIntelligence\RequirementSnapshot { return $snapshot; }
   }
  );
  $result = ( new SaveContentOutline( $brief_repo, $blueprint_repo, $builder ) )->execute(
   4,
   array(
    array( 'id' => 'h1_main', 'level' => 1, 'title' => 'Kopi Liong' ),
    array( 'id' => 'sec_a', 'level' => 2, 'title' => 'Mengenal Kopi Liong' ),
    array( 'id' => 'sec_b', 'level' => 2, 'title' => 'Cara Menyeduh' ),
   ),
   array( 11 => 'sec_a', 12 => 'sec_b' )
  );

  $data = $result->toArray();
  $this->assertSame( 2, $data['version'] );
  $this->assertSame( 'approved_for_generation', $data['data']['outline_status'] );
  $this->assertCount( 2, $data['data']['support_keyword_mapping'] );
  $this->assertSame( 'sec_b', $data['data']['support_keyword_mapping'][1]['section_id'] );
 }

 public function test_outline_save_rejects_missing_support_mapping(): void {
  $brief = new ContentBrief(
   4,
   array( 'keyword' => array( 'phrase' => 'kopi liong' ), 'support_keywords' => array( array( 'id' => 11, 'phrase' => 'kopi liong asli' ) ) ),
   1,
   'draft',
   7,
   20
  );
  $repo = new class( $brief ) implements ContentBriefRepositoryInterface {
   private $brief;
   public function __construct( ContentBrief $brief ) { $this->brief = $brief; }
   public function findById( int $id ): ?ContentBrief { return $this->brief; }
   public function findByKeywordId( int $keyword_id ): ?ContentBrief { return $this->brief; }
   public function findAll( int $limit = 100 ): array { return array( $this->brief ); }
   public function save( ContentBrief $brief ): ContentBrief { return $brief; }
  };
  $blueprints = new class implements PageBlueprintRepositoryInterface {
   public function findById( int $id ): ?PageBlueprint { return null; }
   public function findByKeywordId( int $keyword_id ): ?PageBlueprint { return null; }
   public function findAll( int $limit = 100 ): array { return array(); }
   public function save( PageBlueprint $blueprint ): PageBlueprint { return $blueprint; }
  };

  $this->expectException( \DomainException::class );
  $builder = new PageBlueprintBuilder(
   new class implements ContentRequirementRepositoryInterface {
    public function findByKey( string $key ): ?\Amanah\SEOEngine\Domain\ContentIntelligence\ContentRequirement { return null; }
    public function findByKeywordId( int $keyword_id, string $status = '' ): array { return array(); }
    public function save( \Amanah\SEOEngine\Domain\ContentIntelligence\ContentRequirement $requirement ): \Amanah\SEOEngine\Domain\ContentIntelligence\ContentRequirement { return $requirement; }
   },
   new class implements RequirementSnapshotRepositoryInterface {
    public function findByRequirementVersion( int $requirement_id, int $version ): ?\Amanah\SEOEngine\Domain\ContentIntelligence\RequirementSnapshot { return null; }
    public function save( \Amanah\SEOEngine\Domain\ContentIntelligence\RequirementSnapshot $snapshot ): \Amanah\SEOEngine\Domain\ContentIntelligence\RequirementSnapshot { return $snapshot; }
   }
  );
  ( new SaveContentOutline( $repo, $blueprints, $builder ) )->execute(
   4,
   array( array( 'id' => 'h1', 'level' => 1, 'title' => 'Kopi Liong' ) ),
   array()
  );
 }
 public function test_default_outline_uses_support_topics_and_not_intent_names_as_headings(): void {
  $result = ( new ArticleOutlineBuilder() )->build(
   'kopi liong',
   'pillar',
   array(
    array( 'slug' => 'informational', 'name' => 'Informational' ),
    array( 'slug' => 'commercial', 'name' => 'Commercial' ),
   ),
   array(
    array( 'id' => 11, 'phrase' => 'kopi liong adalah' ),
    array( 'id' => 12, 'phrase' => 'kopi liong robusta' ),
    array( 'id' => 13, 'phrase' => 'cara menyeduh kopi liong' ),
    array( 'id' => 14, 'phrase' => 'harga kopi liong' ),
   )
  );

  $titles = array_map(
   static function ( array $section ): string {
    return (string) $section['title'];
   },
   $result['article_outline']
  );

  $this->assertContains( 'Mengenal kopi liong', $titles );
  $this->assertContains( 'Apa Itu kopi liong?', $titles );
  $this->assertContains( 'Jenis dan Karakteristik kopi liong', $titles );
  $this->assertContains( 'Cara Menggunakan atau Menikmati kopi liong', $titles );
  $this->assertContains( 'Harga dan Pertimbangan Pembelian kopi liong', $titles );
  $this->assertContains( 'Kesimpulan', $titles );
  $this->assertNotContains( 'Informational', $titles );
  $this->assertNotContains( 'Commercial', $titles );

  $mapping = array();
  foreach ( $result['support_keyword_mapping'] as $item ) {
   $mapping[ (int) $item['keyword_id'] ] = $item['section_id'];
  }

  $this->assertCount( 4, $mapping );
  $this->assertSame( 'topic_definition', $mapping[11] );
  $this->assertSame( 'topic_types', $mapping[12] );
  $this->assertSame( 'topic_how_to', $mapping[13] );
  $this->assertSame( 'topic_pricing', $mapping[14] );
  $this->assertNotSame( $mapping[11], $mapping[14] );
 }

 public function test_default_outline_keeps_support_phrases_as_required_coverage_without_keyword_dump(): void {
  $result = ( new ArticleOutlineBuilder() )->build(
   'kopi liong',
   'pillar',
   array(),
   array(
    array( 'id' => 21, 'phrase' => 'kopi liong asli' ),
    array( 'id' => 22, 'phrase' => 'kopi liong enak' ),
   )
  );

  $h3_titles = array();
  foreach ( $result['article_outline'] as $section ) {
   if ( 3 === (int) $section['level'] ) {
    $h3_titles[] = (string) $section['title'];
   }
  }

  $this->assertContains( 'Kopi liong asli', $h3_titles );
  $this->assertContains( 'Kopi liong enak', $h3_titles );
  $this->assertCount( 2, $result['support_keyword_mapping'] );
  $this->assertSame( 'topic_selection', $result['support_keyword_mapping'][0]['section_id'] );
  $this->assertSame( 'topic_character', $result['support_keyword_mapping'][1]['section_id'] );
 }

}
