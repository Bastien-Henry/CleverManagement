<?php

namespace app\engine\models;

use R;

class Session
{
    public function create($id_task)
    {
        $session = R::dispense('sessions');
        $session->created_at = $_POST['createdAt'];
        $session->hour_number = $_POST['hourNumber'];
        $session->percent = $_POST['percent'];
        $session->comment = $_POST['comment'];
        $session->id_user = $_SESSION['user']['id'];
        $session->id_task = $id_task;
        $id = R::store($session);
        
        return true;
    }
}