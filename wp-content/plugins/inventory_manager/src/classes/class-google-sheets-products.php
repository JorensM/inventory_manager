<?php

    require_once 'class-google-sheets.php';

    class Google_Sheets_Products extends Google_Sheets {

        //private Google_Sheets $sheets_mgr;

        // function __construct() {
        //     super
        //     //$sheets_mgr = new Google_Sheets();
        // }

        function add_product( WC_Product $product = null ) {

            $values = array(
                array(
                    'hello',
                    '50',
                    '50'
                ),
                array(
                    'eee',
                    '20',
                    '20'
                )
            );

            return $this->append( $values );
        }

    }