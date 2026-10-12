<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\Keyword;
defined( 'ABSPATH' ) || exit;
final class KeywordAnalysisService {
	public function validate(Keyword $keyword):void{if(''===$keyword->getNormalizedPhrase()){throw new \DomainException('Normalized keyword cannot be empty.');}}
	public function normalize(string $phrase):string{return Keyword::normalize($phrase);}
}