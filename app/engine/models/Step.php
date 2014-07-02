<?php

namespace app\engine\models;

use R;

class Step
{
    public function status($id_step)
    {
        $tasks = R::find(
            'tasks',
            ' id_step = :id_step',
            array(
                ':id_step' => $id_step
            )
        );

        $value = 0;
        foreach($tasks as $task)
        {
            $value += $task->getProperties()['percent'];
        }

        if((count($tasks)*100 == $value) && (count($tasks) != 0))
            $result = 3;
        elseif($value != 0)
            $result = 2;
        else
            $result = 1;

        return $result;
    }

    public function time_step($id_step)
    {
        $tasks = R::find(
            'tasks',
            ' id_step = :id_step',
            array(
                ':id_step' => $id_step
            )
        );

        $value = 0;
        foreach($tasks as $task)
        {
            $value += $task->getProperties()['total_hour'];
        }

        return $value;
    }

    public function show($id)
    {
        $step = R::load('steps', $id);

        if($step->getProperties()['id'] == 0)
        {
            return array('step.not_found' => 'Step doesnt exist');
        }

        return $step;
    }

    public function index($id_project)
    {
        $steps = R::findAll('steps', 'id_project = ?', array($id_project));

        return $steps;
    }

    public function delete($id)
    {
        $step = $this->show($id);

        R::trash($step);
    }

    public function edit($id)
    {
        $step = R::load('steps', $id);

        if(empty($_POST['name']))
        {
            return array('name.empty' => 'Name can\'t be empty');
        }

        $step->name = $_POST['name'];
        $step->description = $_POST['description'];
        $step->startline = $_POST['startline'];
        $step->deadline = $_POST['deadline'];

        R::store($step);
        return $step;
    }

    public function create($id_project)
    {
        $step = R::dispense('steps');

        if(empty($_POST['name']))
        {
            return array('name.empty' => 'Name can\'t be empty');
        }

        if(!empty($errors))
            return $errors;
        //___________________________________
        $step->name = $_POST['name'];
        $step->id_project = $id_project;
        $step->description = $_POST['description'];
        $step->startline = $_POST['startline'];
        $step->deadline = $_POST['deadline'];
        R::store($step);

        return true;
    }
}