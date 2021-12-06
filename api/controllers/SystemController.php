<?php declare(strict_types=1);

namespace api\controllers;

use Yii;
use yii\filters\ContentNegotiator;
use yii\web\Response;

class SystemController extends \yii\web\Controller
{

	public function beforeAction ($action)
	{
		Yii::$app->controller->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}

 	public function behaviors()
 	{
    	return [
    		'contentNegotiator' => [
    			'class' => ContentNegotiator::class,
    			'formatParam' => 'o',
    			'formats' => [
    				'application/json' => Response::FORMAT_JSON,
    				'application/xml' => Response::FORMAT_XML,
    			],
    		],
      		'corsFilter' => [
        		'class' => \yii\filters\Cors::className(),
      		],
      		'verbs' => [
	            'class' =>  \yii\filters\VerbFilter::className(),
	            'actions' => [
	                'write-log' => ['POST'],
	            ],
        	],
    	];
 	}

 	public function actionWriteLog ()
 	{
 		$ip = $_SERVER['REMOTE_ADDR'];

 		$post = Yii::$app->request->post();

 		if (isset($post['app_id']))
 		{
 			Yii::$app->id = $post['app_id'] . ' (' . $ip . ')';
 		}

 		if (isset($post['level']) && isset($post['category']) && isset($post['message']))
 		{
 			switch ($post['level'])
 			{
 				case '200':
	 				\Yii::info($post['message'], $post['category']);
 					break;
 				case '300':
 					\Yii::warning($post['message'], $post['category']);
 					break;
 				case '400':
 					\Yii::error($post['message'], $post['category']);
 					break;
 				default:
 					return false;
 					break;
 			}
 		}

 		return true;
 	}

}
