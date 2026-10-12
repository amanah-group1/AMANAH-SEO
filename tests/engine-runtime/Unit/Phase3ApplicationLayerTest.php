<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\Application\Command\CreateKeywordCommand;
use Amanah\SEOEngine\Application\DTO\KeywordData;
use Amanah\SEOEngine\Application\Error\ErrorNormalizer;
use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
use Amanah\SEOEngine\Application\UseCase\CreateKeyword;
use Amanah\SEOEngine\Application\UseCase\GetKeyword;
use Amanah\SEOEngine\Application\Query\GetKeywordQuery;
use Amanah\SEOEngine\Domain\Keyword\Keyword;
use PHPUnit\Framework\TestCase;

final class Phase3ApplicationLayerTest extends TestCase {

	public function test_create_keyword_use_case_returns_dto(): void {
		$repository = new class implements KeywordRepositoryInterface {
			private $keyword;

			public function findById( int $id ): ?Keyword { return $this->keyword; }

			public function findAll( int $limit = 100 ): array { return null !== $this->keyword ? array( $this->keyword ) : array(); }

			public function save( Keyword $keyword ): Keyword {
				$this->keyword = new Keyword(
					$keyword->getPhrase(),
					$keyword->getKeywordType(),
					$keyword->getStatus(),
					1
				);
				return $this->keyword;
			}
		};

		$owner_urls = new class implements OwnerUrlRepositoryInterface {
			public function findById( int $id ): ?OwnerUrl { return null; }
			public function findByNormalizedUrl( string $normalized_url ): ?OwnerUrl { return null; }
			public function findAll( int $limit = 100 ): array { return array(); }
			public function save( OwnerUrl $owner_url ): OwnerUrl { return new OwnerUrl( $owner_url->getUrl(), 'active', 7 ); }
		};

		$result = ( new CreateKeyword( $repository, new ResolveOwnerUrl( $owner_urls ) ) )->execute(
			new CreateKeywordCommand( '  Jual   iPhone  ', 'primary', 'https://example.com/jual-iphone/', 'pillar' )
		);

		$this->assertInstanceOf( KeywordData::class, $result );
		$this->assertSame( 'jual iphone', $result->toArray()['normalized_phrase'] );
	}

	public function test_get_keyword_use_case_returns_null_when_missing(): void {
		$repository = new class implements KeywordRepositoryInterface {
			public function findById( int $id ): ?Keyword { return null; }
			public function findAll( int $limit = 100 ): array { return array(); }
			public function save( Keyword $keyword ): Keyword { return $keyword; }
		};

		$result = ( new GetKeyword( $repository ) )->execute( new GetKeywordQuery( 99 ) );

		$this->assertNull( $result );
	}

	public function test_error_normalizer_hides_unexpected_exception_message(): void {
		$error = ( new ErrorNormalizer() )->normalize( new \RuntimeException( 'secret internal detail' ) );

		$this->assertSame( 'application_error', $error->getCode() );
		$this->assertSame( 'The requested operation could not be completed.', $error->getMessage() );
	}
}
