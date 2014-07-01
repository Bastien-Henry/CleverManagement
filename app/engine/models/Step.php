<?php

namespace app\engine\models;

use R;

class Step
{
    public function show($id)
    {
        $step = R::load('steps', $id);

        if($step->getProperties()['id'] == 0)
        {
            return array('step.not_found' => 'Step doesnt exist');
        }

        return $step;
    }

    public function index()
    {
        $steps = R::findAll('steps');

        return $steps;
    }

    public function delete($id)
    {
        $step = $this->show($id);

        if($_SESSION['user']['id'] != $step->getProperties()['users_id'])
        {
            return array('user.forbidden' => 'Vous n\'avez pas les droits de faire ca');
        }

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
        //$step->id_project = $_POST['project'];        hidden field storing project id
        R::store($step);

        return true;
    }
}