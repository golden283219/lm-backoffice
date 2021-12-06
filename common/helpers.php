<?php

use common\helpers\SimpleHtmlDom;
use yii\helpers\ArrayHelper;

function dd($var)
{
    var_dump($var);
    die();
}

if (!function_exists('extract_domain_from')) {
    function extract_domain_from_url($url)
    {
        $re = '/http|s:\/\/(.+?)\//m';

        preg_match_all($re, $url, $matches, PREG_SET_ORDER, 0);

        return ArrayHelper::getValue($matches, '1.1');
    }
}

if (!function_exists('find_id_meta_by_id_episode')) {
    function find_id_meta_by_id_episode($id_episode)
    {
        $episode = \common\models\ShowsEpisodes::find()
            ->where(['id' => $id_episode])
            ->one();

        if ($episode === null) {
            return null;
        }

        $shows_meta = \backend\models\queue\ShowsMeta::find()
            ->where([
                'id_tvshow' => $episode->id_shows,
                'episode' => $episode->episode,
                'season' => $episode->season
            ])
            ->one();

        if ($shows_meta === null) {
            return null;
        }

        return $shows_meta;
    }
}

if (!function_exists('backup_config')) {
    function backup_config()
    {
        $original_path = env('LB_CONFIG_PATH');
        $backup_path = env('LB_CONFIG_PATH') . '.bak';

        if (!file_exists($original_path)) {
            return true;
        }
        return copy($original_path, $backup_path);
    }
}

if (!function_exists('restore_config_backup')) {
    function restore_config_backup()
    {
        $original_path = env('LB_CONFIG_PATH');
        $backup_path = env('LB_CONFIG_PATH') . '.bak';

        if (!file_exists($backup_path)) {
            return true;
        }

        return copy($backup_path, $original_path);
    }
}

if (!function_exists('lb_api_do_config_update')) {
    function lb_api_do_config_update()
    {
        $update_url = env('LB_API_URL') . '/update';

        $result = null;
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $update_url, ['connect_timeout' => 10]);

            $result = json_decode($response->getBody());
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $req = $e->getRequest();
            $resp = $e->getResponse();
            \Yii::error([
                'message' => $resp->getBody()
            ], 'LB Config Update');
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $req = $e->getRequest();
            $resp = $e->getResponse();
            \Yii::error([
                'message' => $resp->getBody()
            ], 'LB Config Update');
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $req = $e->getRequest();
            $resp = $e->getResponse();
            \Yii::error([
                'message' => $resp->getBody()
            ], 'LB Config Update');
        } catch( \Exception $e) {
            \Yii::error([
                'message' => $e->getMessage()
            ], 'LB Config Update');
        }

        if (isset($result) && $result->result === true) {
            return true;
        }

        return false;
    }
}

if (!function_exists('extractMagnetDN')) {
    function extractMagnetDN($magnet_link)
    {
        $re = '/dn=(.+?)(&|$)/m';
        preg_match_all($re, $magnet_link, $matches, PREG_SET_ORDER, 0);

        if (!empty($matches) && !empty($matches['0']) && !empty($matches['0']['1'])) {
            return $matches['0']['1'];
        }

        return '';
    }
}

if (!function_exists('base64_md5_hash')) {
    /**
     * Creates Hash For Given String
     *
     * @param $data
     *
     * @return mixed
     */
    function base64_md5_hash($data)
    {
        return str_replace('=', '', strtr(base64_encode(md5($data, TRUE)), '+/', '-_'));
    }
}

if (!function_exists('get_lmb_id_from_magnet')) {
    function get_lmb_id_from_magnet($magnet_link)
    {
        preg_match_all('/lmb_id=(\d+)/m', $magnet_link, $matches, PREG_SET_ORDER, 0);

        $id = null;
        try {
            $id = ArrayHelper::getValue($matches, '0.1');
        } catch (Exception $e) {
        }

        return $id;
    }
}

if (!function_exists('imdb_find_original_language')) {
    function imdb_find_original_language($contents)
    {
        $re = '/primary_language=([a-z]{2})/m';

        preg_match_all($re, $contents, $matches, PREG_SET_ORDER, 0);

        if (!empty($matches) && !empty($matches['0']) && !empty($matches['0']['0'])) {
            return $matches['0']['1'];
        }

        return null;
    }
}

