<?php

namespace app\engine\controllers;
 
use Walrus\core\WalrusController;
use Walrus\core\WalrusForm;
use Walrus\core\WalrusACL;

/**
* Class CommonController
* @package engine\controllers
*/
class CommonController extends WalrusController
{
	public function userDirectories()
	{
		$directories = $this->model('common')->userDirectories();
        $this->register('directories', $directories);
	}
}