<?php

namespace console\models\imdb\dataset;

class ImdbNameBasicsDataset extends AbstractDataset
{
    protected $dataset_url = 'https://datasets.imdbws.com/name.basics.tsv.gz';

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
                'nconst'              => isset($parts['0']) ? $parts['0'] : null,
                'primaryName'         => isset($parts['1']) ? $parts['1'] : null,
                'birthYear'           => isset($parts['2']) ? $parts['2'] : null,
                'deathYear'           => isset($parts['3']) ? $parts['3'] : null,
                'primaryProfession'   => isset($parts['4']) ? $parts['4'] : null,
                'knownForTitles'      => isset($parts['5']) ? $parts['5'] : null,
            ];
        }

        return null;
    }
}
