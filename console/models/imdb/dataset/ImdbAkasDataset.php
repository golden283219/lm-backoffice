<?php

namespace console\models\imdb\dataset;

class ImdbAkasDataset extends AbstractDataset
{
    protected $dataset_url = 'https://datasets.imdbws.com/title.akas.tsv.gz';

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
                'titleId'         => isset($parts['0']) ? $parts['0'] : null,
                'ordering'        => isset($parts['1']) ? $parts['1'] : null,
                'title'           => isset($parts['2']) ? $parts['2'] : null,
                'region'          => isset($parts['3']) ? $parts['3'] : null,
                'language'        => isset($parts['4']) ? $parts['4'] : null,
                'types'           => isset($parts['5']) ? $parts['5'] : [],
                'attributes'      => isset($parts['6']) ? $parts['6'] : [],
                'isOriginalTitle' => isset($parts['7']) ? $parts['7'] : 0,
            ];
        }

        return null;
    }
}
