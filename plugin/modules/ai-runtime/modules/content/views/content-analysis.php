<?php
defined( 'ABSPATH' ) || exit;

$content_input_context = $result instanceof AMANAH_SEO_AI_Content_Analysis ? $result->get_input_context() : array();
$content_reference     = $result instanceof AMANAH_SEO_AI_Content_Analysis ? $result->get_content_reference() : array();
$content_author        = $post ? get_the_author_meta( 'display_name', (int) $post->post_author ) : '';
$content_author        = $content_author ? sanitize_text_field( $content_author ) : '';
$analysis              = $result instanceof AMANAH_SEO_AI_Content_Analysis ? $result->get_analysis() : array();
$analysis_completed    = $result instanceof AMANAH_SEO_AI_Content_Analysis && 'COMPLETED' === $result->get_status();
$analysis_failed       = $result instanceof AMANAH_SEO_AI_Content_Analysis && 'FAILED' === $result->get_status();

$quality_dimensions = array(
	'Content Identity',
	'Search Intent Satisfaction',
	'Topic Coverage',
	'Content Completeness',
	'Factual Integrity',
	'Entity & Context',
	'Article Structure',
	'Readability & User Experience',
	'Semantic SEO',
	'Value & Differentiation',
	'FAQ Quality',
	'AI/Search Readiness',
);

$requirement_sections = array(
	'must_improve'         => 'Must Improve',
	'must_cover'           => 'Must Cover',
	'must_answer'          => 'Must Answer',
	'must_preserve'        => 'Must Preserve',
	'must_avoid'           => 'Must Avoid',
	'recommended_direction'=> 'Recommended Direction',
);

