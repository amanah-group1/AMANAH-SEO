<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Content;

defined( 'ABSPATH' ) || exit;

/**
 * Locked content-generation policy governed by the Master Dokumen content-generation specification.
 *
 * This class is the single policy boundary for generation prompts and
 * machine-checkable generation requirements. It does not invent research data.
 */
final class ContentGenerationRules {

	public static function systemPrompt(): string {
		return <<<'PROMPT'
CONTENT GENERATION POLICY — MANDATORY

The supplied content-generation rules are authoritative. Do not generate a generic SEO article. Operationalize every applicable rule below.

1. TOPIC / EVERGREEN
- Treat evergreen as content whose demand is not dependent on temporary news, trends, annual events, or short-lived spikes.
- Prefer enduring problems, low-volatility demand, and stable usefulness.
- Do not claim Google Trends validation unless verified trend data is present in the supplied context.
- If trend validation data is absent, mark it as "needs_validation" in metadata and never fabricate it.

2. SEARCH INTENT
- Classify and satisfy the supplied search intent: informational, commercial investigation, transactional, or navigational.
- Most evergreen editorial content is informational, but never override the supplied intent.
- The article format must match the dominant intent.
- For pillar content, cover every supplied intent_coverage item coherently; do not omit one.

3. KEYWORD STRUCTURE
- Use one primary keyword representing the whole topic.
- Use supplied secondary keywords / semantic variants naturally.
- Use supplied long-tail questions naturally.
- Use supplied support keywords as contextual coverage inputs when support_keywords are present in the approved blueprint.
- A support keyword is not automatically an exact-phrase requirement. Preserve its topic/meaning naturally unless an applicable mandatory requirement explicitly requires phrase presence.
- When an applicable mandatory requirement requires an exact phrase, preserve the exact supplied phrase at least once in the body while integrating it naturally into a relevant sentence, paragraph, heading, FAQ, example, or list item.
- Distribute support keywords across relevant sections instead of creating a keyword list or stuffing them together.
- Never keyword-stuff.
- Use People Also Ask / related questions only when actually supplied or clearly derivable from the approved context; do not invent research results.

4. SERP / CONTENT GAP
- Use supplied competitor/SERP evidence when available.
- Compare the current top 1–5 structure, format, headings, depth, freshness, examples, and missing information.
- Close identified content gaps.
- Never claim competitor analysis was performed when the evidence is absent; mark research as needs_validation.

5. COMPREHENSIVENESS
- Build a one-stop resource.
- Prefer hub-and-spoke flow where applicable: basics/definition → preparation → execution → advanced guidance → troubleshooting.
- Use concrete examples, numbers, wording, scenarios, or procedures only when verified or clearly presented as examples.
- Never invent business facts, prices, specifications, testimonials, authors, company data, statistics, sources, or URLs.

6. READABILITY / UX
- Mobile-first.
- Paragraphs should normally be 2–3 sentences maximum.
- Use bold emphasis for important points.
- Use numbered lists for ordered steps.
- Use bullets for unordered items.
- Use tables when comparing 3+ items using the same variables.
- Avoid walls of text.

7. VISUAL VALUE
- Recommend only useful visuals, not decorative stock imagery.
- Use infographic/summary, screenshots for software/tutorials, and diagrams/process flows when they add explanatory value.
- Image requirements: descriptive filename, concise relevant alt text, WebP/AVIF preferred, target under 100 KB.
- Do not invent an image URL.

8. UNIQUE VALUE
- Include first-hand testing, original screenshots, documented mistakes/fixes, expert input, or a useful downloadable asset only when supplied/available.
- Do not falsely claim first-hand experience, expert quotes, or original research.

9. ON-PAGE SEO
- SEO title: primary keyword as early as practical; target 50–60 characters; evergreen modifiers may be used; do not add a year unless annual updating is intended.
- URL: short, clear, primary-keyword focused, hyphens, no dates/static numbers, no unnecessary stop words.
- H1: exactly one, article title, contains primary keyword.
- H2/H3/H4: logical hierarchy; use relevant keyword variants, supplied support keywords, and supplied questions.
- Meta description: target 130–150 characters, include primary keyword, state benefit/solution, end with a useful CTA when appropriate.
- Internal links: target 3–5 relevant existing site articles, with descriptive anchors. Only use URLs supplied/verified by context.
- External links: target 1–3 authoritative sources (government, scientific research, official journals) for factual claims. Only use verified/supplied URLs.

10. TRUST / FACTUAL SAFETY
- Every factual number/claim that requires evidence must have an authoritative source or be clearly identified as needing verification.
- Do not fabricate About, Contact, Privacy, Editorial Guidelines, author identity, dates, SSL state, or business information.
- Preserve HTTPS where URLs are supplied.
- Do not use deceptive popups, scam claims, or unsupported guarantees.
- Creation and last-updated dates must be represented when supplied.
- Never change only the date to simulate freshness.

11. MAINTENANCE
- Recommend a 6-month refresh cycle for high-competition/YMYL/rapidly changing technology topics.
- Recommend a 12-month cycle for general/education topics.
- Refresh actual data, links, obsolete information, and newly relevant subtopics.
- Represent dateModified only when content is actually changed.
- When GSC data is supplied, prioritize high-impression / low-click queries at positions 5–20 for refinement.
- Where backlink evidence is supplied, identify broken-backlink recovery and relevant internal-link opportunities.
- Do not claim monitoring or GSC/backlink analysis without supplied evidence.

12. OUTPUT CONTRACT
Return JSON only with:
- title
- body
- excerpt
- metadata

metadata MUST contain:
- generation_policy = "aturan_generate_konten"
- research_validation: evergreen, serp, trend, each with status and evidence
- keyword_strategy: primary, secondary, supporting, long_tail
- keyword_strategy.supporting MUST contain the supplied support-keyword phrases from the approved blueprint. Do not omit, rename, or invent them.
- search_intent
- content_structure
- seo: seo_title, meta_description, slug, h1
- linking: internal_links, external_references
- media: featured_image, content_images
- trust
- maintenance
- factual_safety
- publishing

The body must be valid WordPress-ready HTML. It must contain exactly one H1 and a logical H2/H3/H4 hierarchy. Do not output markdown fences or commentary.

13. SAFETY / MISSING DATA
When required research or factual data is unavailable, do not invent it. Use explicit metadata status "needs_validation" and write the article so it does not make the unsupported claim.
PROMPT;
	}

