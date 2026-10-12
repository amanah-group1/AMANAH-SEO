<?php
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_Authority_Controller {
	private $model;
	private $entity_model;

	public function __construct( $model, $entity_model ) {
		$this->model        = $model;
		$this->entity_model = $entity_model;
	}

	public function save( $input, $current_user_id ) {
		if ( ! current_user_can( 'amanah_manage_authority' ) ) {
			return new WP_Error( 'forbidden', __( 'You are not allowed to manage authority profiles.', 'amanah-seo-ai' ), array( 'status' => 403 ) );
		}

		$user_id   = isset( $input['user_id'] ) ? absint( $input['user_id'] ) : 0;
		$entity_id = isset( $input['entity_id'] ) ? absint( $input['entity_id'] ) : 0;

		if ( ! $user_id || ! $entity_id || ! $this->model->get_user( $user_id ) ) {
			return new WP_Error( 'validation', __( 'A valid WordPress user and Entity are required.', 'amanah-seo-ai' ), array( 'status' => 400 ) );
		}

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return new WP_Error( 'forbidden', __( 'You are not allowed to edit this WordPress user.', 'amanah-seo-ai' ), array( 'status' => 403 ) );
		}

		if ( ! $this->entity_model->get_entity( $entity_id, $current_user_id, true ) ) {
			return new WP_Error( 'not_found', __( 'Entity not found or not owned by the current user.', 'amanah-seo-ai' ), array( 'status' => 404 ) );
		}

		$expert = $this->sanitize_expert( $input );
		$social = $this->sanitize_social( $input );
		$photo_result = $this->validate_profile_photo( isset( $input['profile_photo_id'] ) ? $input['profile_photo_id'] : 0 );
		if ( is_wp_error( $photo_result ) ) {
			return $photo_result;
		}

		$author_result = $this->model->save_author_profile(
			$user_id,
			isset( $input['display_name'] ) ? $input['display_name'] : '',
			isset( $input['description'] ) ? $input['description'] : '',
			isset( $input['website'] ) ? $input['website'] : ''
		);

		if ( is_wp_error( $author_result ) ) {
			return $author_result;
		}

		$this->model->save_expert_profile( $user_id, $entity_id, $expert );
		$this->model->save_social_profiles( $user_id, $social );
		$this->model->save_profile_photo( $user_id, absint( $photo_result ) );

		return true;
	}

	public function validate_profile_photo( $attachment_id ) {
		$attachment_id = absint( $attachment_id );
		if ( ! $attachment_id ) {
			return 0;
		}
		if ( ! current_user_can( 'upload_files' ) || ! current_user_can( 'edit_post', $attachment_id ) ) {
			return new WP_Error( 'forbidden', __( 'You are not allowed to use this media item as a profile photo.', 'amanah-seo-ai' ), array( 'status' => 403 ) );
		}
		$attachment = get_post( $attachment_id );
		if ( ! $attachment || 'attachment' !== $attachment->post_type || ! wp_attachment_is_image( $attachment_id ) ) {
			return new WP_Error( 'invalid_profile_photo', __( 'Profile Photo must be an image from the WordPress Media Library.', 'amanah-seo-ai' ), array( 'status' => 400 ) );
		}
		return $attachment_id;
	}

	public function delete( $input, $current_user_id ) {
		if ( ! current_user_can( 'amanah_manage_authority' ) ) {
			return new WP_Error( 'forbidden', __( 'You are not allowed to manage authority profiles.', 'amanah-seo-ai' ), array( 'status' => 403 ) );
		}
		$user_id   = isset( $input['user_id'] ) ? absint( $input['user_id'] ) : 0;
		$entity_id = isset( $input['entity_id'] ) ? absint( $input['entity_id'] ) : 0;
		if ( ! $user_id || ! $entity_id || ! $this->model->get_user( $user_id ) ) {
			return new WP_Error( 'validation', __( 'A valid WordPress user and Entity are required.', 'amanah-seo-ai' ), array( 'status' => 400 ) );
		}
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return new WP_Error( 'forbidden', __( 'You are not allowed to edit this WordPress user.', 'amanah-seo-ai' ), array( 'status' => 403 ) );
		}
		if ( ! $this->entity_model->get_entity( $entity_id, $current_user_id, true ) ) {
			return new WP_Error( 'not_found', __( 'Entity not found or not owned by the current user.', 'amanah-seo-ai' ), array( 'status' => 404 ) );
		}
		$this->model->delete_expert_profile( $user_id, $entity_id );
		return true;
	}

	public function handle_delete() {
		check_admin_referer( 'amanah_delete_authority', 'amanah_authority_delete_nonce' );
		$result = $this->delete( wp_unslash( $_POST ), get_current_user_id() );
		$url = admin_url( 'admin.php?page=amanah-authority' );
		$url = add_query_arg( 'authority_message', rawurlencode( is_wp_error( $result ) ? $result->get_error_message() : __( 'Authority assignment removed. WordPress User, Author data and Social Profile were not deleted.', 'amanah-seo-ai' ) ), $url );
		wp_safe_redirect( $url );
		exit;
	}

	public function sanitize_expert( $input ) {
		$fields = array(
			'expert_name',
			'role',
			'short_bio',
			'full_bio',
			'primary_expertise',
			'secondary_expertise',
			'topics',
			'industries',
			'years_experience',
			'experience',
			'education',
			'certifications',
			'credentials',
			'awards',
		);

		$data = array();
		foreach ( $fields as $field ) {
			$value          = isset( $input[ $field ] ) ? $input[ $field ] : '';
			$data[ $field ] = in_array( $field, array( 'short_bio', 'full_bio', 'primary_expertise', 'secondary_expertise', 'topics', 'industries', 'experience', 'education', 'certifications', 'credentials', 'awards' ), true )
				? sanitize_textarea_field( $value )
				: sanitize_text_field( $value );
		}

		return $data;
	}

	public function sanitize_social( $input ) {
		$data = array();
		foreach ( AMANAH_SEO_AI_Authority_Model::SOCIAL_PLATFORMS as $platform ) {
			$data[ $platform ] = isset( $input[ 'social_' . $platform ] ) ? esc_url_raw( $input[ 'social_' . $platform ] ) : '';
		}

		return $data;
	}

	public function handle_save() {
		check_admin_referer( 'amanah_save_authority', 'amanah_authority_nonce' );

		$result = $this->save( wp_unslash( $_POST ), get_current_user_id() );
		$url    = admin_url( 'admin.php?page=amanah-authority' );

		$url = add_query_arg(
			'authority_message',
			rawurlencode(
				is_wp_error( $result )
					? $result->get_error_message()
					: __( 'Authority, Author and Social Profile data synchronized with WordPress.', 'amanah-seo-ai' )
			),
			$url
		);

		if ( ! is_wp_error( $result ) ) {
			$url = add_query_arg(
				array(
					'user_id'   => absint( $_POST['user_id'] ),
					'entity_id' => absint( $_POST['entity_id'] ),
				),
				$url
			);
		}

		wp_safe_redirect( $url );
		exit;
	}
}
