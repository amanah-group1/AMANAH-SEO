<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\Content;
use Amanah\SEOEngine\Domain\PageBlueprint\PageBlueprint;
defined( 'ABSPATH' ) || exit;
final class ContentValidationService {
	public function validate(Content $content,?PageBlueprint $blueprint=null):void{if(''===trim($content->getBody())){throw new \DomainException('Content body is required.');}if($blueprint&&$content->getBlueprintId()!==$blueprint->getId()){throw new \DomainException('Content blueprint relationship is invalid.');}}
}