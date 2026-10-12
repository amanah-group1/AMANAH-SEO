<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\AI\Provider;

use Amanah\SEOEngine\AI\AIRequest;
use Amanah\SEOEngine\AI\AIResponse;
use Amanah\SEOEngine\Application\Content\ContentGenerationRules;

defined( 'ABSPATH' ) || exit;

/**
 * Zero-configuration content engine.
 *
 * Produces a substantive, factual-safe first draft from the approved
 * Page Blueprint. It never invents business facts, prices, testimonials,
 * authors, or unverifiable URLs.
 */
final class DefaultAIProvider implements AIProviderInterface {

 public function getProviderIdentifier(): string { return 'amanah_seo_engine'; }
 public function getProviderName(): string { return 'Amanah SEO Engine Default'; }

 public function getCapabilities(): array {
  return array( ProviderCapabilities::TEXT_GENERATION, ProviderCapabilities::STRUCTURED_OUTPUT );
 }

 public function validateCredentials( array $credentials ): array {
  return array( 'valid' => true, 'category' => null );
 }

 public function discoverModels( array $credentials ): array {
  return array( array( 'id' => 'amanah-default-v1', 'name' => 'Amanah Default Content Engine v1' ) );
 }

 public function generate( AIRequest $request, array $credentials ): AIResponse {
  $context = $request->getContext();
  $blueprint = isset( $context['page_blueprint'] ) && is_array( $context['page_blueprint'] ) ? $context['page_blueprint'] : array();
  $data = isset( $blueprint['data'] ) && is_array( $blueprint['data'] ) ? $blueprint['data'] : $blueprint;

  $keyword = $this->extractKeyword( $data );
  $article_type = sanitize_key( (string) ( $data['article_type'] ?? 'supporting' ) );
  $sections = isset( $data['sections'] ) && is_array( $data['sections'] ) ? $data['sections'] : array();
  $coverage = isset( $data['intent_coverage'] ) && is_array( $data['intent_coverage'] ) ? $data['intent_coverage'] : array();
  $search_intent = isset( $data['search_intent'] ) && is_array( $data['search_intent'] ) ? $data['search_intent'] : array();

  $title = $this->extractTitle( $data, $keyword );
  $body = $this->buildBody( $keyword, $sections, $coverage, $search_intent, $article_type, $title );

  $metadata = array(
   'generation_mode' => 'default',
   'provider' => 'amanah_seo_engine',
   'model' => 'amanah-default-v1',
   'content_package' => array(
    'seo' => array(
     'seo_title' => $title,
     'meta_description' => $this->buildMetaDescription( $keyword ),
     'slug' => sanitize_title( $keyword ),
    ),
    'media' => array(
     'featured_image' => array(
      'required' => true,
      'alt_text' => 'Ilustrasi yang relevan dengan ' . $keyword,
     ),
     'content_images' => array(),
    ),
    'taxonomy' => array( 'category' => null, 'tags' => array() ),
    'linking' => array( 'internal_links' => array(), 'external_references' => array() ),
    'schema' => array(
     'recommended_type' => 'Article',
     'faq' => $this->faqSchema( $keyword ),
    ),
    'publishing' => array( 'post_type' => 'post', 'status' => 'draft' ),
   ),
   'factual_safety' => array(
    'mode' => 'verified_context_only',
    'business_claims_require_source' => true,
    'prices_require_source' => true,
    'external_urls_require_verification' => true,
   ),
   'generation_policy' => 'aturan_generate_konten',
   'research_validation' => array(
    'evergreen' => array( 'status' => 'needs_validation', 'evidence' => null ),
    'serp' => array( 'status' => 'needs_validation', 'evidence' => null ),
    'trend' => array( 'status' => 'needs_validation', 'evidence' => null ),
   ),
   'keyword_strategy' => array( 'primary' => $keyword, 'secondary' => array(), 'long_tail' => array() ),
   'search_intent' => $search_intent,
   'content_structure' => array( 'format' => $article_type, 'sections' => $sections, 'hub_and_spoke' => true ),
   'trust' => array( 'factual_verification_required' => true, 'author_status' => 'needs_validation', 'dates_status' => 'needs_validation' ),
   'maintenance' => array( 'refresh_cycle' => '12_months', 'date_modified_rule' => 'only_after_actual_content_change', 'gsc_optimization' => 'needs_validation', 'backlink_recovery' => 'needs_validation' ),
  );

  $payload = array(
   'title' => $title,
   'body' => $body,
   'excerpt' => 'Panduan informatif tentang ' . $keyword . ' yang disusun dari Page Blueprint dan siap ditinjau sebelum diterbitkan.',
   'metadata' => $metadata,
  );

  return new AIResponse(
   $this->getProviderIdentifier(),
   'amanah-default-v1',
   wp_json_encode( $payload ),
   array( 'usage_status' => 'not_applicable' ),
   array( 'mode' => 'deterministic_default' )
  );
 }

