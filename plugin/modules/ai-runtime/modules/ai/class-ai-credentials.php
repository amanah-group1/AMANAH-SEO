<?php
defined( 'ABSPATH' ) || exit;
class AMANAH_SEO_AI_AI_Credentials {
	const VERSION = 'v1';
	private $settings;
	public function __construct( AMANAH_SEO_AI_AI_Settings $settings ) { $this->settings = $settings; }
	public function set( $key, $secret, $user_id = null ) {
		if ( ! function_exists( 'sodium_crypto_secretbox' ) ) throw new AMANAH_SEO_AI_AI_Exception( 'Credential encryption is unavailable.', 'configuration' );
		$secret = (string) $secret;
		if ( '' === $secret ) throw new AMANAH_SEO_AI_AI_Exception( 'Credential cannot be empty.', 'configuration' );
		$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$ciphertext = sodium_crypto_secretbox( $secret, $nonce, $this->key() );
		return $this->settings->set( $key, self::VERSION . ':' . base64_encode( $nonce . $ciphertext ), $user_id );
	}
	public function get( $key, $user_id = null ) {
		$value = $this->settings->get( $key, $user_id );
		if ( ! is_string( $value ) || 0 !== strpos( $value, self::VERSION . ':' ) ) return null;
		$decoded = base64_decode( substr( $value, strlen( self::VERSION . ':' ) ), true );
		if ( false === $decoded || strlen( $decoded ) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ) throw new AMANAH_SEO_AI_AI_Exception( 'Stored credential could not be decoded.', 'configuration' );
		$nonce = substr( $decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$ciphertext = substr( $decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$secret = sodium_crypto_secretbox_open( $ciphertext, $nonce, $this->key() );
		if ( false === $secret ) throw new AMANAH_SEO_AI_AI_Exception( 'Stored credential could not be decrypted.', 'configuration' );
		return $secret;
	}
	private function key() {
		if ( ! function_exists( 'wp_salt' ) ) throw new AMANAH_SEO_AI_AI_Exception( 'WordPress secret material is unavailable.', 'configuration' );
		return hash_hmac( 'sha256', 'amanah-seo-ai-ai-credential-v1', wp_salt( 'auth' ), true );
	}
}
