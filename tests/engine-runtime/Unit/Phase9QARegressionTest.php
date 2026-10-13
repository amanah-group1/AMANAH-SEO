<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\AI\Provider\CustomAIProvider;
use Amanah\SEOEngine\Application\Job\JobContract;
use Amanah\SEOEngine\Application\Job\JobExecutionException;
use Amanah\SEOEngine\Application\Job\JobHandlerInterface;
use Amanah\SEOEngine\Application\Job\JobHandlerRegistry;
use Amanah\SEOEngine\Application\Job\JobRecord;
use Amanah\SEOEngine\Application\Job\JobRepositoryInterface;
use Amanah\SEOEngine\Application\Job\JobWorker;
use Amanah\SEOEngine\Application\Job\RetryPolicy;
use Amanah\SEOEngine\Infrastructure\Http\HttpClientInterface;
use PHPUnit\Framework\TestCase;

final class Phase9QARegressionTest extends TestCase {

	public function test_retry_policy_rejects_non_retryable_categories(): void {
		$policy = new RetryPolicy( 10, 100 );

		$this->assertFalse(
			$policy->shouldRetry( 'authentication_error', 1, 3 )
		);

		$this->assertFalse(
			$policy->shouldRetry( 'invalid_model_error', 1, 3 )
		);

		$this->assertTrue(
			$policy->shouldRetry( 'network_error', 1, 3 )
		);
	}

	public function test_retry_policy_stops_at_max_attempts(): void {
		$policy = new RetryPolicy( 10, 100 );

		$this->assertFalse(
			$policy->shouldRetry( 'timeout', 3, 3 )
		);

		$this->assertFalse(
			$policy->shouldRetry( 'network_error', 4, 3 )
		);
	}

	public function test_retry_delay_is_exponential_and_bounded(): void {
		$policy = new RetryPolicy( 10, 25 );

		$this->assertSame( 10, $policy->nextDelay( 1 ) );
		$this->assertSame( 20, $policy->nextDelay( 2 ) );
		$this->assertSame( 25, $policy->nextDelay( 3 ) );
		$this->assertSame( 25, $policy->nextDelay( 5 ) );
	}

	public function test_job_record_preserves_cancellation_state(): void {
		$job = new JobRecord(
			array(
				'job_id'                  => 'qa-job-1',
				'job_type'                => 'qa',
				'status'                  => JobContract::QUEUED,
				'cancellation_requested' => 1,
			)
		);

		$this->assertTrue( $job->isCancellationRequested() );
	}

	public function test_worker_cancels_job_before_handler_execution(): void {
		$repository = new class implements JobRepositoryInterface {
			public $cancelled = false;
			public $handledJob;

			public function create( array $job ): JobRecord {
				return new JobRecord( $job );
			}

			public function findByJobId( string $jobId ): ?JobRecord {
				return new JobRecord(
					array(
						'job_id'                  => $jobId,
						'job_type'                => 'qa',
						'status'                  => JobContract::PROCESSING,
						'cancellation_requested' => 1,
					)
				);
			}

			public function findByIdempotencyKey( string $key ): ?JobRecord {
				return null;
			}

			public function findNextDue( int $timestamp ): ?JobRecord {
				return new JobRecord(
					array(
						'job_id'          => 'qa-job-1',
						'job_type'        => 'qa',
						'status'          => JobContract::QUEUED,
						'timeout_seconds' => 300,
					)
				);
			}

			public function countProcessing( string $jobType = '', string $provider = '' ): int { return 0; }

			public function claim(
				string $jobId,
				string $workerId,
				int $leaseExpiresAt
			): bool {
				return true;
			}

			public function complete( string $jobId, array $result = array() ): void {}

			public function fail(
				string $jobId,
				string $category,
				string $message,
				array $details = array()
			): void {}

			public function scheduleRetry(
				string $jobId,
				int $timestamp,
				string $category,
				string $message
			): void {}

			public function cancel( string $jobId ): bool {
				$this->cancelled = true;
				return true;
			}

			public function requestCancellation( string $jobId ): bool {
				return true;
			}

			public function updateProgress(
				string $jobId,
				int $processed,
				int $total
			): void {}

			public function recoverStale( int $timestamp ): int {
				return 0;
			}
		};

		$handlers = new JobHandlerRegistry();

		$handler = new class implements JobHandlerInterface {
			public function getJobType(): string {
				return 'qa';
			}

			public function handle( JobRecord $job ): array {
				throw new \RuntimeException( 'Handler must not execute.' );
			}
		};

		$handlers->register( $handler );

		$worker = new JobWorker(
			$repository,
			$handlers,
			new RetryPolicy( 10, 100 ),
			'qa-worker'
		);

		$this->assertTrue( $worker->runOnce( 1000 ) );
		$this->assertTrue( $repository->cancelled );
	}

