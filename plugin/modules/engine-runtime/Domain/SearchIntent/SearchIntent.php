<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\SearchIntent;
defined( 'ABSPATH' ) || exit;
final class SearchIntent {
	private $id;private $slug;private $name;private $description;
	public function __construct(string $slug,string $name,string $description='',?int $id=null){$slug=trim($slug);$name=trim($name);if(''===$slug||''===$name){throw new \InvalidArgumentException('Search intent identity is required.');}$this->id=$id;$this->slug=$slug;$this->name=$name;$this->description=trim($description);}
	public function getId():?int{return $this->id;}public function getSlug():string{return $this->slug;}public function getName():string{return $this->name;}public function getDescription():string{return $this->description;}
}