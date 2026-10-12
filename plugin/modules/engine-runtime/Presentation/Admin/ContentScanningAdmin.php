<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Presentation\Admin;
use Amanah\SEOEngine\Application\UseCase\RunContentScanning;
use Amanah\SEOEngine\Application\UseCase\SaveScanningReview;
use Amanah\SEOEngine\Application\UseCase\GenerateOutlineFromScanning;
use Amanah\SEOEngine\Application\Repository\ContentScanRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\SerpResultRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
defined('ABSPATH') || exit;
final class ContentScanningAdmin {
 private $run;private $save;private $outline;private $scans;private $results;private $keywords;
 public function __construct(RunContentScanning $run,SaveScanningReview $save,GenerateOutlineFromScanning $outline,ContentScanRepositoryInterface $scans,SerpResultRepositoryInterface $results,KeywordRepositoryInterface $keywords){$this->run=$run;$this->save=$save;$this->outline=$outline;$this->scans=$scans;$this->results=$results;$this->keywords=$keywords;}
 public function render():void{
  if(!current_user_can('manage_options'))wp_die('Not allowed.');
  $keyword_id=absint($_GET['keyword_id']??$_POST['keyword_id']??0);$message='';
  if('POST'===$_SERVER['REQUEST_METHOD']){$action=sanitize_key((string)($_POST['content_scanning_action']??''));
   try{
    if('run'===$action){check_admin_referer('amanah_run_content_scan');$this->run->execute($keyword_id);$message='Scanning completed. Review the editable result below.';}
    elseif('save_review'===$action){check_admin_referer('amanah_save_scanning_review');$review=array();foreach(array('topics','questions','common_coverage','limited_coverage','opportunities') as $key){$review[$key]=$this->decodeLines((string)($_POST[$key]??''));}$this->save->execute(absint($_POST['scan_id']??0),$review);$message='Scanning review saved.';}
    elseif('build_outline'===$action){check_admin_referer('amanah_build_outline');$this->outline->execute($keyword_id);$message='Article Outline created from the reviewed scanning result.';}
   }catch(\Throwable $e){$message='Action failed: '.$e->getMessage();}
  }
  $keyword=$keyword_id>0?$this->keywords->findById($keyword_id):null;$scan=$keyword_id>0?$this->scans->findLatestByKeywordId($keyword_id):null;$results=null!==$scan?$this->results->findByScanId((int)$scan->getId()):array();$data=null!==$scan?$scan->getData():array();$analysis=isset($data['analysis'])&&is_array($data['analysis'])?$data['analysis']:array();
  echo '<div class="wrap"><h1>Content Scanning</h1>';
  if($message)echo '<div class="notice notice-info"><p>'.esc_html($message).'</p></div>';
  echo '<p><strong>Workflow:</strong> Keyword → Strategy → Scanning → Editable Scanning Result → Buat Outline.</p>';
  if(null!==$keyword)echo '<p><strong>Keyword:</strong> '.esc_html($keyword->getPhrase()).'</p>';
  echo '<h2>SERP Provider</h2><p><strong>Google Search</strong> — menggunakan kredensial Gemini yang sudah dikonfigurasi pada AI Provider.</p><p class="description">Scanning mengambil grounded web sources dari Google Search untuk dianalisis oleh Amanah.</p>';
  if($keyword_id>0){echo '<form method="post" style="margin:20px 0;">';wp_nonce_field('amanah_run_content_scan');echo '<input type="hidden" name="content_scanning_action" value="run"/><input type="hidden" name="keyword_id" value="'.esc_attr((string)$keyword_id).'"/><button class="button button-primary">Run Google SERP & Content Scan</button></form>';}
  if(null!==$scan){echo '<h2>Scanning Result — '.esc_html($scan->getStatus()).'</h2><p>Results: '.count($results).' | Analyzed pages: '.esc_html((string)($data['analyzed_count']??0)).' | Failed: '.esc_html((string)($data['failed_count']??0)).'</p>';
   echo '<table class="widefat striped"><thead><tr><th>#</th><th>Page</th><th>URL</th><th>Fetch</th><th>Words</th></tr></thead><tbody>';
   foreach($results as $r)echo '<tr><td>'.esc_html((string)$r->getPosition()).'</td><td>'.esc_html($r->getTitle()).'</td><td><a href="'.esc_url($r->getUrl()).'" target="_blank" rel="noopener">'.esc_html($r->getDomain()).'</a></td><td>'.esc_html($r->getFetchStatus()).'</td><td>'.esc_html((string)($r->getPageData()['word_count']??0)).'</td></tr>';
   echo '</tbody></table>';
   echo '<h2>Editable Analysis</h2><form method="post">';wp_nonce_field('amanah_save_scanning_review');echo '<input type="hidden" name="content_scanning_action" value="save_review"/><input type="hidden" name="scan_id" value="'.esc_attr((string)$scan->getId()).'"/>';
   foreach(array('topics'=>'Topics','questions'=>'Questions','common_coverage'=>'Common Coverage','limited_coverage'=>'Limited Coverage / Under-covered','opportunities'=>'Content Opportunities') as $key=>$label){echo '<p><strong>'.esc_html($label).'</strong><br><textarea name="'.esc_attr($key).'" rows="7" style="width:100%;max-width:1100px;">'.esc_textarea($this->lines(isset($analysis[$key])?$analysis[$key]:array())).'</textarea></p>';}
   echo '<p><strong>Google Autocomplete / Potential Support Keywords</strong></p><pre style="max-width:1100px;white-space:pre-wrap;">'.esc_html(wp_json_encode(array('autocomplete'=>$data['autocomplete']??array(),'support_keyword_correlation'=>$analysis['support_keyword_correlation']??array()),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)).'</pre>';
   echo '<p><button class="button">Save Scanning Review</button></p></form>';
   echo '<form method="post" style="margin-top:12px;">';wp_nonce_field('amanah_build_outline');echo '<input type="hidden" name="content_scanning_action" value="build_outline"/><input type="hidden" name="keyword_id" value="'.esc_attr((string)$keyword_id).'"/><button class="button button-primary">BUAT OUTLINE</button></form>';
  }
  echo '</div>';
 }
 private function lines(array $items):string{$out=array();foreach($items as $item){$out[]=is_array($item)?wp_json_encode($item,JSON_UNESCAPED_UNICODE):((string)$item);}return implode("\n",$out);}
 private function decodeLines(string $value):array{$out=array();foreach(preg_split('/\r?\n/',$value) as $line){$line=trim($line);if(''===$line)continue;$json=json_decode($line,true);$out[]=is_array($json)?$json:$line;}return $out;}
}