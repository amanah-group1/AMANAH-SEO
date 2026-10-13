<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Platform\Update;
defined( 'ABSPATH' ) || exit;
final class UpdateExecutor implements UpdateExecutorInterface {
 public function install(string $package_path):bool{
  if(!is_file($package_path))return false;
  if(!class_exists('Plugin_Upgrader')&&file_exists(ABSPATH.'wp-admin/includes/class-wp-upgrader.php'))require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
  if(!class_exists('Plugin_Upgrader')||!class_exists('Automatic_Upgrader_Skin'))return false;
  $skin=new \Automatic_Upgrader_Skin();$upgrader=new \Plugin_Upgrader($skin);
  $result=$upgrader->run(array('package'=>$package_path,'destination'=>WP_PLUGIN_DIR,'clear_destination'=>true,'clear_working'=>true));
  return false!==$result&&!is_wp_error($result);
 }
}
