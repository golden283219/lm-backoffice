<?php


namespace console\models\imdb\dataset;

abstract class AbstractDataset
{

    /**
     * file path
     * @var string
     */
    protected $f_path;

    protected $sql_file_handler;
    /**
     * @var string dataset file handler
     */
    protected $f_handler;

    /**
     * @var string temp dir path
     */
    protected $temp_dir = '/tmp';

    /**
     * @var string data set url
     */
    protected $dataset_url;

    /**
     * Can specify tsv file path to use instead of downloading
     * AbstractDataset constructor.
     * @param null $tsv_file_path
     */
    public function __construct($tsv_file_path = null)
    {
        if (empty($tsv_file_path)) {
            $file_dest = $this->temp_dir . '/lm-dash-imdb-dataset-' . GUIDv4() . '.gz';

            download_large_file($this->dataset_url, $file_dest);

            $this->f_path = $this->ungzip($file_dest, true);
        } else {
            $this->f_path = $tsv_file_path;
        }


        $this->f_handler = fopen($this->f_path, 'rb');

        // go to next line and skip head of dataset
        fgets($this->f_handler);
    }

    private function ungzip($source, $force_remove = false)
    {
        // Raising this value may increase performance
        $buffer_size = 12000;
        $out_file_name = str_replace('.gz', '', $source);

        // Open our files (in binary mode)
        $file = gzopen($source, 'rb');
        $out_file = fopen($out_file_name, 'wb');

        // Keep repeating until the end of the input file
        while (!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            fwrite($out_file, gzread($file, $buffer_size));
        }

        // Files are done, close files
        fclose($out_file);
        gzclose($file);

        if ($force_remove) unlink($source);

        return $out_file_name;
    }

    public function flushFiles()
    {
        if (fclose($this->f_handler) && unlink($this->f_path))
            return true;
        return false;
    }
}
