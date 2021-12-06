<?php


namespace backend\models;


use Yii;

class PremUsers extends \common\models\site\PremUsers
{
    /**
     * @param $lastLogin
     */
    public function setLastLogin($lastLogin)
    {
        $this->last_login = $lastLogin;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     *
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
        $this->plain_password = $password;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->getSecurity()->generateRandomString();
    }

    /**
     * Revokes Given Package From User Account
     *
     * @param PremPlans $package
     *
     * @return bool
     */
    public function revokePackage(PremPlans $package): bool
    {
        $this->cancel_timestamp = max($this->cancel_timestamp - $package->extra_time, 0);
        return $this->save();
    }
}
