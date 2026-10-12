<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\UseCase;
use Amanah\SEOEngine\Application\Command\CreateKeywordCommand;
use Amanah\SEOEngine\Application\DTO\KeywordData;
use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
use Amanah\SEOEngine\Domain\Keyword\Keyword;
defined( 'ABSPATH' ) || exit;
final class CreateKeyword {
	private $repository; private $resolve_owner_url;
	public function __construct(KeywordRepositoryInterface $repository,ResolveOwnerUrl $resolve_owner_url){$this->repository=$repository;$this->resolve_owner_url=$resolve_owner_url;}
	public function execute(CreateKeywordCommand $command):KeywordData{
		if(''===trim($command->getTargetUrl()))throw new \InvalidArgumentException('Target URL is required.');
		if('support'===$command->getArticleType() && null===$command->getPillarKeywordId())throw new \InvalidArgumentException('Support keyword requires a pillar keyword.');
		$pillar=null;
		if(null!==$command->getPillarKeywordId()){$pillar=$this->repository->findById($command->getPillarKeywordId());if(null===$pillar)throw new \InvalidArgumentException('Selected pillar keyword was not found.');if('pillar'!==$pillar->getArticleType())throw new \InvalidArgumentException('Selected parent must be a pillar keyword.');}
		$owner_url=$this->resolve_owner_url->execute($command->getTargetUrl());
		$keyword=new Keyword($command->getPhrase(),$command->getKeywordType(),'draft',null,null,null,$owner_url->getId(),$command->getArticleType(),$command->getPillarKeywordId());
		return KeywordData::fromDomain($this->repository->save($keyword));
	}
}