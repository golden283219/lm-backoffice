<?php


namespace backend\modules\moderation\models;


class EpisodesModerationHistory extends \common\models\EpisodesModerationHistory
{

    const STATUS_DOWNLOADED = 1;
    const STATUS_WAITING_TORRENT_DOWNLOADER= 4;
    const STATUS_MISSING_DOWNLOAD_CANDIDATE = 5;
    const STATUS_BEING_CONVERTED = 3;
    const STATUS_WAITING_USENET_DOWNLOADER = 0;

    /**
     * Get Formatted Season and Episode
     * @return string
     */
    public function get_episode_season()
    {
        return strtr("S{s}E{e}", [
            '{s}' => $this->season < 10 ? '0'.$this->season : $this->season,
            '{e}' => $this->episode < 10 ? '0'.$this->episode : $this->episode
        ]);
    }

    public static function get_status_formatted_message($value)
    {
        switch ($value) {
            case 0:
                $value = '<span class="badge badge-secondary">WAITING(usenet)</span>';
                break;

            case 1:
                $value = '<span class="badge badge-success">Finished</span>';
                break;

            case 3:
                $value = '<span class="badge badge-dark">BEING CONVERTED</span>';
                break;

            case 4:
                $value = '<span class="badge badge-info">WAITING(torrent)</span>';
                break;

            case 5:
                $value = '<span class="badge badge-danger">Declined</span>';
                break;

            default:
                $value = '(not set)';
                break;
        }
        return $value;
    }

}