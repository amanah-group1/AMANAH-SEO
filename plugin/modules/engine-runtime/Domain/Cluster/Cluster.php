<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\Cluster;
defined( 'ABSPATH' ) || exit;
final class Cluster {
	private $id;private $name;private $slug;private $description;private $status;
	public function __construct(string $name,string $slug,string $status='active',string $description='',?int $id=null){if(''===trim($name)||''===trim($slug)){throw new \InvalidArgumentException('Cluster identity is required.');}$this->id=$id;$this->name=trim($name);$this->slug=trim($slug);$this->description=trim($description);$this->status=$status;}
	public function getId():?int{return $this->id;}public function getName():string{return $this->name;}public function getSlug():string{return $this->slug;}public function getDescription():string{return $this->description;}public function getStatus():string{return $this->status;}
}