$unavailable_message = __( 'Belum dijalankan karena analisis AI belum berhasil dijalankan. Hasil akan tersedia setelah provider AI dapat memproses permintaan.', 'amanah-seo-ai' );
$unknown_message     = __( 'Belum tersedia dari hasil analisis.', 'amanah-seo-ai' );
$display_keyword      = isset( $content_input_context['keyword'] ) && '' !== trim( (string) $content_input_context['keyword'] ) ? $content_input_context['keyword'] : $keyword;
$display_keyword      = $display_keyword ? $display_keyword : 'Belum terdeteksi';
?>
<div class="wrap">
	<h1><?php echo esc_html__( 'Content Analyzer', 'amanah-seo-ai' ); ?></h1>
	<p><?php echo esc_html__( 'Analyze WordPress content with AMANAH SEO AI and review the structured analysis result.', 'amanah-seo-ai' ); ?></p>

	<?php if ( $error ) : ?>
		<div class="notice notice-error">
			<p><strong><?php echo esc_html__( 'Analisis gagal.', 'amanah-seo-ai' ); ?></strong></p>
			<p><?php
				if ( ! empty( $diagnostic['cause'] ) && 'provider_request_invalid' === $diagnostic['cause'] ) {
					echo esc_html__( 'Layanan AI menolak permintaan. Periksa konfigurasi atau API key provider yang digunakan.', 'amanah-seo-ai' );
				} elseif ( ! empty( $diagnostic['cause'] ) && 'provider_quota_exceeded' === $diagnostic['cause'] ) {
					echo esc_html__( 'Kuota provider AI tidak mencukupi untuk menjalankan analisis.', 'amanah-seo-ai' );
				} elseif ( ! empty( $diagnostic['cause'] ) && 'provider_model_not_found' === $diagnostic['cause'] ) {
					echo esc_html__( 'Model AI yang dikonfigurasi tidak tersedia pada provider.', 'amanah-seo-ai' );
				} elseif ( ! empty( $diagnostic['cause'] ) && 'authentication_failed' === $diagnostic['cause'] ) {
					echo esc_html__( 'Autentikasi ke provider AI gagal. Periksa kredensial provider.', 'amanah-seo-ai' );
				} elseif ( ! empty( $diagnostic['cause'] ) && 'provider_server_failure' === $diagnostic['cause'] ) {
					echo esc_html__( 'Provider AI sedang mengalami gangguan saat memproses permintaan.', 'amanah-seo-ai' );
				} else {
					echo esc_html( $error );
				}
			?></p>
			<?php if ( ! empty( $diagnostic['message'] ) ) : ?>
				<p>
					<strong><?php echo esc_html__( 'Detail Provider:', 'amanah-seo-ai' ); ?></strong>
					<?php echo esc_html( $diagnostic['message'] ); ?>
				</p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<form method="post">
		<?php wp_nonce_field( 'amanah_content_analyze' ); ?>
		<input type="hidden" name="amanah_content_analyze" value="1" />
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="amanah-content-post-id"><?php echo esc_html__( 'Post ID', 'amanah-seo-ai' ); ?></label></th>
				<td><input id="amanah-content-post-id" class="small-text" type="number" min="1" name="post_id" value="<?php echo esc_attr( $post_id ); ?>" required /></td>
			</tr>
			<?php if ( $post ) : ?>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Title', 'amanah-seo-ai' ); ?></th>
					<td><input class="large-text" type="text" value="<?php echo esc_attr( get_the_title( $post_id ) ); ?>" readonly /></td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Content', 'amanah-seo-ai' ); ?></th>
					<td><textarea class="large-text" rows="10" readonly><?php echo esc_textarea( $post->post_content ); ?></textarea></td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Detected Keyword', 'amanah-seo-ai' ); ?></th>
					<td><?php echo esc_html( $keyword ? $keyword : 'Belum terdeteksi' ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Entity', 'amanah-seo-ai' ); ?></th>
					<td><?php echo esc_html( $entity ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Author', 'amanah-seo-ai' ); ?></th>
					<td><?php echo esc_html( $content_author ); ?></td>
				</tr>
			<?php endif; ?>
		</table>
		<?php submit_button( __( 'Analyze Content', 'amanah-seo-ai' ) ); ?>
	</form>

	<?php if ( $analysis_completed || $analysis_failed ) : ?>
		<hr />
		<h2><?php echo esc_html__( 'Analysis Result', 'amanah-seo-ai' ); ?></h2>

		<h2><?php echo esc_html__( '1. Content Context', 'amanah-seo-ai' ); ?></h2>
		<table class="widefat striped">
			<tbody>
				<tr><th><?php echo esc_html__( 'Post ID', 'amanah-seo-ai' ); ?></th><td><?php echo esc_html( $post_id ); ?></td></tr>
				<tr><th><?php echo esc_html__( 'Title', 'amanah-seo-ai' ); ?></th><td><?php echo esc_html( $content_input_context['title'] ?? get_the_title( $post_id ) ); ?></td></tr>
				<tr><th><?php echo esc_html__( 'Keyword', 'amanah-seo-ai' ); ?></th><td><?php echo esc_html( $display_keyword ); ?></td></tr>
				<tr><th><?php echo esc_html__( 'Entity', 'amanah-seo-ai' ); ?></th><td><?php echo esc_html( $content_input_context['entity'] ?? $entity ); ?></td></tr>
				<tr><th><?php echo esc_html__( 'Author', 'amanah-seo-ai' ); ?></th><td><?php echo esc_html( $content_author ); ?></td></tr>
				<tr><th><?php echo esc_html__( 'Content Version', 'amanah-seo-ai' ); ?></th><td><?php echo esc_html( $content_reference['content_version'] ?? '' ); ?></td></tr>
				<tr><th><?php echo esc_html__( 'Context', 'amanah-seo-ai' ); ?></th><td><?php echo esc_html( $content_reference['context'] ?? '' ); ?></td></tr>
			</tbody>
		</table>

		<h2><?php echo esc_html__( '2. Overall Content Assessment', 'amanah-seo-ai' ); ?></h2>
		<?php if ( $analysis_completed && ! empty( $analysis['overall_assessment'] ) ) : ?>
			<p><?php echo esc_html( is_scalar( $analysis['overall_assessment'] ) ? $analysis['overall_assessment'] : wp_json_encode( $analysis['overall_assessment'] ) ); ?></p>
		<?php else : ?>
			<p><?php echo esc_html( $analysis_failed ? $unavailable_message : $unknown_message ); ?></p>
		<?php endif; ?>

		<h2><?php echo esc_html__( '3. Analysis Score', 'amanah-seo-ai' ); ?></h2>
		<?php if ( $analysis_completed && isset( $analysis['score'] ) ) : ?>
			<p><strong style="font-size: 24px;"><?php echo esc_html( $analysis['score'] ); ?>/100</strong></p>
		<?php else : ?>
			<p><?php echo esc_html( $analysis_failed ? $unavailable_message : $unknown_message ); ?></p>
		<?php endif; ?>

		<h2><?php echo esc_html__( '4. Content Understanding', 'amanah-seo-ai' ); ?></h2>
		<?php if ( $analysis_completed && ! empty( $analysis['content_understanding'] ) ) : ?>
			<p><?php echo esc_html( is_scalar( $analysis['content_understanding'] ) ? $analysis['content_understanding'] : wp_json_encode( $analysis['content_understanding'] ) ); ?></p>
		<?php else : ?>
			<p><?php echo esc_html( $analysis_failed ? $unavailable_message : $unknown_message ); ?></p>
		<?php endif; ?>

		<h2><?php echo esc_html__( '5. Strengths', 'amanah-seo-ai' ); ?></h2>
		<?php if ( $analysis_completed && ! empty( $analysis['strengths'] ) && is_array( $analysis['strengths'] ) ) : ?>
			<ul>
				<?php foreach ( $analysis['strengths'] as $strength ) : ?>
					<li><?php echo esc_html( is_scalar( $strength ) ? (string) $strength : wp_json_encode( $strength ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p><?php echo esc_html( $analysis_failed ? $unavailable_message : $unknown_message ); ?></p>
		<?php endif; ?>

		<h2><?php echo esc_html__( '6. Quality Dimensions', 'amanah-seo-ai' ); ?></h2>
		<table class="widefat striped">
			<thead>
				<tr><th><?php echo esc_html__( 'Quality Dimension', 'amanah-seo-ai' ); ?></th><th><?php echo esc_html__( 'Assessment', 'amanah-seo-ai' ); ?></th></tr>
			</thead>
			<tbody>
				<?php foreach ( $quality_dimensions as $dimension ) : ?>
					<?php
					$dimension_data = isset( $analysis['quality_dimensions'] ) && is_array( $analysis['quality_dimensions'] ) && isset( $analysis['quality_dimensions'][ $dimension ] ) ? $analysis['quality_dimensions'][ $dimension ] : null;
					$dimension_value = '';
					if ( is_array( $dimension_data ) ) {
						$dimension_value = isset( $dimension_data['assessment'] ) ? $dimension_data['assessment'] : ( isset( $dimension_data['status'] ) ? $dimension_data['status'] : '' );
					} elseif ( is_scalar( $dimension_data ) ) {
						$dimension_value = (string) $dimension_data;
					}
					?>
					<tr>
						<td><?php echo esc_html( $dimension ); ?></td>
						<td><?php echo esc_html( $dimension_value ? $dimension_value : ( $analysis_failed ? $unavailable_message : 'UNKNOWN / UNASSESSED' ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<h2><?php echo esc_html__( '7. Coverage Gaps', 'amanah-seo-ai' ); ?></h2>
		<?php if ( $analysis_completed && ! empty( $analysis['coverage_gaps'] ) && is_array( $analysis['coverage_gaps'] ) ) : ?>
			<ul>
				<?php foreach ( $analysis['coverage_gaps'] as $gap ) : ?>
					<li><?php echo esc_html( is_scalar( $gap ) ? (string) $gap : wp_json_encode( $gap ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p><?php echo esc_html( $analysis_failed ? $unavailable_message : $unknown_message ); ?></p>
		<?php endif; ?>

		<h2><?php echo esc_html__( '8. Weaknesses', 'amanah-seo-ai' ); ?></h2>
		<?php if ( $analysis_completed && ! empty( $analysis['weaknesses'] ) && is_array( $analysis['weaknesses'] ) ) : ?>
			<ul>
				<?php foreach ( $analysis['weaknesses'] as $weakness ) : ?>
					<li><?php echo esc_html( is_scalar( $weakness ) ? (string) $weakness : wp_json_encode( $weakness ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p><?php echo esc_html( $analysis_failed ? $unavailable_message : __( 'No weaknesses were identified from the supplied content.', 'amanah-seo-ai' ) ); ?></p>
		<?php endif; ?>

		<h2><?php echo esc_html__( '9. Recommendations', 'amanah-seo-ai' ); ?></h2>
		<?php if ( $analysis_completed && ! empty( $analysis['recommendations'] ) && is_array( $analysis['recommendations'] ) ) : ?>
			<ul>
				<?php foreach ( $analysis['recommendations'] as $recommendation ) : ?>
					<li><?php echo esc_html( is_scalar( $recommendation ) ? (string) $recommendation : wp_json_encode( $recommendation ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p><?php echo esc_html( $analysis_failed ? $unavailable_message : __( 'No recommendations were returned from the supplied content.', 'amanah-seo-ai' ) ); ?></p>
		<?php endif; ?>

		<h2><?php echo esc_html__( '10. FAQ Opportunities', 'amanah-seo-ai' ); ?></h2>
		<?php if ( $analysis_completed && ! empty( $analysis['faq_suggestions'] ) && is_array( $analysis['faq_suggestions'] ) ) : ?>
			<ul>
				<?php foreach ( $analysis['faq_suggestions'] as $faq ) : ?>
					<li><?php echo esc_html( is_scalar( $faq ) ? (string) $faq : wp_json_encode( $faq ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p><?php echo esc_html( $analysis_failed ? $unavailable_message : __( 'No FAQ suggestions were identified from the supplied content.', 'amanah-seo-ai' ) ); ?></p>
		<?php endif; ?>

		<h2><?php echo esc_html__( '11. Generation Requirements', 'amanah-seo-ai' ); ?></h2>
		<?php
		$generation_requirements = isset( $analysis['generation_requirements'] ) && is_array( $analysis['generation_requirements'] ) ? $analysis['generation_requirements'] : array();
		?>
		<p><?php echo esc_html__( 'Persyaratan editorial ini diturunkan dari hasil analisis. Persyaratan bukan fakta dan tidak menggantikan Quality Evaluation.', 'amanah-seo-ai' ); ?></p>
		<?php foreach ( $requirement_sections as $type => $label ) : ?>
			<h3><?php echo esc_html( $label ); ?></h3>
			<?php if ( $analysis_completed && ! empty( $generation_requirements[ $type ] ) ) : ?>
				<ul>
					<?php foreach ( $generation_requirements[ $type ] as $req ) : ?>
						<li>
							<strong><?php echo esc_html( $req['severity'] ?? 'UNKNOWN/UNASSESSED' ); ?></strong>
							· <?php echo esc_html( $req['statement'] ?? '' ); ?>
							<em>(<?php echo esc_html( $req['quality_dimension'] ?? 'UNKNOWN/UNASSESSED' ); ?> · <?php echo esc_html( $req['disposition'] ?? 'recommended' ); ?>)</em>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php elseif ( $analysis_failed ) : ?>
				<p><?php echo esc_html( $unavailable_message ); ?></p>
			<?php else : ?>
				<p><?php echo esc_html__( 'Tidak ada requirement pada kategori ini.', 'amanah-seo-ai' ); ?></p>
			<?php endif; ?>
		<?php endforeach; ?>

		<h2><?php echo esc_html__( '12. Recommended Article Direction', 'amanah-seo-ai' ); ?></h2>
		<?php
		$direction = ! empty( $generation_requirements['recommended_direction'] ) ? $generation_requirements['recommended_direction'] : ( ! empty( $analysis['recommended_direction'] ) ? $analysis['recommended_direction'] : array() );
		?>
		<?php if ( $analysis_completed && ! empty( $direction ) ) : ?>
			<ul>
				<?php foreach ( $direction as $item ) : ?>
					<li><?php echo esc_html( is_array( $item ) ? ( $item['statement'] ?? wp_json_encode( $item ) ) : (string) $item ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p><?php echo esc_html( $analysis_failed ? $unavailable_message : $unknown_message ); ?></p>
		<?php endif; ?>

		<?php if ( $analysis_completed && ! empty( $generation_requirements ) ) : ?>
			<p><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=amanah-content-generation&post_id=' . absint( $post_id ) ) ); ?>"><?php echo esc_html__( 'Continue to Article Generation with this Analysis', 'amanah-seo-ai' ); ?></a></p>
		<?php endif; ?>

		<h2><?php echo esc_html__( '13. Execution & Traceability', 'amanah-seo-ai' ); ?></h2>
		<table class="widefat striped">
			<tbody>
				<tr><th><?php echo esc_html__( 'Status', 'amanah-seo-ai' ); ?></th><td><?php echo esc_html( $result->get_status() ); ?></td></tr>
				<tr><th><?php echo esc_html__( 'Analysis ID', 'amanah-seo-ai' ); ?></th><td><?php echo esc_html( $result->get_analysis_id() ); ?></td></tr>
				<tr><th><?php echo esc_html__( 'Analysis Version', 'amanah-seo-ai' ); ?></th><td><?php echo esc_html( $result->get_version() ); ?></td></tr>
				<?php if ( $analysis_completed ) : ?>
					<tr><th><?php echo esc_html__( 'Provider', 'amanah-seo-ai' ); ?></th><td><?php echo esc_html( $result->get_traceability()['provider'] ?? '' ); ?></td></tr>
					<tr><th><?php echo esc_html__( 'Model', 'amanah-seo-ai' ); ?></th><td><?php echo esc_html( $result->get_traceability()['model'] ?? '' ); ?></td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	<?php elseif ( $post_id && ! $error ) : ?>
		<p><?php echo esc_html__( 'No analysis is available for this post yet.', 'amanah-seo-ai' ); ?></p>
	<?php endif; ?>
</div>
