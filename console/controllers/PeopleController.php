<?php

namespace console\controllers;

use yii\console\Controller;
use yii\helpers\Console;
use common\models\CastImdb;
use common\helpers\SimpleHtmlDom;
use common\components\imageStorage\ImageStorage;
use common\helpers\Tmdb;

class PeopleController extends Controller
{
    const DOMAIN = 'https://www.imdb.com';


    public function actionUpdateAvatar($imdb_id = null) {
		if(is_null($imdb_id)){
			$casts = CastImdb::find()
				->select(['id', 'imdb_actor_id', 'photo'])
				->all();
		} else {
			$casts = [CastImdb::findOne([
				'imdb_actor_id' => $imdb_id
			])];
		}

		$total = count($casts);
		$updated = 0;

		if(is_array($casts)){
			$imgStorage = new ImageStorage();

			foreach($casts as $cast){
				$photo = $this->getPersonAvatar($cast, $imgStorage);

				if($photo){
					$cast->photo = $photo;

					if($cast->save()){
						$updated++;
					}
				}
			}
		}

		$this->stdout("Total " . $total . PHP_EOL, Console::BOLD);
		$this->stdout("Updated " . $updated . PHP_EOL, Console::FG_GREEN);
	}

	private function getPersonAvatar($person, $imgStorage){
		$url = self::DOMAIN . '/name/nm' . $person->imdb_actor_id;

        // Create DOM from URL
        $simple_html_dom = new SimpleHtmlDom();
        $html = $simple_html_dom->file_get_html($url);

		$imageEl = $html->find('#img_primary .image > a', 0) ;

		// If image empty return empty string
		if(is_null($imageEl)) return false;

		// Get avatar link
		$imgPrimaryLink = $html->find('#img_primary .image > a', 0)->attr['href'];

		$avatar_link = self::DOMAIN . $imgPrimaryLink;
        $html = $simple_html_dom->file_get_html($avatar_link);

		$metaOgImage = $html->find('meta[property="og:image"]', 0)->attr['content'];

		$photoSrc = file_get_contents($metaOgImage);
		if (!empty($photoSrc)) {

			$uploadInfo = $imgStorage->handleFaceUpload($photoSrc);
			if ($uploadInfo['success'] == true && !empty($uploadInfo['path'])) {
				return '/' . $uploadInfo['path'];
			}
		}

		return false;
	}

	/*
	*
	* Update Actor Biography
	*
	*/
    public function actionUpdateBiography($imdb_id = null) {
		if(is_null($imdb_id)){
			$casts = CastImdb::find()
				->select(['id', 'imdb_actor_id', 'bio'])
				->all();
		} else {
			$casts = [CastImdb::findOne([
				'imdb_actor_id' => $imdb_id
			])];
		}

		$total = count($casts);
		$updated = 0;

		if(is_array($casts)){
			foreach($casts as $cast){
				# Find out about the director
				$person = new \Imdb\Person($cast->imdb_actor_id);
				$bio = $person->bio();

				if(!empty($bio[0]['desc'])){
					$dbActorBio = $this->removeTextFromLinks($bio[0]['desc']);
					$cast->bio = $dbActorBio;

					if($cast->save()){
						$updated++;
					}
				}
			}
		}

		$this->stdout("Total " . $total . PHP_EOL, Console::BOLD);
		$this->stdout("Updated " . $updated . PHP_EOL, Console::FG_GREEN);
	}

	private function removeTextFromLinks($content){
		// Create DOM from URL
		$simple_html_dom = new SimpleHtmlDom();
		$html = $simple_html_dom->str_get_html($content);
		$links = $html->find('a');

		// If html content have links
		if($links){
			foreach($links as $link){
				// Outertext show whole element with tags
				// With innertext we change whole element to text
				// which is a inner text of the element
				$link->outertext = $link->innertext();
			}

			$content = $html->save();
		}

		return $content;
	}

	/*
	*
	* Update Actor Name
	*
	*/
    public function actionUpdateName($imdb_id = null) {
		if(is_null($imdb_id)){
			$casts = CastImdb::find()
				->select(['id', 'imdb_actor_id', 'birth_name'])
				->all();
		} else {
			$casts = [CastImdb::findOne([
				'imdb_actor_id' => $imdb_id
			])];
		}

		$total = count($casts);
		$updated = 0;

		if(is_array($casts)){
			foreach($casts as $cast){
				# Find out about the person
				$person = new \Imdb\Person($cast->imdb_actor_id);

				// Get Names
				$cast->birth_name = $person->birthname();
				$cast->full_name  = $person->name();

				$nick_name = $person->nickname();
				if(!empty($nick_name)){
					$cast->nick_name  = $nick_name[0];
				}

                yii2_ping_connection();

				if($cast->save()){
					$updated++;
				}
			}
		}

		$this->stdout("Total " . $total . PHP_EOL, Console::BOLD);
		$this->stdout("Updated " . $updated . PHP_EOL, Console::FG_GREEN);
	}


    public function actionUpdateWithTmdb($imdb_id = null) {
		if(is_null($imdb_id)){
			$casts = CastImdb::find()
				->select(['id', 'imdb_actor_id'])
				->all();
		} else {
			$casts = [CastImdb::findOne([
				'imdb_actor_id' => $imdb_id
			])];
		}

		$total = count($casts);
		$updated = 0;

		if(is_array($casts)){
			foreach($casts as $cast){
				// Get data from TMDB API
				$cast = $this->setTmdbData($cast);

				if($cast->save()){
					$updated++;
				}
			}
		}

		$this->stdout("Total " . $total . PHP_EOL, Console::BOLD);
		$this->stdout("Updated " . $updated . PHP_EOL, Console::FG_GREEN);
	}

	private function setTmdbData($dbActorsData){
		// Get person data from tmdb
		$tmdb_person = Tmdb::getPersonDataByImdbId($dbActorsData->imdb_actor_id, ['external_ids', 'combined_credits']);

		$dbActorsData->known_for_department = $tmdb_person['known_for_department'];
		$dbActorsData->popularity = $tmdb_person['popularity'];

		// Set gender
		if($tmdb_person['gender']){
			$genders = ['Not specified', 'Female', 'Male', 'Non-Binary'];
			$dbActorsData->gender = $genders[$tmdb_person['gender']];
		}

		if(!empty($tmdb_person['also_known_as'])){
			$also_known_as = '';
			foreach($tmdb_person['also_known_as'] as $name){
				$also_known_as .= $name . ',';
			}

			$dbActorsData->also_known_as = trim($also_known_as, ',');
		}

		$dbActorsData->deathday = $tmdb_person['deathday'];
		$dbActorsData->homepage = $tmdb_person['homepage'];

		// Set External Ids
		$dbActorsData->facebook = $tmdb_person['external_ids']['facebook_id'];
		$dbActorsData->instagram = $tmdb_person['external_ids']['instagram_id'];
		$dbActorsData->twitter = $tmdb_person['external_ids']['twitter_id'];

		$cast_credits = 0;
		if(is_array($tmdb_person['combined_credits']['cast'])){
			$cast_credits = count($tmdb_person['combined_credits']['cast']);
		}

		$crew_credits = 0;
		if(is_array($tmdb_person['combined_credits']['crew'])){
			$crew_credits = count($tmdb_person['combined_credits']['crew']);
		}

		// Count credits
		$credits = $cast_credits + $crew_credits;

		$dbActorsData->known_credits = $credits;

		return $dbActorsData;
	}

}
