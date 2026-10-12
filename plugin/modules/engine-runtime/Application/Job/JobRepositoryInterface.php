<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Job;
defined( 'ABSPATH' ) || exit;
interface JobRepositoryInterface {
 public function create(array $job): JobRecord;
 public function findByJobId(string $jobId): ?JobRecord;
 public function findByIdempotencyKey(string $key): ?JobRecord;
 public function findNextDue(int $timestamp): ?JobRecord;
 public function countProcessing(string $jobType='',string $provider=''): int;
 public function claim(string $jobId,string $workerId,int $leaseExpiresAt): bool;
 public function complete(string $jobId,array $result=array()): void;
 public function fail(string $jobId,string $category,string $message,array $details=array()): void;
 public function scheduleRetry(string $jobId,int $timestamp,string $category,string $message): void;
 public function cancel(string $jobId): bool;
 public function requestCancellation(string $jobId): bool;
 public function updateProgress(string $jobId,int $processed,int $total): void;
 public function recoverStale(int $timestamp): int;
}