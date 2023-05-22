<?php

    require_once 'class-google-sheets.php';

    class Google_Sheets_Products extends Google_Sheets {

        /**
         * Google Sheets integration for WC products
         */

        /**
         * @var $column_map map of column ids to their indexes. Defined in the constructor
         */
        private $column_map;

        /**
         * Constructor. Calls parent constructor and defines constants such as $column_map
         */
        function __construct() {
            parent::__construct();
            
            $this->column_map = array (
                'title' => 0,
                'regular_price' => 1,
                'sale_price' => 2,
                'id' => 3
            );

        }

        //--Misc methods--//

        /**
         * Get row number according to a specified product id
         * 
         * @param $product_id id of product to find
         * 
         * @return int row number where specified product id was first found, or -1 if not found
         */
        function get_row_number_by_product_id ( $product_id ) {

            //Get the column letter where IDs are stored
            $id_column = $this->index_to_column_letter( $this->column_map['id'] + 1 );

            //Get row number where ID was first found (or -1 if ID was not found)
            $row_number = $this->get_row_number_by_column_value( $id_column, $product_id );

            //Return the row number
            return $row_number;

        }

        /**
         * Convert WC_Product to row data accepted by methods such as write()
         * 
         * @param WC_Product $product product to convert
         * 
         * @return any[] row data
         */
        function product_to_row( WC_Product $product ) {
            $column_map = $this->column_map;

            $row = array();

            $row[ $column_map['title'] ] = $product->get_title();
            $row[ $column_map['regular_price'] ] = $product->get_regular_price();
            $row[ $column_map['sale_price'] ] = $product->get_sale_price();
            $row[ $column_map['id'] ] = $product->get_id();

            return $row;
        }

        /**
         * Get the rightmost column letter
         * 
         * @return string rightmost column letter
         */
        function get_max_column() {
            //return the rightmost column letter
            return $this->index_to_column_letter( max( $this->column_map ) + 1 );
        }

        //--Main methods--//

        /**
         * Append product data to end of current sheet
         * 
         * @param WC_Product $product product to write
         * 
         * @return any[]|null API response or null on error
         */
        function add_product( WC_Product $product ) {

            //Convert WC_Product to row data
            $row = $this->product_to_row( $product );

            //$column_map = $this->column_map;

            //log_activity('values', '<pre>' . print_r($row, true) . '</pre>');

            //Use the parent class's append() method to append a new product to the sheet. Then return the response
            return $this->append( array( $row ) );
        }

        /**
         * Update product in current sheet
         * 
         * @param WC_Product $product product to update
         * 
         * @return int|null row number of updated product, or null on error
         */
        function update_product( WC_Product $product ) {
            //Get ID of product
            $product_id = $product->get_id();

            //Get row number of product in the sheet
            $row_number = $this->get_row_number_by_product_id( $product_id );

            //Check if row with matching product ID was found
            if( $row_number > -1 ) {
                //If yes

                //Get rightmost column letter
                $max_column = $this->get_max_column();

                //Set range to the product's row
                $range = "A$row_number:$max_column$row_number";
                //log_activity('updating range', $range);

                //Convert WC_Product to row data
                $row = $this->product_to_row( $product );

                //Write to the products row the new data
                $this->write($range, array( $row ) );

                //Return the row number of the product
                return $row_number;
            }

            //If row with matching product ID was not found, return null
            return null;
        }

        /**
         * Update product in current sheet, or append one if it's not in the sheet
         * 
         * @param WC_Product $product product to update/add
         * 
         * @return void
         */
        function update_or_add_product( WC_Product $product ) {
            //Check whether product already exists in the sheet by calling $this->update_product() 
            //(it returns null if product was not found in sheet)
            //If it already exists in the sheet, then the row gets updated because we called update_product()
            $exists = $this->update_product( $product );
            if( ! $exists ) {
                //If product was not found, add it
                $this->add_product( $product );
            }
        }

        

        

    }


//--Initialize a Google_Sheets_Products instance--//
$spreadsheet_id = '10j-z9e95OxpKApmGaJON_V7WrUEag02UuKwL3K1G15o';
$sheet_name = 'Sheet1';
$service_account_email = 'test-541@inventory-manager-386314.iam.gserviceaccount.com';

$google_sheets = new Google_Sheets_Products();

$google_sheets->set_spreadsheet( $spreadsheet_id );
$google_sheets->set_sheet( $sheet_name );
$google_sheets->set_service_account( $service_account_email );