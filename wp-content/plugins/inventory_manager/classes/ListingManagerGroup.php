<?php

    class ListingManagerGroup{

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

        function updateOrCreateListing(WC_Product $product){
            foreach($this->selected_managers as $manager){
                $manager->updateOrCreateListing($product);
            }
        }
        
        function deleteListing(WC_Product $product){
            foreach($this->selected_managers as $manager){
                $manager->deleteListing($product);
            }
        }
    }