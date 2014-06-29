<?php
namespace app\engine\models;

use R;

class User
{
    public function signup()
    {
        $user = R::dispense('users');

        $email_exist = $users = R::find(
            'users',
            ' email = :email',
            array(
                ':email' => $_POST['email']
            )
        );

        // VALIDATION
        if (!$email_exist) {
            $user->email = $_POST['email'];
        }
        else
        {
            $errors['email.taken'] = 'Email already taken';
        }

        if(!empty($errors))
            return $errors;
        //___________________________________

        $user->firstname = $_POST['firstname'];
        $user->lastname = $_POST['lastname'];
        $user->email = $_POST['email'];
        $user->password = hash("sha256", 'salt' . $_POST['password']);
        $user->job = $_POST['job'];
        $user->hourprice = $_POST['hour_price'];

        R::store($user);

        return true;
    }

    public function signin()
    {
        $password = hash("sha256", 'salt' . $_POST['password']);

        $user = R::findOne(
            'users',
            'email = :email AND password = :password',
            array(
                ':email' => $_POST['email'],
                ':password' => $password
            )
        );

        if ($user) {
            $this->fillSession($user);
            return true;
        }

        return false;
    }

    public function fillSession($bean)
    {
        $_SESSION['user']['id'] = (string)$bean->id;
        $_SESSION['user']['email'] = (string)$bean->email;
    }
}