 public function normalizeError( $response ): array { return array( 'category' => 'system' ); }

 private function buildBody( string $keyword, array $sections, array $coverage, array $search_intent, string $article_type, string $title ): string {
  $body  = '<h1>' . esc_html( $title ) . '</h1>';
  $body .= '<p>' . esc_html( $this->intro( $keyword, $search_intent ) ) . '</p>';
  $body .= '<p>Istilah ini dapat dicari dengan kebutuhan yang berbeda: mengenal topik, memahami pilihan, membandingkan alternatif, sampai mencari cara mendapatkan produk atau layanan yang sesuai. Karena itu, pembahasan berikut disusun bertahap agar pembaca dapat menemukan informasi yang dibutuhkan tanpa harus menebak konteksnya.</p>';

  $seen = array();
  foreach ( $sections as $index => $section ) {
   $title = $this->sectionTitle( $section, $index + 1 );
   $key = sanitize_title( $title );
   if ( isset( $seen[ $key ] ) ) { continue; }
   $seen[ $key ] = true;
   $purpose = $this->sectionPurpose( $section );
   $body .= '<h2>' . esc_html( $title ) . '</h2>';
   $body .= '<p>' . esc_html( $this->sectionOpening( $keyword, $title, $purpose ) ) . '</p>';
   $body .= '<p>' . esc_html( $this->sectionValue( $keyword, $title ) ) . '</p>';
   $body .= '<p>' . esc_html( $this->sectionSafety( $title ) ) . '</p>';
  }

  if ( empty( $sections ) ) {
   $body .= '<h2>Mengenal ' . esc_html( $keyword ) . '</h2>';
   $body .= '<p>' . esc_html( $this->sectionValue( $keyword, 'Mengenal ' . $keyword ) ) . '</p>';
   $body .= '<h2>Hal yang Perlu Dipertimbangkan</h2>';
   $body .= '<p>Pertimbangkan tujuan penggunaan, bentuk atau varian yang tersedia, informasi produk yang dapat diverifikasi, serta sumber pembelian yang jelas. Hindari mengambil keputusan hanya berdasarkan klaim yang tidak memiliki sumber.</p>';
  }

  if ( ! empty( $coverage ) ) {
   $body .= '<h2>Pertanyaan yang Sering Dicari</h2>';
   $faq = $this->faqItems( $keyword );
   foreach ( $faq as $item ) {
    $body .= '<h3>' . esc_html( $item['question'] ) . '</h3>';
    $body .= '<p>' . esc_html( $item['answer'] ) . '</p>';
   }
  }

  $body .= '<h2>Kesimpulan</h2>';
  $body .= '<p>' . esc_html( $this->conclusion( $keyword, $article_type ) ) . '</p>';
  $body .= '<h2>Langkah Berikutnya</h2>';
  $body .= '<p>Jika halaman ini digunakan untuk kebutuhan bisnis, lengkapi informasi produk atau layanan, harga bila memang tersedia, tautan internal yang benar, media, taxonomy, dan data schema sebelum Quality Evaluation. Informasi yang belum dapat diverifikasi harus diperiksa oleh editor manusia.</p>';

  return $body;
 }

 private function intro( string $keyword, array $intent ): string {
  $name = isset( $intent['name'] ) ? trim( (string) $intent['name'] ) : '';
  return '' !== $name
   ? 'Jika Anda sedang mencari informasi tentang ' . $keyword . ', kebutuhan utamanya perlu dipahami lebih dulu: ' . $name . '. Panduan ini membantu memetakan informasi penting secara terstruktur.'
   : 'Jika Anda sedang mencari informasi tentang ' . $keyword . ', halaman ini memberikan pembahasan terstruktur mulai dari pengenalan, hal yang perlu diperhatikan, sampai langkah berikutnya.';
 }

 private function sectionOpening( string $keyword, string $title, string $purpose ): string {
  if ( '' !== $purpose ) {
   return 'Bagian ' . $title . ' membahas ' . $purpose . ' dalam konteks ' . $keyword . '. Fokusnya adalah membantu pembaca memahami informasi yang relevan sebelum mengambil langkah berikutnya.';
  }
  return 'Bagian ini menjelaskan ' . $title . ' dalam konteks ' . $keyword . ', sehingga pembaca dapat memahami topik tanpa bergantung pada asumsi atau klaim yang belum terverifikasi.';
 }

 private function sectionValue( string $keyword, string $title ): string {
  return 'Saat menilai ' . $keyword . ', perhatikan hubungan antara kebutuhan Anda dan informasi yang tersedia pada bagian ' . $title . '. Untuk keputusan yang bersifat komersial, bandingkan informasi yang benar-benar tersedia seperti pilihan produk, bentuk atau varian, cara memperoleh, dan ketentuan yang berlaku.';
 }