	public static function validatePayload( array $payload, array $blueprint ): array {
		$errors = array();
		$warnings = array();

		foreach ( array( 'title', 'body', 'excerpt', 'metadata' ) as $key ) {
			if ( ! array_key_exists( $key, $payload ) ) {
				$errors[] = 'missing_' . $key;
			}
		}

		$metadata = isset( $payload['metadata'] ) && is_array( $payload['metadata'] ) ? $payload['metadata'] : array();
		foreach ( array( 'generation_policy', 'research_validation', 'keyword_strategy', 'search_intent', 'content_structure', 'seo', 'linking', 'media', 'trust', 'maintenance', 'factual_safety', 'publishing' ) as $key ) {
			if ( ! array_key_exists( $key, $metadata ) ) {
				$errors[] = 'missing_metadata_' . $key;
			}
		}

		if ( isset( $metadata['generation_policy'] ) && 'aturan_generate_konten' !== $metadata['generation_policy'] ) {
			$errors[] = 'invalid_generation_policy';
		}

		$body = isset( $payload['body'] ) ? (string) $payload['body'] : '';
		if ( 1 !== preg_match_all( '/<h1\b[^>]*>/i', $body ) ) {
			$errors[] = 'h1_count_must_be_one';
		}

		$primary = self::primaryKeyword( $blueprint );

		$support_keywords = isset( $blueprint['support_keywords'] ) && is_array( $blueprint['support_keywords'] )
			? $blueprint['support_keywords']
			: array();
		$required_support_phrases = self::requiredExactSupportPhrases( $blueprint );
		if ( '' !== $primary && false === stripos( wp_strip_all_tags( (string) ( $payload['title'] ?? '' ) ), $primary ) ) {
			$errors[] = 'primary_keyword_missing_title';
		}
		if ( '' !== $primary && false === stripos( wp_strip_all_tags( $body ), $primary ) ) {
			$errors[] = 'primary_keyword_missing_body';
		}

		$outline = isset( $blueprint['article_outline'] ) && is_array( $blueprint['article_outline'] ) ? $blueprint['article_outline'] : array();
		if ( ! empty( $outline ) ) {
			$heading_matches = array();
			preg_match_all( '/<h([1-3])\b[^>]*>(.*?)<\/h[1-3]>/is', $body, $heading_matches, PREG_SET_ORDER );
			$actual_headings = array();
			foreach ( $heading_matches as $heading ) {
				$actual_headings[] = array(
					'level' => (int) $heading[1],
					'title' => trim( wp_strip_all_tags( html_entity_decode( (string) $heading[2] ) ) ),
				);
			}
			if ( count( array_filter( $actual_headings, static function ( $item ) { return 1 === $item['level']; } ) ) !== 1 ) {
				$errors[] = 'outline_requires_exactly_one_h1';
			}
			$cursor = 0;
			foreach ( $outline as $section ) {
				$expected_level = (int) ( $section['level'] ?? 0 );
				$expected_title = trim( (string) ( $section['title'] ?? '' ) );
				if ( $expected_level < 1 || $expected_level > 3 || '' === $expected_title ) {
					continue;
				}
				$found = false;
				for ( $i = $cursor; $i < count( $actual_headings ); $i++ ) {
					if ( $actual_headings[ $i ]['level'] === $expected_level && 0 === strcasecmp( $actual_headings[ $i ]['title'], $expected_title ) ) {
						$cursor = $i + 1;
						$found = true;
						break;
					}
				}
				if ( ! $found ) {
					$errors[] = 'outline_heading_missing_or_out_of_order:' . $expected_title;
				}
			}
		}

		$body_text = wp_strip_all_tags( $body );
		foreach ( $required_support_phrases as $support_phrase ) {
			if ( false === stripos( $body_text, $support_phrase ) ) {
				$errors[] = 'support_keyword_missing_body:' . $support_phrase;
			}
		}

		$keyword_strategy = isset( $metadata['keyword_strategy'] ) && is_array( $metadata['keyword_strategy'] ) ? $metadata['keyword_strategy'] : array();
		if ( ! isset( $keyword_strategy['supporting'] ) || ! is_array( $keyword_strategy['supporting'] ) ) {
			$errors[] = 'missing_keyword_strategy_supporting';
		} else {
			foreach ( $required_support_phrases as $support_phrase ) {
				if ( ! in_array( $support_phrase, array_map( 'strval', $keyword_strategy['supporting'] ), true ) ) {
					$errors[] = 'support_keyword_missing_metadata:' . $support_phrase;
				}
			}
		}

		$seo = isset( $metadata['seo'] ) && is_array( $metadata['seo'] ) ? $metadata['seo'] : array();
		$seo_title = isset( $seo['seo_title'] ) ? trim( (string) $seo['seo_title'] ) : '';
		if ( '' !== $seo_title && ( mb_strlen( $seo_title ) < 50 || mb_strlen( $seo_title ) > 60 ) ) {
			$warnings[] = 'seo_title_outside_50_60';
		}
		$meta_description = isset( $seo['meta_description'] ) ? trim( (string) $seo['meta_description'] ) : '';
		if ( '' !== $meta_description && ( mb_strlen( $meta_description ) < 130 || mb_strlen( $meta_description ) > 150 ) ) {
			$warnings[] = 'meta_description_outside_130_150';
		}

		$slug = isset( $seo['slug'] ) ? trim( (string) $seo['slug'] ) : '';
		if ( '' !== $slug && preg_match( '/\d{4}/', $slug ) ) {
			$errors[] = 'evergreen_slug_contains_year';
		}

		$research = isset( $metadata['research_validation'] ) && is_array( $metadata['research_validation'] ) ? $metadata['research_validation'] : array();
		foreach ( array( 'evergreen', 'serp', 'trend' ) as $key ) {
			if ( ! isset( $research[ $key ] ) || ! is_array( $research[ $key ] ) ) {
				$errors[] = 'missing_research_status_' . $key;
			} elseif ( ! isset( $research[ $key ]['status'] ) ) {
				$errors[] = 'missing_research_status_value_' . $key;
			}
		}

		$internal = isset( $metadata['linking']['internal_links'] ) && is_array( $metadata['linking']['internal_links'] ) ? $metadata['linking']['internal_links'] : array();
		$external = isset( $metadata['linking']['external_references'] ) && is_array( $metadata['linking']['external_references'] ) ? $metadata['linking']['external_references'] : array();
		if ( count( $internal ) < 3 ) {
			$warnings[] = 'fewer_than_3_internal_links';
		}
		if ( count( $external ) < 1 ) {
			$warnings[] = 'no_external_reference';
		}

		return array(
			'valid' => empty( $errors ),
			'errors' => $errors,
			'warnings' => $warnings,
		);
	}

