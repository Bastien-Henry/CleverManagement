<?php

namespace app\engine\models;

use R;

class Task
{
    public function show($id)
    {
        $task = R::load('tasks', $id);

        if($task->getProperties()['id'] == 0)
        {
            return array('task.not_found' => 'task doesnt exist');
        }

        return $task;
    }

    public function index($id_step)
    {
        $tasks = R::findAll('tasks', 'id_step = ?', array($id_step));

        return $tasks;
    }

    public function delete($id)
    {
        $find_session = R::find(
            'sessions',
            ' id_task = :id_task',
            array(
                ':id_task' => $id
            )
        );

        foreach($find_session as $session)
        {
            R::trash($session);
        }

        $task = $this->show($id);

        R::trash($task);
    }

    public function edit($id)
    {
        $task = R::load('tasks', $id);

        if(empty($_POST['name']))
        {
            return array('name.empty' => 'Name can\'t be empty');
        }

        $task->name = $_POST['name'];
        $task->description = $_POST['description'];
        $task->urgent = $_POST['urgent'];
        $task->startline = $_POST['startline'];
        $task->deadline = $_POST['deadline'];

        R::store($task);
        return $task;
    }

    public function create($id_step)
    {
        $task = R::dispense('tasks');

        if(empty($_POST['name']))
        {
            return array('name.empty' => 'Name can\'t be empty');
        }

        //___________________________________

        $task->name = $_POST['name'];
        $task->id_step = $id_step;
        $task->description = $_POST['description'];
        // $task->urgent = $_POST['urgent'];
        $task->startline = $_POST['startline'];
        $task->deadline = $_POST['deadline'];

        R::store($task);

        return true;
    }
}