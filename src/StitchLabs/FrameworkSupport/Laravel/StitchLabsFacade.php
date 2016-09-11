<?php 

namespace StitchLabs\FrameworkSupport\Laravel;

use Illuminate\Support\Facades\Facade;

class StitchLabsFacade extends Facade {
	protected static function getFacadeAccessor() { return 'StitchLabs'; }
}