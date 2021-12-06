<?php


namespace common\models\site;

use Yii;
use yii\base\Model;

/**
 * Class SignupForm
 *
 * @package yii2mod\user\models
 */
class PremSignupForm extends Model
{
    /**
     * @var string $password
     */
    public $password;
    /**
     * @var string username
     */
    public $status;

    /**
     * @var string email
     */
    public $email;

    /**
     * @var UserModel
     */
    protected $user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => PremUsers::class, 'message' => 'This email address has already been taken.'],
            [['status'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'email' => 'Email',
            'status' => 'Status',
        ];
    }

    /**
     * Signs user up.
     *
     * @return PremUsers|null the saved model or null if saving fails
     * @throws \yii\base\Exception
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        if (empty($this->password)) {
            $this->password = random_str(8);
        }

        $this->user = new \backend\models\PremUsers();
        $this->user->setAttributes($this->attributes);
        $this->user->setPassword($this->password);
        $this->user->created_at = time();
        $this->user->updated_at = time();
        $this->user->setLastLogin(time());
        $this->user->token_key = Yii::$app->getSecurity()->generateRandomString(8);
        $this->user->status = 1;
        $this->user->generateAuthKey();

        return $this->user->save() ? $this->user : null;
    }

    /**
     * @return UserModel|null
     */
    public function getUser()
    {
        return $this->user;
    }
}
