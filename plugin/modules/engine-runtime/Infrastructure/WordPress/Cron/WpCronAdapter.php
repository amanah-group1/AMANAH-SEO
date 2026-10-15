<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Infrastructure\WordPress\Cron;
use Amanah\SEOEngine\Application\Port\SchedulerInterface;
defined( 'ABSPATH' ) || exit;
final class WpCronAdapter implements SchedulerInterface {
 public function schedule(string $hook,int $timestamp,array $args=array()):bool{if(wp_next_scheduled($hook,$args))return true;return false!==wp_schedule_single_event($timestamp,$hook,$args);}
 public function scheduleRecurring(string $hook,int $timestamp,int $interval,array $args=array()):bool{if(wp_next_scheduled($hook,$args))return true;return false!==wp_schedule_event($timestamp,$interval,$hook,$args);}
 public function unschedule(string $hook):void{$timestamp=wp_next_scheduled($hook);if(false!==$timestamp)wp_unschedule_event($timestamp,$hook);}
}