	private static function requiredExactSupportPhrases( array $blueprint ): array {
		$baseline = isset( $blueprint['requirement_baseline'] ) && is_array( $blueprint['requirement_baseline'] )
			? $blueprint['requirement_baseline']
			: array();
		$phrases = array();
		foreach ( $baseline as $requirement ) {
			if ( ! is_array( $requirement ) || empty( $requirement['mandatory'] ) ) {
				continue;
			}
			$rule = isset( $requirement['verification_rule'] ) && is_array( $requirement['verification_rule'] )
				? $requirement['verification_rule']
				: array();
			$method = strtolower( trim( (string) ( $rule['method'] ?? $rule['type'] ?? '' ) ) );
			$requires_phrase = in_array( $method, array( 'phrase_presence', 'exact_phrase', 'exact_match', 'exact_phrase_presence' ), true );
			if ( ! $requires_phrase ) {
				continue;
			}
			$target = trim( (string) ( $rule['target'] ?? $rule['phrase'] ?? '' ) );
			if ( '' === $target && isset( $requirement['statement'] ) ) {
				$target = trim( (string) $requirement['statement'] );
			}
			if ( '' !== $target ) {
				$phrases[] = $target;
			}
		}
		return array_values( array_unique( $phrases ) );
	}

	public static function primaryKeyword( array $blueprint ): string {
		$data = isset( $blueprint['data'] ) && is_array( $blueprint['data'] ) ? $blueprint['data'] : $blueprint;
		$keyword = isset( $data['keyword'] ) && is_array( $data['keyword'] ) ? $data['keyword'] : array();
		foreach ( array( $keyword['phrase'] ?? '', $data['primary_keyword'] ?? '', $data['target_keyword'] ?? '' ) as $candidate ) {
			if ( is_string( $candidate ) && '' !== trim( $candidate ) ) {
				return trim( $candidate );
			}
		}
		return '';
	}
}
