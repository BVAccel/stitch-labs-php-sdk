<?php 

namespace StitchLabs\Api\Traits;

use StitchLabs\StitchLabsException;

trait CannotSync {

	public function sync($id) {
		throw new StitchLabsException(
			__CLASS__.' cannot use '.__FUNCTION__.' function.'
		);
	}

}