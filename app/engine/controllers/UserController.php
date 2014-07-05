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
	        $form = new WalrusForm('form_signup');
	        $this->register('formSignUp', $form->render());
	        $this->setView('login');

	        if (!empty($_POST)) {
	            $res = $this->model('user')->signup();
	            
	            if (isset($res['errors'])) {
	                $this->register('errors', $res['errors']);
	            }
	            else
	            {
	                $this->go('/CleverManagement/');
	            }
	        }
	    }
    }

    public function signin()
    {
        if (!empty($_POST)) 
        {
	        if(!$this->model('user')->signin())
	        {
	            $this->register('errors', array('credentials' => 'wrong email/password'));
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