if (!function_exists('imdb_find_year_title')) {
    function imdb_find_year_title($contents)
    {
        $re = '/<title>(.+)\((.+)\)\s-\sIMDb<\/title>/m';

        preg_match_all($re, $contents, $matches, PREG_SET_ORDER, 0);

        $year = 0;
        $title = '';


        if (!empty($matches) && !empty($matches['0']) && !empty($matches['0']['1'])) {
            $title = trim($matches['0']['1']);
        }

        if (!empty($matches) && !empty($matches['0']) && !empty($matches['0']['2'])) {
            $year_blob = trim($matches['0']['2']);

            $re_year = '/(\d+)/m';
            preg_match_all($re_year, $year_blob, $matches_year, PREG_SET_ORDER, 0);

            $year = !empty($matches_year) && !empty($matches_year['0']) && !empty($matches_year['0']['1']) ? $matches_year['0']['1'] : 0;
        }

        return [
            'title' => $title,
            'year' => $year
        ];
    }
}

if (!function_exists('extract_imdb_id')) {
    /**
     * Extracts imdb_id from string
     *
     * @param $title
     *
     * @return null|string
     */
    function extract_imdb_id($title)
    {
        preg_match_all('/tt[0-9]{7,15}+/m', $title, $matches, PREG_SET_ORDER, 0);

        if (isset($matches['0']) && isset($matches['0']['0'])) {
            return $matches['0']['0'];
        }

        return null;
    }
}

if (!function_exists('get_bin_file_contents')) {
    function get_bin_file_contents($filename)
    {
        $handle = fopen($filename, "rb");
        $contents = fread($handle, filesize($filename));
        fclose($handle);

        return $contents;
    }
}

if (!function_exists('array_first_item')) {
    function array_first_item(array $Array)
    {
        foreach ($Array as $key => $item) {
            return $item;
        }

        return null;
    }
}

if (!function_exists('yii2_ping_connection')) {
    function yii2_ping_connection()
    {
        \Yii::$app->getDb()->close();
        \Yii::$app->getDb()->open();
    }
}

if (!function_exists('http_get_contents')) {
    function http_get_contents($url)
    {
        $contents = '';

        try {
            $contents = file_get_contents($url);
        } catch (\Exception $e) {
            print($e->getMessage());
        }

        return $contents;
    }
}

if (!function_exists('parse_console_array')) {
    function parse_console_array(array $params)
    {
        $re = '/([a-zA-Z\-_0-9]+)=([a-zA-Z\-_0-9]+)/m';
        $parsed = [];

        foreach ($params as $param) {
            ;
            if (!is_string($param)) continue;
            preg_match_all($re, $param, $matches, PREG_SET_ORDER, 0);
            if (!empty($matches['0']['1']) && !empty($matches['0']['2'])) {
                $parsed[$matches['0']['1']] = $matches['0']['2'];
            }
        }


        return $parsed;
    }
}

/**
 * @param $premium_timestamp
 *
 * @return mixed
 */
function premium_left($premium_timestamp)
{
    $now = time();

    return max($premium_timestamp - $now, 0);
}

function isMagnetLink($link)
{

    $re = '/magnet:\?.+/m';

    $match = preg_match_all($re, $link, $matches, PREG_SET_ORDER, 0);

    if ($match) {
        return true;
    }

    return false;

}

function calc_percent($val1, $val2, int $decimals = 1)
{
    return number_format($val1 / $val2 * 100, $decimals);
}

/**
 * @return int|string
 */
function getMyId()
{
    if (empty(Yii::$app->user)) {
        return 0;
    }

    return Yii::$app->user->getId();
}

/**
 * @param string $view
 * @param array $params
 *
 * @return string
 */
function render($view, $params = [])
{
    return Yii::$app->controller->render($view, $params);
}

/**
 * @param $url
 * @param int $statusCode
 *
 * @return \yii\web\Response
 */
function redirect($url, $statusCode = 302)
{
    return Yii::$app->controller->redirect($url, $statusCode);
}

if (!function_exists('getLocaleByDisplayName')) {
    /**
     * @param $displayName
     * @param string $localeToSearch
     *
     * @return array
     */
    function getLocaleByDisplayName($displayName, $localeToSearch = 'en')
    {
        // get all available locales
        $allLocales = ResourceBundle::getLocales('');

        $foundLocales = [];
        foreach ($allLocales as $locale) {
            $currentName = Locale::getDisplayLanguage($locale, $localeToSearch);
            if (strncmp($currentName, $displayName, strlen($currentName)) === 0) {
                $foundLocales[] = $locale;
            }
        }
        return $foundLocales;
    }
}

/**
 * @param string $key
 * @param mixed $default
 *
 * @return mixed
 */
function env($key, $default = null)
{

    $value = getenv($key) ?? $_ENV[$key] ?? $_SERVER[$key];

    if ($value === false) {
        return $default;
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;

        case 'false':
        case '(false)':
            return false;
    }

    return $value;
}

function query_edge()
{

    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, env('API_QUERY_EDGE'));
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($handle);
    curl_close($handle);

    return json_decode($output);

}

