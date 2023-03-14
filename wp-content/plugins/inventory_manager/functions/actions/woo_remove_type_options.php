<?php

//Remove type options from woocommerce product editor
function woo_remove_type_options($options){
    // remove "Virtual" checkbox
	if( isset( $options[ 'virtual' ] ) ) {
		unset( $options[ 'virtual' ] );
	}
	// remove "Downloadable" checkbox
	if( isset( $options[ 'downloadable' ] ) ) {
		unset( $options[ 'downloadable' ] );
	}
	return $options;
}
add_filter( 'product_type_options', "woo_remove_type_options");