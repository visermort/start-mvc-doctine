<?php

namespace app\components;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use app\App;
use app\Component;
/**
 * Class Doctrine
 * @package app\components
 */
class Doctrine extends Component
{
    public $db;
    /**
     * Db init
     */
    public static function init()
    {
        $instance = parent::init();

        $appConfig = App::getComponent('config');
        $dbConfig = $appConfig->get('database.connection');
        $isDevMode = $appConfig->get('app.debug');

        //Php annotation
        $config = Setup::createAnnotationMetadataConfiguration([
            'path' => App::getRootPath() . "/app/entities",
        ], $isDevMode);

        //XML
//        $config = Setup::createXMLMetadataConfiguration([
//            'path' => App::getRequest('root_path') . "/app/schema",
//        ], $isDevMode);

        // obtaining the entity manager
        $instance->db = EntityManager::create($dbConfig, $config);

        if ($appConfig->get('app.debug') && $appConfig->get('app.clear_doctrine_metadata_cache_on_debug')) {
            //deleting metadata cache on debug and set it in config
            $cacheDriver = $instance->db->getConfiguration()->getMetadataCacheImpl();
            $cacheDriver->deleteAll();
        }

        return $instance;
    }

}