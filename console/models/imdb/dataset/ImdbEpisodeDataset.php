<?php

namespace console\models\imdb\dataset;

class ImdbEpisodeDataset extends AbstractDataset
{
    protected $dataset_url = 'https://datasets.imdbws.com/title.episode.tsv.gz';

    public function read_line()
    {
        while (($f_line = fgets($this->f_handler))) {
            $parsed = $this->parse_line($f_line);

            yield $parsed;
        }
    }

    private function parse_line($line)
    {
        if (!empty($line)) {
            $parts = explode("\t", $line);

            return [
                'tconst'          => isset($parts['0']) ? $parts['0'] : null,
                'parentTconst'    => isset($parts['1']) ? $parts['1'] : null,
                'seasonNumber'    => isset($parts['2']) ? $parts['2'] : null,
                'episodeNumber'   => isset($parts['3']) ? $parts['3'] : null,
            ];
        }

        return null;
    }
}
