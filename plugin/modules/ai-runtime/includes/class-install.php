<?php
/**
 * Database Installation Class
 *
 * Creates and upgrades the AMANAH SEO AI database foundation.
 *
 * @package AMANAH_SEO_AI
 */

defined( 'ABSPATH' ) || exit;

/**
 * AMANAH_SEO_AI_Install
 */
class AMANAH_SEO_AI_Install {

	/**
	 * Current database schema version.
	 *
	 * @var string
	 */
	const DATABASE_VERSION = '1.3.0';

	/**
	 * Install or upgrade the database foundation.
	 *
	 * @return bool True when the database is ready.
	 */
	public static function install() {
		global $wpdb;

		$installed_version = get_option( 'amanah_version', '' );

		if ( self::DATABASE_VERSION === $installed_version ) {
			if ( false === get_option( 'amanah_installed', false ) ) {
				add_option( 'amanah_installed', 1, '', 'no' );
			}

			return self::is_ready( $wpdb );
		}

		if ( '' !== $installed_version && version_compare( $installed_version, self::DATABASE_VERSION, '>' ) ) {
			return false;
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$tables = self::get_table_definitions( $wpdb );
		foreach ( $tables as $table_name => $sql ) {
			dbDelta( $sql );

			if ( ! self::table_exists( $wpdb, $table_name ) ) {
				return false;
			}
		}

		if ( ! self::archive_legacy_content_analysis_table( $wpdb ) ) {
			return false;
		}

		if ( ! update_option( 'amanah_version', self::DATABASE_VERSION, false ) ) {
			$stored_version = get_option( 'amanah_version', '' );
			if ( self::DATABASE_VERSION !== $stored_version ) {
				return false;
			}
		}

		if ( ! update_option( 'amanah_installed', 1, false ) ) {
			$installed = get_option( 'amanah_installed', '' );
			if ( '1' !== (string) $installed ) {
				return false;
			}
		}

		return self::is_ready( $wpdb );
	}

	/**
	 * Check the database version during normal plugin loading.
	 *
	 * WordPress does not run the activation hook during a plugin update.
	 *
	 * @return void
	 */
	public static function maybe_upgrade() {
		global $wpdb;

		$installed_version = get_option( 'amanah_version', '' );

		if ( self::DATABASE_VERSION === $installed_version && self::is_ready( $wpdb ) ) {
			return;
		}

		if ( ! self::install() && is_admin() ) {
			add_action(
				'admin_notices',
				array( __CLASS__, 'database_error_notice' )
			);
		}
	}

	/**
	 * Display a friendly database initialization message.
	 *
	 * @return void
	 */
	public static function database_error_notice() {
		echo '<div class="notice notice-error"><p>' .
			esc_html__( 'AMANAH SEO AI could not initialize its database. The current database version was not advanced.', 'amanah-seo-ai' ) .
			'</p></div>';
	}

	/**
	 * Determine whether the database foundation is ready.
	 *
	 * @param wpdb $wpdb WordPress database object.
	 * @return bool True when all required tables and options are present.
	 */
	public static function is_ready( $wpdb ) {
		$tables = self::get_table_definitions( $wpdb );

		foreach ( array_keys( $tables ) as $table_name ) {
			if ( ! self::table_exists( $wpdb, $table_name ) ) {
				return false;
			}
		}

		return self::DATABASE_VERSION === get_option( 'amanah_version', '' )
			&& '1' === (string) get_option( 'amanah_installed', '' );
	}

	/**
	 * Move the pre-CORE-006 content analysis table out of the active schema.
	 *
	 * Legacy rows are retained in an archive table because they cannot be
	 * promoted to CORE-006 without the required Title/Content/Keyword/Entity
	 * input context.
	 *
	 * @param wpdb $wpdb WordPress database object.
	 * @return bool True when no legacy active table remains.
	 */
	private static function archive_legacy_content_analysis_table( $wpdb ) {
		$legacy = $wpdb->prefix . 'amanah_content_analysis';
		$archive = $wpdb->prefix . 'amanah_content_analysis_legacy';

		if ( ! self::table_exists( $wpdb, $legacy ) ) {
			return true;
		}

		if ( ! self::table_exists( $wpdb, $archive ) ) {
			$renamed = $wpdb->query( "RENAME TABLE {$legacy} TO {$archive}" );
			if ( false === $renamed ) {
				return false;
			}
		} else {
			// Never destroy a legacy table when an archive already exists. Preserve
			// the source for a later reviewed, row-safe reconciliation.
			return true;
		}

		return ! self::table_exists( $wpdb, $legacy );
	}

	/**
	 * Check whether a table exists in the active WordPress database.
	 *
	 * @param wpdb   $wpdb       WordPress database object.
	 * @param string $table_name Table name.
	 * @return bool True when the table exists.
	 */
	private static function table_exists( $wpdb, $table_name ) {
		$query = $wpdb->prepare(
			'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = %s',
			$table_name
		);

		return 1 === (int) $wpdb->get_var( $query );
	}

	/**
	 * Get the authoritative CORE-002 table definitions.
	 *
	 * @param wpdb $wpdb WordPress database object.
	 * @return array Table names and CREATE TABLE statements.
	 */
	private static function get_table_definitions( $wpdb ) {
		$charset_collate = $wpdb->get_charset_collate();
		$prefix           = $wpdb->prefix;

		return array(
			$prefix . 'amanah_entities' => "CREATE TABLE {$prefix}amanah_entities (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				user_id bigint(20) unsigned NOT NULL,
				entity_name varchar(255) NOT NULL,
				entity_type varchar(50) NOT NULL,
				industry varchar(100) DEFAULT NULL,
				description text DEFAULT NULL,
				location varchar(255) DEFAULT NULL,
				website_url varchar(255) DEFAULT NULL,
				expertise text DEFAULT NULL,
				products text DEFAULT NULL,
				services text DEFAULT NULL,
				target_audience text DEFAULT NULL,
				logo_url varchar(500) DEFAULT NULL,
				schema_type varchar(100) DEFAULT NULL,
				status varchar(50) DEFAULT 'active',
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				updated_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id),
				KEY idx_entity_user (user_id)
			) $charset_collate;",

			$prefix . 'amanah_entity_topics' => "CREATE TABLE {$prefix}amanah_entity_topics (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				entity_id bigint(20) unsigned NOT NULL,
				topic_name varchar(255) DEFAULT NULL,
				topic_type varchar(50) DEFAULT NULL,
				parent_topic bigint(20) DEFAULT NULL,
				importance int(11) DEFAULT 0,
				coverage_score int(11) DEFAULT 0,
				status varchar(50) DEFAULT 'active',
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			$prefix . 'amanah_audits' => "CREATE TABLE {$prefix}amanah_audits (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				entity_id bigint(20) unsigned DEFAULT NULL,
				url varchar(500) DEFAULT NULL,
				audit_type varchar(50) DEFAULT NULL,
				category varchar(100) DEFAULT NULL,
				score int(11) DEFAULT 0,
				issue_title varchar(255) DEFAULT NULL,
				issue_description text DEFAULT NULL,
				recommendation text DEFAULT NULL,
				priority varchar(50) DEFAULT NULL,
				status varchar(50) DEFAULT 'open',
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id),
				KEY idx_audit_entity (entity_id)
			) $charset_collate;",

			$prefix . 'amanah_content_analyses' => "CREATE TABLE {$prefix}amanah_content_analyses (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				analysis_id varchar(64) NOT NULL,
				post_id bigint(20) unsigned NOT NULL,
				content_version varchar(100) DEFAULT NULL,
				input_context longtext NOT NULL,
				analysis longtext NOT NULL,
				status varchar(20) NOT NULL DEFAULT 'pending',
				version int(11) unsigned NOT NULL DEFAULT 1,
				traceability longtext DEFAULT NULL,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				updated_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id),
				UNIQUE KEY uq_content_analysis_id (analysis_id),
				KEY idx_content_analysis_post (post_id),
				KEY idx_content_analysis_post_version (post_id,version)
			) $charset_collate;",

			$prefix . 'amanah_content_plan' => "CREATE TABLE {$prefix}amanah_content_plan (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				entity_id bigint(20) unsigned DEFAULT NULL,
				topic varchar(255) DEFAULT NULL,
				title varchar(500) DEFAULT NULL,
				search_intent varchar(100) DEFAULT NULL,
				content_type varchar(100) DEFAULT NULL,
				priority varchar(50) DEFAULT NULL,
				status varchar(50) DEFAULT NULL,
				brief longtext DEFAULT NULL,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			$prefix . 'amanah_schema' => "CREATE TABLE {$prefix}amanah_schema (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				entity_id bigint(20) unsigned DEFAULT NULL,
				post_id bigint(20) unsigned DEFAULT NULL,
				schema_type varchar(100) DEFAULT NULL,
				schema_json longtext DEFAULT NULL,
				status varchar(50) DEFAULT 'active',
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			$prefix . 'amanah_ai_logs' => "CREATE TABLE {$prefix}amanah_ai_logs (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				user_id bigint(20) unsigned DEFAULT NULL,
				provider varchar(50) DEFAULT NULL,
				model varchar(100) DEFAULT NULL,
				task_type varchar(100) DEFAULT NULL,
				prompt longtext DEFAULT NULL,
				response longtext DEFAULT NULL,
				input_token int(11) DEFAULT 0,
				output_token int(11) DEFAULT 0,
				cost decimal(10,4) DEFAULT 0,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id),
				KEY idx_ai_logs_user (user_id)
			) $charset_collate;",

			$prefix . 'amanah_tasks' => "CREATE TABLE {$prefix}amanah_tasks (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				entity_id bigint(20) unsigned DEFAULT NULL,
				task_title varchar(255) DEFAULT NULL,
				task_description text DEFAULT NULL,
				category varchar(100) DEFAULT NULL,
				priority varchar(50) DEFAULT NULL,
				impact varchar(50) DEFAULT NULL,
				status varchar(50) DEFAULT 'pending',
				action_url varchar(500) DEFAULT NULL,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				completed_at datetime DEFAULT NULL,
				PRIMARY KEY  (id),
				KEY idx_tasks_status (status)
			) $charset_collate;",

			$prefix . 'amanah_settings' => "CREATE TABLE {$prefix}amanah_settings (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				user_id bigint(20) unsigned DEFAULT NULL,
				setting_key varchar(100) DEFAULT NULL,
				setting_value longtext DEFAULT NULL,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				updated_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			$prefix . 'amanah_article_packages' => "CREATE TABLE {$prefix}amanah_article_packages (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				package_id varchar(64) NOT NULL,
				content_brief_id bigint(20) unsigned NOT NULL,
				version int(11) unsigned NOT NULL DEFAULT 1,
				parent_version int(11) unsigned DEFAULT NULL,
				status varchar(50) NOT NULL DEFAULT 'GENERATING',
				package longtext NOT NULL,
				quality_evaluation longtext DEFAULT NULL,
				traceability longtext DEFAULT NULL,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				updated_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id),
				UNIQUE KEY uq_article_package_id (package_id),
				KEY idx_article_package_brief_version (content_brief_id,version),
				KEY idx_article_package_status (status)
			) $charset_collate;",

			$prefix . 'amanah_license' => "CREATE TABLE {$prefix}amanah_license (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				user_id bigint(20) unsigned DEFAULT NULL,
				license_key varchar(255) DEFAULT NULL,
				plan varchar(50) DEFAULT NULL,
				domain varchar(255) DEFAULT NULL,
				status varchar(50) DEFAULT NULL,
				expired_date date DEFAULT NULL,
				last_check datetime DEFAULT NULL,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",
		);
	}
}
