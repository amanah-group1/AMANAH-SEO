<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
use Amanah\SEOEngine\Application\UseCase\ListKeywords;
use Amanah\SEOEngine\Domain\Keyword\Keyword;
use PHPUnit\Framework\TestCase;

final class KeywordRepositoryTest extends TestCase {

	public function test_keyword_repository_contract_supports_listing(): void {
		$repository = new class implements KeywordRepositoryInterface {
			public function findById( int $id ): ?Keyword {
				return null;
			}

			public function findAll( int $limit = 100 ): array {
				return array(
					new Keyword( 'Jual iPhone', 'primary', 'draft', 1 ),
				);
			}

			public function save( Keyword $keyword ): Keyword {
				return $keyword;
			}
		};

		$result = ( new ListKeywords( $repository ) )->execute();

		$this->assertCount( 1, $result );
		$this->assertSame( 'jual iphone', $result[0]->toArray()['normalized_phrase'] );
	}
}
