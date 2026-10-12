<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Infrastructure\ContentScanning;
defined('ABSPATH') || exit;
final class HtmlContentExtractor {
 public function extract(string $html):array{
  if(''===trim($html))return array('title'=>'','h1'=>array(),'h2'=>array(),'h3'=>array(),'text'=>'','word_count'=>0,'questions'=>array());
  libxml_use_internal_errors(true);$dom=new \DOMDocument();@$dom->loadHTML('<?xml encoding="UTF-8">'.$html);libxml_clear_errors();
  foreach(array('script','style','noscript','svg','nav','footer','form') as $tag){$nodes=$dom->getElementsByTagName($tag);$remove=array();foreach($nodes as $n)$remove[]=$n;foreach($remove as $n){if($n->parentNode)$n->parentNode->removeChild($n);}}
  $title='';$titles=$dom->getElementsByTagName('title');if($titles->length)$title=trim($titles->item(0)->textContent);
  $headings=array();foreach(array('h1','h2','h3') as $tag){$headings[$tag]=array();foreach($dom->getElementsByTagName($tag) as $n){$v=trim(preg_replace('/\s+/u',' ',$n->textContent));if(''!==$v)$headings[$tag][]=$v;}}
  $body=$dom->getElementsByTagName('body');$text=$body->length?trim(preg_replace('/\s+/u',' ',$body->item(0)->textContent)):'';$words=preg_split('/\s+/u',trim($text),-1,PREG_SPLIT_NO_EMPTY);
  $sentences=preg_split('/(?<=[?!])\s+/u',$text,-1,PREG_SPLIT_NO_EMPTY);$questions=array();foreach((array)$sentences as $s){$s=trim($s);if('?'===substr($s,-1)&&mb_strlen($s)<220)$questions[]=$s;}
  return array('title'=>$title,'h1'=>$headings['h1'],'h2'=>$headings['h2'],'h3'=>$headings['h3'],'text'=>mb_substr($text,0,8000),'word_count'=>count($words),'questions'=>array_values(array_unique($questions)));
 }
}