<?php

use app\App;

/**
 * custom functions for twig
 */
return [
    'config' => function ($name) {
        return App::getComponent('config')->get($name);
    },
    'date' => function ($pattern, $date = null) {
        if ($date) {
            if ($date instanceof DateTime) {
                $date = $date->getTimestamp();
            } else {
                $date = is_integer($date) ? $date : strtotime($date);
            }
        } else {
            $date = time();
        }
        return date($pattern, $date);
    },
    'user' => function () {
        $auth = App::getComponent('auth');
        return $auth->getUser();
    },
    'sort_by' => function ($name, $title) {
        $help = App::getComponent('help');
        return $help->sortBy($name, $title);
    },
    'user_has' => function ($permission) {
        $auth = App::getComponent('auth');
        $user =  $auth->getUser();
        return $user && $user->hasAccessTo($permission) ? 1 : 0;
    },
    'status' => function ($status) {
        return $status ? 'Completed' : 'In process';
    },
    'csrf' => function () {
        $session = App::getComponent('session');
        return $session->setCsrf();
    }

];