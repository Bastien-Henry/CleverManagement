<?php

namespace app\engine\controllers;
 
use Walrus\core\WalrusController;
use Walrus\core\WalrusForm;
use Walrus\core\WalrusACL;

/**
* Class CommonController
* @package engine\controllers
*/
class ErrorController extends WalrusController
{
	public function error($id)
	{
		if ($id == 1) {
			$this->register('message','Vous n\'avez pas les droits pour acc&eacute;der a ces informations.');
		}
		elseif ($id == 2) {
			$this->register('message','Cette t&acirc;che est acctuellement ferm&eacute;e.');
		}
		elseif ($id == 3) {
			$this->register('message','Vous n\'avez pas les droits pour effectuer cette action.');
			
		}
		$this->setView('error');
	}
}