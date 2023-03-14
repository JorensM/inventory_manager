<?php

    require_once(__DIR__."/../classes/enums/ProductStatusEnum.php");

    /**
     * Set product status to sold.
     * 
     * @param WC_Product $product target product
     * @param bool $save whether to save product. Default true. 
     * If you are going to do additional actions on product, 
     * set this to false and save the product manually
     */
    function markProductSold(WC_Product $product, bool $save = true){
        $product->set_status(ProductStatusEnum::sold);

        if($save){
            $product->save();
        }
    }