<?php 

namespace StitchLabs\Api\Traits;

use StitchLabs\StitchLabsException;

trait CannotSave {

	public function save() {
		throw new StitchLabsException(
			__CLASS__.' cannot use '.__FUNCTION__.' function.'
		);
	}

}