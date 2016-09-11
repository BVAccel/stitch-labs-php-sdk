<?php 

namespace StitchLabs\Api;

use StitchLabs\Api\Traits\CannotCreate;
use StitchLabs\Api\Traits\CannotDelete;
use StitchLabs\Api\Traits\CannotSave;

class ContactsService extends RestModel {

  use CannotCreate;
  use CannotSave;
  use CannotDelete;

  public $full_url = 'https://api-pub.stitchlabs.com/api2/v2';
  public $return_key = ['Contacts','Addresses'];

  public function getIndexUrl()
  {
    $url = $this->full_url.'/Contacts';

    return $url;
  }
}