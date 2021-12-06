<?php


namespace console\jobs\metadata;


use common\components\imageStorage\ImageStorage;
use common\helpers\Tmdb;
use common\libs\Imdb\Person;
use common\models\CastImdb;
use common\models\queue\KnownForMovies;
use common\models\queue\KnownForShows;
use Exception;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class CastImdbMetadataJob extends AbstractMetaData
{
    public $castImdbId;

    /**
     * @var Person
     */
    private $imdbPerson;

    /**
     * @var ImageStorage
     */
    protected $ImageStorage;

    public function execute($queue)
    {
        $this->ImageStorage = new ImageStorage();
        $this->imdbPerson = self::getPerson('nm' . $this->castImdbId);

        try {
            $dbCast = CastImdb::find()->where(['imdb_actor_id' => $this->castImdbId])->one();

            $imdbPersonData = $this->collectIMDbPersonData();
            $tmdbPersonData = $this->collectTMDbPersonData();
        } catch (Exception $e) {
        }


        if (empty($dbCast)) {
            Console::output('Can\'t Find cast: "nm' . $this->castImdbId . '" on database.');
            return false;
        }

        foreach ($imdbPersonData as $key => $value) {
            if (in_array($key, $dbCast->attributes()) && !empty($value)) {
                $dbCast->{$key} = $value;
            }

        }

        foreach ($tmdbPersonData as $key => $value) {
            if (in_array($key, $dbCast->attributes()) && !empty($value)) {
                $dbCast->{$key} = $value;
            }
        }
        if ($dbCast->validate() && $dbCast->save()) {
        // unable to get normally known jobs for now
//            $this->updateKnownForJobs($dbCast, $tmdbPersonData['id']);
            return true;
        } else {
            Yii::error(json_encode($dbCast->errors, JSON_PRETTY_PRINT), 'CastJob: Cast Metadata Save Error');
        }

        return false;
    }

    private function collectTMDbPersonData()
    {
        $genders = ['Not specified', 'Female', 'Male', 'Non-Binary'];
        $personData = [];

        // Get person data from tmdb
        try {
            $tmdb_person = Tmdb::getPersonDataByImdbId('nm' . $this->castImdbId, ['external_ids', 'combined_credits']);
        } catch (Exception $e) {}

        if (empty($tmdb_person)) {
            return $personData;
        }

        $personData['tmdb_id'] = ArrayHelper::getValue($tmdb_person, 'id');
        $personData['known_for_department'] = ArrayHelper::getValue($tmdb_person, 'known_for_department', 'Other');
        $personData['popularity'] = ArrayHelper::getValue($tmdb_person, 'popularity');
        $personData['gender'] = $genders[ArrayHelper::getValue($tmdb_person, 'gender', 0)];
        $personData['also_known_as'] = implode(', ', ArrayHelper::getValue($tmdb_person, 'also_known_as', []));

        // socials
        $personData['facebook'] = ArrayHelper::getValue($tmdb_person, 'external_ids.facebook_id');
        $personData['instagram'] = ArrayHelper::getValue($tmdb_person, 'external_ids.instagram_id');
        $personData['twitter'] = ArrayHelper::getValue($tmdb_person, 'external_ids.twitter_id');

        // count known credits
        $cast_credits = count(ArrayHelper::getValue($tmdb_person, 'combined_credits.cast', []));
        $crew_credits = count(ArrayHelper::getValue($tmdb_person, 'combined_credits.crew', []));
        $personData['known_credits'] = $cast_credits + $crew_credits;

        $personData['deathday'] = ArrayHelper::getValue($tmdb_person, 'deathday');
        $personData['homepage'] = ArrayHelper::getValue($tmdb_person, 'homepage');

        $profile_path = ArrayHelper::getValue($tmdb_person, 'profile_path', null);
        if (!empty($profile_path)) {
            try {
                $contents = http_get_contents('https://image.tmdb.org/t/p/original' . $profile_path);
                $response = $this->ImageStorage->handleFaceUpload($contents);

                if ($response['success'] == true && !empty($response['path'])) {
                    $personData['photo'] = '/' . $response['path'];
                }
            } catch (Exception $e) {
            }
        }


        return $personData;
    }

    /**
     * @throws Exception
     */
    private function collectIMDbPersonData()
    {
        $personData = [];

        $picture = $this->imdbPerson->photo(false);
        if (!empty($picture)) {
            try {
                $contents = http_get_contents(amazon_remove_filters($picture));
                $response = $this->ImageStorage->handleFaceUpload($contents);

                if ($response['success'] == true && !empty($response['path'])) {
                    $personData['photo'] = '/' . $response['path'];
                }
            } catch (Exception $e) {
            }
        }

        $bio = ArrayHelper::getValue($this->imdbPerson->bio(), '0.desc');
        if (!empty($bio)) {
            $dbActorBio = removeLinksFromText($bio);
            $personData['bio'] = $dbActorBio;
        }

        $personData['birth_name'] = $this->imdbPerson->birthname();
        $personData['nick_name'] = implode(', ', $this->imdbPerson->nickname());

        $born = $this->imdbPerson->born();
        $personData['birth_place'] = ArrayHelper::getValue($born, 'place');

        $y = ArrayHelper::getValue($born, 'year');
        $m = ArrayHelper::getValue($born, 'mon');
        $d = ArrayHelper::getValue($born, 'day');
        if (!empty($y) && !empty($m) && !empty($d)) {
            $personData['birth_date'] = "$y-$m-$d";
        }

        return $personData;
    }

    /**
     * Updates Cast Known For Jobs
     *
     * @param $cast
     *
     * @param $tmdb_id
     *
     * @return bool
     */
    private function updateKnownForJobs($cast, $tmdb_id)
    {
        if (empty($cast)) {
            return false;
        }

        // Delete old data from actor knows for shows and movies
        KnownForMovies::deleteAll([
            'id_cast' => $cast->id
        ]);

        KnownForShows::deleteAll([
            'id_cast' => $cast->id
        ]);

        // Find the people with imdb_id
        $person = Tmdb::getPersonById($tmdb_id, ['known_for_']);

        // person result is array and should contain one person only
        foreach ($find['person_results'] as $person) {
            // Getting person known for movies and shows
            foreach ($person['known_for'] as $item) {
                $result = $this->savePeopleKnownFor($cast->id, $item['id'], $item['media_type']);

                if ($result) {
                    $cast->knows_for_job = 1;
                    $cast->save();
                }
            }
        }
    }

    /**
     * Get person imdb id
     *
     * @param $id
     *
     * @return Person
     */
    private static function getPerson($id)
    {
        return new Person($id);
    }


}
