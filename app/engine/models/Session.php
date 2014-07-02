<?php

namespace app\engine\models;

use R;

class Session
{
    public function show($id)
    {
        $session = R::load('sessions', $id);

        if($session->getProperties()['id'] == 0)
        {
            return array('session.not_found' => 'Session doesnt exist');
        }

        return $session;
    }

    public function index($task)
    {
        $sessions = R::findAll('sessions', 'id_task = ?', array($task));

        return $sessions;
    }

    public function delete($id)
    {
        $session = $this->show($id);

        R::trash($session);
    }

    public function edit($id)
    {
        $session = R::load('sessions', $id);
        $session->hour_number = $_POST['hour_number'];
        $session->percent = $_POST['percent'];
        $session->comment = $_POST['comment'];

        R::store($session);
        return $session;
    }

    public function create($id_task)
    {
        $session = R::dispense('sessions');
        $session->created_at = date('Y-m-d H:i:s.');
        $session->hour_number = $_POST['hour_number'];
        $session->percent = $_POST['percent'];
        $session->comment = $_POST['comment'];
        $session->id_user = $_SESSION['user']['id'];
        $session->id_task = $id_task;
        $id = R::store($session);
        
        return true;
    }
}
