<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Your Stitch Labs OAuth2 Credentials
	|--------------------------------------------------------------------------
	*/
    'clientId'     => getenv('STITCHLABS_CLIENT_ID'),
    'clientSecret' => getenv('STITCHLABS_CLIENT_SECRET'),
    'redirectUri'  => getenv('STITCHLABS_REDIRECT_URL'),
);
