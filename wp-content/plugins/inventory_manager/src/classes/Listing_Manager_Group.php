<?php

    //--Requires--//

    //Classes
    require_once("Reverb_Listing_Manager.php");
    require_once("Ebay_Listing_Manager.php");

    class Listing_Manager_Group{

        /**
         * Listing Manager Group singleton class. Used to use multiple listing managers at the same time.
         */

        /**
         * @var array $all_managers all managers stored in the object
         */
        private array $all_managers;

        /**
         * @var array $selected_managers currently active managers (inactive managers won't be used)
         */
        private array $selected_managers;

        /**
         * Constructor. Used to define managers
         * 
         * @param Listing_Manager_Interface[] $managers managers to use
         * Example:
         *  [
         *      ['manager_id'] => new ReverbListingManager(),
         *      ['manager_id_2'] => new EbayListingManager()
         *  ]
         * 
         * @return void
         */
        function __construct(array $managers){
            //Define private vars
            $this->all_managers = $managers;
            $this->selected_managers = $this->all_managers;
        }

        /**
         * Retrive manager by ID
         * 
         * @param string $manager_id ID of manager to return
         * 
         * @return Listing_Manager_Interface|null listing manager, or NULL if not found
         */
        function getManager(string $manager_id){
            //Check if manager with given ID exists
            if(isset($this->all_managers[$manager_id])){
                //If yes, return it
                return $this->all_managers[$manager_id];
            }

            //Otherwise, return null
            return null;
        }

        /**
         * Set all managers to active
         * 
         * @return void
         */
        function includeAllManagers(){
            $this->selected_managers = $this->all_managers;
        }

        /**
         * Only use specified managers
         * 
         * @param $manager_ids ids of managers to use
         * 
         * @return void
         */
        function includeManagers(array $manager_ids){
            $selected_managers = array_filter($this->all_managers, function($current_manager_id) use($manager_ids){
                return array_key_exists($manager_ids, $current_manager_id);
            }, ARRAY_FILTER_USE_KEY);
        }

        /**
         * Exclude specified managers from use
         * 
         * @param $manager_ids = ids of managers to exclude
         * 
         * @return void
         */
        function excludeManagers(array $manager_ids){
            $selected_managers = array_filter($this->all_managers, function($current_manager_id) use($manager_ids){
                return !array_key_exists($manager_ids, $current_manager_id);
            }, ARRAY_FILTER_USE_KEY);
        }
        
        /*
            The following functions simply call the corresponding functions of
            every active manager. To see docs for these functions, see Listing_Manager_Interface
            or a concrete manager class such as Reverb_Listing_Manager

            BEGIN SECTION
        */
        function createListing(WC_Product $product){
            foreach($this->selected_managers as $manager){
                $manager->createListing($product);
            }
        }

        function updateListing(WC_Product $product){
            foreach($this->selected_managers as $manager){
                $manager->updateListing($product);
            }
        }

        function update_or_create_listing(WC_Product $product){
            $responses = [];
            foreach($this->selected_managers as $manager_id => $manager){
                $response = $manager->update_or_create_listing($product);
                $responses[$manager_id] = $response;
            }

            return $responses;
        }
        
        function deleteListing(WC_Product $product){
            foreach($this->selected_managers as $manager){
                $manager->deleteListing($product);
            }
        }


        function end_listing(WC_Product $product){
            // foreach($this->selected_managers as $manager){
            //     $manager->end_or_delete_listing($product);
            // }
            return $this->call_manager_fns("end_listing", $product);
        }

        function end_or_delete_listing(WC_Product $product){
            foreach($this->selected_managers as $manager){
                $manager->end_or_delete_listing($product);
            }
        }

        function check_listing_and_mark_sold(WC_Product $product, bool $save_product = true){
            $responses = $this->call_manager_fns("check_listing_and_mark_sold", $product, false);
            if($save_product){
                $product->save();
            }
            return $responses;
        }

        /*
            END SECTION
        */

        /**
         * Calls specified method of all selected managers
         * 
         * @param string $fn_name method to call
         * @param mixed $args,... Arguments to pass to the methods
         */
        function call_manager_fns(string $fn_name, ...$args){
            $responses = [];
            foreach($this->selected_managers as $manager){
                $response = $manager->{$fn_name}(...$args);
                array_push($responses, $response);
            }
            return $responses;
        }
    }

    /*
        Create singleton instance and register managers
    */  

    //Tokens
    $REVERB_TOKEN = get_option("reverb_token");
    $EBAY_TOKEN = get_option("ebay_token");

    //Array to store managers in
    $managers_arr = [];

    //Check if Reverb token is defined
    if($REVERB_TOKEN){
        //If yes, assign a Reverb Manager instance to the array
        $mode = get_option('reverb_mode') || 'sandbox';
        $managers_arr['reverb'] = new Reverb_Listing_Manager( [ 'token' => $REVERB_TOKEN ], $mode );
    }

    //Do the same for eBay
    if($EBAY_TOKEN){
        $mode = get_option('ebay_mode') || 'sandbox';
        $managers_arr['ebay'] = new Ebay_Listing_Manager( [ 'token' => $EBAY_TOKEN ], 'live' );
    }

    //Refresh eBay's user access token if it is expired
    $managers_arr['ebay']->maybe_refresh_token();

    //Create Listing_Manager_Group singleton and register the managers with the $managers_arr
    $listing_managers = new Listing_Manager_Group( $managers_arr );