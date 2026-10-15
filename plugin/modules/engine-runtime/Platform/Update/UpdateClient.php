<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Platform\Update;
defined( 'ABSPATH' ) || exit;
final class UpdateClient implements UpdateClientInterface {
 private $metadata;private $eligibility;private $preflight;private $verifier;
 public function __construct(?UpdateMetadata $metadata,UpdateEligibility $eligibility,UpdatePreflight $preflight,UpdatePackageVerifier $verifier){$this->metadata=$metadata;$this->eligibility=$eligibility;$this->preflight=$preflight;$this->verifier=$verifier;}
 public function setMetadata(UpdateMetadata $metadata):void{$this->metadata=$metadata;}
 public function is_update_available():bool{return null!==$this->metadata;}
 public function getMetadata():?UpdateMetadata{return $this->metadata;}
 public function evaluate(bool $entitled):array{
  if(null===$this->metadata)return array('available'=>false,'eligible'=>false,'reasons'=>array('update_unavailable'));
  $eligibility=$this->eligibility->check($this->metadata,$entitled);$preflight=$this->preflight->check($this->metadata);
  return array('available'=>true,'eligible'=>$eligibility['eligible']&&$preflight['ready'],'reasons'=>array_merge($eligibility['reasons'],$preflight['reasons']));
 }
 public function verifyPackage(string $package_path):bool{return null!==$this->metadata&&$this->verifier->verify($this->metadata,$package_path);}
}
