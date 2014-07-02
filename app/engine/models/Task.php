<?php

namespace app\engine\models;

use R;

class Task
{
    public function time_task($id_task)
    {
        $sessions = R::find(
            'sessions',
            ' id_task = :id_task',
            array(
                ':id_task' => $id_task
            )
        );

        $hour = 0;
        $price = 0;
        $value = 0;
        foreach($sessions as $session)
        {
            $hour += $session->getProperties()['hour_number'];
            $price += $session->getProperties()['price'];
        }

        $tab = array();
        $tab['price'] = $price;
        $tab['hour'] = $hour;

        return $tab;
    }

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
        $query = 'SELECT * FROM tasks_users WHERE id_task = :task';
        $params = array(':task' => $id);
        $relations = R::getAll($query, $params);

        $members = array();
        foreach ($relations as $key => $object) {
            $members[] = R::load('users', $object['id_user']);
        }

        return $members;
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

        $id_task = R::store($task);

        foreach ($_POST['members'] as $email) {
            $user = R::findOne('users', 'email = ?', [$email]);
            R::exec('INSERT INTO tasks_users (id_user, id_task) VALUES (:user, :task)', array(
                    ':user'     => $user->getProperties()['id'], 
                    ':task'  => $id_task
                ));
        }

        return true;
    }
}