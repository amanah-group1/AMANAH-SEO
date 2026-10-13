<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Domain\ContentScanning;
defined('ABSPATH') || exit;
final class ContentScan {
 private $id; private $keyword_id; private $strategy_id; private $query; private $status; private $data; private $version;
 public function __construct(int $keyword_id,int $strategy_id,string $query,array $data=[],string $status='draft',int $version=1,?int $id=null){$this->keyword_id=$keyword_id;$this->strategy_id=$strategy_id;$this->query=$query;$this->data=$data;$this->status=$status;$this->version=$version;$this->id=$id;}
 public function getId():?int{return $this->id;} public function getKeywordId():int{return $this->keyword_id;} public function getStrategyId():int{return $this->strategy_id;} public function getQuery():string{return $this->query;} public function getStatus():string{return $this->status;} public function getData():array{return $this->data;} public function getVersion():int{return $this->version;}
}