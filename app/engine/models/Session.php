<?php

namespace app\engine\models;

use R;
use app\engine\models\Common;

class Session extends Common
{
    public function show($id_project, $id_session)
    {
        $this->permission_access($id_project);
        $session = R::load('sessions', $id_session);

        if($session->getProperties()['id'] == 0)
        {
            return array('session.not_found' => 'Session doesnt exist');
        }

        return $session;
    }

    public function index($id_project, $task)
    {
        $this->permission_access($id_project);
        $sessions = R::findAll('sessions', 'id_task = ?', array($task));

        return $sessions;
    }

    public function delete($id_project, $id_session)
    {
        $this->permission_exec($id_project);

        $session = $this->show($id_session);

        R::trash($session);
    }

    public function edit($id_project, $id_task, $id_session)
    {
        $this->permission_exec($id_project);

        $task = R::load('tasks', $id_task);
        $task->percent = $_POST['percent'];
        R::store($task);

        $session = R::load('sessions', $id_session);
        $session->hour_number = $_POST['hour_number'];
        $session->percent = $_POST['percent'];
        $session->comment = $_POST['comment'];

        R::store($session);
        return $session;
    }

    public function create($id_project, $id_task)
    {
        $this->permission_access($id_project);
        $task = R::load('tasks', $id_task);
        $task->percent = $_POST['percent'];
        R::store($task);

        $session = R::dispense('sessions');

        $user = R::load('users', $_SESSION['user']['id']);
        $price_hour = $user->getProperties()['hour_price'];

        $session->price = $price_hour*$_POST['hour_number'];
        $session->created_at = date('Y-m-d H:i:s.');
        $session->hour_number = $_POST['hour_number'];
        $session->comment = $_POST['comment'];
        $session->id_user = $_SESSION['user']['id'];
        $session->id_task = $id_task;
        $id = R::store($session);
        
        return true;
    }
}
