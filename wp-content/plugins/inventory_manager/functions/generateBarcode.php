<?php

    require_once(__DIR__."/../vendor/autoload.php");

    /**
     * Generate barcode
     */
    function generateBarcode($data, $filename){
        $generator = new Picqer\Barcode\BarcodeGeneratorPNG();

        $raw_data = $generator->getBarcode($data, $generator::TYPE_CODE_128);

        //$temp_file = tmpfile();
        //fwrite($temp_file, $raw_data);
        
        //$temp_file_path = stream_get_meta_data($temp_file)["uri"];

        //echo basename($temp_file_path);

        //$filetype = wp_check_filetype( basename( $filename ), null );
        $filename = wp_upload_dir()["basedir"] . "/" . $filename . ".png";

        //echo $filename;

        file_put_contents($filename, $raw_data);
    }