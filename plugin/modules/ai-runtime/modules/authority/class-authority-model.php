<?php
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_Authority_Model {
	const EXPERT_META_KEY  = '_amanah_expert_profiles';
	const PROFILE_PHOTO_META_KEY = '_amanah_author_profile_photo_id';
	const SOCIAL_META_PREFIX = '_amanah_author_social_';
	const SOCIAL_PLATFORMS = array( 'linkedin', 'facebook', 'instagram', 'youtube', 'x', 'telegram', 'whatsapp' );

	public function get_user( $user_id ) {
		$user = get_userdata( absint( $user_id ) );
		return $user instanceof WP_User ? $user : null;
	}

	public function get_expert_profile( $user_id, $entity_id ) {
		$profiles = get_user_meta( absint( $user_id ), self::EXPERT_META_KEY, true );
		$profiles = is_array( $profiles ) ? $profiles : array();
		$profile  = isset( $profiles[ absint( $entity_id ) ] ) && is_array( $profiles[ absint( $entity_id ) ] )
			? $profiles[ absint( $entity_id ) ]
			: array();

		$defaults = array(
			'expert_name'           => '',
			'role'                  => '',
			'short_bio'            => '',
			'full_bio'             => '',
			'primary_expertise'     => '',
			'secondary_expertise'   => '',
			'topics'                => '',
			'industries'            => '',
			'years_experience'      => '',
			'experience'            => '',
			'education'             => '',
			'certifications'        => '',
			'credentials'           => '',
			'awards'                => '',
		);

		return array_merge( $defaults, $profile );
	}

	public function has_expert_profile( $user_id, $entity_id ) {
		$profiles = get_user_meta( absint( $user_id ), self::EXPERT_META_KEY, true );
		return is_array( $profiles ) && isset( $profiles[ absint( $entity_id ) ] ) && is_array( $profiles[ absint( $entity_id ) ] );
	}

	public function delete_expert_profile( $user_id, $entity_id ) {
		$profiles = get_user_meta( absint( $user_id ), self::EXPERT_META_KEY, true );
		if ( ! is_array( $profiles ) || ! array_key_exists( absint( $entity_id ), $profiles ) ) {
			return false;
		}
		unset( $profiles[ absint( $entity_id ) ] );
		if ( empty( $profiles ) ) {
			return delete_user_meta( absint( $user_id ), self::EXPERT_META_KEY );
		}
		return update_user_meta( absint( $user_id ), self::EXPERT_META_KEY, $profiles );
	}

	public function get_authority_assignments( $entities, $users ) {
		$assignments = array();
		foreach ( $entities as $entity ) {
			foreach ( $users as $user ) {
				if ( $user && $this->has_expert_profile( $user->ID, $entity->id ) ) {
					$assignments[] = array(
						'user' => $user,
						'entity' => $entity,
					);
				}
			}
		}
		return $assignments;
	}

	public function save_expert_profile( $user_id, $entity_id, $data ) {
		$profiles = get_user_meta( absint( $user_id ), self::EXPERT_META_KEY, true );
		$profiles = is_array( $profiles ) ? $profiles : array();
		$profiles[ absint( $entity_id ) ] = $data;

		return update_user_meta( absint( $user_id ), self::EXPERT_META_KEY, $profiles );
	}

	public function get_social_profiles( $user_id ) {
		$result = array();
		foreach ( self::SOCIAL_PLATFORMS as $platform ) {
			$result[ $platform ] = (string) get_user_meta( absint( $user_id ), self::SOCIAL_META_PREFIX . $platform, true );
		}
		return $result;
	}

	public function save_social_profiles( $user_id, $profiles ) {
		foreach ( self::SOCIAL_PLATFORMS as $platform ) {
			$value = isset( $profiles[ $platform ] ) ? esc_url_raw( $profiles[ $platform ] ) : '';
			update_user_meta( absint( $user_id ), self::SOCIAL_META_PREFIX . $platform, $value );
		}
		return true;
	}

	public function get_profile_photo_id( $user_id ) {
		return absint( get_user_meta( absint( $user_id ), self::PROFILE_PHOTO_META_KEY, true ) );
	}

	public function save_profile_photo( $user_id, $attachment_id ) {
		$user_id       = absint( $user_id );
		$attachment_id = absint( $attachment_id );
		if ( ! $attachment_id ) {
			return delete_user_meta( $user_id, self::PROFILE_PHOTO_META_KEY );
		}
		return update_user_meta( $user_id, self::PROFILE_PHOTO_META_KEY, $attachment_id );
	}

	public function get_profile_photo_url( $user_id, $size = 'thumbnail' ) {
		$attachment_id = $this->get_profile_photo_id( $user_id );
		return $attachment_id ? (string) wp_get_attachment_image_url( $attachment_id, $size ) : '';
	}

	public function get_authority_context( $user_id, $entity_id ) {
		return array(
			'entity_id' => absint( $entity_id ),
			'author'    => $this->get_author_profile( $user_id ),
			'expert'    => $this->get_expert_profile( $user_id, $entity_id ),
			'social'    => $this->get_social_profiles( $user_id ),
		);
	}

	public function get_author_profile( $user_id ) {
		$user = $this->get_user( $user_id );
		if ( ! $user ) {
			return array();
		}

		return array(
			'display_name' => (string) $user->display_name,
			'description'  => (string) $user->description,
			'website'      => (string) $user->user_url,
			'profile_photo_id' => $this->get_profile_photo_id( $user_id ),
		);
	}

	public function save_author_profile( $user_id, $display_name, $description, $website ) {
		return wp_update_user(
			array(
				'ID'           => absint( $user_id ),
				'display_name' => sanitize_text_field( $display_name ),
				'description'  => sanitize_textarea_field( $description ),
				'user_url'     => esc_url_raw( $website ),
			)
		);
	}
}
