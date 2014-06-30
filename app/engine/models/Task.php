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

    public function index()
    {
        $tasks = R::findAll('tasks');

        return $tasks;
    }

    public function delete($id)
    {
        $task = $this->find($id);

        if($_SESSION['user']['id'] != $task->getProperties()['users_id'])
        {
            return array('user.forbidden' => 'Vous n\'avez pas les droits de faire ca');
        }

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

        if(!empty($errors))
            return $errors;
        //___________________________________

        $task->name = $_POST['name'];
        $task->id_step = $id_step;
        $task->descrption = $_POST['description'];
        // $task->urgent = $_POST['urgent'];
        $task->startline = $_POST['startline'];
        $task->deadline = $_POST['deadline'];

        R::store($task);

        return true;
    }
}