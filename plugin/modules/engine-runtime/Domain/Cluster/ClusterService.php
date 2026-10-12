<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\Cluster;
use Amanah\SEOEngine\Domain\Keyword\Keyword;
defined( 'ABSPATH' ) || exit;
final class ClusterService {
	public function assign(Keyword $keyword,Cluster $cluster):void{$keyword->assignCluster($cluster->getId());}
	public function remove(Keyword $keyword):void{$keyword->assignCluster(null);}
}