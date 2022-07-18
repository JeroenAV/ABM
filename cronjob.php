
<?php

// include some Wordpress php to use the CLass WP_query and $wpdb

$path = preg_replace('/wp-content.*$/', '', __DIR__);
include($path . 'wp-load.php');

global $wpdb;

// Create Empty array
$modified = array();
$now = date("d/m/Y h:i:s");

$modified += array('starttime' => $now);

$args = array(
    'post_type'      => 'product'
);

$loop = new WP_Query($args);
while ($loop->have_posts()) : $loop->the_post();
    $lego[get_the_ID()] = get_the_ID() . " " . get_the_title();
    $legoname[get_the_ID()] = get_the_title();
endwhile;

wp_reset_query();
$keys = array_keys($lego);

// set the variables with prefix from Wordpress 
$dbavailability = $wpdb->prefix . 'rnb_availability';
$dbmeta = $wpdb->prefix . 'postmeta';

// Loop through all LegeSets and update badge


for ($i = 0; $i < count($keys); $i++) {

    $setnr = $keys[$i];
    // Get the latest 3 rental date's
    $returndate = $wpdb->get_col("SELECT return_datetime FROM $dbavailability WHERE product_id = $setnr ORDER BY `return_datetime` DESC LIMIT 3");
    // Pickupdate from latest rental
    $pickupdate = $wpdb->get_col("SELECT pickup_datetime FROM $dbavailability WHERE product_id = $setnr ORDER BY `return_datetime` DESC LIMIT 1");
    // 2nd last Rental
    // if (isset($returndate[1])) {
    //     $prev_returndate = $returndate[1];
    // } else {
    //     $prev_returndate = $returndate[0];
    // }
    $prev_returndate = $returndate[1];
    $returndate = $returndate[0];
    $pickupdate = $pickupdate[0];

    $legoID = $wpdb->get_col("SELECT meta_value FROM $dbmeta WHERE meta_key = '_yith_wcbm_badge_ids' AND post_id = $setnr");
    $legoID = $legoID[0];
    $badge = $wpdb->get_col("SELECT meta_value FROM $dbmeta WHERE post_id = $legoID AND meta_key = '_text' ");
    $badgecurrent = strip_tags($badge[0]);
    $startnew = strtotime($pickupdate);
    $endprev = strtotime($prev_returndate);
    $now = date("Y-m-d H:i:s");

    // echo "$legoname[$setnr] returndate $returndate <br>";
    // echo "$legoname[$setnr] badgenew $badgenew <br>";
    // echo "$legoname[$setnr] badgecurrent =  $badgecurrent <br>";
    // echo "$legoname[$setnr] today " .date("Y-m-d H:i:s") . "<br>";



    if ($now > $returndate) {
        $badgenew = "Direct";
        // echo "DIRECT  = $legoname[$setnr] <br>";
    }

    if ($now < $returndate) {

        $badgenew = makebadge($returndate);
        // echo "aanpassen -> $legoname[$setnr] naar $badgenew <br>";
    }


    if ($returndate > $prev_returndate && $prev_returndate > $now && floor(abs($startnew - $endprev)  / 86400) > 10) {
        $badgenew = makebadge($prev_returndate);
    } else {
        $badgenew = "Direct";
    }


    // if (floor(abs($startnew - $endprev)  / 86400) > 10) {
    //     echo "<br> FLOOR    $legoname[$setnr] <br>";
    // }


    if ($badgenew !== $badgecurrent) {
        $wpdb->update($dbmeta, array('meta_value' => $badgenew), array('post_id' => $legoID, 'meta_key'   => '_text',));
        $modified += array($legoname[$setnr] => "badge was : $badgecurrent is nu : $badgenew");
    }
}

function makebadge($date)
{
    $badgenew = date('y-m-d', strtotime($date . ' + 1 days'));
    $month = (int) date('m', strtotime($badgenew));
    $day = (int) date('d', strtotime($badgenew));
    $badgenew = "Vanaf $day-$month";
    return $badgenew;
}




$modified += array('endtime' => $now);

file_put_contents('./lastrun.txt', print_r($modified, true));
?>
