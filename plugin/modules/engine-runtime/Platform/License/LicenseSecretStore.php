<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Platform\License;
defined( 'ABSPATH' ) || exit;
final class LicenseSecretStore {
 private const OPTION='amanah_seo_license_secret'; private const CIPHER='aes-256-gcm';
 private function key(): string { return hash('sha256',defined('AUTH_KEY')?AUTH_KEY:'amanah-seo-engine',true); }
 public function set(string $secret): void {
  if ( class_exists( 'AMANAH_SEO_Module_Licenses' ) ) {
   if ( ! AMANAH_SEO_Module_Licenses::set_license( 'engine', $secret ) ) throw new \RuntimeException( 'license_secret_storage_error' );
   return;
  }
  $iv=random_bytes(openssl_cipher_iv_length(self::CIPHER));$tag='';
  $cipher=openssl_encrypt($secret,self::CIPHER,$this->key(),OPENSSL_RAW_DATA,$iv,$tag,'',16);
  if(false===$cipher)throw new \RuntimeException('license_secret_storage_error');
  update_option(self::OPTION,'v1:'.base64_encode($iv.$tag.$cipher),false);
 }
 public function get(): ?string {
  if ( class_exists( 'AMANAH_SEO_Module_Licenses' ) ) {
   $shared = AMANAH_SEO_Module_Licenses::get_license( 'engine' );
   if ( null !== $shared ) return $shared;
  }
  $value=get_option(self::OPTION,'');if(!is_string($value)||0!==strpos($value,'v1:'))return null;
  $raw=base64_decode(substr($value,3),true);$ivlen=openssl_cipher_iv_length(self::CIPHER);
  if(false===$raw||strlen($raw)<$ivlen+16)return null;
  $iv=substr($raw,0,$ivlen);$tag=substr($raw,$ivlen,16);$cipher=substr($raw,$ivlen+16);
  $plain=openssl_decrypt($cipher,self::CIPHER,$this->key(),OPENSSL_RAW_DATA,$iv,$tag);
  return false===$plain?null:$plain;
 }
 public function delete(): void {
  if ( class_exists( 'AMANAH_SEO_Module_Licenses' ) ) AMANAH_SEO_Module_Licenses::delete_license( 'engine' );
  delete_option(self::OPTION);
 }
}
