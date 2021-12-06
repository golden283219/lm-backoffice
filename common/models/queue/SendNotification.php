<?php

namespace common\models\queue;

use yii\base\BaseObject;

class SendNotification extends BaseObject implements \yii\queue\JobInterface
{
    public $email;

    public function execute($queue)
    {
        // Send notification to user
        $mailer = \Yii::$app->mailer->compose()
            ->setFrom('support@lookmovie.ag')
            ->setTo($this->email)
            ->setSubject('Send Notification about new episode')
            ->setTextBody('They are new episode!')
            ->setHtmlBody('<b>They are new episode</b>');

        return $mailer->send();
    }
}
