<?php

namespace console\controllers;

use common\models\site\PremUsers;
use console\jobs\SendEmail;
use Yii;
use yii\helpers\Console;
use yii\web\NotFoundHttpException;

class PremController extends \yii\console\Controller
{
    public function actionUpdateAuthToken($forceUpdate = false)
    {
        if (is_string($forceUpdate)) {
            $forceUpdate = $forceUpdate === 'true' ? true : false;
        }

        foreach (PremUsers::find()->batch(400) as $premUsers) {
            foreach ($premUsers as $premUser) {
                if (empty($premUser->token_key) || $forceUpdate) {
                    Console::output($premUser->email . ': Updated.');
                    $premUser->token_key = Yii::$app->getSecurity()->generateRandomString(8);
                    $premUser->save();
                }
            }
        }
    }

    public function actionSendCredentials($email)
    {
        $premium_user = PremUsers::find()->where(['email' => $email])->one();

        if (empty($premium_user)) {
            throw new NotFoundHttpException('User With Requested email not found', 404);
        }

        Yii::$app->emailSendQueue->push(new SendEmail([
            'toEmail'   => $premium_user->email,
            'fromEmail' => [env('APP_DONT_REPLY_EMAIL') => env('APP_DONT_REPLY_FROM')],
            'replyTo'   => [env('APP_PREM_SUPPORT_EMAIL') => env('APP_PREM_SUPPORT_FROM')],
            'subject'   => 'Lookmovie Premium Account Credentials',
            'template'  => 'AccountCreated',
            'fields'    => [
                'userEmail' => $premium_user->email,
                'password'  => $premium_user->plain_password,
                'auth_key'  => $premium_user->auth_key
            ]
        ]));
    }
}
