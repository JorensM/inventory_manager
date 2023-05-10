<?php

    require_once __DIR__ . '/../../vendor/autoload.php';

    /*
        Get the center position of text relative to a container box
    */
    function get_center_with_text($text, $font_size, $font_filename, $box_x, $box_y, $box_width, $box_height) {
        $text_bounding_box = imagettfbbox($font_size, 0, $font_filename, $text);

        $text_width = $text_bounding_box[4];

        $text_x = $box_x + ($box_width / 2) - ($text_width / 2);
        $text_y = $box_y + $box_height / 2 + $font_size / 2;

        return array (
            'x' => $text_x,
            'y' => $text_y
        );
    }

    function get_text_width($text, $font_size, $font_filename) {
        $text_bounding_box = imagettfbbox($font_size, 0, $font_filename, $text);
        return $text_bounding_box[4];
    }

    //Create an image object with given text list
    function image_text_list($texts, $font_filename) {
        $font_size = 18;
        $v_padding = 8;
        //calculate total height of image
        $img_height = count($texts) * ($font_size + $v_padding * 2);

        $text_widths = [];
        //Calculate width of image
        foreach ( $texts as $text ) {
            array_push($text_widths, get_text_width( $text, $font_size, $font_filename ) );
        }

        $img_width = max($text_widths);

        $img = imagecreate($img_width, $img_height);
        //BG color of image
        imagecolorallocate($img, 255, 255, 255);

        $text_color = imagecolorallocate($img, 0, 0, 0);

        //Add texts to image
        foreach ( $texts as $index => $text ) {
            imagettftext(
                $img,
                $font_size,
                0,
                0,
                $index * ($v_padding * 2 + $font_size) + $font_size,
                $text_color,
                $font_filename,
                $text
            );
        }        

        return $img;
    }

    /**
     * Generate barcode
     */
    function generate_barcode($data, $filename, $title ,$discount_price ,$regular_price ,$location, $serial_number ){
        try{
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
            //$text_bounding_box = imagettfbbox(16, 0, $font_filename, $text);
            //$text_width = $text_bounding_box[4];

            //Font size
            $font_size = 16;
            $large_font_size = 32;
            //$text_height = $text_bounding_box[7];

            //echo "img width = $width";
            //echo "text width = $text_width";

            //$text_x = ($width - $text_width) / 2;
            //$text_y = $height / 2 + $font_size / 2;
            
            // imagettftext(
            //     $text_img,
            //     $font_size,
            //     0,
            //     $text_x,
            //     $text_y,
            //     $text_color,
            //     $font_filename,
            //     $text
            // );

            //Discount price image
            $discount_price_text = $discount_price ? $discount_price . "$" : '';
            //Generate discount price text box
            $discount_price_text_box = imagettfbbox($large_font_size + 8, 0, $font_filename, $discount_price_text);
            $discount_price_img_width = $discount_price_text_box[2] + 160;
            //Get position of discount price text
            $discount_price_text_position = get_center_with_text($discount_price_text, $large_font_size,  $font_filename, 0, 0, $discount_price_img_width, $barcode_height);
            $discount_price_img = imagecreate($discount_price_img_width, $barcode_height);
            //Set BG color for discount price img to white
            imagecolorallocate($discount_price_img, 255, 255, 255);
            //Get text color
            $discount_text_color = imagecolorallocate($discount_price_img, 0, 0, 0);

            imagettftext(
                $discount_price_img,
                $large_font_size,
                0,
                $discount_price_text_position['x'],
                $discount_price_text_position['y'],
                $discount_text_color,
                $font_filename,
                $discount_price_text
            );

            //imagecopymerge($barcode_img, $text_img, 0, 0, 0, 0, $width, $height, 0);
            //imagecopy($barcode_img, $text_img, 0, -30, 0, 0, 50, 40);

            $title_text_width = get_text_width($title, $font_size, $font_filename);

            $title_text_size = 30;//$font_size;
            $title_img_width = 300;//$title_text_width + 16;
            $title_img_height = $title_text_size + 16;
            $title_img = imagecreate($title_img_width, $title_img_height);
            //Set BG color for title img to white
            imagecolorallocate($title_img, 200, 200, 200);
            //Get text color
            $title_img_color = imagecolorallocate($title_img, 0, 0, 0);

            $title_text_position = get_center_with_text($title, $font_size, $font_filename, 0, 0, $title_img_width, $title_img_height);

            imagettftext(
                $title_img, 
                $title_text_size,
                0,
                0,//$title_text_position['x'],
                0,//$title_text_position['y'],
                $title_img_color,
                $font_filename,
                "test"
            );

            $regular_price_text = $regular_price ? $regular_price . '$' : '';
                
            $bottom_img = image_text_list( array(
                $title,
                "REGULAR -" . $regular_price_text,
                "LOCATION -" . $location,
                "SERIAL # -" . $serial_number
            ), $font_filename );

            //Width of the top part of the image (barcode and discounted price)
            $top_width = $discount_price_img_width + $barcode_width;

            //Width and height of the bottom part of the image (everything below barcode and discounted price)
            $bottom_width = imagesx($bottom_img);
            $bottom_height = imagesy($bottom_img);

            //Final image width
            $final_img_width = max($bottom_width, $top_width);

            $final_img = imagecreate($final_img_width, $barcode_height + $bottom_height);

            imagecolorallocate($final_img, 255, 255, 255);

            imagecopy($final_img, $discount_price_img, 0, 0, 0, 0, $discount_price_img_width, $barcode_height);
            //imagecopy($final_img, $text_img, 0, 0, 0, 0, $width, $height);
            imagecopy($final_img, $barcode_img, $discount_price_img_width, 0, 0, 0, $barcode_width, $barcode_height);
            imagecopy($final_img, $bottom_img, 0, $barcode_height, 0, 0, $bottom_width, $bottom_height);
            //imagecopy($final_img, $title_img, 0, $barcode_height, 0, 0, $title_img_width, $title_img_height);

            //imagejpeg($text_img, $new_filename);
            imagepng($final_img, $new_filename);

            imagedestroy($text_img);
            imagedestroy($barcode_img);
            imagedestroy($final_img);
        }
        catch(Exception $e) {
            log_activity("error: ", $e->getMessage());
        }
    }