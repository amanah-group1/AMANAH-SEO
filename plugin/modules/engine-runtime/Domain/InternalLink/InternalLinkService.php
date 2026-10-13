<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\InternalLink;
defined( 'ABSPATH' ) || exit;
final class InternalLinkService {
	public function validate(InternalLinkOpportunity $opportunity):void{if($opportunity->getSourceUrlId()===$opportunity->getTargetUrlId()){throw new \DomainException('Self-link opportunity is invalid.');}}
}