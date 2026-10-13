<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Port;
defined( 'ABSPATH' ) || exit;
interface SchedulerInterface {
 public function schedule(string $hook,int $timestamp,array $args=array()): bool;
 public function scheduleRecurring(string $hook,int $timestamp,int $interval,array $args=array()): bool;
 public function unschedule(string $hook): void;
}