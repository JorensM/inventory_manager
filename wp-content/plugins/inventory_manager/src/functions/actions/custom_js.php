<?php



/**
 * Adds custom JS to the page
 * To add custom JS to every page, edit the custom_js_all() function
 * To add custom JS to a specific page, create/edit a function custom_js_<PAGEID>() and echo the JS script there (examples below)
 * 
 * @return void
 */
function custom_js() {

    custom_js_all();

    $sanitized_page_id = get_sanitized_page_id();

    $fn_name = "custom_css_$sanitized_page_id";

    if ( function_exists( $fn_name ) ) {
        $fn_name();
    }
}
//Add action for custom JS
add_action('admin_footer', 'custom_js');


/**
 * Custom JS for all pages
 */
function custom_js_all() {

}

/**
 * Custom JS for dashboard page
 */
function custom_js_dashboard() {
    $ACTIVITY_MESSAGES = [];

    $log_dir = __DIR__ . '/../../activity_log.txt';

    $handle = fopen($log_dir, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            array_push($ACTIVITY_MESSAGES, $line);
        }

        fclose($handle);
    }

    ?>
        <script>

            const ACTIVITY_MESSAGES = <?php echo json_encode($ACTIVITY_MESSAGES) ?>;

            const ACTIVITY_LOG_HTML = `
                <h4>Activity log</h4>
                <div id='activity-log' class='activity-log'>
                    
                </div>
            `;
            document.getElementById('wpbody-content').insertAdjacentHTML('beforeend', ACTIVITY_LOG_HTML);

            ACTIVITY_MESSAGES.forEach(message => {
                const MESSAGE_HTML = `
                    <div class='activity-log-message'>
                        ${message}
                    </div>
                `
                document.getElementById('activity-log').insertAdjacentHTML('afterbegin', MESSAGE_HTML)
            })
        </script>
    <?php
}

/**
 * Custom JS for users page
 */
function custom_js_users() {
    ?>
        <script>
            console.log("test");
            document.getElementById("posts").innerHTML = "Products";
        </script>
    <?php
}

/**
 * Custom JS for product page
 */
