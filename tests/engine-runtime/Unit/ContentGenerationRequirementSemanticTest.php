<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Content {
	if ( ! function_exists( __NAMESPACE__ . '\\wp_strip_all_tags' ) ) {
		function wp_strip_all_tags( $text ) {
			return strip_tags( (string) $text );
		}
	}
}

namespace Amanah\SEOEngine\Tests\Unit {
use Amanah\SEOEngine\Application\Content\ContentGenerationRules;
use PHPUnit\Framework\TestCase;

defined( 'ABSPATH' ) || define( 'ABSPATH', __DIR__ . '/../../' );


final class ContentGenerationRequirementSemanticTest extends TestCase {

	public function test_potential_support_keyword_does_not_require_exact_phrase_without_mandatory_phrase_requirement(): void {
		$blueprint = array(
			'keyword' => array( 'phrase' => 'kopi hitam' ),
			'support_keywords' => array(
				array( 'id' => 49, 'phrase' => 'kopi hitam tanpa ampas selain nescafe' ),
			),
			'requirement_baseline' => array(),
			'article_outline' => array(),
		);

		$payload = $this->payload(
			'<h1>Kopi hitam</h1><p>Kopi hitam dapat dijelaskan dengan konteks kopi instan tanpa ampas dan pilihan merek yang relevan.</p>'
		);

		$result = ContentGenerationRules::validatePayload( $payload, $blueprint );

		$this->assertTrue( $result['valid'], implode( ', ', $result['errors'] ) );
		$this->assertNotContains( 'support_keyword_missing_body:kopi hitam tanpa ampas selain nescafe', $result['errors'] );
	}

	public function test_mandatory_phrase_requirement_still_requires_exact_phrase(): void {
		$blueprint = array(
			'keyword' => array( 'phrase' => 'kopi hitam' ),
			'support_keywords' => array(
				array( 'id' => 49, 'phrase' => 'kopi hitam tanpa ampas selain nescafe' ),
			),
			'requirement_baseline' => array(
				array(
					'requirement_id' => 77,
					'mandatory' => true,
					'verification_rule' => array(
						'method' => 'phrase_presence',
						'target' => 'kopi hitam tanpa ampas selain nescafe',
					),
				),
			),
			'article_outline' => array(),
		);

		$payload = $this->payload(
			'<h1>Kopi hitam</h1><p>Kopi hitam dapat dijelaskan dengan konteks kopi instan tanpa ampas.</p>'
		);

		$result = ContentGenerationRules::validatePayload( $payload, $blueprint );

		$this->assertFalse( $result['valid'] );
		$this->assertContains( 'support_keyword_missing_body:kopi hitam tanpa ampas selain nescafe', $result['errors'] );
	}

	private function payload( string $body ): array {
		return array(
			'title' => 'Kopi hitam',
			'body' => $body,
			'excerpt' => 'Pembahasan kopi hitam.',
			'metadata' => array(
				'generation_policy' => 'aturan_generate_konten',
				'research_validation' => array(
					'evergreen' => array( 'status' => 'needs_validation' ),
					'serp' => array( 'status' => 'needs_validation' ),
					'trend' => array( 'status' => 'needs_validation' ),
				),
				'keyword_strategy' => array(
					'supporting' => array( 'kopi hitam tanpa ampas selain nescafe' ),
				),
				'search_intent' => array(),
				'content_structure' => array(),
				'seo' => array(),
				'linking' => array(),
				'media' => array(),
				'trust' => array(),
				'maintenance' => array(),
				'factual_safety' => array(),
				'publishing' => array(),
			),
		);
	}
}
}
