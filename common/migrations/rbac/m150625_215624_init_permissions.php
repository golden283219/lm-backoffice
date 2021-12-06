<?php

use common\models\User;
use common\rbac\Migration;

class m150625_215624_init_permissions extends Migration
{
    public function up()
    {
        $moderatorRole = $this->auth->getRole(User::ROLE_MODERATOR);
        $superModeratorRole = $this->auth->getRole(User::ROLE_SUPER_MODERATOR);
        $administratorRole = $this->auth->getRole(User::ROLE_ADMINISTRATOR);

        $loginToBackend = $this->auth->createPermission('loginToBackend');
        $this->auth->add($loginToBackend);

        $this->auth->addChild($moderatorRole, $loginToBackend);
        $this->auth->addChild($superModeratorRole, $loginToBackend);
        $this->auth->addChild($administratorRole, $loginToBackend);
    }

    public function down()
    {
        $this->auth->remove($this->auth->getPermission('loginToBackend'));
    }
}
