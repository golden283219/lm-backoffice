<?php


namespace console\jobs;

use Yii;
use yii\base\BaseObject;

class SendEmail extends BaseObject implements \yii\queue\JobInterface
{

    /**
     * To Email
     */
    public $toEmail;

    /**
     * fromEmail
     */
    public $fromEmail;

    /**
     * replyTo Email
     */
    public $replyTo;

    /**
     * Email Subject
     */
    public $subject;

    /**
     * Email Template Name
     **/
    public $template;

    /**
     * Fields We Pass To Email Template
     */
    public $fields;

    public function execute($queue)
    {
        Yii::$app->mailer->setViewPath('@common/mail');
        Yii::$app->mailer->compose($this->template, $this->fields)
            ->setFrom($this->fromEmail)
            ->setTo($this->toEmail)
            ->setReplyTo($this->replyTo)
            ->setSubject($this->subject)
            ->send();
    }
}
