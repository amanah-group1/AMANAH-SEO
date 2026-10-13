<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\UseCase;
use Amanah\SEOEngine\Application\DTO\ClusterData;
use Amanah\SEOEngine\Application\Repository\ClusterRepositoryInterface;
defined( 'ABSPATH' ) || exit;
final class ListClusters {
 private $repository;
 public function __construct( ClusterRepositoryInterface $repository ){ $this->repository=$repository; }
 public function execute(): array {
  $items=array();
  foreach($this->repository->findAll() as $cluster){ $items[]=ClusterData::fromDomain($cluster); }
  return $items;
 }
}