 private function sectionSafety( string $title ): string {
  return 'Jika informasi spesifik belum tersedia pada data website, jangan mengisinya dengan perkiraan. Harga, spesifikasi, sejarah, ketersediaan, testimonial, manfaat, dan klaim bisnis harus berasal dari sumber yang dapat diperiksa dan diperbarui oleh editor.';
 }

 private function conclusion( string $keyword, string $article_type ): string {
  return 'Pembahasan ' . $keyword . ' sebaiknya digunakan sebagai dasar untuk memahami kebutuhan pencarian dan memilih informasi yang relevan. Untuk ' . $article_type . ' content, langkah berikutnya adalah memastikan setiap fakta dan detail bisnis telah diverifikasi sebelum halaman dianggap siap diterbitkan.';
 }

 private function faqItems( string $keyword ): array {
  return array(
   array( 'question' => 'Apa yang perlu diketahui tentang ' . $keyword . '?', 'answer' => 'Mulailah dari konteks, tujuan penggunaan, pilihan yang tersedia, serta informasi yang dapat diverifikasi dari sumber resmi atau website terkait.' ),
   array( 'question' => 'Bagaimana memilih ' . $keyword . ' yang sesuai?', 'answer' => 'Sesuaikan pilihan dengan kebutuhan dan bandingkan informasi produk atau layanan yang benar-benar tersedia. Jangan menjadikan satu klaim tanpa sumber sebagai dasar keputusan.' ),
   array( 'question' => 'Di mana mendapatkan informasi produk atau layanan ' . $keyword . '?', 'answer' => 'Gunakan halaman produk, halaman layanan, atau kanal resmi yang tersedia pada website. Tautan yang belum diverifikasi sebaiknya tidak dicantumkan sebagai rekomendasi.' ),
   array( 'question' => 'Apakah harga ' . $keyword . ' selalu sama?', 'answer' => 'Harga dapat berbeda menurut produk, varian, penjual, periode, atau ketentuan transaksi. Cantumkan harga hanya jika tersedia dan masih dapat diverifikasi.' ),
   array( 'question' => 'Apa yang harus diperiksa sebelum membeli?', 'answer' => 'Periksa identitas produk atau layanan, varian, informasi yang relevan, harga bila tersedia, ketentuan transaksi, dan sumber penjual sebelum melakukan pembelian.' ),
  );
 }

 private function faqSchema( string $keyword ): array {
  $items = array();
  foreach ( $this->faqItems( $keyword ) as $item ) {
   $items[] = array( 'question' => $item['question'], 'answer' => $item['answer'] );
  }
  return $items;
 }

 private function extractKeyword( array $data ): string {
  $keyword = $data['keyword'] ?? '';
  if ( is_array( $keyword ) ) {
   foreach ( array( 'phrase', 'keyword', 'term', 'value' ) as $key ) {
    if ( isset( $keyword[ $key ] ) && is_string( $keyword[ $key ] ) && '' !== trim( $keyword[ $key ] ) ) {
     return trim( $keyword[ $key ] );
    }
   }
  }
  foreach ( array( $keyword, $data['primary_keyword'] ?? '', $data['target_keyword'] ?? '' ) as $candidate ) {
   if ( is_string( $candidate ) && '' !== trim( $candidate ) ) { return trim( $candidate ); }
  }
  return 'topik utama';
 }

 private function extractTitle( array $data, string $keyword ): string {
  foreach ( array( 'title', 'h1', 'page_title' ) as $key ) {
   if ( isset( $data[ $key ] ) && is_string( $data[ $key ] ) && '' !== trim( $data[ $key ] ) ) { return trim( $data[ $key ] ); }
  }
  return 'Panduan Lengkap ' . $keyword . ': Informasi, Pilihan, dan Cara Menentukannya';
 }

 private function sectionTitle( $section, int $index ): string {
  if ( is_string( $section ) && '' !== trim( $section ) ) { return trim( $section ); }
  if ( is_array( $section ) ) {
   foreach ( array( 'title', 'heading', 'h2', 'name' ) as $key ) {
    if ( isset( $section[ $key ] ) && is_string( $section[ $key ] ) && '' !== trim( $section[ $key ] ) ) { return trim( $section[ $key ]); }
   }
  }
  return 'Pembahasan ' . $index;
 }

 private function sectionPurpose( $section ): string {
  if ( ! is_array( $section ) ) { return ''; }
  foreach ( array( 'purpose', 'objective', 'description', 'intent' ) as $key ) {
   if ( isset( $section[ $key ] ) && is_string( $section[ $key ] ) && '' !== trim( $section[ $key ] ) ) { return trim( $section[ $key ] ); }
  }
  return '';
 }

 private function buildMetaDescription( string $keyword ): string {
  return 'Panduan tentang ' . $keyword . ' dengan pembahasan terstruktur, hal yang perlu diperhatikan, dan langkah memilih informasi yang relevan.';
 }
}
