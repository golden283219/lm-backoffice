<?php


namespace common\models;


class ImdbCharts extends \Imdb\MdbBase
{
    public function getMostPopularChart()
    {
        $page = $this->getPage('moviemeter');
        $page = trim(preg_replace('/\s+/', ' ', $page));
        $offset = strpos($page, 'Most Popular Movies');
        $end = strpos($page, 'Our Most Popular charts use data');
        $res = array();
        while (true) {
            $matches = null;
            preg_match('#<td class="titleColumn">\s+<a\s+href="/title/tt(\d+).+?\s<span class="secondaryInfo">\((\d+)\)</span>#', $page, $matches, PREG_OFFSET_CAPTURE, $offset);
            if (!$matches || $offset > $end) {
                break;
            }

            if ($matches[2][0] == 2021 || $matches[2][0] == 2020) {
                $res[] = $matches[1][0];
            }

            $offset = $matches[0][1] + 1;
        }

        return $res;
    }

    protected function buildUrl($context = null)
    {
        return "https://" . $this->config->imdbsite . "/chart/$context";
    }
}
