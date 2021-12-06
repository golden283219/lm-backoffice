<?php

namespace console\controllers;

use yii\console\Controller;
use yii\helpers\Console;
use common\models\Collection;
use common\models\CollectionData;
use common\models\Movies;
use common\helpers\SimpleHtmlDom;

class CollectionsController extends Controller
{
    const DOMAIN = 'https://www.imdb.com';

    private $current_collection_id;
    private $count = 0;
    private $count_no_movies = 0;

    // The command "yii collections/update" will call "actionUpdate()"
    public function actionCreate() {
        $collections = Collection::find()->all();

        if(!$collections){
            $this->stdout("Collections are empty!\n", Console::BOLD);
        }

        // Remove all collection data
        CollectionData::deleteAll();

        // Collections Loop
        foreach($collections as $collection){
            $this->stdout("\n" . $collection->title . "\n", Console::BOLD);

            $this->count = 0;
            $this->count_no_movies = 0;

            if($collection->url){
                $this->stdout($collection->url . "\n", Console::UNDERLINE);

                // Set current collection ID
                $this->current_collection_id = $collection->collection_id;

                // Get CSV String
                $main_url = explode('?', $collection->url)[0];
                $csv_path = $main_url."export?ref_=ttls_otexp";
                $csv = $this->url_exists($csv_path);

                // Check if not CSV 
                if(!$csv){
                    $this->setCollectionData($collection->url);
                } else {
                    // Parse a CSV string into an array
                    $csv_array = str_getcsv($csv, "\n");
                    array_shift($csv_array); # remove column header

                    //
                    if(count($csv_array)){
                        foreach($csv_array as $row){
                            $movie = str_getcsv($row, ",");
                            $imdb_id = $movie[1];

                            // Insert Collection Data
                            $this->insertCollectionData($imdb_id);
                        }
                    }
                }
            } else {
                $this->stdout("Collection URL is Empty\n", Console::FG_RED);
            }

            // Show result
            $this->stdout("Added: " . $this->count . "\n", Console::FG_GREEN);
            $this->stdout("Not found movies: " . $this->count_no_movies . "\n", Console::FG_RED);
        }

    }

    private function url_exists($url) {
        $result = true;
        $handle = curl_init($url);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

        /* Get the HTML or whatever is linked in $url. */
        $response = curl_exec($handle);

        /* Check for 404 (file not found). */
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if($httpCode == 404) {
            $result = false;
        }
        curl_close($handle);

        return ($result) ? $response : false;
    }

    private function insertCollectionData($imdb_id){
        $imdb_id = substr($imdb_id, 2); 
        $movie = Movies::findOne(['imdb_id' => $imdb_id]);

        // If movie exist
        if($movie){
            $collectionData = new CollectionData();
            $collectionData->collection_id = $this->current_collection_id;
            $collectionData->imdb_id = $imdb_id;
            $collectionData->movie_id = $movie->id_movie;
            $collectionData->insert();

            $this->count++;
        } else {
            // count not found movies
            $this->count_no_movies++;
        }
    }

    private function setCollectionData($url){
        // Create DOM from URL
        $simple_html_dom = new SimpleHtmlDom();
        $html = $simple_html_dom->file_get_html($url);

        // Start Movies Loop
        foreach($html->find('div.lister-item') as $movie) {
            // Check if movie exist in our database
            $imdb_id = $movie->find('div.ribbonize', 0)->attr['data-tconst'];

            // Insert Collection Data
            $this->insertCollectionData($imdb_id);
        }
        
        // next page
        if(is_object($html->find('a.next-page', 0))){
            $next_page_url = $html->find('a.next-page', 0)->attr['href']; 
            $simple_html_dom->clear($html);
            $this->setCollectionData(self::DOMAIN . $next_page_url);
        } else {
            $simple_html_dom->clear($html);
        }
    }
}
