<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Presentation\Admin;

use Amanah\SEOEngine\Application\UseCase\GenerateContentPlan;
use Amanah\SEOEngine\Application\UseCase\GenerateContentBrief;
use Amanah\SEOEngine\Application\UseCase\ListKeywords;
use Amanah\SEOEngine\Application\Repository\ContentPlanRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentBriefRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentRepositoryInterface;

defined( 'ABSPATH' ) || exit;

final class ContentPlanAdmin {

	private $generate_plan;
	private $list_keywords;
	private $plan_repository;
	private $generated_plan;
	private $generate_brief;
	private $brief_repository;
	private $content_repository;

	public function __construct(
		GenerateContentPlan $generate_plan,
		ListKeywords $list_keywords,
		ContentPlanRepositoryInterface $plan_repository,
		?GenerateContentBrief $generate_brief = null,
		?ContentBriefRepositoryInterface $brief_repository = null,
		?ContentRepositoryInterface $content_repository = null
	) {
		$this->generate_plan = $generate_plan;
		$this->list_keywords = $list_keywords;
		$this->plan_repository = $plan_repository;
		$this->generated_plan = null;
		$this->generate_brief = $generate_brief;
		$this->brief_repository = $brief_repository;
		$this->content_repository = $content_repository;
	}

	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage Content Plans.', 'amanah-seo-engine' ) );
		}
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['amanah_content_plan_action'] ) ) $this->handlePost();
		$view_plan_id = absint( $_GET['view_plan_id'] ?? 0 );
		if ( $view_plan_id > 0 ) { $this->renderDetail( $view_plan_id ); return; }

		$keywords = $this->list_keywords->execute();
		$plans = $this->plan_repository->findAll();
		$briefs = null !== $this->brief_repository ? $this->brief_repository->findAll() : array();
		$contents = null !== $this->content_repository ? $this->content_repository->findAll() : array();
		$brief_by_keyword = array(); foreach ( $briefs as $brief ) $brief_by_keyword[ $brief->getKeywordId() ] = $brief;
		$content_by_keyword = array();
		foreach ( $contents as $content ) {
			$kid = $content->getKeywordId();
			if ( null !== $kid && ( ! isset( $content_by_keyword[ $kid ] ) || $content->getId() > $content_by_keyword[ $kid ]->getId() ) ) $content_by_keyword[ $kid ] = $content;
		}
		$plan_by_keyword = array(); foreach ( $plans as $plan ) $plan_by_keyword[ $plan->getKeywordId() ] = $plan;
		$covered_support_ids = array(); $pillar_plan_by_keyword = array();
		foreach ( $plan_by_keyword as $plan ) {
			$d = $plan->getData();
			if ( 'pillar' !== ( $d['article_type'] ?? '' ) ) continue;
			$pillar_plan_by_keyword[ $plan->getKeywordId() ] = $plan;
			foreach ( (array) ( $d['support_keywords'] ?? array() ) as $support_keyword ) if ( is_array( $support_keyword ) && isset( $support_keyword['id'] ) ) $covered_support_ids[ (int) $support_keyword['id'] ] = true;
		}

		$filters = array(
			'search' => sanitize_text_field( wp_unslash( (string) ( $_GET['plan_search'] ?? '' ) ) ),
			'keyword_type' => sanitize_key( (string) ( $_GET['keyword_type'] ?? '' ) ),
			'article_type' => sanitize_key( (string) ( $_GET['article_type'] ?? '' ) ),
			'plan_status' => sanitize_key( (string) ( $_GET['plan_status'] ?? '' ) ),
			'content_status' => sanitize_key( (string) ( $_GET['content_status'] ?? '' ) ),
			'coverage' => sanitize_key( (string) ( $_GET['coverage'] ?? '' ) ),
			'sort' => sanitize_key( (string) ( $_GET['plan_sort'] ?? 'keyword' ) ),
			'direction' => 'desc' === sanitize_key( (string) ( $_GET['plan_direction'] ?? 'asc' ) ) ? 'desc' : 'asc',
		);

		$rows = array();
		foreach ( $keywords as $keyword ) {
			$d = $keyword->toArray(); $kid = (int) $d['id'];
			$covered = 'support' === ( $d['article_type'] ?? '' ) && isset( $covered_support_ids[ $kid ] );
			$plan = $plan_by_keyword[ $kid ] ?? null; $display_plan = $plan;
			if ( $covered && ! empty( $d['pillar_keyword_id'] ) && isset( $pillar_plan_by_keyword[ (int) $d['pillar_keyword_id'] ] ) ) $display_plan = $pillar_plan_by_keyword[ (int) $d['pillar_keyword_id'] ];
			$pd = null !== $display_plan ? $display_plan->getData() : array();
			$content = $content_by_keyword[ $kid ] ?? null;
			$status = null !== $plan ? $plan->getStatus() : ( $covered ? 'covered' : 'not_planned' );
			$content_status = null !== $content ? sanitize_key( $content->getStatus() ) : 'not_created';
			$coverage = $covered ? 'covered' : ( null !== $plan ? 'standalone' : 'not_planned' );
			if ( '' !== $filters['search'] && false === stripos( (string) $d['phrase'], $filters['search'] ) ) continue;
			if ( '' !== $filters['keyword_type'] && $filters['keyword_type'] !== (string) $d['keyword_type'] ) continue;
			if ( '' !== $filters['article_type'] && $filters['article_type'] !== (string) $d['article_type'] ) continue;
			if ( '' !== $filters['plan_status'] && $filters['plan_status'] !== sanitize_key( $status ) ) continue;
			if ( '' !== $filters['content_status'] && $filters['content_status'] !== $content_status ) continue;
			if ( '' !== $filters['coverage'] && $filters['coverage'] !== $coverage ) continue;
			$rows[] = array(
				'keyword'=>(string)$d['phrase'],'keyword_type'=>(string)$d['keyword_type'],'article_type'=>(string)$d['article_type'],
				'target_url'=>(string)($pd['target_url']??'Unassigned'),'plan_status'=>$status,
				'content_status'=>$content_status,'coverage'=>$coverage,'keyword_id'=>$kid,
				'plan_id'=>null!==$display_plan?$display_plan->getId():null,'is_direct_plan'=>null!==$plan,'is_covered_support'=>$covered,
			);
		}
		usort($rows,static function($a,$b)use($filters){$field=array('keyword'=>'keyword','keyword_type'=>'keyword_type','article_type'=>'article_type','status'=>'plan_status','content_status'=>'content_status')[$filters['sort']]??'keyword';$cmp=strcasecmp((string)$a[$field],(string)$b[$field]);return 'desc'===$filters['direction']?- $cmp:$cmp;});
		$total=count($rows);$planned=count(array_filter($rows,static function($r){return 'not_planned'!==$r['coverage'];}));
		echo '<div class="wrap"><h1>Content Plan</h1>'; settings_errors('amanah_content_plan');
		echo '<div style="display:flex;gap:24px;flex-wrap:wrap;margin:16px 0;"><div><strong>Plan Coverage</strong><br />'.esc_html($total?round(($planned/$total)*100).'%':'0%').'</div><div><strong>Planning Units</strong><br />'.esc_html((string)$total).'</div><div><strong>Planned / Covered</strong><br />'.esc_html((string)$planned).'</div></div>';
		echo '<form method="get" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin:16px 0;padding:12px;background:#fff;border:1px solid #dcdcde;">';
		echo '<input type="hidden" name="page" value="amanah-seo-engine-content-plan" /><input type="search" name="plan_search" value="'.esc_attr($filters['search']).'" placeholder="Search keyword..." />';
		echo '<select name="keyword_type"><option value="">All Keyword Types</option>';
		foreach(array('primary'=>'Primary','secondary'=>'Secondary','supporting'=>'Supporting') as $v=>$l) echo '<option value="'.esc_attr($v).'"'.selected($filters['keyword_type'],$v,false).'>'.esc_html($l).'</option>'; echo '</select>';
		echo '<select name="article_type"><option value="">All Article Types</option><option value="pillar"'.selected($filters['article_type'],'pillar',false).'>Pillar</option><option value="support"'.selected($filters['article_type'],'support',false).'>Support</option></select>';
		echo '<select name="plan_status"><option value="">All Plan Statuses</option><option value="planned"'.selected($filters['plan_status'],'planned',false).'>Planned</option><option value="covered"'.selected($filters['plan_status'],'covered',false).'>Covered by Pillar</option><option value="not_planned"'.selected($filters['plan_status'],'not_planned',false).'>Not planned</option></select>';
		echo '<select name="content_status"><option value="">All Content Statuses</option><option value="not_created"'.selected($filters['content_status'],'not_created',false).'>Not created</option><option value="draft"'.selected($filters['content_status'],'draft',false).'>Draft</option><option value="ready_for_review"'.selected($filters['content_status'],'ready_for_review',false).'>Ready for review</option><option value="approved"'.selected($filters['content_status'],'approved',false).'>Approved</option><option value="published"'.selected($filters['content_status'],'published',false).'>Published</option></select>';
		echo '<select name="coverage"><option value="">All Coverage</option><option value="covered"'.selected($filters['coverage'],'covered',false).'>Covered by Pillar</option><option value="standalone"'.selected($filters['coverage'],'standalone',false).'>Standalone Plan</option><option value="not_planned"'.selected($filters['coverage'],'not_planned',false).'>Not planned</option></select>';
		echo '<select name="plan_sort"><option value="keyword"'.selected($filters['sort'],'keyword',false).'>Sort: Keyword</option><option value="keyword_type"'.selected($filters['sort'],'keyword_type',false).'>Sort: Type</option><option value="article_type"'.selected($filters['sort'],'article_type',false).'>Sort: Article</option><option value="status"'.selected($filters['sort'],'status',false).'>Sort: Plan Status</option><option value="content_status"'.selected($filters['sort'],'content_status',false).'>Sort: Content</option></select><select name="plan_direction"><option value="asc"'.selected($filters['direction'],'asc',false).'>A → Z</option><option value="desc"'.selected($filters['direction'],'desc',false).'>Z → A</option></select><button class="button button-primary">Filter</button>';
		if($filters['search']||$filters['keyword_type']||$filters['article_type']||$filters['plan_status']||$filters['content_status']||$filters['coverage']) echo ' <a class="button" href="'.esc_url(admin_url('admin.php?page=amanah-seo-engine-content-plan')).'">Reset</a>';
		echo '</form><table class="widefat striped"><thead><tr><th>Keyword</th><th>Type</th><th>Article</th><th>Target URL</th><th>Plan</th><th>Content</th><th>Coverage</th><th>Action</th></tr></thead><tbody>';
		if(empty($rows)) echo '<tr><td colspan="8">No Content Plan records match the current filters.</td></tr>';
		foreach($rows as $row){
			echo '<tr><td>'.esc_html($row['keyword']).'</td><td>'.esc_html(ucfirst($row['keyword_type'])).'</td><td>'.esc_html(ucfirst($row['article_type'])).'</td><td>'.esc_html($row['target_url']).'</td><td>'.esc_html($row['plan_status']).'</td><td>'.esc_html($row['content_status']).'</td><td>'.esc_html($row['coverage']).'</td><td>';
			if(null!==$row['plan_id']) echo '<a class="button" href="'.esc_url(add_query_arg(array('page'=>'amanah-seo-engine-content-plan','view_plan_id'=>(int)$row['plan_id']),admin_url('admin.php'))).'">View Plan</a> ';
			if($row['is_covered_support']&&null===$row['plan_id']) echo 'Covered by Pillar'; else { echo '<form method="post" style="display:inline-block;margin-left:4px;">'; wp_nonce_field('amanah_generate_content_plan','amanah_content_plan_nonce'); echo '<input type="hidden" name="amanah_content_plan_action" value="generate" /><input type="hidden" name="keyword_id" value="'.esc_attr((string)$row['keyword_id']).'" /><button type="submit" class="button">'.esc_html($row['is_direct_plan']?'Refresh Plan':'Create Plan').'</button></form>'; }
			if($row['is_direct_plan']) echo ' <a class="button" href="'.esc_url(admin_url('admin.php?page=amanah-seo-engine-content-scanning&keyword_id='.(int)$row['keyword_id'])).'">Scan SERP &amp; Content</a>';
			echo '</td></tr>';
		}
		echo '</tbody></table></div>';
	}

	private function renderDetail( int $plan_id ): void {
		$plan=$this->plan_repository->findById($plan_id);
		echo '<div class="wrap">';
		if(null===$plan){echo '<h1>Content Plan Detail</h1><div class="notice notice-error"><p>Content Plan not found.</p></div><p><a class="button" href="'.esc_url(admin_url('admin.php?page=amanah-seo-engine-content-plan')).'">← Back to Content Plan</a></p></div>';return;}
		$data=$plan->getData();$keyword=null;foreach($this->list_keywords->execute() as $item){$d=$item->toArray();if((int)$d['id']===$plan->getKeywordId()){$keyword=$d;break;}}
		echo '<h1>Content Plan Detail</h1><p><a class="button" href="'.esc_url(admin_url('admin.php?page=amanah-seo-engine-content-plan')).'">← Back to Content Plan</a></p><div class="postbox" style="padding:16px;max-width:1100px;">';
		echo '<h2 style="margin-top:0;">'.esc_html($keyword['phrase']??('Keyword #'.$plan->getKeywordId())).'</h2><p><strong>Plan ID:</strong> '.esc_html((string)$plan->getId()).' &nbsp; <strong>Version:</strong> '.esc_html((string)$plan->getVersion()).' &nbsp; <strong>Status:</strong> '.esc_html($plan->getStatus()).'</p>';
		echo '<p><strong>Target URL:</strong> '.esc_html((string)($data['target_url']??'Unassigned')).' &nbsp; <strong>Article Type:</strong> '.esc_html(ucfirst((string)($data['article_type']??'—'))).' &nbsp; <strong>Role:</strong> '.esc_html((string)($data['content_role']??'—')).'</p>';
		$intents=is_array($data['intent_coverage']??null)?$data['intent_coverage']:array(); echo '<h2>Search Intent Coverage</h2><ul>'; if(empty($intents)) echo '<li>Not specified</li>'; foreach($intents as $intent) if(is_array($intent)) echo '<li>'.esc_html((string)($intent['name']??$intent['slug']??'Intent')).'</li>'; echo '</ul>';
		$support=is_array($data['support_keywords']??null)?$data['support_keywords']:array(); echo '<h2>Support Keyword Coverage ('.esc_html((string)count($support)).')</h2><table class="widefat striped"><thead><tr><th>#</th><th>Keyword</th><th>Source</th><th>Coverage</th></tr></thead><tbody>'; if(empty($support)) echo '<tr><td colspan="4">No support keywords in this plan.</td></tr>'; foreach($support as $i=>$item) if(is_array($item)) echo '<tr><td>'.esc_html((string)($i+1)).'</td><td>'.esc_html((string)($item['phrase']??$item['keyword']??'')).'</td><td>'.esc_html((string)($item['source']??'Strategy Coverage')).'</td><td>Covered by Pillar Plan</td></tr>'; echo '</tbody></table>';
		echo '<h2>Content Requirement</h2><p>'.nl2br(esc_html((string)($data['content_requirement']??'—'))).'</p>';

		$brief = null !== $this->brief_repository ? $this->brief_repository->findByKeywordId( $plan->getKeywordId() ) : null;
		echo '<h2>Content Brief</h2>';
		if ( null !== $brief ) {
			echo '<p><strong>Status:</strong> '.esc_html($brief->getStatus()).' &nbsp; <strong>Version:</strong> '.esc_html((string)$brief->getVersion()).'</p>';
			echo '<p><a class="button button-primary" href="'.esc_url(admin_url('admin.php?page=amanah-seo-engine-content-production&keyword_id='.(int)$plan->getKeywordId())).'">Open Content Production</a></p>';
		} else {
			echo '<p>Content Brief has not been created for this Content Plan.</p>';
			if ( null !== $this->generate_brief ) {
				echo '<form method="post" style="margin-top:12px;">';
				wp_nonce_field('amanah_generate_content_brief','amanah_content_brief_nonce');
				echo '<input type="hidden" name="amanah_content_plan_action" value="generate_brief" />';
				echo '<input type="hidden" name="keyword_id" value="'.esc_attr((string)$plan->getKeywordId()).'" />';
				echo '<button type="submit" class="button button-primary">Generate Content Brief</button>';
				echo '</form>';
			}
		}
		echo '</div></div>';
	}

	private function handlePost(): void {
		$action = sanitize_key( (string) ( $_POST['amanah_content_plan_action'] ?? '' ) );

		if ( 'generate_brief' === $action ) {
			$this->handleBriefPost();
			return;
		}

		check_admin_referer( 'amanah_generate_content_plan', 'amanah_content_plan_nonce' );

		$keyword_id = absint( $_POST['keyword_id'] ?? 0 );

		if ( $keyword_id < 1 ) {
			add_settings_error( 'amanah_content_plan', 'invalid_keyword', 'Invalid keyword.', 'error' );
			return;
		}

		try {
			$this->generated_plan = $this->generate_plan->execute( $keyword_id );
			add_settings_error( 'amanah_content_plan', 'generated', 'Content Plan created successfully.', 'success' );
		} catch ( \Throwable $e ) {
			add_settings_error( 'amanah_content_plan', 'generation_failed', 'Content Plan could not be created. Generate SEO Strategy first.', 'error' );
		}
	}

	private function handleBriefPost(): void {
		check_admin_referer( 'amanah_generate_content_brief', 'amanah_content_brief_nonce' );
		$keyword_id = absint( $_POST['keyword_id'] ?? 0 );
		if ( $keyword_id < 1 || null === $this->generate_brief ) {
			add_settings_error( 'amanah_content_plan', 'invalid_brief', 'Content Brief service is not available.', 'error' );
			return;
		}
		try {
			$this->generate_brief->execute( $keyword_id );
			add_settings_error( 'amanah_content_plan', 'brief_generated', 'Content Brief created successfully.', 'success' );
		} catch ( \Throwable $e ) {
			add_settings_error( 'amanah_content_plan', 'brief_generation_failed', 'Content Brief could not be created: ' . $e->getMessage(), 'error' );
		}
	}
}
