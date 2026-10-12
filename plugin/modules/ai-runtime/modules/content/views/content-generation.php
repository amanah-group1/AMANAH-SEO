<?php defined('ABSPATH')||exit; ?>
<div class="wrap"><h1>Article Generation</h1>
<?php if($error): ?><div class="notice notice-error"><p><?php echo esc_html($error); ?></p></div><?php endif; ?>
<form method="post"><?php wp_nonce_field('amanah_generate_article'); ?>
<table class="form-table">
<tr><th>Content Brief ID</th><td><input type="number" name="content_brief_id" min="1" required></td></tr>
<tr><th>Source Analysis Post ID</th><td><input type="number" name="post_id" min="1" placeholder="Optional: attach latest CORE-006 analysis"></td></tr>
<tr><th>Topic</th><td><input class="regular-text" name="topic" required></td></tr>
<tr><th>Primary Keyword</th><td><input class="regular-text" name="primary_keyword" required></td></tr>
<tr><th>Search Intent</th><td><input class="regular-text" name="search_intent" required></td></tr>
<tr><th>Content Type</th><td><input class="regular-text" name="content_type" required></td></tr>
<tr><th>Target Audience</th><td><input class="regular-text" name="target_audience" required></td></tr>
<tr><th>Main Entity</th><td><input class="regular-text" name="main_entity"></td></tr>
<tr><th>Related Entities</th><td><input class="regular-text" name="related_entities"></td></tr>
<tr><th>Author ID</th><td><input type="number" name="author_id" min="0"></td></tr>
</table><p><button class="button button-primary" name="amanah_generate_article" value="1">Generate Article Package</button></p></form>
<?php if($result): $p=$result['package']??array();$a=$p['article']??array();$q=$result['quality_evaluation']??array(); ?>
<hr><h2>Article Package</h2>
<h3>Generation Context</h3><p><?php echo !empty($brief['post_id'])?'CORE-006 Generation Requirements attached from analyzed content.':'No CORE-006 Generation Requirements attached.'; ?></p>
<h3>Article</h3><p><strong><?php echo esc_html($a['title']??''); ?></strong></p>
<?php if(!empty($a['introduction'])): ?><h4>Introduction</h4><p><?php echo esc_html($a['introduction']); ?></p><?php endif; ?>
<?php if(!empty($a['h1'])): ?><h4>H1</h4><p><?php echo esc_html($a['h1']); ?></p><?php endif; ?>
<?php if(!empty($a['sections'])&&is_array($a['sections'])): ?><h4>Sections</h4><ol><?php foreach($a['sections'] as $section): ?><li><?php echo esc_html(is_array($section)?($section['heading']??wp_json_encode($section)):$section); ?></li><?php endforeach; ?></ol><?php endif; ?>
<?php if(!empty($a['conclusion'])): ?><h4>Conclusion</h4><p><?php echo esc_html($a['conclusion']); ?></p><?php endif; ?>
<h3>Media Plan</h3><p><?php echo esc_html(count((array)($p['media_plan']??array()))); ?> contextual media item(s).</p>
<h3>Publishing Metadata</h3><pre><?php echo esc_html(wp_json_encode($p['publishing_metadata']??array(),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)); ?></pre>
<h3>Internal Linking Plan</h3><p><?php echo esc_html(count((array)($p['internal_linking_plan']??array()))); ?> recommendation(s).</p>
<h3>Quality Evaluation</h3><p><strong>Score:</strong> <?php echo esc_html((string)($q['score']??0)); ?>/100</p><p><strong>Severity:</strong> <?php echo esc_html(strtoupper($q['severity']??'')); ?></p><p><strong>Gate:</strong> <?php echo esc_html($q['gate']??''); ?></p>
<?php foreach(array('weaknesses'=>'Weaknesses','recommendations'=>'Recommendations','faq_suggestions'=>'FAQ Suggestions') as $key=>$label): ?><h4><?php echo esc_html($label); ?></h4><ul><?php foreach((array)($q[$key]??array()) as $item): ?><li><?php echo esc_html(is_scalar($item)?$item:wp_json_encode($item)); ?></li><?php endforeach; ?></ul><?php endforeach; ?>
<?php if('READY_FOR_HUMAN_REVIEW'===($result['status']??'')): ?><p><strong>Ready for Human Review.</strong></p><p>Publishing remains a separate operation.</p><?php endif; ?>
<?php if(in_array((string)($result['status']??''),array('NEEDS_REFINEMENT','NOT_READY'),true)): ?><p><strong>Refinement required before Human Review.</strong></p><?php endif; ?>
<h3>Execution & Traceability</h3><p>Status: <?php echo esc_html($result['status']??''); ?> · Version: <?php echo esc_html((string)($result['version']??'')); ?> · Package ID: <?php echo esc_html($result['package_id']??''); ?></p>
<?php endif; ?></div>