<?php
defined('ABSPATH')||exit;
class AMANAH_SEO_AI_Entity_Model{
 private $wpdb;private $table;
 public function __construct($db=null){global $wpdb;$this->wpdb=$db?:$wpdb;$this->table=$this->wpdb->prefix.'amanah_entities';}
 public function create_entity($data){$data['user_id']=absint($data['user_id']);$r=$this->wpdb->insert($this->table,$data,$this->formats($data));return false===$r?false:(int)$this->wpdb->insert_id;}
 public function get_entity($id,$user_id=null,$require_owner=false){$sql="SELECT * FROM {$this->table} WHERE id = %d";$args=array(absint($id));if($require_owner||null!==$user_id){$sql.=' AND user_id = %d';$args[]=absint($user_id);}$sql.=' LIMIT 1';return $this->wpdb->get_row($this->wpdb->prepare($sql,$args));}
 public function get_current_user_entities($user_id){return $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE user_id = %d ORDER BY id DESC",array(absint($user_id))));}
 public function update_entity($id,$user_id,$data){return false!==$this->wpdb->update($this->table,$data,array('id'=>absint($id),'user_id'=>absint($user_id)),$this->formats($data),array('%d','%d'));}
 public function delete_entity($id,$user_id){return false!==$this->wpdb->delete($this->table,array('id'=>absint($id),'user_id'=>absint($user_id)),array('%d','%d'));}
 public function has_dependencies($id){foreach(array('amanah_entity_topics','amanah_audits','amanah_content_analysis','amanah_content_plan','amanah_schema','amanah_tasks') as $table){$full=$this->wpdb->prefix.$table;$exists=$this->wpdb->get_var($this->wpdb->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = %s',array($full)));if((int)$exists<1)continue;$count=$this->wpdb->get_var($this->wpdb->prepare("SELECT COUNT(*) FROM {$full} WHERE entity_id = %d",array(absint($id))));if((int)$count>0)return true;}return false;}
 private function formats($data){$formats=array();foreach($data as $key=>$value)$formats[]=in_array($key,array('id','user_id'),true)?'%d':'%s';return $formats;}
}