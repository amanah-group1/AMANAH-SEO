<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Platform\Update;
use Amanah\SEOEngine\Application\Port\MigrationRunnerInterface;
defined( 'ABSPATH' ) || exit;
final class UpdateService {
 private $client; private $server; private $executor; private $safety; private $migrations; private $currentVersion;
 public function __construct( UpdateClient $client, UpdateServerClient $server, UpdateExecutorInterface $executor, UpdateSafetyInterface $safety, MigrationRunnerInterface $migrations, string $currentVersion ) {
  $this->client=$client; $this->server=$server; $this->executor=$executor; $this->safety=$safety; $this->migrations=$migrations; $this->currentVersion=$currentVersion;
 }
 public function check( bool $entitled=true, string $channel='stable' ): array {
  $metadata=$this->server->fetch($channel);
  if(null===$metadata)return array('available'=>false,'eligible'=>false,'reasons'=>array('update_unavailable'));
  if(version_compare($metadata->getVersion(),$this->currentVersion,'<='))return array('available'=>false,'eligible'=>false,'reasons'=>array('already_current'));
  $this->client->setMetadata($metadata); return $this->client->evaluate($entitled);
 }
 public function install( string $packagePath, bool $entitled=true ): array {
  $result=$this->client->evaluate($entitled);
  if(!$result['eligible'])return array('success'=>false,'reasons'=>$result['reasons']);
  if(!$this->client->verifyPackage($packagePath))return array('success'=>false,'reasons'=>array('package_invalid'));
  if(!$this->safety->createBackup())return array('success'=>false,'reasons'=>array('backup_failed'));
  if(!$this->executor->install($packagePath)){
   $restored=$this->safety->restoreBackup();
   return array('success'=>false,'reasons'=>array($restored?'update_failed_restored':'update_failed_restore_failed'));
  }
  try{$this->migrations->migrate();}catch(\Throwable $e){
   $restored=$this->safety->restoreBackup();
   return array('success'=>false,'reasons'=>array($restored?'migration_failed_restored':'migration_failed_restore_failed'));
  }
  return array('success'=>true,'reasons'=>array());
 }
 public function downloadAndInstall( bool $entitled=true, string $channel='stable' ): array {
  $metadata=$this->server->fetch($channel);
  if(null===$metadata)return array('success'=>false,'reasons'=>array('update_unavailable'));
  if(version_compare($metadata->getVersion(),$this->currentVersion,'<='))return array('success'=>false,'reasons'=>array('already_current'));
  $this->client->setMetadata($metadata); $path=$this->server->download($metadata);
  if(null===$path)return array('success'=>false,'reasons'=>array('package_download_failed'));
  $result=$this->install($path,$entitled); @unlink($path); return $result;
 }
}
