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
        //$query = $manager->createQuery('SELECT t,u FROM app\entities\Tasks t LEFT JOIN t.user u ORDER BY u.email');
        //$queryResult = $query->getArrayResult();
        $queryBuilder = $manager->createQueryBuilder();
        $queryBuilderUsers = $manager->createQueryBuilder();
        $queryBuilderUsers->select('subu.id')
            ->from('app\entities\Users', 'subu')
            ->where($queryBuilderUsers->expr()->like('subu.email', $queryBuilderUsers->expr()->literal('some%')))
        ;

        $queryBuilder->select('t', 'u')
            ->from('app\entities\Tasks', 't')
            ->leftJoin('t.user', 'u')
            ->where($queryBuilder->expr()->in('u.id', $queryBuilderUsers->getDQL()))
        ;
        $queryResult = $queryBuilder->getQuery()->getArrayResult();
        //$queryResultModel = $queryBuilder->getQuery()->getResult();

        d($queryResult);

        foreach ($queryResult as $task) {
            d($task, $task['user']);
        }

    }


}
