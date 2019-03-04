<?php

namespace app\components;


use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use app\App;
use app\Component;
/**
 * Class Help
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

        $dbConfig = App::getConfig('database.connection');

        $isDevMode = App::getConfig('app.debug');

        //Php annotation
        $config = Setup::createAnnotationMetadataConfiguration([
            'path' => App::getRequest('root_path') . "/app/entities",
        ], $isDevMode);

        //XML
//        $config = Setup::createXMLMetadataConfiguration([
//            'path' => App::getRequest('root_path') . "/app/schema",
//        ], $isDevMode);

        // obtaining the entity manager
        $instance->db = EntityManager::create($dbConfig, $config);

        return $instance;
    }

}