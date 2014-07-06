<?php

namespace app\engine\models;

use R;

class Common
{
    protected function permission_exec($id_project, $type = NULL, $id_type = NULL)
    {
        $permission = R::getRow('SELECT * FROM projects_users WHERE id_user = :user AND id_project = '.$id_project.'',
            [':user' => $_SESSION['user']['id']]
        );

        if($permission['admin'] == 0)
        {
            if($type)
            {
                $type_obj = R::load($type, $id_type);
                if($type_obj->getProperties()['id_user'] == $_SESSION['user']['id'])
                    return;
            }
            die('Vous n\'avez pas les droits pour effectuer cette action.');
        }
    }

    protected function permission_access($id_project, $type = 'project')
    {
        if ($type == 'project')
        {
            $permission = R::getRow('SELECT * FROM projects_users WHERE id_user = :user AND id_project = '.$id_project.'',
                [':user' => $_SESSION['user']['id']]
            );
        }
        else
        {
            $permission = R::findOne('directories', 'id_user = ?', array($_SESSION['user']['id']));
        }

        if(!$permission)
            die('Vous n\'avez pas les droits pour acceder a ces information.');
    }

    public function userDirectories()
    {
        $directories = R::find('directories', 'id_user = ?', array($_SESSION['user']['id']));
        
        return $directories;
    }

    public function time_project($id_project)
    {
        $steps = R::find(
            'steps',
            ' id_project = :id_project',
            array(
                ':id_project' => $id_project
            )
        );

        $hour = 0;
        $price = 0;
        foreach($steps as $step)
        {
            $value = $this->time_step($step->getProperties()['id']);
            $hour += $value['price'];
            $price += $value['hour'];
        }

        $tab = array();
        $tab['price'] = $price;
        $tab['hour'] = $hour;

        return $tab;
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

        $hour = 0;
        $price = 0;
        foreach($tasks as $task)
        {
            $value = $this->time_task($task->getProperties()['id']);
            $hour += $value['price'];
            $price += $value['hour'];
        }

        $tab = array();
        $tab['price'] = $price;
        $tab['hour'] = $hour;

        return $tab;
    }

    
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


    public function status_step($id_project)
    {
        $steps = R::find(
            'steps',
            ' id_project = :id_project',
            array(
                ':id_project' => $id_project
            )
        );

        $value = 0;
        foreach($steps as $step)
        {
            $value += $this->status_task($step->getProperties()['id']);
        }

        if((count($steps)*3 == $value) && (count($steps) != 0))
            $result = 3;
        elseif(count($steps) == $value)
            $result = 1;
        else
            $result = 2;

        $project_status = R::load('projects', $id_project);
        $project_status->status = $result;
        R::store($project_status);

        return $result;
    }

    public function status_task($id_step)
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

        if(count($tasks)*100 == $value && (count($tasks) != 0))
            $result = 3;
        elseif($value != 0)
            $result = 2;
        else
            $result = 1;

        $step_status = R::load('steps', $id_step);
        $step_status->status = $result;
        R::store($step_status);

        return $result;
    }
}