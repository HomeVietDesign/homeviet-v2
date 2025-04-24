<?php
function _theme_as_increase_time_limit( $time_limit ) {
	return defined('AS_TIME_LIMIT') ? AS_TIME_LIMIT : 30;
}
#add_filter( 'action_scheduler_queue_runner_time_limit', '_theme_as_increase_time_limit' );

function as_increase_action_scheduler_batch_size( $batch_size ) {
	return defined('AS_BATCH_SIZE') ? AS_BATCH_SIZE : 25;
}
#add_filter( 'action_scheduler_queue_runner_batch_size', 'as_increase_action_scheduler_batch_size' );

function as_increase_action_scheduler_concurrent_batches( $concurrent_batches ) {
	return defined('AS_CONCURRENT_BATCHES') ? AS_CONCURRENT_BATCHES : 1;
}
#add_filter( 'action_scheduler_queue_runner_concurrent_batches', 'as_increase_action_scheduler_concurrent_batches' );

/**
 * Trigger 2 additional loopback requests with unique URL params.
 */
function as_request_additional_runners() {
	$batches = defined('AS_CONCURRENT_BATCHES') ? AS_CONCURRENT_BATCHES : 1;
	// allow self-signed SSL certificates
	add_filter( 'https_local_ssl_verify', '__return_false', 100 );

	for ( $i = 0; $i < $batches; $i++ ) {
		$response = wp_remote_post( admin_url( 'admin-ajax.php' ), array(
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => $batches,
			'httpversion' => '1.0',
			'blocking'    => false,
			'headers'     => array(),
			'body'        => array(
				'action'     => 'as_create_additional_runners',
				'instance'   => $i,
				'as_nonce' => wp_create_nonce( 'as_additional_runner_' . $i ),
			),
			'cookies'     => array(),
		) );
	}
}
#add_action( 'action_scheduler_run_queue', 'as_request_additional_runners', 0 );

/**
 * Handle requests initiated by as_request_additional_runners() and start a queue runner if the request is valid.
 */
function as_create_additional_runners() {

	if ( isset( $_POST['as_nonce'] ) && isset( $_POST['instance'] ) && wp_verify_nonce( $_POST['as_nonce'], 'as_additional_runner_' . $_POST['instance'] ) ) {
		\ActionScheduler_QueueRunner::instance()->run();
	}

	wp_die();
}
#add_action( 'wp_ajax_nopriv_as_create_additional_runners', 'as_create_additional_runners', 0 );

function custom_as_retention_period($period) {
	//error_log(DAY_IN_SECONDS);
    return DAY_IN_SECONDS;
}
// add_filter( 'action_scheduler_timeout_period', 'custom_as_retention_period' );
// add_filter( 'action_scheduler_failure_period', 'custom_as_retention_period' );
// add_filter( 'action_scheduler_retention_period', 'custom_as_retention_period' );

function custom_as_cleaner_statuses( $statuses ) {
	$statuses[] = \ActionScheduler_Store::STATUS_FAILED;
	$statuses[] = \ActionScheduler_Store::STATUS_COMPLETE;
	$statuses[] = \ActionScheduler_Store::STATUS_CANCELED;
	return $statuses;
}
//add_filter( 'action_scheduler_default_cleaner_statuses', 'custom_as_cleaner_statuses' );

function remove_ignored_action_logging() {
	$logger = \ActionScheduler::logger();

	remove_action( 'action_scheduler_canceled_action', array( $logger, 'log_canceled_action' ), 10, 1 );
	remove_action( 'action_scheduler_begin_execute', array( $logger, 'log_started_action' ), 10, 2 );
	remove_action( 'action_scheduler_after_execute', array( $logger, 'log_completed_action' ), 10, 3 );
	remove_action( 'action_scheduler_failed_execution', array( $logger, 'log_failed_action' ), 10, 3 );
	remove_action( 'action_scheduler_failed_action', array( $logger, 'log_timed_out_action' ), 10, 2 );
	remove_action( 'action_scheduler_unexpected_shutdown', array( $logger, 'log_unexpected_shutdown' ), 10, 2 );
	remove_action( 'action_scheduler_reset_action', array( $logger, 'log_reset_action' ), 10, 1 );
	remove_action( 'action_scheduler_execution_ignored', array( $logger, 'log_ignored_action' ), 10, 2 );
	remove_action( 'action_scheduler_failed_fetch_action', array( $logger, 'log_failed_fetch_action' ), 10, 2 );
	remove_action( 'action_scheduler_failed_to_schedule_next_instance', array( $logger, 'log_failed_schedule_next_instance' ), 10, 2 );
	remove_action( 'action_scheduler_bulk_cancel_actions', array( $logger, 'bulk_log_cancel_actions' ), 10, 1 );

	$logger->unhook_stored_action();
}
//add_action( 'init', 'remove_ignored_action_logging', 100 );