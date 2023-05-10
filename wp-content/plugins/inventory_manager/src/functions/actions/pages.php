<?php

    /**
     * This file contains all code related to custom pages
     */

    function echo_chapter( $href, $title ) {
        echo "<li><a href='#$href'>$title</a></li>";
    }

    function echo_chapter_no_li( $href, $title ) {
        echo "<a href='#$href'>$title</a>";
    }

    function echo_table_of_contents($table_of_contents) {
        echo '<ol>';
        foreach ( $table_of_contents as $chapter_id => $chapter ) {
            // echo "<li><a href='#$chapter_id'>$chapter</a></li>";
            if( is_array( $chapter) ) {
                //echo '<li><ul>';
                $first = true;
                foreach ( $chapter as $subchapter_id => $subchapter ) {
                    //Check if first element of array
                    if( $first ) {
                        
                        echo '<li>';
                        echo_chapter_no_li( $chapter_id, $subchapter);
                        echo '<ul class="guide-ul">';
                        $first = false;
                    } else {
                        echo echo_chapter($subchapter_id, $subchapter);
                    }
                    
                    
                }
                echo '</ul></li>';
            }else{
                echo_chapter( $chapter_id, $chapter );
            }
        }
        echo '</ol>';
    }

    // General settings page
    function settings_general_page(){
        ?>
            <form method="POST" action="options.php">
                <?php 
                    settings_fields( 'settings_page_settings-general' );	//pass slug name of page, also referred to in Settings API as option group name
                    do_settings_sections( 'settings_page_settings-general' ); 	//pass slug name of page
                    submit_button();
                ?>
            </form>
        <?php
    }

    //User guide page
    function inv_mgr_user_guide() {

        $dev_guide_url = admin_url() . 'admin.php?page=developer-guide';
        $reverb_categories_url = plugins_url('', __FILE__) . '/../../reverb_categories.csv';

        $table_of_contents = array(
            'guide-overview' => 'Overview',
            'guide-linking' => array(
                0 => 'Linking',
                'guide-linking-reverb' => 'Linking Reverb',
                'guide-linking-ebay' => 'Linking eBay'
            ),
            'guide-products' => array(
                0 => 'Products',
                'guide-products-create' => 'Creating a product',
                'guide-products-view' => 'Viewing products'
            ),
            'guide-categories' => array(
                0 => 'Categories',
                'guide-categories-add' => 'Adding a category',
                'guide-categories-map' => 'Mapping categories'
            ),
            'guide-users' => array(
                0 => 'Users',
                'guide-users-create' => 'Creating a user'
            ),
            'guide-activity-log' => 'Activity log',
            'guide-conclusion' => 'Conclusion'
        );

        ?>
            <div
                class='guide-content'
            >
                <h1>User guide</h1>
                <h2>Table of contents</h2>
                <?php echo_table_of_contents( $table_of_contents ); ?>
                <h2 id='guide-overview'>Overview</h2>
                <p>
                    Inventory Manager is a web app built on top of WordPress and WooCommerce. Its purpose is to allow the user to manage
                    product across multiple platforms, namely eBay and Reverb. This web app is designed for internal use and is not meant to
                    be distributed commercially.
                </p>
                <p>
                    This guide explains how to use Inventory Manager. For technical documentation, see the 
                    <a href='<?php echo $dev_guide_url ?>'>Developer guide</a>. If there are any questions that this guide fails to answer, feel
                    free to contact me at <a href='mailto:jorensmerenjanu@gmail.com'>jorensmerenjanu@gmail.com</a>
                </p>
                <h2 id='guide-linking'>Linking</h2>
                <p>
                    Before you can publish your products on Reverb and eBay, you must link the platforms to the web app. Below are
                    instructions on how to link each platform to the web app.
                </p>
                <h3 id='guide-linking-reverb'>Linking Reverb</h3>
                <p>
                    In order to link with Reverb, log in to your Reverb account, then navigate to <b>Settings</b> > <b>My Account</b> >
                    <b>API & Integrations</b>. There, you will find a section called <b>Personal Access Tokens</b> 
                    and a button <b>Generate New Token</b> - click on that button. On the next page you will see a checklist. In the 'What's this token for'
                    field you can enter whatever you want. Check every box except 'guest', then click 'Generate Token'. After you've done this,
                    you will be taken back to the previous page and your generated token will be shown. Copy the token. Next, in the web app, go to <b>Settings</b>.
                    On the settings page, find the <b>Reverb token</b> field and paste the copied token into that field. If <b>Reverb mode</b> is not set to <b>Live</b>,
                    set it to that. Click <b>Save changes</b>. You've now linked Reverb to the web app, and any published products will also get published on Reverb!
                </p>
                <h3 id='guide-linking-ebay'>Linking eBay</h3>
                <p>
                    Linking with eBay is much easier. On the web app's <b>Settings</b> page, find and click a link called <b>Link with eBay</b>. Next you will be prompted
                    to authorize your eBay account. Click 'Agree'. You will be taken to a status page that will either show the message 'Success' or 'Error'. If you get the
                    message 'Error', try refreshing the page a few times (due to a bug with eBay, it sometimes doesn't work). Go back to the <b>Settings</b> page. <b>eBay status</b>
                    should show you the username of the eBay account that you linked. If <b>eBay mode</b> is not set to <b>Live</b>, set it to that. Click <b>Save changes</b>. You've
                    now linked eBay with the web app, and any published products will also get published on eBay!
                </p>
                <h3>Linking Reverb</h3>
                <h2 id='guide-products'>Products</h2>
                <h3 id='guide-products-create'>Creating a product</h3>
                <p>To create a product, navigate to <b>Products</b> > <b>Add New</b></p>
                <p>
                    There you will find a product form that you have to fill with the details of the product. In the <b>Product data</b> box
                    you can see two or three tabs, depending on your role:
                    <ul class='guide-ul'>
                        <li>
                            <b>General:</b> in this tab you can fill out general information about 
                            the product such as make, model, year. This information will be display on
                            Reverb and eBay once the product gets published by an admin.
                            If you are an admin, you will additionally see price fields
                        </li>
                        <li>
                            <b>Inventory:</b> In this tab you can fill out inventory related infromation such as SKU and location. If you 
                            fill out the SKU field, then a barcode will be generated once the product gets saved, and in the barcode will be
                            encoded the SKU value
                        </li>
                        <li>
                            <b>Shipping:</b> In this tab you can selected shipping profiles for each individual platform. This tab is only
                            available to admins.
                        </li>
                    </ul>
                </p>
                <p>
                    In the <b>Product Gallery</b> box you can upload images of the product. These will show up on eBay and Reverb listings
                    once published
                </p>
                <p>
                    In the <b>Publish</b> box you can save the product as a draft, or publish the product immediatelly (if you are an admin) by clicking
                    the corresponding buttons.
                </p>
                <p>
                    In the <b>Product categories</b> box you can select categories for the product.
                </p>
                <h3 id='guide-products-view'>Viewing products</h3>
                <p>
                    To view all products, head to <b>Products</b> > <b>All Products</b>. There you will find a list of all created products.
                    Clicking on an individual list item will take you to that product's page, where you can view and edit the product's details.
                    After a product has been published, you can see its Reverb and eBay status and links in the <b>Publish</b> box.
                </p>
                <h2 id='guide-categories'>
                    Categories
                </h2>
                <h3 id='guide-categories-add'>Adding a category</h3>
                    <p>
                        To add a category, head to <b>Products</b> > <b>Categories</b>.
                        On this page, on the right you will see a list of all categories
                        that you can edit by clicking on one of the categories. On the left you will see a form that you can fill out to create a new
                        category. To map the category to a Reverb and eBay category, you must fill out the <b>Category ID</b> fields for respective platforms.
                    </p>
                <h3 id='guide-categories-map'>Mapping categories</h3>
                    <p>
                        In order to map a category to a Reverb/eBay category, you must find out the category IDS for Reverb/eBay.
                        <br>
                        To find out the eBay category ID, search for the category 
                        <a href='https://pages.ebay.com/sellerinformation/news/categorychanges/preview2022.html'>on this page</a>
                        <br>
                        To find out the Reverb category ID, see <a href='<?php echo $reverb_categories_url ?>'>this table</a>. The ID is the 'uuid' value in the table
                    </p>
                <h2 id='guide-users'>Users</h2>
                    <p>
                        There are two user roles: <b>Administrator</b> and <b>Regular</b>. Administrator has full privileges, while Regular has
                        some actions disabled, namely: Publishing product, Altering product price, altering product shipping information.
                    </p>
                <h3 id='guide-users-create'>Creating a user</h3>
                <p>
                    To create a user, head over to <b>Users</b> > <b>Add New</b>. There you will find a form that you have to fill out
                    in order to create the user. Once created, you can log into the user's account using nickname/email and password that you provided
                </p>
                <h2 id='guide-activity-log'>Activity log</h2>
                <p>
                    On <b>Dashboard</b> > <b>Home</b> you will find an activity log of all activity that has been happening on the web app.
                    You can see activity such as product creation, product publishing, product status change.
                </p>
                <h2 id='guide-conclusion'>Conclusion</h2>
                <p>
                    In this guide you learned how to use the Inventory Manager app. If there are still any questions, feel free to contact
                    the original developer at <a href='mailto:jorensmerenjanu@gmail.com'>jorensmerenjanu@gmail.com</a>
                </p>
            </div>
        <?php
    }

    //User guide page
    function inv_mgr_dev_guide() {

        $table_of_contents = array (
            'guide-overview' => 'Overview',
            'guide-custom-manager' => array(
                0 => 'Implementing additional platforms',
                'guide-custom-manager-class' => 'Listing Manager class',
                'guide-custom-manager-group' => 'Listing Manager Group'
            )
        );

        ?>
            <div class='guide-content'>
                <h1>Developer guide</h1>
                <h2>
                    Table of Contents
                </h2>
                <?php echo_table_of_contents( $table_of_contents ) ?>
                <h2 id='guide-overview'>Overview</h2>
                <p>
                    This is the developer guide for Inventory Manager. Inventory Manager is a web app built to allow the user to manage all
                    their products from one place, and publish them to other platforms from one place. Inventory Manager is designed as an
                    internal web app and is not meant to be distributed commercially.
                </p>
                <p>
                    Inventory Manager is built as a WordPress/WooCommerce plugin and resides in plugins/inventory_manager. Since it is built on 
                    top of WP/Woo, you can use WP/Woo functions, filters and hooks.
                </p>
                <h2 id='guide-custom-manager'>Implementing additional platforms</h2>
                <p>
                    If you wish to add support to a platform other than eBay/Reverb, below are instructions on how to do this.
                </p>
                <h3 id='guide-custom-manager-class'>Listing Manager class</h3>
                <p>
                    <b>Listing Manager</b> is a class that manages the actions of a single platforms, for example there is the Reverb Listing Manager 
                    and there is the eBay Listing Manager. To create a listing manager for an additional platform, implement the <b>Listing_Manager_Interface</b>
                    After you've created a manager class for your platform, add it to the <b>Listing_Manager_Group</b> singleton, as shown in the next section
                </p>
                <h3 id='guide-custom-manager-group'>Listing Manager Group</h3>
                <p>
                    <b>Listing_Manager_Group</b> is a singleton class that can simultaneously call multiple listing manager class's functions. So for example
                    if you call <b>create_listing()</b> from the <b>Listing_Manager_Group</b>, it will call the <b>create_listing()</b> methods of each registered
                    listing manager. <b>Listing_Manager_Group</b> is already set up to run on certain events, such as product publish or product update. So all you have
                    to do is create a <b>Listing Manager</b> class and register it in the <b>Listing_Manager_Group</b>. To register your custom Listing Manager with 
                    the Listing_Manager_Group, go to plugins/inventory_manager/classes/Listing_Manager_Group.php .In that file, at the bottom you will find a variable
                    <b>$managers_arr</b> that holds all the managers. Simply append an instance of your custom manager to the array, and the manager will be registered
                    in the <b>Listing_Manager_Group</b>. Now when certain actions are taken, such as product create/update, your custom manager's corresponding functions will be 
                    called.
                </p>
            </div>
        <?php
    }