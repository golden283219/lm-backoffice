<?php

namespace console\models\imdb\dataset;

class ImdbPrincipalsDataset extends AbstractDataset
{
    protected $dataset_url = 'https://datasets.imdbws.com/title.principals.tsv.gz';

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
                'tconst'         => isset($parts['0']) ? $parts['0'] : null,
                'ordering'       => isset($parts['1']) ? $parts['1'] : null,
                'nconst'         => isset($parts['2']) ? $parts['2'] : null,
                'category'       => isset($parts['3']) ? $parts['3'] : null,
                'job'            => isset($parts['4']) ? $parts['4'] : null,
                'characters'     => isset($parts['5']) ? $parts['5'] : null,
            ];
        }

        return null;
    }
}
