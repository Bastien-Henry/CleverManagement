<?php

namespace app\engine\models;

use R;

class Step
{
    public function find($id)
    {
        $step = R::load('steps', $id);

        if($step->getProperties()['id'] == 0)
        {
            return array('step.not_found' => 'step doesnt exist');
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
        $step = $this->find($id);

        if($_SESSION['user']['id'] != $step->getProperties()['users_id'])
        {
            return array('user.forbidden' => 'Vous n\'avez pas les droits de faire ca');
        }

        R::trash($step);
    }

    public function edit($id)
    {
        $step = R::load('steps', $id);

        if(empty($_step['name']))
        {
            return array('name.empty' => 'Name can\'t be empty');
        }

        $step->name = $_step['name'];
        $step->description = $_step['description'];
        $step->startline = $_step['startline'];
        $step->deadline = $_step['deadline'];

        R::store($step);
        return $step;
    }

    public function create()
    {
        $step = R::dispense('steps');

        if(empty($_step['name']))
        {
            return array('name.empty' => 'Name can\'t be empty');
        }

        if(!empty($errors))
            return $errors;
        //___________________________________

        $step->name = $_step['name'];
        $step->descrption = $_step['description'];
        $step->startline = $_step['startline'];
        $step->deadline = $_step['deadline'];
        //$step->id_project = $_step['project'];        hidden field storing project id

        R::store($step);

        return true;
    }
}