<?php

    /**
     * Generate HTML link for WC product
     * 
     * @param WC_Product $product product to generate link for
     * 
     * @return string <a> element with link to product
     */
    function product_link( $product ) {
        $product_id = $product->get_ID();
        $product_url = admin_url() . "post.php?post={$product_id}&action=edit";
        return "<a href='$product_url'>{$product->get_title()}</a>";
    }

    /**
     * Generate HTML link for eBay listing
     * 
     * @param WC_Product    $product    product associated with listing
     * @param string        $text       text of link 
     * 
     * @return string <a> element with link to listing
     */
    function ebay_product_link( $product, $text = 'listing') {
        $listing_id = $product->get_meta('ebay_id');
        $listing_url = "https://www.ebay.com/itm/$listing_id";
        return "<a href='$listing_url'>{$text}</a>";
    }

    /**
     * Generate HTML link for Reverb listing
     * 
     * @param WC_Product    $product    product associated with listing
     * @param string        $text       text of link 
     * 
     * @return string <a> element with link to listing
     */
    function reverb_product_link ( WC_Product $product, $text = 'listing' ) {
        $url = $product->get_meta('reverb_link');

        return "<a href='$url'>$text</a>";
    }