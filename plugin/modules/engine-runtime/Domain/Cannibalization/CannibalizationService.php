<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\Cannibalization;
use Amanah\SEOEngine\Domain\Keyword\Keyword;
defined( 'ABSPATH' ) || exit;
final class CannibalizationService {
	public function detect(Keyword $keyword,array $ownerUrlIds):?CannibalizationFinding{$ids=array_values(array_unique(array_map('intval',$ownerUrlIds)));if(count($ids)<2){return null;}sort($ids);return new CannibalizationFinding($keyword->getId()?:1,$ids[0],$ids[1],'multiple_active_owners','high','deterministic');}
}