<?php
/**
 * AI configuration view.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

$model_options = 'gemini' === $data['provider'] ? $data['gemini_models'] : $data['openai_models'];
?>
<div class="wrap">
	<h1><?php echo esc_html__( 'AI Provider & Model', 'amanah-seo-ai' ); ?></h1>
	<p><?php echo esc_html__( 'Pilih provider dan model yang tersedia pada katalog terkontrol AMANAH SEO. Simpan API key provider yang akan digunakan oleh modul AI.', 'amanah-seo-ai' ); ?></p>

	<?php if ( isset( $_GET['updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo esc_html__( 'AI configuration saved.', 'amanah-seo-ai' ); ?></p></div>
	<?php endif; ?>

	<?php if ( isset( $_GET['error'] ) ) : ?>
		<div class="notice notice-error"><p><?php echo esc_html__( 'AI configuration could not be saved. Pastikan API key provider tersedia dan model yang dipilih masih disediakan provider.', 'amanah-seo-ai' ); ?></p></div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="amanah_seo_ai_save_settings" />
		<?php wp_nonce_field( 'amanah_seo_ai_save_settings' ); ?>

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="ai_provider"><?php echo esc_html__( 'AI Provider', 'amanah-seo-ai' ); ?></label></th>
				<td>
					<select id="ai_provider" name="ai_provider" required>
						<option value=""><?php echo esc_html__( 'Select provider', 'amanah-seo-ai' ); ?></option>
						<option value="gemini" <?php selected( $data['provider'], 'gemini' ); ?>>Gemini</option>
						<option value="openai" <?php selected( $data['provider'], 'openai' ); ?>>OpenAI</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="ai_model"><?php echo esc_html__( 'Model', 'amanah-seo-ai' ); ?></label></th>
				<td>
					<select class="regular-text" id="ai_model" name="ai_model">
						<option value=""><?php echo esc_html__( 'Select a model provided by the selected provider', 'amanah-seo-ai' ); ?></option>
						<?php foreach ( $model_options as $model ) : ?>
							<option value="<?php echo esc_attr( $model ); ?>" <?php selected( $data['current_model'], $model ); ?>><?php echo esc_html( $model ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description" id="ai_model_help"><?php echo esc_html( $data['provider'] ? 'Models are shown from the AMANAH SEO supported model catalog.' : 'Select a provider and choose a supported model.' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="gemini_api_key"><?php echo esc_html__( 'Gemini API Key', 'amanah-seo-ai' ); ?></label></th>
				<td><input class="regular-text" id="gemini_api_key" name="gemini_api_key" type="password" value="" autocomplete="new-password" /><p class="description"><?php echo esc_html__( 'Leave empty to keep the existing credential.', 'amanah-seo-ai' ); ?></p></td>
			</tr>
			<tr>
				<th scope="row"><label for="openai_api_key"><?php echo esc_html__( 'OpenAI API Key', 'amanah-seo-ai' ); ?></label></th>
				<td><input class="regular-text" id="openai_api_key" name="openai_api_key" type="password" value="" autocomplete="new-password" /><p class="description"><?php echo esc_html__( 'Leave empty to keep the existing credential.', 'amanah-seo-ai' ); ?></p></td>
			</tr>
		</table>

		<?php submit_button( __( 'Save AI Configuration', 'amanah-seo-ai' ) ); ?>
	</form>

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
	const provider = document.getElementById('ai_provider');
	const model = document.getElementById('ai_model');
	const help = document.getElementById('ai_model_help');
	const catalogs = <?php echo wp_json_encode( array( 'gemini' => $data['gemini_models'], 'openai' => $data['openai_models'] ) ); ?>;
	const selectedModels = <?php echo wp_json_encode( array( 'gemini' => $data['gemini_model'], 'openai' => $data['openai_model'] ) ); ?>;

	function refreshModels() {
		const currentProvider = provider.value;
		const models = Array.isArray(catalogs[currentProvider]) ? catalogs[currentProvider] : [];
		const selected = selectedModels[currentProvider] || '';

		model.innerHTML = '';
		const placeholder = document.createElement('option');
		placeholder.value = '';
		placeholder.textContent = models.length ? 'Select a model provided by the selected provider' : 'No provider models available';
		model.appendChild(placeholder);

		models.forEach(function (modelName) {
			const option = document.createElement('option');
			option.value = modelName;
			option.textContent = modelName;
			option.selected = modelName === selected;
			model.appendChild(option);
		});

		help.textContent = models.length
			? 'Models are loaded from the selected provider.'
			: 'Choose a model from the supported catalog. The provider API key is required for AI requests.';
	}

	provider.addEventListener('change', refreshModels);
	refreshModels();
});
</script>
