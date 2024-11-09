<?php
/**
 * Prometheus metrics endpoint handler for Monolog
 *
 * Handles all features of Prometheus metrics endpoint handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   3.0.0
 */

namespace Decalog\Handler;

use Decalog\System\Blog;
use Decalog\System\Environment;
use DLMonolog\Logger;
use DLMonolog\Formatter\FormatterInterface;
use Decalog\Plugin\Feature\DMonitor;
use Prometheus\RenderTextFormat;
use Prometheus\CollectorRegistry;

/**
 * Define the Monolog Prometheus metrics endpoint handler.
 *
 * Handles all features of Prometheus metrics endpoint handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   3.0.0
 */
class PrometheusMetricsEPHandler extends AbstractMonitoringHandler {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param   string  $uuid       The UUID of the logger.
	 * @param   int     $profile    The profile of collected metrics (500, 550 or 600).
	 * @param   int     $sampling   The sampling rate (0->1000).
	 * @param   string  $filters    Optional. The filter to exclude metrics.
	 * @since    3.0.0
	 */
	public function __construct( string $uuid, int $profile, int $sampling, string $filters = '' ) {
		parent::__construct( $uuid, $profile, $sampling, $filters );
		$this->post_args                            = [];
		$this->post_args['headers']['Content-Type'] = RenderTextFormat::MIME_TYPE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function close(): void {
		$monitor  = new DMonitor( 'plugin', DECALOG_PRODUCT_NAME, DECALOG_VERSION );
		$renderer = new RenderTextFormat();
		if ( $monitor->prod_registry() && $monitor->dev_registry() ) {
			$production              = $monitor->prod_registry()->getMetricFamilySamples();
			$development             = ( Logger::ALERT === $this->level ? $monitor->dev_registry()->getMetricFamilySamples() : [] );
			$this->post_args['body'] = $renderer->render( $this->filter( $production, $development ) );
			parent::set_cache();
		}
	}

}
