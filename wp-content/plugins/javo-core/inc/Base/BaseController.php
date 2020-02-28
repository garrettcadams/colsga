<?php 
/**
 * @package JavoCore
 */
namespace Jvbpd\Base;

class BaseController
{

	public function __construct() {

		$this->file = plugin_basename( dirname( __FILE__, 3 ) ) . '/javo-core.php';
		$this->folder = basename( dirname( $this->file ) );
		$this->path = dirname( $this->file );

		/* webpack path */
		$this->dist_url = esc_url( trailingslashit( plugins_url( '/dist/', $this->file ) ) );
		$this->inc_path = trailingslashit( $this->path ) . 'inc';
	}
	
}