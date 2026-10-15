<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\OwnerUrl;
use Amanah\SEOEngine\Domain\Keyword\Keyword;
defined( 'ABSPATH' ) || exit;
final class OwnerUrlService {
	public function assign(Keyword $keyword,OwnerUrl $owner):void{$keyword->assignOwnerUrl($owner->getId());}
	public function remove(Keyword $keyword):void{$keyword->assignOwnerUrl(null);}
	public function hasOwnershipConflict(array $ownerIds):bool{return count(array_unique(array_map('intval',$ownerIds)))>1;}
}