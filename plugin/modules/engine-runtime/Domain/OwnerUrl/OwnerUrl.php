<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\OwnerUrl;
defined( 'ABSPATH' ) || exit;
final class OwnerUrl {
	private $id;private $url;private $normalized_url;private $wordpress_post_id;private $post_type;private $status;
	public function __construct(string $url,string $status='active',?int $id=null,?int $wordpress_post_id=null,?string $post_type=null){$url=trim($url);if(''===$url||false===filter_var($url,FILTER_VALIDATE_URL)){throw new \InvalidArgumentException('Owner URL must be a valid URL.');}$this->id=$id;$this->url=$url;$this->normalized_url=self::normalize($url);$this->status=$status;$this->wordpress_post_id=$wordpress_post_id;$this->post_type=$post_type;}
	public static function normalize(string $url):string{$parts=parse_url(trim($url));if(false===$parts||empty($parts['host'])){throw new \InvalidArgumentException('Invalid URL.');}$scheme=strtolower($parts['scheme']??'https');$host=strtolower($parts['host']);$path='/'.ltrim($parts['path']??'/','/');$path=rtrim($path,'/').'/';return $scheme.'://'.$host.$path;}
	public function getId():?int{return $this->id;}public function getUrl():string{return $this->url;}public function getNormalizedUrl():string{return $this->normalized_url;}public function getWordPressPostId():?int{return $this->wordpress_post_id;}public function getPostType():?string{return $this->post_type;}public function getStatus():string{return $this->status;}
}