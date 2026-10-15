<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Platform\Update;
defined( 'ABSPATH' ) || exit;
final class UpdateSafety implements UpdateSafetyInterface {
 private $backup='';
 public function createBackup():bool{
  if(!defined('WP_PLUGIN_DIR')||!is_dir(WP_PLUGIN_DIR))return false;
  $source=WP_PLUGIN_DIR.'/amanah-seo-engine-v2';if(!is_dir($source))return false;
  $this->backup=sys_get_temp_dir().'/amanah-seo-engine-backup-'.uniqid('',true);
  return $this->copy($source,$this->backup);
 }
 public function restoreBackup():bool{
  if(''===$this->backup||!is_dir($this->backup)||!defined('WP_PLUGIN_DIR'))return false;
  $target=WP_PLUGIN_DIR.'/amanah-seo-engine-v2';$this->delete($target);
  return $this->copy($this->backup,$target);
 }
 private function copy(string $source,string $destination):bool{
  if(!is_dir($destination)&&!mkdir($destination,0755,true)&&!is_dir($destination))return false;
  $items=scandir($source);if(false===$items)return false;
  foreach($items as $item){if('.'===$item||'..'===$item)continue;$from=$source.'/'.$item;$to=$destination.'/'.$item;
   if(is_dir($from)){if(!$this->copy($from,$to))return false;}else if(!copy($from,$to))return false;
  }return true;
 }
 private function delete(string $path):void{
  if(!is_dir($path))return;$items=scandir($path);if(false===$items)return;
  foreach($items as $item){if('.'===$item||'..'===$item)continue;$target=$path.'/'.$item;if(is_dir($target))$this->delete($target);else@unlink($target);}@rmdir($path);
 }
}
