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

    public function retrieveMembers($id)
    {
        $task = R::load('tasks', $id);
    }

    public function index($id_step)
    {
        $tasks = R::findAll('tasks', 'id_step = ?', array($id_step));

        return $tasks;
    }

    public function delete($id)
    {
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
        if(isset($_POST['urgent']))
            $task->urgent = $_POST['urgent'][0];
        else
            $task->urgent = 0;
        $task->startline = $_POST['startline'];
        $task->deadline = $_POST['deadline'];

        R::store($task);
        return $task;
    }

    public function create($id_step)
    {
        var_dump($_POST['members']);
        die('test');

        $task = R::dispense('tasks');

        if(empty($_POST['name']))
        {
            return array('name.empty' => 'Name can\'t be empty');
        }

        $task->name = $_POST['name'];
        $task->id_step = $id_step;
        $task->description = $_POST['description'];
        if(isset($_POST['urgent']))
            $task->urgent = $_POST['urgent'][0];
        else
            $task->urgent = 0;
        $task->startline = $_POST['startline'];
        $task->deadline = $_POST['deadline'];

        R::store($task);

        return true;
    }
}