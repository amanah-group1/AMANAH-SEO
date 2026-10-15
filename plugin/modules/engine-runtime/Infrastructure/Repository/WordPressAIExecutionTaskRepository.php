<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Repository;

use Amanah\SEOEngine\AI\AIExecutionTaskRepositoryInterface;

defined( 'ABSPATH' ) || exit;

final class WordPressAIExecutionTaskRepository implements AIExecutionTaskRepositoryInterface {
 private $wpdb;
 private $table;
 public function __construct( \wpdb $wpdb ) { $this->wpdb=$wpdb; $this->table=$wpdb->prefix.'amanah_seo_ai_tasks'; }
 public function create( string $taskIdentifier, string $taskType, ?int $providerId, string $model, array $requestData ): int {
  $ok=$this->wpdb->insert($this->table,array('task_identifier'=>$taskIdentifier,'task_type'=>$taskType,'provider_id'=>$providerId,'model_identifier'=>$model,'status'=>'processing','request_data'=>wp_json_encode($this->sanitize($requestData)),'created_at'=>current_time('mysql',true)),array('%s','%s','%d','%s','%s','%s'));
  return false===$ok?0:(int)$this->wpdb->insert_id;
 }
 public function complete( int $taskId, string $status, array $resultData=array(), ?string $errorCategory=null ): void {
  $this->wpdb->update($this->table,array('status'=>sanitize_key($status),'result_data'=>wp_json_encode($this->sanitize($resultData)),'error_category'=>null!==$errorCategory?sanitize_key($errorCategory):null,'completed_at'=>current_time('mysql',true)),array('id'=>$taskId),array('%s','%s','%s','%s'),array('%d'));
 }
 private function sanitize(array $data): array {
  $blocked=array('secret','token','credential','authorization','password','key');
  foreach($data as $k=>$v){if(in_array(strtolower((string)$k),$blocked,true)){$data[$k]='[redacted]';}elseif(is_array($v)){$data[$k]=$this->sanitize($v);}}
  return $data;
 }
}
