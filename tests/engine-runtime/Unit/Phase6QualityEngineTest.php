<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\Domain\Content\Content;
use Amanah\SEOEngine\Domain\PageBlueprint\PageBlueprint;
use Amanah\SEOEngine\Domain\Quality\DeterministicQualityEngine;
use Amanah\SEOEngine\Domain\Quality\PrePublishValidator;
use PHPUnit\Framework\TestCase;

final class Phase6QualityEngineTest extends TestCase {

	public function test_deterministic_quality_passes_valid_content(): void {
		$content = new Content(
			'Jual iPhone murah dengan proses aman.',
			1,
			'validated',
			1,
			10,
			20,
			'Jual iPhone Murah',
			'Informasi jual iPhone murah.',
			array( 'description' => 'Informasi jual iPhone murah.' )
		);
		$result = ( new DeterministicQualityEngine() )->evaluate( $content, 'jual iphone' );
		$this->assertSame( 'passed', $result->getStatus() );
		$this->assertSame( 100.0, $result->getScore() );
	}

	public function test_missing_required_content_fails(): void {
		$content = new Content( 'Teks tanpa judul.', 1, 'draft', 1, 10, 20, null, null, array() );
		$result = ( new DeterministicQualityEngine() )->evaluate( $content, 'jual iphone' );
		$this->assertSame( 'failed', $result->getStatus() );
	}

	public function test_optional_excerpt_creates_warning_only(): void {
		$content = new Content(
			'Jual iPhone murah.',
			1,
			'validated',
			1,
			10,
			20,
			'Jual iPhone',
			null,
			array()
		);
		$result = ( new DeterministicQualityEngine() )->evaluate( $content, 'jual iphone' );
		$this->assertSame( 'warning', $result->getStatus() );
	}

	public function test_required_blueprint_section_is_checked(): void {
		$blueprint = new PageBlueprint( 1, array( 'required_sections' => array( 'Harga dan Kondisi' ) ), 1, 'draft', null, 10 );
		$content = new Content(
			'Jual iPhone murah.',
			1,
			'draft',
			1,
			10,
			20,
			'Jual iPhone',
			'Ringkas.',
			array()
		);
		$result = ( new DeterministicQualityEngine() )->evaluate( $content, 'jual iphone', $blueprint );
		$this->assertSame( 'failed', $result->getStatus() );
	}

	public function test_pre_publish_rejects_warning_and_failed_results(): void {
		$validator = new PrePublishValidator();
		$warning = new Content( 'Jual iPhone murah.', 1, 'draft', 1, null, 20, 'Jual iPhone', null, array() );
		$result = ( new DeterministicQualityEngine() )->evaluate( $warning, 'jual iphone' );
		$this->expectException( \DomainException::class );
		$validator->validate( $result );
	}
}
