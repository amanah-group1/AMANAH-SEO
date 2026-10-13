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
	<h1><?php echo esc_html__( 'AMANAH SEO AI Settings', 'amanah-seo-ai' ); ?></h1>
	<p><?php echo esc_html__( 'Konfigurasi provider AI dan pilih model yang tersedia langsung dari provider. Model tidak perlu diisi secara manual.', 'amanah-seo-ai' ); ?></p>

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
					<p class="description" id="ai_model_help"><?php echo esc_html( $data['provider'] ? 'Models are loaded from the selected provider.' : 'Select a provider and configure its API key to load available models.' ); ?></p>
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

	<div class="card" style="max-width: 900px; margin-top: 32px; padding: 28px;">
		<h2 style="font-size: 24px; margin-top: 0;"><?php echo esc_html__( 'Jika AI belum mengenal bisnis Anda, apakah calon pelanggan akan menemukannya?', 'amanah-seo-ai' ); ?></h2>
		<h3><?php echo esc_html__( 'Dunia pencarian sedang berubah.', 'amanah-seo-ai' ); ?></h3>
		<p><?php echo esc_html__( 'Orang tidak lagi hanya mengetik kata kunci di Google. Mereka mulai bertanya langsung kepada ChatGPT, Gemini, Perplexity, dan berbagai AI lainnya untuk menemukan bisnis, brand, produk, layanan, dan informasi yang mereka percaya.', 'amanah-seo-ai' ); ?></p>
		<p><strong><?php echo esc_html__( 'Ketika seseorang bertanya kepada AI tentang bisnis Anda, apakah AI tahu siapa Anda — dan mengapa Anda layak direkomendasikan?', 'amanah-seo-ai' ); ?></strong></p>

		<h3><?php echo esc_html__( 'AMANAH SEO AI membantu Anda membangun fondasinya.', 'amanah-seo-ai' ); ?></h3>
		<p><?php echo esc_html__( 'Bukan sekadar membuat konten. Bukan sekadar mengejar ranking. AMANAH SEO AI dirancang untuk membantu membangun fondasi AI Visibility Optimization dengan menghubungkan elemen-elemen penting yang membantu AI memahami sebuah brand secara lebih utuh.', 'amanah-seo-ai' ); ?></p>
		<ul>
			<li><strong><?php echo esc_html__( 'Identity', 'amanah-seo-ai' ); ?></strong> — <?php echo esc_html__( 'siapa Anda dan apa yang Anda representasikan.', 'amanah-seo-ai' ); ?></li>
			<li><strong><?php echo esc_html__( 'Authority', 'amanah-seo-ai' ); ?></strong> — <?php echo esc_html__( 'mengapa Anda layak dipercaya.', 'amanah-seo-ai' ); ?></li>
			<li><strong><?php echo esc_html__( 'Expertise', 'amanah-seo-ai' ); ?></strong> — <?php echo esc_html__( 'bidang apa yang benar-benar Anda kuasai.', 'amanah-seo-ai' ); ?></li>
			<li><strong><?php echo esc_html__( 'AI Capability', 'amanah-seo-ai' ); ?></strong> — <?php echo esc_html__( 'bagaimana kemampuan AI dikonfigurasi dan digunakan secara terkontrol.', 'amanah-seo-ai' ); ?></li>
		</ul>

		<h3><?php echo esc_html__( 'Karena AI tidak hanya membaca apa yang Anda tulis.', 'amanah-seo-ai' ); ?></h3>
		<p><?php echo esc_html__( 'AI juga berusaha memahami siapa yang berada di balik informasi tersebut, apa keahliannya, seberapa kredibel sumbernya, dan bagaimana seluruh informasi itu saling terhubung.', 'amanah-seo-ai' ); ?></p>
		<p><strong><?php echo esc_html__( 'Semakin AI menjadi bagian dari perjalanan pencarian seseorang, semakin penting bagi sebuah bisnis untuk memiliki identitas dan authority yang dapat dipahami dengan jelas.', 'amanah-seo-ai' ); ?></strong></p>

		<h3><?php echo esc_html__( 'Bangun fondasinya sebelum persaingannya semakin ramai.', 'amanah-seo-ai' ); ?></h3>
		<p><?php echo esc_html__( 'Gunakan halaman ini untuk mengatur AI Provider dan model AI yang tersedia. Sementara itu, Entity Profile dan Authority & Author menjadi kemampuan terpisah untuk membangun fondasi identitas dan authority bisnis Anda sesuai hak akses yang tersedia.', 'amanah-seo-ai' ); ?></p>
		<p><strong><?php echo esc_html__( 'Jangan menunggu sampai AI mulai menentukan siapa yang terlihat. Mulai bangun bagaimana AI mengenal bisnis Anda hari ini.', 'amanah-seo-ai' ); ?></strong></p>
	</div>
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
			: 'Configure the selected provider API key to load its available models.';
	}

	provider.addEventListener('change', refreshModels);
	refreshModels();
});
</script>
