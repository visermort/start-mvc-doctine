<?php

namespace app\controllers;

use app\Controller;
use app\App;
//use app\models\Task;
//use app\lib\Paginator;


/**
 * Class SiteController
 * @package app\controlers
 */
class SiteController extends Controller
{

    /**
     * @return string
     */
    public function actionTest()
    {

        $manager = App::getComponent('doctrine')->db;
//        //d($repository);exit;
////        //$repository = App::getComponent('doctrine')->db->getRepository('app\doctrine\Tasks');
////        //$tasks = $repository->findAll();
//////        //$query = App::getComponent('doctrine')->db->createQuery('select `id` from `xx_task`');
//////        //$tasks = $query->getArrayResult();
////        //$repository = App::getComponent('doctrine')->db->getRepository('app\entities\Users');
////        $user = $repository->find('app\entities\Users', 1);
////        //$task = $repository->find(11);
//////        //\Doctrine\Common\Util\Debug::dump($task);
//////      d($);
////        //d($repository, $task, $task->getUsername());exit;
////     //   d($repository, $task, $task->getStatus(), $task->getUserId());exit;
////        $task = new \app\entities\Tasks();
////        $task->setText('nnnnnnnnnnnnnnnnnnnn');
////        $task->setUser($user);
////
////        $repository->persist($task);
////        $repository->flush();
////        d($user, $task);exit;
         $query = $manager->createQuery('SELECT t,u FROM app\entities\Tasks t JOIN t.user u ORDER BY u.email');
         //$queryResult = $query->getResult();
         $queryResult = $query->getArrayResult();
         $repository = $manager->getRepository('app\entities\Tasks');
         //$repositoryUser = $manager->getRepository('app\entities\Users');
         $tasks = $repository->findAll();
         //$users = $repositoryUser->findAll();
         d($tasks, $queryResult);


    }


}
