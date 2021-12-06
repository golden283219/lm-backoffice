<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;

class CoverProcessController extends Controller
{
    private $temp_dir = '/tpm';

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        $this->temp_dir = Yii::getAlias('@app/runtime');
        return true;
    }

    private function isAllowedExtension($name)
    {
        $allowed = array('gif', 'png', 'jpg', 'jpeg');
        $ext = pathinfo($name, PATHINFO_EXTENSION);

        if (in_array($ext, $allowed)) {
            return true;
        }

        return false;
    }

    /**
     * Script that upload image to image storage, read response and send to user
     * @return array
     */
    public function actionIndex ()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (isset($_FILES['image']) && file_exists($_FILES['image']['tmp_name'])) {

            if (!$this->isAllowedExtension($_FILES['image']['name'])) {
                return [
                    "success" => false,
                    "message" => "File extension not allowed"
                ];
            }

            $content = file_get_contents($_FILES['image']['tmp_name']);
            $response = \Yii::$app->imageStorage->handlePosterUpload($content);

            if ($response["success"] == false) {
                return $response;
            }

            $type = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

            $response['image'] = 'data:image/' . $type . ';base64,' . base64_encode($content);

            return $response;
        }

        return [
            'success' => false,
            'message' => 'File didn\'t send',
        ];
    }

    /**
     * Uploads Backdrop
     */
    public function actionBackdrop()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (isset($_FILES['image']) && file_exists($_FILES['image']['tmp_name'])) {

            if (!$this->isAllowedExtension($_FILES['image']['name'])) {
                return [
                    "success" => false,
                    "message" => "File extension not allowed"
                ];
            }

            $content = file_get_contents($_FILES['image']['tmp_name']);
            $response = \Yii::$app->imageStorage->handleBackdropUpload($content);

            if ($response["success"] == false) {
                return $response;
            }

            $type = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

            $response['image'] = 'data:image/' . $type . ';base64,' . base64_encode($content);

            return $response;
        }

        return [
            'success' => false,
            'message' => 'File didn\'t send',
        ];
    }
}
