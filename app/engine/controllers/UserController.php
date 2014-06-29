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
     $this->setView('signup');

     $form = new WalrusForm('form_signup');
echo $form->render();

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

        if (isset($_POST['type'])) {
            if ($_POST['type'] === 'signin') {
                if(!$this->model('user')->signin())
                {
                 var_dump('if');
                    $this->register('errors', array('credentials' => 'wrong email/password'));
                }
                else
                {
                 var_dump('else');
                    $this->go('/CleverManagement/');
                }
            }
        }

        $this->setView('signin');
    }
}