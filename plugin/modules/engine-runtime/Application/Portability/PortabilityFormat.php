<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Portability;
defined( 'ABSPATH' ) || exit;
final class PortabilityFormat {
 public const VERSION=1;
 public const PRODUCT='amanah-seo-engine';
 public const TYPE='amanah_backup';
 public const SCOPE_SEO='seo';
 public const SCOPE_CONFIGURATION='configuration';
 public const SCOPE_DIAGNOSTICS='diagnostics';
 public const SCOPE_JOBS='jobs';
 public static function allowedScopes():array{return array(self::SCOPE_SEO,self::SCOPE_CONFIGURATION,self::SCOPE_DIAGNOSTICS,self::SCOPE_JOBS);}
}
