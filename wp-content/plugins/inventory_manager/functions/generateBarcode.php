<?php

    require_once(__DIR__."/../vendor/autoload.php");

    /**
     * Generate barcode
     */
    function generateBarcode($data, $filename, $text = "Hello"){
        $generator = new Picqer\Barcode\BarcodeGeneratorPNG();

        $scale = 3;
        $raw_data = $generator->getBarcode($data, $generator::TYPE_CODE_128, 2 * $scale, 30 * $scale);

        //$temp_file = tmpfile();
        //fwrite($temp_file, $raw_data);
        
        //$temp_file_path = stream_get_meta_data($temp_file)["uri"];

        //echo basename($temp_file_path);

        //$filetype = wp_check_filetype( basename( $filename ), null );
        $original_filename = $filename;
        $filename = wp_upload_dir()["basedir"] . "/" . $original_filename . ".png";

        //echo $filename;

        file_put_contents($filename, $raw_data);

        //$img = imagecreatefrompng($filename);

        // $img = imagecrop($img, [
        //     "x" => 0,
        //     "y" => 0,
        //     "height" => 25,
        //     "width" => 25
        // ]);

        // $output = imagecreatetruecolor(25, 25);
        // $white = imagecolorallocate($output,  255, 255, 255);

        $new_filename = wp_upload_dir()["basedir"] . "/" . $original_filename . ".png";

        $font_filename = wp_upload_dir()["basedir"] . "/Arial.TTF";

        
        $height = 50;

        $barcode_img = imagecreatefrompng($filename);
        //$black = imagecolorallocate($barcode_img, 0, 0, 0);
        //imagecolorallocate($barcode_img, 0, 200, 0);
        //imagealphablending($barcode_img, true);
        //imagesavealpha($barcode_img, true);
        //$barcode_img = imagescale($barcode_img, imagesx($barcode_img) * $scale_by, imagesy($barcode_img) * $scale_by);

        $barcode_width = imagesx($barcode_img);
        $barcode_height = imagesy($barcode_img);

        // $new_width = imagesx($barcode_img) * $scale_by;
        // $new_height = imagesy($barcode_img) * $scale_by;

        // $resized_barcode_img = imagecreatetruecolor(imagesx($barcode_img) * $scale_by, imagesy($barcode_img) * $scale_by);
        // imagecopyresampled($resized_barcode_img, $barcode_img, 0, 0, 0, 0, $new_width, $new_height, $barcode_width, $barcode_height);

        

        $width = imagesx($barcode_img);

        //Text image
        $text_img = imagecreate($width, 50);
        //BG color for text image
        imagecolorallocate($text_img, 255, 255, 255);
        //Text color for text image
        $text_color = imagecolorallocate($text_img, 0, 0, 0);

        //Bounding box of text
        $text_bounding_box = imagettfbbox(16, 0, $font_filename, $text);
        $text_width = $text_bounding_box[4];

        //Font size
        $font_size = 16;
        //$text_height = $text_bounding_box[7];

        //echo "img width = $width";
        //echo "text width = $text_width";

        $text_x = ($width - $text_width) / 2;
        $text_y = $height / 2 + $font_size / 2;
        
        imagettftext(
            $text_img,
            $font_size,
            0,
            $text_x,
            $text_y,
            $text_color,
            $font_filename,
            $text
        );

        //imagecopymerge($barcode_img, $text_img, 0, 0, 0, 0, $width, $height, 0);
        //imagecopy($barcode_img, $text_img, 0, -30, 0, 0, 50, 40);

        $final_img = imagecreate($width, $height + imagesy($barcode_img));

        imagecolorallocate($final_img, 255, 255, 255);

        imagecopy($final_img, $text_img, 0, 0, 0, 0, $width, $height);
        imagecopy($final_img, $barcode_img, 0, $height, 0, 0, $barcode_width, $barcode_height);

        

        //imagejpeg($text_img, $new_filename);
        imagepng($final_img, $new_filename);

        imagedestroy($text_img);
        imagedestroy($barcode_img);
        imagedestroy($final_img);
    }