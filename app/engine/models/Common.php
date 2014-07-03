<?php

namespace app\engine\models;

use R;

class Common
{
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

        return $result;
    }
}