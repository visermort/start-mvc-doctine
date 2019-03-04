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

    /**
     * @param $data
     * @param null $className. if null takes its name
     * @param bool $flush - flush after persisting or not
     * @return entity
     */
    public static function create($data, $className = null, $flush = true)
    {
        if (empty($data)) {
            return null;
        }
        $className = $className ? $className : get_called_class();//if null take itself
        $entity = new $className();
        foreach ($data as $key => $value) {
            //fields like fist_name convert to setFirstName
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
