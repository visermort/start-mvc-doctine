<?php

namespace app;

/**
 * Class Entity parent class for entities
 * @package app
 */
class Entity
{

    public function update($data, $flush = true)
    {
        if (empty($data)) {
            return null;
        }
        try {
            foreach ($data as $key => $value) {
                //fields like fist_name convet to  setFirstName
                $method  = 'set' . App::getComponent('help')->commandToAction($key);
                if (method_exists($this, $method)) {
                    $this->$method($value);
                }
            }
            if ($flush) {
                App::getComponent('doctrine')->db->flush();
            }
            return true;
        } catch (\Exception $e) {
            if (App::getConfig('app.debug')) {
                d($e);
            }
            return false;
        }
    }

    public static function create($className, $data, $flush = true)
    {
        if (empty($data)) {
            return null;
        }
        $entity = new $className();
        foreach ($data as $key => $value) {
            //fields like fist_name convet to setFirstName
            $method  = 'set' . App::getComponent('help')->commandToAction($key);
            if (method_exists($entity, $method)) {
                $entity->$method($value);
            }
        }
        $entityManager = App::getComponent('doctrine')->db;
        $entityManager->persist($entity);
        if ($flush) {
            $entityManager->flush();
        }
        return $entity;
    }
}
