<?php

declare(strict_types=1);

namespace app\migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use app\App;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190304072546 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add Admin User';
    }

    public function up(Schema $schema) : void
    {
        $auth = App::getComponent('auth');
        $user = $auth->register([
            'email' => 'account@doctrine.ru',
            'password' => '123456',
            'first_name' => 'Admin',
            'last_name' => 'Doctrine',
        ]);
        $this->abortIf(!$user, 'User did not created.');
        $permissions = $auth->setUserPermision($user, ['admin']);
        $this->abortIf(empty($permissions), 'Permission "admin" did not add.');
        d($user, $permissions);
    }

    public function down(Schema $schema) : void
    {
        $em = App::getComponent('doctrine')->db;
        $usersRepository = $em->getRepository('app\entities\Users');
        $user = $usersRepository->findOneBy(['email' => 'account@doctrine.ru']);
        $em->remove($user);
        $em->flush();
    }
}
