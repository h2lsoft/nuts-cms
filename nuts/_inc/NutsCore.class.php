<?php
/**
 * NutsCore
 *
 * @package Nuts
 * @version 1.1
 *
 * 2013/07/26: 1.1 add construct with autocast super globals
 * 2012/02/23: 1.0
 *
 *
 * @date
 */

class NutsCore extends TPLN {

	public function __construct($autocast_globals=true){
		if($autocast_globals)autocastSuperGlobals();
	}


}