if (!function_exists('removeLinksFromText')) {
    function removeLinksFromText($content)
    {
        // Create DOM from URL
        $simple_html_dom = new SimpleHtmlDom();
        $html = $simple_html_dom->str_get_html($content);
        $links = $html->find('a');

        // If html content have links
        if ($links) {
            foreach ($links as $link) {
                // Outertext show whole element with tags
                // With innertext we change whole element to text
                // which is a inner text of the element
                $link->outertext = $link->innertext();
            }

            $content = $html->save();
        }

        return $content;
    }
}


/**
 * Removes amazon filters from image url
 *
 * @param $image_url
 *
 * @return string|null
 *
 * @throws Exception
 */
function amazon_remove_filters($image_url)
{
    $re_remove_filters = '/((https)|(http)):\/\/([a-z\.\-]+\/.+\.).+\./m';
    $re_get_ext = '/\.([a-z]+)$/m';

    preg_match_all($re_remove_filters, $image_url, $url_without_filters_matched, PREG_SET_ORDER, 0);
    preg_match_all($re_get_ext, $image_url, $ext_match, PREG_SET_ORDER, 0);

    $ext = ArrayHelper::getValue($ext_match, '0.1', null);
    $base_url = ArrayHelper::getValue($url_without_filters_matched, '0.4', null);

    if (!empty($ext) && !empty($base_url)) {
        return 'https://' . $base_url . $ext;
    }

    return null;
}

function sanitize_imdb_id($ImdbId)
{
    return str_ireplace('tt', '', $ImdbId);
}

if (!function_exists('download_large_file')) {
    function download_large_file($source, $dest)
    {
        print('Downloading file `' . $source . '` TO `' . $dest . '`' . PHP_EOL);

        $options = array(
            CURLOPT_FILE => is_resource($dest) ? $dest : fopen($dest, 'w'),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_URL => $source,
            CURLOPT_FAILONERROR => true, // HTTP code > 400 will throw curl error
        );

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $return = curl_exec($ch);

        if ($return === false) {
            print('Error Downloading file.' . PHP_EOL);
            return curl_error($ch);
        } else {
            print('Finished file download.' . PHP_EOL);
            return true;
        }
    }
}


/**
 * @param array $episode_storage
 *
 * @return array
 */
function ExtractStoragePath(array $episode_storage)
{
    $path = [];
    foreach ($episode_storage as $key => $value) {
        $re = '/([a-zA-Z]+)\/(.+)\/([\-0-9a-zA-Z]+)\/([0-9]+p)\/(.+)/m';

        preg_match_all($re, $value, $matches, PREG_SET_ORDER, 0);

        if ($matches !== null && count($matches) > 0 && !in_array($matches['0']['1'] . '/' . $matches['0']['2'] . '/' . $matches['0']['3'], $path)) {
            $path[] = $matches['0']['1'] . '/' . $matches['0']['2'] . '/' . $matches['0']['3'];
        }
    }
    return $path;
}

/*
 * Checks
 * \common\models\queue\Movies update/insert event handler
 * @param $event
 */
function handle_update_movie_moderation_history($event)
{
    $movie = $event->sender;

    $movie->detachEvents();

    if ($movie->history_guid === null && $movie->history_guid === '') {
        return false;
    }

    $history_item = \common\models\MoviesModerationHistory::find()->where(['guid' => $movie->history_guid])->one();

    if ($history_item === null) {
        return false;
    }

    $history_item->setAttributes([
        'status' => $movie->is_downloaded
    ]);
    $history_item->save();

    if ((int)$movie->is_downloaded === 14 || (int)$movie->is_downloaded === 1) {
        $movie->history_guid = null;
        $movie->save();
    }

    return true;
}

/**
 * Event Handler When Updating
 * Or Inserting New Movie On Site
 *
 * @param $event
 */
function handle_update_site_movie($event)
{
    $movie = $event->sender;

    $movie->detachEvents();

    \backend\models\queue\Movies::updateAll(['flag_quality' => $movie->flag_quality], "id = {$movie->id_movie}");
}

/**
 * @param $event
 *
 * @return bool
 */
