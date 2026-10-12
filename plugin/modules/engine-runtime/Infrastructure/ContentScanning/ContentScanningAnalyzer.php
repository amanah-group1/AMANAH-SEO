<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Infrastructure\ContentScanning;
defined('ABSPATH') || exit;
final class ContentScanningAnalyzer {
 public function analyze(array $pages,array $autocomplete,array $support_keywords):array{
  $topic_counts=array();$question_counts=array();$all_topics=array();
  foreach($pages as $page){foreach(array_merge((array)($page['h2']??array()),(array)($page['h3']??array())) as $topic){$key=$this->key($topic);if(''===$key)continue;$topic_counts[$key]=($topic_counts[$key]??0)+1;$all_topics[$key]=$topic;}foreach((array)($page['questions']??array()) as $q){$key=$this->key($q);$question_counts[$key]=($question_counts[$key]??0)+1;}}
  arsort($topic_counts);arsort($question_counts);
  $topics=array();foreach(array_slice($topic_counts,0,30,true) as $k=>$count)$topics[]=array('topic'=>$all_topics[$k],'coverage'=>$count,'coverage_ratio'=>round($count/max(1,count($pages)),3));
  $questions=array();foreach(array_slice($question_counts,0,25,true) as $q=>$count)$questions[]=array('question'=>$q,'coverage'=>$count);
  $autocomplete=array_values(array_unique(array_filter(array_map('sanitize_text_field',$autocomplete))));
  $support=array();foreach($support_keywords as $item){if(!is_array($item))continue;$phrase=trim((string)($item['phrase']??$item['keyword']??''));if(''===$phrase)continue;$support[]=array('keyword_id'=>(int)($item['id']??0),'phrase'=>$phrase,'matched_topic'=>$this->bestMatch($phrase,$topics),'required'=>true);}
  $common=array_values(array_filter($topics,function($x){return $x['coverage']>=max(2,(int)ceil(count($pages)*0.5));}));
  $limited=array_values(array_filter($topics,function($x){return $x['coverage']===1;}));
  return array('topics'=>$topics,'questions'=>$questions,'common_coverage'=>$common,'limited_coverage'=>$limited,'opportunities'=>array(),'autocomplete_correlation'=>$autocomplete,'support_keyword_correlation'=>$support,'page_count'=>count($pages));
 }
 private function bestMatch(string $phrase,array $topics):string{foreach($topics as $t){$a=$this->key($phrase);$b=$this->key($t['topic']);if(''!==$a&&(''!==$b&&(false!==strpos($b,$a)||false!==strpos($a,$b))))return $t['topic'];}return '';}
 private function key(string $v):string{$v=mb_strtolower(trim($v),'UTF-8');$v=preg_replace('/[^\p{L}\p{N}\s?]/u',' ',$v);$v=preg_replace('/\s+/u',' ',$v);return trim((string)$v);}
}