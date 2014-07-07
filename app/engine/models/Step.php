<?php

namespace app\engine\models;

use R;
use app\engine\models\Common;

class Step extends Common
{
    public function find($id_step)
    {
        var_dump('Bite');
        $step = R::load('steps', $id_step);

        return $step;
    }

    public function show($id_project, $id_step)
    {
        $this->permission_access($id_project);
        $step = R::load('steps', $id_step);

        $relation = R::getRow('SELECT * FROM projects_users WHERE id_user = :user AND id_project = '.$id_project.'',
                [':user' => $_SESSION['user']['id']]
            );

        $tab = array();
        if($relation['admin'] == 1)
            $tab['admin'] = $step;
        else
            $tab['member'] = $step;
    
        return $tab;
    }

    public function index($id_project)
    {
        $this->permission_access($id_project);
        $steps = R::findAll('steps', 'id_project = ?', array($id_project));

        $tab = array();
        foreach($steps as $step)
        {
            $relation = R::getRow('SELECT * FROM projects_users WHERE id_user = :user AND id_project = '.$id_project.'',
                [':user' => $_SESSION['user']['id']]
            );

            if($relation['admin'] == 1)
                $tab['admin'][] = $step;
            else
                $tab['member'][] = $step;

            $this->status_task($step->getProperties()['id']);
        }

        return $tab;
    }

    public function delete($id_project, $id_step)
    {
        $this->permission_exec($id_project);

        $step = $this->find($id_step);

        R::trash($step);
    }

    public function edit($id_project, $id_step)
    {
        $this->permission_exec($id_project);

        $step = R::load('steps', $id_step);

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
        $this->permission_exec($id_project);
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