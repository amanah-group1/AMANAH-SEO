<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Command;
defined( 'ABSPATH' ) || exit;
final class CreateKeywordCommand {
	private $phrase; private $keyword_type; private $target_url; private $article_type; private $pillar_keyword_id;
	public function __construct( string $phrase, string $keyword_type = 'primary', string $target_url = '', string $article_type = 'pillar', ?int $pillar_keyword_id = null ) {
		$this->phrase=$phrase; $this->keyword_type=$keyword_type; $this->target_url=$target_url; $this->article_type=$article_type; $this->pillar_keyword_id=$pillar_keyword_id;
	}
	public function getPhrase():string{return $this->phrase;} public function getKeywordType():string{return $this->keyword_type;} public function getTargetUrl():string{return $this->target_url;} public function getArticleType():string{return $this->article_type;} public function getPillarKeywordId():?int{return $this->pillar_keyword_id;}
}