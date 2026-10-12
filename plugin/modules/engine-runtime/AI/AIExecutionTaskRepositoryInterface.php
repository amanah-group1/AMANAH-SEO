<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\AI;

defined( 'ABSPATH' ) || exit;

interface AIExecutionTaskRepositoryInterface {
 public function create( string $taskIdentifier, string $taskType, ?int $providerId, string $model, array $requestData ): int;
 public function complete( int $taskId, string $status, array $resultData=array(), ?string $errorCategory=null ): void;
}
