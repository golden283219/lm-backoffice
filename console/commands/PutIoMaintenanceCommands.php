<?php


namespace console\commands;

use Yii;
use yii\queue\amqp\Command;
use console\jobs\putio\DeleteUntrackedFiles;
use console\jobs\putio\DeleteFinishedTransfers;

class PutIoMaintenanceCommands extends Command
{
    public function actionDeleteFinishedTransfers()
    {
        Yii::$app->putIoMaintenance->push(new DeleteFinishedTransfers());
    }

    public function actionDeleteUntrackedFiles()
    {
        Yii::$app->putIoMaintenance->push(new DeleteUntrackedFiles());
    }
}
