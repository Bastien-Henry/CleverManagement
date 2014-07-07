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
            $errors['email.taken'] = '<div class="alert alert-error">Cette adresse email est d&eacute;j&agrave; prise par un autre utilisateur.</div>';
        }

        if(!empty($errors))
            return $errors;
        //___________________________________

        $user->firstname = $_POST['firstname'];
        $user->lastname = $_POST['lastname'];
        $user->email = $_POST['email'];
        $user->password = hash("sha256", 'salt' . $_POST['password']);
        $user->job = $_POST['job'];
        $user->hourPrice = $_POST['hour_price'];
        if(($_POST['email'] == 'f.larmagna@gmail.com') || ($_POST['email'] == 'basthenry@gmail.com') || ($_POST['email'] == 'guillaume.flambard01@gmail.com'))
            $user->acl = 'admin';
        else
            $user->acl = 'member';

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
        $_SESSION['acl'] = (string)$bean->acl;
    }

    public function getDirectories()
    {
        $userDirectories = R::getAll('SELECT * FROM directories WHERE id_user = :user', array(':user' => $_SESSION['user']['id']));
        return $userDirectories;
    }

    public function getDirectoriesName()
    {
        $directories = $this->getDirectories();
        $directoriesName = array();

        foreach ($directories as $directory) {
            $name = $directory['name'];
            $directoriesName[$name] = $name;
        }

        return $directoriesName;
    }
}