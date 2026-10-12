<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\Application\Repository\ContentBriefRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentRequirementRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\PageBlueprintRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\RequirementSnapshotRepositoryInterface;
use Amanah\SEOEngine\Application\Service\PageBlueprintBuilder;
use Amanah\SEOEngine\Application\UseCase\GeneratePageBlueprint;
use Amanah\SEOEngine\Application\UseCase\SaveContentOutline;
use Amanah\SEOEngine\Domain\ContentBrief\ContentBrief;
use Amanah\SEOEngine\Domain\ContentIntelligence\ContentRequirement;
use Amanah\SEOEngine\Domain\ContentIntelligence\RequirementSnapshot;
use Amanah\SEOEngine\Domain\PageBlueprint\PageBlueprint;
use Amanah\SEOEngine\Domain\PageBlueprint\PageBlueprintService;
use PHPUnit\Framework\TestCase;

final class BlueprintOutlineGenerationTraceabilityTest extends TestCase {

	public function test_saved_outline_builds_blueprint_with_requirement_baseline(): void {
		$brief = new ContentBrief(
			7,
			array(
				'keyword' => array( 'phrase' => 'kopi liong bulan' ),
				'support_keywords' => array(
					array( 'id' => 11, 'phrase' => 'kopi liong bulan asli' ),
					array( 'id' => 12, 'phrase' => 'kopi liong bulan harga' ),
				),
				'article_type' => 'pillar',
				'intent_coverage' => array( array( 'slug' => 'informational' ) ),
			),
			1,
			'draft',
			9,
			40
		);

		$briefs = new class( $brief ) implements ContentBriefRepositoryInterface {
			private $brief;
			public function __construct( ContentBrief $brief ) { $this->brief = $brief; }
			public function findById( int $id ): ?ContentBrief { return $this->brief; }
			public function findByKeywordId( int $keyword_id ): ?ContentBrief { return $this->brief; }
			public function findAll( int $limit = 100 ): array { return array( $this->brief ); }
			public function save( ContentBrief $brief ): ContentBrief {
				$this->brief = new ContentBrief( $brief->getKeywordId(), $brief->getData(), $brief->getVersion(), $brief->getStatus(), $brief->getStrategyId(), 41 );
				return $this->brief;
			}
		};

		$blueprints = new class implements PageBlueprintRepositoryInterface {
			private $latest;
			public function findById( int $id ): ?PageBlueprint { return $this->latest; }
			public function findByKeywordId( int $keyword_id ): ?PageBlueprint { return $this->latest; }
			public function findAll( int $limit = 100 ): array { return null !== $this->latest ? array( $this->latest ) : array(); }
			public function save( PageBlueprint $blueprint ): PageBlueprint {
				$this->latest = new PageBlueprint( $blueprint->getKeywordId(), $blueprint->getData(), $blueprint->getVersion(), $blueprint->getStatus(), $blueprint->getContentBriefId(), 51 );
				return $this->latest;
			}
		};

		$requirement = new ContentRequirement(
			7,
			301,
			'keyword:7|finding:abc',
			'SUPPORT_KEYWORD',
			'Cover kopi liong bulan asli naturally.',
			'HIGH',
			true,
			array( 'method' => 'phrase_presence', 'target' => 'kopi liong bulan asli', 'minimum' => 1 ),
			'ACTIVE',
			1,
			101
		);
		$snapshot = new RequirementSnapshot( 101, 1, array( 'statement' => 'Cover kopi liong bulan asli naturally.' ), 201 );

		$requirements = new class( $requirement ) implements ContentRequirementRepositoryInterface {
			private $requirement;
			public function __construct( ContentRequirement $requirement ) { $this->requirement = $requirement; }
			public function findByKey( string $key ): ?ContentRequirement { return $this->requirement; }
			public function findByKeywordId( int $keyword_id, string $status = '' ): array { return array( $this->requirement ); }
			public function save( ContentRequirement $requirement ): ContentRequirement { return $requirement; }
		};
		$snapshots = new class( $snapshot ) implements RequirementSnapshotRepositoryInterface {
			private $snapshot;
			public function __construct( RequirementSnapshot $snapshot ) { $this->snapshot = $snapshot; }
			public function findByRequirementVersion( int $requirement_id, int $version ): ?RequirementSnapshot { return $this->snapshot; }
			public function save( RequirementSnapshot $snapshot ): RequirementSnapshot { return $snapshot; }
		};

		$builder = new PageBlueprintBuilder( $requirements, $snapshots );
		$result = ( new SaveContentOutline( $briefs, $blueprints, $builder ) )->execute(
			7,
			array(
				array( 'id' => 'h1_main', 'level' => 1, 'title' => 'Kopi Liong Bulan' ),
				array( 'id' => 'section_intro', 'level' => 2, 'title' => 'Mengenal Kopi Liong Bulan' ),
			),
			array( 11 => 'section_intro', 12 => 'section_intro' )
		);

		$data = $result->toArray();
		$this->assertSame( 'approved_for_generation', $data['data']['outline_status'] );
		$this->assertCount( 2, $data['data']['support_keyword_mapping'] );

		$blueprint = $blueprints->findByKeywordId( 7 );
		$this->assertNotNull( $blueprint );
		$this->assertSame( 'editable_content_brief', $blueprint->getData()['source'] );
		$this->assertSame( $data['data']['article_outline'], $blueprint->getData()['article_outline'] );
		$this->assertSame( $data['data']['support_keyword_mapping'], $blueprint->getData()['support_keyword_mapping'] );
		$this->assertCount( 1, $blueprint->getData()['requirement_baseline'] );
		$this->assertSame( 101, $blueprint->getData()['requirement_baseline'][0]['requirement_id'] );
		$this->assertSame( 201, $blueprint->getData()['requirement_baseline'][0]['snapshot_id'] );
		$this->assertSame( 101, $blueprint->getData()['requirement_baseline'][0]['snapshot']['requirement_id'] );
	}

