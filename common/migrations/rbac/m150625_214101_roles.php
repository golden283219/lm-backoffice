<?php

use common\models\User;
use common\rbac\Migration;

class m150625_214101_roles extends Migration
{
    /**
     * @return bool|void
     * @throws \yii\base\Exception
     */
    public function up()
    {
        $this->auth->removeAll();

        $moderator = $this->auth->createRole(User::ROLE_MODERATOR);
        $this->auth->add($moderator);

        $super_moderator = $this->auth->createRole(User::ROLE_SUPER_MODERATOR);
        $this->auth->add($super_moderator);
        $this->auth->addChild($super_moderator, $moderator);

        $admin = $this->auth->createRole(User::ROLE_ADMINISTRATOR);
        $this->auth->add($admin);
        $this->auth->addChild($admin, $super_moderator);
        $this->auth->addChild($admin, $moderator);

    }

    /**
     * @return bool|void
     */
    public function down()
    {
        $this->auth->remove($this->auth->getRole(User::ROLE_ADMINISTRATOR));
        $this->auth->remove($this->auth->getRole(User::ROLE_MANAGER));
        $this->auth->remove($this->auth->getRole(User::ROLE_USER));
    }
}
