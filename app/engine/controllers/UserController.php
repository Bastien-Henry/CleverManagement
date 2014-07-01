<?php

namespace app\engine\controllers;

use Walrus\core\WalrusController;
use Walrus\core\WalrusForm;

/**
* Class UserController
* @package engine\controllers
*/
class UserController extends WalrusController
{

    public function signup()
    {
        $form = new WalrusForm('form_signup');
        $this->register('formSignUp', $form->render());
        $this->setView('login');

        if (isset($_POST['type'])) {
            if ($_POST['type'] === 'signup') {

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
        $form = new WalrusForm('form_signin');
        echo $form->render();
            
        if (isset($_POST)) 
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
    	session_destroy();

    	$this->go('/CleverManagement/');
    }
}