	public function test_refresh_blueprint_reuses_saved_editable_outline_and_mapping(): void {
		$brief = new ContentBrief(
			7,
			array(
				'keyword' => array( 'phrase' => 'kopi liong bulan' ),
				'support_keywords' => array( array( 'id' => 11, 'phrase' => 'kopi liong bulan asli' ) ),
				'article_type' => 'pillar',
				'intent_coverage' => array( array( 'slug' => 'informational' ) ),
				'article_outline' => array(
					array( 'id' => 'custom_h1', 'level' => 1, 'type' => 'h1', 'title' => 'Judul H1 Pilihan User' ),
					array( 'id' => 'custom_h2', 'level' => 2, 'type' => 'h2', 'title' => 'Bagian Pilihan User' ),
				),
				'support_keyword_mapping' => array(
					array( 'keyword_id' => 11, 'phrase' => 'kopi liong bulan asli', 'section_id' => 'custom_h2', 'required' => true ),
				),
				'outline_status' => 'approved_for_generation',
			),
			2,
			'draft',
			9,
			40
		);

		$briefs = new class( $brief ) implements ContentBriefRepositoryInterface {
			private $brief;
			public function __construct( ContentBrief $brief ) { $this->brief = $brief; }
			public function findById( int $id ): ?ContentBrief { return $this->brief; }
			public function findByKeywordId( int $keyword_id ): ?ContentBrief { return $this->brief; }
			public function findAll( int $limit = 100 ): array { return array( $this->brief ); }
			public function save( ContentBrief $brief ): ContentBrief { $this->brief = $brief; return $brief; }
		};

		$blueprints = new class implements PageBlueprintRepositoryInterface {
			private $latest;
			public function findById( int $id ): ?PageBlueprint { return $this->latest; }
			public function findByKeywordId( int $keyword_id ): ?PageBlueprint { return $this->latest; }
			public function findAll( int $limit = 100 ): array { return null !== $this->latest ? array( $this->latest ) : array(); }
			public function save( PageBlueprint $blueprint ): PageBlueprint { $this->latest = $blueprint; return $blueprint; }
		};

		$requirements = new class implements ContentRequirementRepositoryInterface {
			public function findByKey( string $key ): ?ContentRequirement { return null; }
			public function findByKeywordId( int $keyword_id, string $status = '' ): array { return array(); }
			public function save( ContentRequirement $requirement ): ContentRequirement { return $requirement; }
		};
		$snapshots = new class implements RequirementSnapshotRepositoryInterface {
			public function findByRequirementVersion( int $requirement_id, int $version ): ?RequirementSnapshot { return null; }
			public function save( RequirementSnapshot $snapshot ): RequirementSnapshot { return $snapshot; }
		};

		$generate = new GeneratePageBlueprint(
			$briefs,
			$blueprints,
			new PageBlueprintService(),
			new PageBlueprintBuilder( $requirements, $snapshots )
		);

		$data = $generate->execute( 7 )->toArray()['data'];
		$this->assertSame( 'Judul H1 Pilihan User', $data['article_outline'][0]['title'] );
		$this->assertSame( 'Bagian Pilihan User', $data['sections'][1]['title'] );
		$this->assertSame( 'custom_h2', $data['support_keyword_mapping'][0]['section_id'] );
		$this->assertSame( 'approved_for_generation', $data['outline_status'] );
	}
}
