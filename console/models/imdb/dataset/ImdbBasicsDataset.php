<?php

namespace console\models\imdb\dataset;

class ImdbBasicsDataset extends AbstractDataset
{
    protected $dataset_url = 'https://datasets.imdbws.com/title.basics.tsv.gz';

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
                'title_type'      => isset($parts['1']) ? $parts['1'] : null,
                'primary_title'   => isset($parts['2']) ? $parts['2'] : null,
                'original_title'  => isset($parts['3']) ? $parts['3'] : null,
                'is_adult'        => isset($parts['4']) ? $parts['4'] : null,
                'start_year'      => isset($parts['5']) ? $parts['5'] : null,
                'end_year'        => isset($parts['6']) ? $parts['6'] : null,
                'runtime_minutes' => isset($parts['7']) ? $parts['7'] : null,
                'genres'          => isset($parts['8']) ? $parts['8'] : null,
            ];
        }

        return null;
    }
}
