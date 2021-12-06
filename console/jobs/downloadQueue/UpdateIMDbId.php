<?php


namespace console\jobs\downloadQueue;

use common\helpers\SimpleHtmlDom;
use console\models\queue\Shows;
use GuzzleHttp\Client;
use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class UpdateIMDbId extends BaseObject implements \yii\queue\JobInterface
{
    /**
     * Id Of TV Show we want to check
     * @var integer
     */
    public $id_tvshow;

    /**
     * Proxy Connection string
     * @var string
     */
    public $injectedProxy;

    public $var;

    /**
     * @var Client
     */
    private $httpClient;

    public function execute($queue)
    {
        $this->httpClient = $this->get_http_client();

        $show = Shows::find()->where(['id_tvshow' => $this->id_tvshow])->one();
        $year = $show->getYear();

        if (empty($show) || empty($year)) {
            return false;
        }

        $page = $this->guzzleGetContents('https://www.imdb.com/find?q=' . urlencode($show->title) . '&s=tt&ttype=tv&exact=true&ref_=fn_tt_ex');

        if (empty($page)) {
            return false;
        }

        $html = (new SimpleHtmlDom())->str_get_html($page);

        $ttSection = null;
        foreach ($html->find('.findSection') as $findSection) {
            if (count($findSection->find('h3 a[name=tt]')) > 0) {
                $ttSection = $findSection;
                break;
            }
        }
        if ($ttSection === null) {
            return false;
        }

        $matched = null;
        foreach ($ttSection->find('td.result_text') as $result_item) {
            $result_item->find('a', 0)->innertext = '';
            $href = $result_item->find('a', 0)->href;
            $plaintext = trim($result_item->plaintext);
            preg_match_all('/(\d{4})/m', $plaintext, $matches_year, PREG_SET_ORDER, 0);
            preg_match_all('/tt\d{4,12}/m', $href, $matches_title, PREG_SET_ORDER, 0);

            $imdb_year = ArrayHelper::getValue($matches_year, '0.0');
            $imdb_id = ArrayHelper::getValue($matches_title, '0.0');

            if (intval($year, 10) === intval($imdb_year, 10)) {
                if (empty($matched)) {
                    $matched = $imdb_id;
                } else {
                    // we found another tv with same name and year, no way to identify correctly
                    $matched = null;
                    break;
                }
            }
        }

        if (!empty($matched)) {
            $show->imdb_id = $matched;
            $show->save();

            Console::output('Update imdb_id for show: ' . $show->title . ' (' . $year . ')');
        }
    }

    private function get_http_client()
    {
        return new Client();
    }

    private function guzzleGetContents($url = null)
    {
        if (empty($url)) {
            return null;
        }

        try {
            $response = $this->httpClient->get($url);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), 'Unable to get page content: UpdateIMDbId()');
        }

        if (empty($response)) {
            return null;
        }

        return $response->getBody();
    }

}
