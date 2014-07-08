<?php

namespace app\engine\controllers;

use app\engine\controllers\CommonController;
use Walrus\core\WalrusForm;

/**
* Class UserController
* @package engine\controllers
*/
class UserController extends CommonController
{

    public function signup()
    {
    	if (!empty($_SESSION)) {
            $this->go('/CleverManagement/');
        }
        else
        {
	        if (!empty($_POST)) 
	        {
	            $res = $this->model('user')->signup();

	            if (isset($res['email.taken'])) {
	                $this->register('errorsMail', $res['email.taken']);
	            }
	            else
	            {
	                $this->register('message', '<div class="alert alert-success">Compte correctement cr&eacute;&eacute; !</div>');
	            }
	        }

	        $this->setView('login');
	    }
    }

    public function signin()
    {
        if (!empty($_POST)) 
        {
	        if(!$this->model('user')->signin())
	        {
	        	//var_dump($this->model('user')->signin());
	            $this->register('messageError', '<div class="alert alert-error">Combinaison email/mot de passe invalide !</div>');
	        }
	        else
	        {
	            $this->go('/CleverManagement/');
	        }
        }

        $this->setView('login');
    }

    public function destroy()
    {
    	if (empty($_SESSION)) {
            $this->go('/CleverManagement/');
        }
        else
        {
	    	session_destroy();

	    	$this->go('/CleverManagement/');
	    }
    }
}