	public function test_worker_retries_retryable_execution_exception(): void {
		$repository = new class implements JobRepositoryInterface {
			public $retryScheduled = false;
			public $retryTimestamp = 0;
			public $retryCategory = '';

			public function create( array $job ): JobRecord {
				return new JobRecord( $job );
			}

			public function findByJobId( string $jobId ): ?JobRecord {
				return new JobRecord(
					array(
						'job_id'          => $jobId,
						'job_type'        => 'qa',
						'status'          => JobContract::PROCESSING,
						'attempt_count'   => 1,
						'max_attempts'    => 3,
						'timeout_seconds' => 300,
					)
				);
			}

			public function findByIdempotencyKey( string $key ): ?JobRecord {
				return null;
			}

			public function findNextDue( int $timestamp ): ?JobRecord {
				return new JobRecord(
					array(
						'job_id'          => 'qa-job-2',
						'job_type'        => 'qa',
						'status'          => JobContract::QUEUED,
						'attempt_count'   => 1,
						'max_attempts'    => 3,
						'timeout_seconds' => 300,
					)
				);
			}

			public function countProcessing( string $jobType = '', string $provider = '' ): int { return 0; }

			public function claim(
				string $jobId,
				string $workerId,
				int $leaseExpiresAt
			): bool {
				return true;
			}

			public function complete( string $jobId, array $result = array() ): void {}

			public function fail(
				string $jobId,
				string $category,
				string $message,
				array $details = array()
			): void {}

			public function scheduleRetry(
				string $jobId,
				int $timestamp,
				string $category,
				string $message
			): void {
				$this->retryScheduled = true;
				$this->retryTimestamp = $timestamp;
				$this->retryCategory = $category;
			}

			public function cancel( string $jobId ): bool {
				return false;
			}

			public function requestCancellation( string $jobId ): bool {
				return false;
			}

			public function updateProgress(
				string $jobId,
				int $processed,
				int $total
			): void {}

			public function recoverStale( int $timestamp ): int {
				return 0;
			}
		};

		$handlers = new JobHandlerRegistry();

		$handlers->register(
			new class implements JobHandlerInterface {
				public function getJobType(): string {
					return 'qa';
				}

				public function handle( JobRecord $job ): array {
					throw new JobExecutionException(
						'network_error',
						'Temporary network failure.',
						true
					);
				}
			}
		);

		$worker = new JobWorker(
			$repository,
			$handlers,
			new RetryPolicy( 10, 100 ),
			'qa-worker'
		);

		$this->assertTrue( $worker->runOnce( 1000 ) );
		$this->assertTrue( $repository->retryScheduled );
		$this->assertSame( 'network_error', $repository->retryCategory );
		$this->assertSame( 1020, $repository->retryTimestamp );
	}

	public function test_custom_ai_provider_rejects_private_network_endpoint(): void {
		$provider = new CustomAIProvider(
			$this->createMock( HttpClientInterface::class )
		);

		$result = $provider->validateCredentials(
			array(
				'endpoint' => 'https://192.168.1.10/v1',
				'api_key'  => 'test-key',
			)
		);

		$this->assertFalse( $result['valid'] );
		$this->assertSame( 'invalid_request_error', $result['category'] );
	}
}