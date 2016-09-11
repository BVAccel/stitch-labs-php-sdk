<?php 

namespace StitchLabs\Api\Traits;

use StitchLabs\StitchLabsException;

trait CannotDelete {

	public function delete() {
		throw new StitchLabsException(
			__CLASS__.' cannot use '.__FUNCTION__.' function.'
		);
	}

}