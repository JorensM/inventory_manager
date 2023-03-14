<?php

    require_once("Reverb_Listing_Manager.php");

    class Listing_Manager_Group{

        private array $all_managers;
        private array $selected_managers;

        /**
         * Constructor. Used to define managers
         * 
         * @param IListingManager[] managers manager to use
         * Example:
         *  [
         *      ['manager_id'] => new ReverbListingManager(),
         *      ['manager_id_2'] => new EbayListingManager()
         *  ]
         * 
         * @return void
         */
        function __construct(array $managers){
            $this->all_managers = $managers;
            $this->selected_managers = $this->all_managers;
        }

        function getManager(string $manager_id){
            if(isset($this->all_managers[$manager_id])){
                return $this->all_managers[$manager_id];
            }
            return null;
        }

        /**
         * Use all managers
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
                $response = $manager->updateOrCreateListing($product);
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

        /**
         * Calls specified function of all selected managers
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

    $REVERB_TOKEN = get_option("reverb_token");
    $EBAY_TOKEN = get_option("ebay_token");
    $managers_arr = [];

    if($REVERB_TOKEN){
        $managers_arr["reverb"] = new Reverb_Listing_Manager(["token" => $REVERB_TOKEN], "sandbox");
    }

    if($EBAY_TOKEN){
        $managers_arr["ebay"] = new Ebay_Listing_Manager(["token" => $EBAY_TOKEN], "sandbox");
    }

    $listing_managers = new Listing_Manager_Group(
        ["reverb" => $reverb_manager]
    );