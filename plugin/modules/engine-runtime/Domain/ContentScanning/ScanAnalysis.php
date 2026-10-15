<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Domain\ContentScanning;
defined('ABSPATH') || exit;
final class ScanAnalysis {
 private $id; private $scan_id; private $data; private $version; private $status;
 public function __construct(int $scan_id,array $data=[],int $version=1,string $status='draft',?int $id=null){$this->scan_id=$scan_id;$this->data=$data;$this->version=$version;$this->status=$status;$this->id=$id;}
 public function getId():?int{return $this->id;} public function getScanId():int{return $this->scan_id;} public function getData():array{return $this->data;} public function getVersion():int{return $this->version;} public function getStatus():string{return $this->status;}
}