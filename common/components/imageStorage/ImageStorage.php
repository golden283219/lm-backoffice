<?php

namespace common\components\imageStorage;

use Intervention\Image\ImageManagerStatic as Image;
/**
 * Image Storage Helper
 */
class ImageStorage extends \yii\base\Component
{
    const FACE_SIZE_W45          = 'w45';
    const FACE_SIZE_W90          = 'w90';
    const FACE_SIZE_W185         = 'w185';
    const FACE_SIZE_ORIGINAL     = 'original';

	const POSTER_SIZE_W100       = 'w100';
	const POSTER_SIZE_W200       = 'w200';
	const POSTER_SIZE_W300       = 'w300';
	const POSTER_SIZE_W400       = 'w400';
	const POSTER_SIZE_W500       = 'w500';
	const POSTER_SIZE_ORIGINAL   = 'original';

	const BACKDROP_SIZE_W342     = 'w342';
	const BACKDROP_SIZE_W780     = 'w780';
	const BACKDROP_SIZE_W1280    = 'w1280';
	const BACKDROP_SIZE_ORIGINAL = 'original';

	const tmdb_image_base_url         = '//image.tmdb.org/t/p/{size}{key}';
	const lookmovie_poster_base_url   = '/images/p/{size}{key}';
	const lookmovie_face_base_url     = '/images/f/{size}{key}';
	const lookmovie_backdrop_base_url = '/images/b/{size}{key}';

    const MIN_POSTER_HEIGHT = 750;
    const MIN_POSTER_WIDTH = 500;
    const MIN_FACE_HEIGHT = 300;
    const MIN_FACE_WIDTH = 200;
    const MIN_BACKDROP_HEIGHT = 720;
    const MIN_BACKDROP_WIDTH = 1280;

    const MAX_FILE_SIZE = 50 * 1024 * 1024;
    const BASE_URL = 'http://31.220.26.23:6600';

    const ALLOWED_IMG_TYPE = ['image/jpeg', 'image/png'];

    private $client;

    public function __construct()
    {
        if (!isset($this->client)) {
            $this->client = new \GuzzleHttp\Client();
        }
        return $this->client;
    }

    /**
     * Creates Poster Url With Given Key
     * @param $size
     * @param $key
     * @param string $default
     * @return string
     */
	public static function poster ($size, $key, $default = '')
    {
        if ($key === null || $key === '') {
            return $default;
        }

        if (self::is_lookmovie_storage_image($key)) {
            return strtr(self::lookmovie_poster_base_url, [
                '{key}'  => $key,
                '{size}' => $size,
            ]);
        }

        return strtr(self::tmdb_image_base_url, [
            '{key}' =>  $key,
            '{size}' => $size
        ]);
    }

    /**
     * Creates Face Url With Given Key
     * @param $size
     * @param $key
     * @param string $default
     * @return string
     */
    public static function face ($size, $key, $default = '')
    {
        if ($key === null || $key === '') {
            return $default;
        }

        if (self::is_lookmovie_storage_image($key)) {
            return strtr(self::lookmovie_face_base_url, [
                '{key}'  => $key,
                '{size}' => $size,
            ]);
        }

        return strtr(self::tmdb_image_base_url, [
            '{key}' =>  $key,
            '{size}' => $size
        ]);
    }

    /**
     * Creates Backdrop Url With Given Key
     * @param $size
     * @param $key
     * @param string $default
     * @return string
     */
    public static function backdrop ($size, $key, $default = '')
    {
        if ($key === null || $key === '') {
            return $default;
        }

        if (self::is_lookmovie_storage_image($key)) {
            return strtr(self::lookmovie_backdrop_base_url, [
                '{key}'  => $key,
                '{size}' => $size,
            ]);
        }

        return strtr(self::tmdb_image_base_url, [
            '{key}' =>  $key,
            '{size}' => $size
        ]);
    }

    public function handlePosterUpload(string $contents) {
        return $this->handleImageUpload($contents, 'poster');
    }

    public function handleFaceUpload(string $contents) {
        return $this->handleImageUpload($contents, 'face');
    }

    public function handleBackdropUpload(string $contents) {
        return $this->handleImageUpload($contents, 'backdrop');
    }

    private static function is_lookmovie_storage_image ($key)
    {
        if(preg_match('/\/[a-f0-9]{32}\.jpg/', $key)) {
            return true;
        }
        return false;
    }

    private function handleImageUpload(string $contents, string $type) {
        $isValid = $this->validateImage($contents, $type);
        $result = [];
        if ($isValid) {
            $postUrl = self::BASE_URL . '/upload/' . $type;
            try {
                $answer = $this->client->post(
                    $postUrl, [
                        'multipart' => [
                        [
                            'name' => 'image',
                            'contents' => $contents,
                            'filename' => md5(time()). '.jpg'
                        ]]
                    ]
                );
                $result = json_decode($answer->getBody(), 1);

            } catch (\Exception $e) {
                $result = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        } else {
            $result = [
                'success' => false,
                'error' => 'Wrong image size',
            ];
        }
        return $result;
    }

    private function validateImage(string $imageSrc, string $type) {
        $isValid = false;
        if (!empty($imageSrc) && !empty($type)) {

            $image = Image::make($imageSrc);
            $height = $image->getHeight();
            $width = $image->getWidth();

            switch ($type) {
                case 'poster':
                    if ($height >= self::MIN_POSTER_HEIGHT && $width >= self::MIN_POSTER_WIDTH) {
                        $isValid = true;
                    }
                    break;
                case 'face':
                    if ($height >= self::MIN_FACE_HEIGHT && $width >= self::MIN_FACE_WIDTH) {
                        $isValid = true;
                    }
                    break;
                case 'backdrop':
                    if ($height >= self::MIN_BACKDROP_HEIGHT && $width >= self::MIN_BACKDROP_WIDTH) {
                        $isValid = true;
                    }
                    break;
            }

            $imageType = $image->mime();
            if ($isValid && !in_array($imageType, self::ALLOWED_IMG_TYPE)) {
                $isValid = false;
            }

            $imageSize = strlen($imageSrc);
            if ($imageSize > self::MAX_FILE_SIZE) {
                $isValid = false;
            }
        }

        return $isValid;
    }
}
