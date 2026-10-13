<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\SearchIntent;
defined( 'ABSPATH' ) || exit;
final class SearchIntentService {
	private const ALLOWED=array('informational','commercial','transactional','navigational');
	public function validate(SearchIntent $intent):void{if(!in_array($intent->getSlug(),self::ALLOWED,true)){throw new \DomainException('Invalid search intent.');}}
	public function compare(SearchIntent $left,SearchIntent $right):bool{return $left->getSlug()===$right->getSlug();}
}