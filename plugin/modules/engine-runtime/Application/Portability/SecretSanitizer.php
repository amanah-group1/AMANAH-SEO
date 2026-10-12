<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Portability;

use Amanah\SEOEngine\Application\Security\SecretFieldPolicy;
defined( 'ABSPATH' ) || exit;
final class SecretSanitizer {
 public function sanitize(array $value):array{$result=array();foreach($value as $key=>$item){if(SecretFieldPolicy::isSensitiveKey((string)$key)){continue;}$result[$key]=is_array($item)?$this->sanitize($item):$item;}return $result;}
}