function handle_init_episode_moderation_history($event)
{
    $queue_episode = $event->sender;

    $queue_episode->detachEvents();


    if ($queue_episode::$ignore_history_init && ($queue_episode->history_guid === null || $queue_episode->history_guid === '')) {
        return false;
    }

    if ($queue_episode->history_guid !== null && $queue_episode->history_guid !== '') {
        $history_item = \common\models\EpisodesModerationHistory::find()->where(['guid' => $queue_episode->history_guid])->one();
    }

    if (!isset($history_item)) {
        $history_item = new \common\models\EpisodesModerationHistory();

        \common\models\EpisodesModerationHistory::updateAll(['is_deleted' => 1], 'is_deleted = 0 and id_meta = ' . $queue_episode->id_meta);
    }

    $history_item->setAttributes([
        'id_user' => getMyId(),
        'id_meta' => $queue_episode->id_meta,
        'title' => $queue_episode->show->title,
        'air_date' => $queue_episode->air_date,
        'season' => $queue_episode->season,
        'episode' => $queue_episode->episode,
        'imdb_id' => $queue_episode->show->imdb_id,
        'tvmaze_ip' => $queue_episode->show->tvmaze_id,
        'guid' => GUIDv4(),
        'worker_ip' => $queue_episode->worker_ip,
        'priority' => $queue_episode->priority,
        'status' => $queue_episode->state,
        'original_language' => $queue_episode->show->original_language,
        'type' => $queue_episode->type,
        'data' => $queue_episode->torrent_blob !== null && $queue_episode->torrent_blob !== '' ? json_encode([
            'torrent_blob' => base64_encode($queue_episode->torrent_blob),
            'torrent_title' => base64_encode($queue_episode->rel_title)
        ]) : null
    ]);

    if ($history_item->save()) {
        $queue_episode->history_guid = $history_item->guid;
        $queue_episode->save();
    }

    return true;
}

if (!function_exists('random_str')) {
    function random_str($length = 64, $keyspace = '1ab2cd4ef5ghij6kl7mno8pqrs9tuvwxy0z')
    {
        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces [] = $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }
}

/**
 * Event Handler When Creating
 * New Entry In MoviesModerationHistory
 *
 * @param $event
 *
 * @return bool
 */
function handle_init_movie_moderation_history($event)
{
    $movie = $event->sender;

    $movie->detachEvents();

    if ($movie->history_guid !== null && $movie->history_guid !== '') {
        $history_item = \common\models\MoviesModerationHistory::find()->where(['guid' => $movie->history_guid])->one();
    }

    if (!isset($history_item)) {
        $history_item = new \common\models\MoviesModerationHistory();

        \common\models\MoviesModerationHistory::updateAll(['is_deleted' => 1], 'is_deleted = 0 and id_movie = ' . $movie->id);
    }

    $history_item->setAttributes([
        'id_user' => getMyId(),
        'id_movie' => $movie->id,
        'title' => $movie->title,
        'year' => $movie->year,
        'imdb_id' => 'tt' . $movie->imdb_id,
        'guid' => GUIDv4(),
        'priority' => $movie->priority,
        'status' => $movie->is_downloaded,
        'original_language' => $movie->original_language,
        'type' => $movie->torrent_blob !== null && $movie->torrent_blob !== '' ? $movie->type : 2,
        'data' => $movie->torrent_blob !== null && $movie->torrent_blob !== '' ? json_encode([
            'torrentBlob' => base64_encode($movie->torrent_blob),
            'torrentTitle' => base64_encode($movie->rel_title)
        ]) : null
    ]);

    if ($history_item->save()) {
        $movie->history_guid = $history_item->guid;
        $movie->save();
    }

    return true;
}

function GUIDv4($trim = true)
{
    if (function_exists('com_create_guid') === true) {
        if ($trim === true)
            return trim(com_create_guid(), '{}');
        else
            return com_create_guid();
    }

    if (function_exists('openssl_random_pseudo_bytes') === true) {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    // Fallback (PHP 4.2+)
    mt_srand((double)microtime() * 10000);
    $charid = strtolower(md5(uniqid(rand(), true)));
    $hyphen = chr(45);                  // "-"
    $lbrace = $trim ? "" : chr(123);    // "{"
    $rbrace = $trim ? "" : chr(125);    // "}"
    $guidv4 = $lbrace .
        substr($charid, 0, 8) . $hyphen .
        substr($charid, 8, 4) . $hyphen .
        substr($charid, 12, 4) . $hyphen .
        substr($charid, 16, 4) . $hyphen .
        substr($charid, 20, 12) .
        $rbrace;
    return $guidv4;
}

if (!function_exists('slugify')) {
    /**
     * @param $string
     *
     * @return string
     */
    function slugify($string)
    {
        # special accents
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'Ð', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', '?', '?', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', '?', '?', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', '?', 'O', 'o', 'O', 'o', 'O', 'o', 'Œ', 'œ', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'Š', 'š', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Ÿ', 'Z', 'z', 'Z', 'z', 'Ž', 'ž', '?', 'ƒ', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', '?', '?', '?', '?', '?', '?');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');

        return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('', '-', ''), str_replace($a, $b, $string)));
    }
}

/**
 * Extracts magnet id from magnet link
 *
 * @param $magnet_link
 *
 * @return string|null
 */
function extract_magnet_id($magnet_link)
{
    $re = '/btih:([a-zA-Z0-9]+)/m';


    preg_match_all($re, $magnet_link, $matches, PREG_SET_ORDER, 0);


    if (isset($matches['0']) && isset($matches['0']['1'])) {
        return $matches['0']['1'];
    }

    return null;
}
