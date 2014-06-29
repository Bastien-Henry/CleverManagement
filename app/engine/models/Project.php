<?php

namespace app\engine\models;

use R;

class Project
{
    public function create()
    {
        $project = R::dispense('projects');

        $project->name = $_POST['name'];
        $project->startline = $_POST['startline'];
        $project->deadline = $_POST['deadline'];
        $project->description = $_POST['description'];

        R::store($project);

        return true;
    }

    public function show($id)
    {
        $project = R::load('projects', $id);

        if($project->getProperties()['id'] == 0)
        {
            return array('project.not_found' => 'project doesnt exist');
        }

        return $project;
    }

    public function index()
    {
        $projects = R::findAll('projects');

        return $projects;
    }

    public function edit($id)
    {
        $project = R::load('projects', $id);

        if(empty($_POST['name']))
        {
            return array('name.empty' => 'Name can\'t be empty');
        }

        $project->name = $_POST['name'];
        $project->description = $_POST['description'];

        R::store($project);
        return $project;
    }

    public function delete($id)
    {
        $project = R::load('projects', $id);

        if($project->getProperties()['id'] == 0)
        {
            return array('project.not_found' => 'Project doesnt exist');
        }

        // if($_SESSION['user']['id'] != $project->getProperties()['users_id'])
        // {
        // return array('user.forbidden' => 'Vous n\'avez pas les droits de faire ca');
        // }

        R::trash($project);
    }
}