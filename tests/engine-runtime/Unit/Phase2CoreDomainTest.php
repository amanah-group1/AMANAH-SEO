<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\Domain\Cannibalization\CannibalizationService;
use Amanah\SEOEngine\Domain\Keyword\Keyword;
use Amanah\SEOEngine\Domain\OwnerUrl\OwnerUrl;
use Amanah\SEOEngine\Infrastructure\Database\Schema;
use PHPUnit\Framework\TestCase;

final class Phase2CoreDomainTest extends TestCase {
	public function test_keyword_normalization_and_owner_url_normalization(): void {
		$keyword = new Keyword( '  Jual   iPhone  ' );
		$this->assertSame( 'jual iphone', $keyword->getNormalizedPhrase() );
		$url = new OwnerUrl( 'HTTPS://Example.COM/jual-iphone' );
		$this->assertSame( 'https://example.com/jual-iphone/', $url->getNormalizedUrl() );
	}
	public function test_cannibalization_detects_multiple_owners(): void {
		$keyword = new Keyword( 'jual iphone', 'primary', 'active', 10 );
		$finding = ( new CannibalizationService() )->detect( $keyword, array( 2, 5 ) );
		$this->assertNotNull( $finding );
		$this->assertSame( 'multiple_active_owners', $finding->getConflictType() );
	}
	public function test_schema_version_matches_current_internal_schema(): void { $this->assertSame( 11, Schema::VERSION ); }
}