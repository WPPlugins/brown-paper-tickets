<?php

namespace BrownPaperTickets\Modules;

use BrownPaperTickets\BptWordpress as Utilities;

require_once( Utilities::plugin_root_dir() . 'lib/BptAPI/vendor/autoload.php' );

class ModuleApi {

	public $dev_id;
	public $client_id;

	public function __construct() {
		$this->dev_id    = get_option( '_bpt_dev_id' );
		$this->client_id = get_option( '_bpt_client_id' );
	}

}
