<?php
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_Authority_Admin {
	private $controller;
	private $model;
	private $entity_model;

	public function __construct( $controller, $model, $entity_model ) {
		$this->controller   = $controller;
		$this->model        = $model;
		$this->entity_model = $entity_model;
	}

	public function register() {
		add_action( 'admin_post_amanah_save_authority', array( $this->controller, 'handle_save' ) );
		add_action( 'admin_post_amanah_delete_authority', array( $this->controller, 'handle_delete' ) );
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_profile_photo_assets' ) );
		add_filter( 'get_avatar_url', array( $this, 'filter_avatar_url' ), 10, 3 );
		add_filter( 'user_contactmethods', array( $this, 'register_social_contact_methods' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'ensure_capabilities' ) );
	}

	public function filter_avatar_url( $url, $id_or_email, $args ) {
		$user_id = 0;
		if ( is_numeric( $id_or_email ) ) {
			$user_id = absint( $id_or_email );
		} elseif ( $id_or_email instanceof WP_User ) {
			$user_id = (int) $id_or_email->ID;
		} elseif ( $id_or_email instanceof WP_Comment ) {
			$user = get_user_by( 'email', $id_or_email->comment_author_email );
			$user_id = $user ? (int) $user->ID : 0;
		} elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
			$user_id = $user ? (int) $user->ID : 0;
		}
		if ( ! $user_id ) {
			return $url;
		}
		$photo_url = $this->model->get_profile_photo_url( $user_id, 'thumbnail' );
		return $photo_url ? $photo_url : $url;
	}

	public function enqueue_profile_photo_assets() {
		if ( ! isset( $_GET['page'] ) || 'amanah-authority' !== sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
			return;
		}
		wp_enqueue_media();
		$script = <<<'JS'
jQuery(function($) {
	var frame;
	$(document).on('click', '.amanah-profile-photo-select', function(e) {
		e.preventDefault();
		if (frame) { frame.open(); return; }
		frame = wp.media({
			title: 'Pilih Profile Photo',
			button: { text: 'Gunakan sebagai Profile Photo' },
			multiple: false,
			library: { type: 'image' }
		});
		frame.on('select', function() {
			var a = frame.state().get('selection').first().toJSON();
			$('#amanah-profile-photo-id').val(a.id);
			if (a.sizes && a.sizes.thumbnail) {
				$('#amanah-profile-photo-preview').attr('src', a.sizes.thumbnail.url).show();
			} else if (a.url) {
				$('#amanah-profile-photo-preview').attr('src', a.url).show();
			}
			$('.amanah-profile-photo-remove').show();
			$('.amanah-profile-photo-select').text('Ganti Foto');
		});
		frame.open();
	});
	$(document).on('click', '.amanah-profile-photo-remove', function(e) {
		e.preventDefault();
		$('#amanah-profile-photo-id').val('0');
		$('#amanah-profile-photo-preview').attr('src', '').hide();
		$(this).hide();
		$('.amanah-profile-photo-select').text('Upload / Pilih Foto');
	});
});
JS;
		wp_add_inline_script( 'media-editor', $script );
	}

	public function ensure_capabilities() {
		$administrator = get_role( 'administrator' );
		if ( $administrator ) {
			$administrator->add_cap( 'amanah_view_authority' );
			$administrator->add_cap( 'amanah_manage_authority' );
		}

		$editor = get_role( 'editor' );
		if ( $editor ) {
			$editor->add_cap( 'amanah_view_authority' );
		}
	}

	public function register_social_contact_methods( $methods, $user ) {
		foreach ( AMANAH_SEO_AI_Authority_Model::SOCIAL_PLATFORMS as $platform ) {
			$methods[ AMANAH_SEO_AI_Authority_Model::SOCIAL_META_PREFIX . $platform ] = ucfirst( $platform );
		}

		return $methods;
	}

	public function register_menu() {
		add_submenu_page(
			'amanah-seo',
			__( 'Authority & Author', 'amanah-seo-ai' ),
			__( 'Authority & Author', 'amanah-seo-ai' ),
			'amanah_view_authority',
			'amanah-authority',
			array( $this, 'render' )
		);
	}

	public function render() {
		if ( ! current_user_can( 'amanah_view_authority' ) ) {
			wp_die( esc_html__( 'You are not allowed to view authority profiles.', 'amanah-seo-ai' ), 403 );
		}

		$current_user_id = get_current_user_id();
		$entities        = $this->entity_model->get_current_user_entities( $current_user_id );
		$user_id         = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : $current_user_id;
		$entity_id       = isset( $_GET['entity_id'] ) ? absint( $_GET['entity_id'] ) : ( ! empty( $entities ) ? (int) $entities[0]->id : 0 );
		$selected_user   = $this->model->get_user( $user_id );
		$author          = $selected_user ? $this->model->get_author_profile( $user_id ) : array();
		$expert          = ( $selected_user && $entity_id ) ? $this->model->get_expert_profile( $user_id, $entity_id ) : array();
		$social          = $selected_user ? $this->model->get_social_profiles( $user_id ) : array();
		$message         = isset( $_GET['authority_message'] ) ? sanitize_text_field( wp_unslash( $_GET['authority_message'] ) ) : '';

		$users = current_user_can( 'list_users' )
			? get_users(
				array(
					'orderby' => 'display_name',
					'order'   => 'ASC',
					'number'  => 200,
				)
			)
			: array( $selected_user );

		$assignments = $this->model->get_authority_assignments( $entities, $users );

		require AMANAH_SEO_AI_PATH . 'modules/authority/views/authority-form.php';
	}
}
