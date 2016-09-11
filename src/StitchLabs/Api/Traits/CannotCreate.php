<?php 

namespace StitchLabs\Api\Traits;

use StitchLabs\StitchLabsException;

trait CannotCreate {

	public function create(array $params = array()) {
		throw new StitchLabsException(
			__CLASS__.' cannot use '.__FUNCTION__.' function.'
		);
	}

}