<?php

namespace console\models\imdb\dataset;

class ImdbCrewDataset extends AbstractDataset
{
    protected $dataset_url = 'https://datasets.imdbws.com/title.crew.tsv.gz';

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
                'directors'       => isset($parts['1']) ? $parts['1'] : null,
                'writers'         => isset($parts['2']) ? $parts['2'] : null,
            ];
        }

        return null;
    }
}