function custom_js_product() {
    global $listing_managers;

    $product = wc_get_product();

    //Get product links for each platform
    $product_ebay_id = $product->get_meta('ebay_id');
    $product_ebay_url = "https://www.ebay.com/itm/$product_ebay_id";
    $product_reverb_url = $product->get_meta('reverb_link');

    $ebay_listing = $listing_managers->getManager('ebay')->get_listing($product);
    $reverb_listing = $listing_managers->getManager('reverb')->get_listing($product);

    $ebay_status = '';
    $reverb_status = '';

    if( $ebay_listing ) {
        $ebay_status = $ebay_listing->Item->SellingStatus->ListingStatus;
    }

    if( $reverb_listing ) {
        $reverb_status = $reverb_listing['state']['description'];
    }

    ?>

        <script>

            const status_dropdown = document.getElementById("post_status");

            <?php
                if(wc_get_product()->get_status() == 'sold'){
                    ?>
                        document.getElementById("post-status-display").innerHTML = "<b>Sold</b>";
                    <?php
                }
            ?>
            status_dropdown.insertAdjacentHTML("beforeend", "<option value='sold'>Sold</option>");

            //Generate title based on entered information
            function generateTitle(input_element){
                //Add a string to an array if the string is set and not empty
                function addStrIfNotEmpty(str, arr){
                    if(str && str !== ""){
                        arr.push(str);
                    }
                }

                let brand_info = document.getElementById("brand_info").value;
                let model_info = document.getElementById("model_info").value;
                let year = document.getElementById("year_field").value;
                let color = document.getElementById("color_field").value;

                // model_info = addSpaceOrEmpty(model_info);
                // year_info = addSpaceOrEmpty(year);
                // color_info = addSpaceOrEmpty(color);

                //Turn info into array of strings
                let title_parts = [];
                addStrIfNotEmpty(brand_info, title_parts);
                addStrIfNotEmpty(model_info, title_parts);
                addStrIfNotEmpty(year, title_parts);
                addStrIfNotEmpty(color, title_parts);

                const title = title_parts.join(" ");

                //Hide input label after generating title
                if(title.replaceAll(" ", "") !== "" && title !== null && title !== undefined){
                    input_element.value = title;
                    document.getElementById("title-prompt-text").classList.add("screen-reader-text");
                }
            }

            const form = document.getElementById("post");

            const categories_pop = document.getElementById("product_cat-pop");
            const categories_all = document.getElementById("product_cat-all");

            const inputs_pop = categories_pop.querySelectorAll("input[type='checkbox']");
            const inputs_all = categories_all.querySelectorAll("input[type='checkbox']");

            const title_input = document.getElementById("title");
            const title_div = document.getElementById("titlediv");

            

            //Make title field required
            title_input.required = true;

            //Add "generate title" button
            title_div.insertAdjacentHTML("afterend", `
                <div
                    style='
                        display: flex;
                        justify-content: flex-end
                    '
                >   
                    <button
                        type='button'
                        class='button button-secondary'
                        onclick='prefillData()'
                    >
                        Prefill data
                    </button>
                    <button 
                        type='button'
                        class='button button-secondary'
                        onclick='generateTitle(title_input)'
                    >
                        Generate title
                    </button>
                </div>
                
            `);

            function prefillData(){
                document.getElementById("_regular_price").value = 50;
                document.getElementById("brand_info").value = 21;
                document.getElementById("model_info").value = 21;
                document.getElementById("year_field").value = 2000;
                document.getElementById("notes_field").value = "abcd";
                document.getElementById("in-product_cat-18").checked = true;
                document.getElementById("in-product_cat-19").checked = true;
            }

            //Prevent "are you sure you want to leave this page" popup
            window.addEventListener('beforeunload', function (event) {
                event.stopImmediatePropagation();
            });

            //On form submit
            form.addEventListener("submit", (e) => {
                //console.log(e);
                //e.preventDefault();
                //return;


                //Check if category is specified, and cancel form submission if false
                let has_category = false;

                for(let i = 0; i < inputs_pop.length; i++) {
                    let item = inputs_pop[i];
                    if(item.value === "15"){
                        console.log ("Is uncategorized");
                    }
                    if(item.checked && item.value !== "15"){
                        has_category = true;
                        break;
                    }
                }
                
                if(!has_category){
                    for(let i = 0; i < inputs_all.length; i++) {
                        let item = inputs_all[i];
                        if(item.checked && item.value !== "15"){
                            has_category = true;
                            break;
                        }
                    }
                }

                if(!has_category){
                    e.preventDefault();
                    alert("Please select a category!");
                }

            })

            summary_box = document.getElementById('misc-publishing-actions');

            addPlatformLinkToSummary('Reverb listing', '<?php echo $product_reverb_url ?>', '<?php echo $reverb_status; ?>');
            addPlatformLinkToSummary('eBay listing', '<?php echo $product_ebay_url ?>', '<?php echo $ebay_status; ?>');

            function addPlatformLinkToSummary(label, link, status) {
                const linkHTML = `<a href='${link}'>${label}</a>`;
                const statusHTML = `<b>Status: </b> ${status}`;
                summary_box.insertAdjacentHTML('beforeend', `
                    <div class='misc-pub-section'>
                        ${linkHTML}
                        <br>
                        ${statusHTML}
                    </div>
                `);
            }
        </script>

    <?php
    //Get barcode
    $file_ext = ".png";

    $product = wc_get_product();

    $barcode_url =  wp_upload_dir()["baseurl"] . "/" . $product->get_id() . $file_ext;

    $barcode_filename = wp_upload_dir()["basedir"] . "/" . $product->get_id() . $file_ext;

    $barcode_exists = false;

    $file_headers = @get_headers($barcode_url);
    if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
        $barcode_exists = false;
    }
    else {
        $barcode_exists = true;
    } 
    //If barcode exists, show it
    if($barcode_exists){
        ?>
            <script>
                const minor_publishing_div = document.getElementById("minor-publishing");

                /*
                    Barcode element HTML code
                    Append current time to the image url to force cache clear (known as a cachebreaker),
                    so that if the barcode gets updated, the new one gets shown instead of the old one
                */
                const barcode_html = `
                    <div 
                        style='
                            display: flex; 
                            align-items: center;
                            justify-content: center;
                            width: 100%;
                            padding: 16px;
                            box-sizing: border-box;
                        '
                    >
                        <img 
                            src='<?php echo $barcode_url; ?>?${new Date().getTime()}'
                            style='
                                width: 100%;
                            '
                        >
                    </div>  
                `

                minor_publishing_div.insertAdjacentHTML("beforeend", barcode_html);
            </script>
        <?php
    }
}

/**
 * Custom JS for profile page
 */
function custom_js_profile() {
    ?>
        <script>
            const divs = document.getElementsByTagName("h2");

            for (let x = 0; x < divs.length; x++) {
                const div = divs[x];
                const content = div.textContent.trim();
            
                if (content == 'Customer billing address' || content == 'Customer shipping address') {
                    div.style.display = 'none';
                }
            }
        </script>
    <?php
}