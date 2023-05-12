<?php

    require_once 'class-google-sheets.php';

    class Google_Sheets_Products extends Google_Sheets {

        private $column_map;

        function __construct() {
            parent::__construct();
            
            $this->column_map = array (
                'title' => 0,
                'regular_price' => 1,
                'sale_price' => 2,
                'id' => 3
            );

        }

        function add_product( WC_Product $product ) {

            $row = array();

            $column_map = $this->column_map;

            $row[ $column_map['title'] ] = $product->get_title();
            $row[ $column_map['regular_price'] ] = $product->get_regular_price();
            $row[ $column_map['sale_price'] ] = $product->get_sale_price();
            $row[ $column_map['id'] ] = $product->get_id();

            log_activity('values', '<pre>' . print_r($row, true) . '</pre>');

            return $this->append( array( $row ) );
        }

    }

//$google_api_key = 'AIzaSyDE6vhvjcgwNYZUnN8kSVJ_DJwWm8qRDb4';
$spreadsheet_id = '10j-z9e95OxpKApmGaJON_V7WrUEag02UuKwL3K1G15o';
$sheet_name = 'Sheet1';

$google_sheets = new Google_Sheets_Products();

$google_sheets->set_spreadsheet( $spreadsheet_id );
$google_sheets->set_sheet( $sheet_name );