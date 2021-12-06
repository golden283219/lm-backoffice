<?php

namespace console\controllers;

use Yii;
use yii\base\Module;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class AppController extends Controller
{
    /** @var array */
    public $writablePaths = [
        '@common/runtime',
        '@frontend/runtime',
        '@frontend/web/assets',
        '@backend/runtime',
        '@backend/web/assets',
        '@storage/cache',
        '@storage/web/source',
        '@api/runtime',
    ];

    /** @var array */
    public $executablePaths = [
        '@backend/yii',
        '@frontend/yii',
        '@console/yii',
        '@api/yii',
    ];

    /** @var array */
    public $generateKeysPaths = [
        '@base/.env'
    ];

    /**
     * Get Current APP Version
     */
    public function actionVersion () {
        Console::output('Author: ' . env('APP_AUTHOR'));
        Console::output('ver. ' . env('APP_VERSION'));
        return true;
    }

    /**
     * GET PHP cached Files
     */
    public function actionGetCachedFiles () {
        print_r(realpath_cache_get());
    }

    /**
     * Sets given keys to .env file
     */
    public function actionSetKeys()
    {
        $this->setKeys($this->generateKeysPaths);
    }

    /**
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function actionSetup()
    {
        $this->runAction('set-writable', ['interactive' => $this->interactive]);
        $this->runAction('set-executable', ['interactive' => $this->interactive]);
        $this->runAction('set-keys', ['interactive' => $this->interactive]);
        \Yii::$app->runAction('migrate/up', ['interactive' => $this->interactive]);
        \Yii::$app->runAction('rbac-migrate/up', ['interactive' => $this->interactive]);
    }

    /**
     * Adds write permissions
     */
    public function actionSetWritable()
    {
        $this->setWritable($this->writablePaths);
    }

    /**
     * Updates Counters On different databases
     * this function used to force all updates
     */
    public function actionUpdateCounters()
    {
        \Yii::$app->db->createCommand("UPDATE movies_moderation SET active_reports_count = 0")->execute();

        $query = (new \yii\db\Query())
            ->select(['id_movie', 'count(id_movie) as total'])
            ->from('movies_reports')
            ->where(['is_closed' => '0'])
            ->groupBy(['id_movie']);

        foreach ($query->batch(100) as $rows) {
            foreach ($rows as $row) {
                $update_query = \Yii::$app->db->createCommand("
                UPDATE movies_moderation SET active_reports_count = {$row['total']}
                WHERE id_movie = {$row['id_movie']}
                ")->execute();
            }
        }

        Console::output("Done.");
    }

    /**
     * Adds execute permissions
     */
    public function actionSetExecutable()
    {
        $this->setExecutable($this->executablePaths);
    }

    /**
     * @param $paths
     */
    private function setWritable($paths)
    {
        foreach ($paths as $writable) {
            $writable = Yii::getAlias($writable);
            Console::output("Setting writable: {$writable}");
            @chmod($writable, 0777);
        }
    }

    /**
     * @param $paths
     */
    private function setExecutable($paths)
    {
        foreach ($paths as $executable) {
            $executable = Yii::getAlias($executable);
            Console::output("Setting executable: {$executable}");
            @chmod($executable, 0755);
        }
    }

    /**
     *
     * @param $paths
     */
    private function setKeys($paths)
    {
        foreach ($paths as $file) {
            $file = Yii::getAlias($file);
            Console::output("Generating keys in {$file}");
            $content = file_get_contents($file);
            $content = preg_replace_callback('/<generated_key>/', function () {
                $length = 32;
                $bytes = openssl_random_pseudo_bytes(32, $cryptoStrong);
                return strtr(substr(base64_encode($bytes), 0, $length), '+/', '_-');
            }, $content);
            file_put_contents($file, $content);
        }
    }
}
