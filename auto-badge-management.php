    <?php
    /*
    * Plugin Name: Automatic Badge management
    * Plugin URI: https://www.adventurevibes.nl
    * Description: Automate Badge Management woocommerce websites.
    * Version: 1.0
    * Author: Jeroen Naron
    * Author URI: http://www.adventurevibes.nl

    */

    function my_admin_menu()
    {

        add_menu_page('Automatic Badge management', 'Automatic Badge management', 'manage_options', 'main-menu', 'my_main_page_contents', 'dashicons-admin-multisite', 99);
        // add_submenu_page('main-menu', 'Options', 'Options', 'manage_options', 'sub-menu', 'my_sub_page_contents');
    }
    add_action('admin_menu', 'my_admin_menu');

    function my_main_page_contents()
    {

    ?>

        <h1>

            <?php
            esc_html_e('Automatic Badge management', 'my-plugin-textdomain');

            echo "<br>";

            ?>

        </h1>
        <?php
        include('./wp-load.php');
        include('./wp-includes/wp-db.php');

        global $wpdb;

        $args = array(
            'post_type'      => 'product'
        );

        $loop = new WP_Query($args);

        while ($loop->have_posts()) : $loop->the_post();
            $lego[get_the_ID()] = get_the_ID() . " " . get_the_title();
            $legoname[get_the_ID()] = get_the_title();

        endwhile;

        wp_reset_query();
        echo count($lego) .  " legosets op de website";
        echo "<br>";
        $keys = array_keys($lego);
        ?>



        <form id="legoset" method="POST">
            <select name="legosets">
                <option selected="selected">Kies je lego set</option>


                <?php
                // Iterating through the product array
                foreach ($lego as $item) {
                    echo "<option value=$item>$item</option>";
                }
                ?>
            </select>
            <input type="submit" value="Check Set">


            <?php
            if (isset($_POST["legosets"])) {
                // set the variables with prefix from Wordpress 
                $dbavailability = $wpdb->prefix . 'rnb_availability';
                $dbmeta = $wpdb->prefix . 'postmeta';
                // $dbposts = $wpdb->prefix . 'posts';

                echo "<h3>" . "Alle details van :" . "</h3>" .  "<br>" . "<h1>" . $lego[$_POST['legosets']] . "</h1>" . "<br>";
                $setnr = $_POST["legosets"];
                // Get the latest rental date
                $returndate = $wpdb->get_col("SELECT return_datetime FROM $dbavailability WHERE product_id = $setnr ORDER BY `return_datetime` DESC LIMIT 3");
                // SELECT return_datetime FROM wp_rnb_availability WHERE product_id = 5418 ORDER BY `return_datetime` DESC LIMIT 1,1
                $pickupdate = $wpdb->get_col("SELECT pickup_datetime FROM $dbavailability WHERE product_id = $setnr ORDER BY `return_datetime` DESC LIMIT 1");
                $prev_returndate = $returndate[1];
                $returndate = $returndate[0];
                $pickupdate = $pickupdate[0];

                echo "<br>" . "Retourdatum: " . date('d-m-Y', strtotime($returndate));
                $getid = $wpdb->get_col("SELECT meta_value FROM $dbmeta WHERE meta_key = '_yith_wcbm_badge_ids' AND post_id = $setnr");
                $legoID = $getid[0];
                // $getid =  $wpdb->get_col("SELECT ID FROM `wp_posts` WHERE post_type = 'yith-wcbm-badge' AND post_title = '$legonaam'");
                $badge = $wpdb->get_col("SELECT meta_value FROM $dbmeta WHERE post_id = $legoID AND meta_key = '_text' ");
                $badgecurrent = strip_tags($badge[0]);
                echo "<br>Huidige Badge : " . $badgecurrent;
                echo "<br>";
                $start = strtotime($pickupdate);
                $end = strtotime($prev_returndate);

                if (date("Y-m-d H:i:s") > $returndate) {
                    $badgenew = "Direct";
                }

                if (date("Y-m-d H:i:s") < $returndate) {
                    if (floor(abs($end - $start) / 86400) > 10) {
                        // createbadge($prev_returndate);
                        $badgenew = date('y-m-d', strtotime($prev_returndate . ' + 1 days'));
                        $month = (int) date('m', strtotime($badgenew));
                        $day = (int) date('d', strtotime($badgenew));
                        $badgenew = "Vanaf $day-$month";
                    } else {
                        $badgenew = date('y-m-d', strtotime($returndate . ' + 1 days'));
                        $month = (int) date('m', strtotime($badgenew));
                        $day = (int) date('d', strtotime($badgenew));
                        $badgenew = "Vanaf $day-$month";
                    }
                }



                if ($badgenew !== $badgecurrent) {
                    $wpdb->update($dbmeta, array('meta_value' => $badgenew), array('post_id' => $legoID, 'meta_key'   => '_text',));
                    echo "<br> Badge is aangepast naar : <h1>$badgenew</h1> <br>";
                }
            }
        }


        function my_sub_page_contents()
        {

            ?>
            <h1> <?php esc_html_e('set options.', 'my-plugin-textdomain'); ?> </h1>
            <form method="POST" action="options.php">
                <?php
                settings_fields('sample-page');
                do_settings_sections('sample-page');
                submit_button();
                ?>
            </form>
        <?php
        }
