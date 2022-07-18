<!DOCTYPE html>
<html>

<head>
    <title>Badges details</title>
</head>

<body>
    <?php

    $path = preg_replace('/wp-content.*$/', '', __DIR__);
    include($path . 'wp-load.php');

    global $wpdb;

    $nr = $_POST['legosets'];

    $args = array(
        'post_type' => 'product'
    );

    $loop = new WP_Query($args);

    while ($loop->have_posts()) : $loop->the_post();
        global $product;
        $lego[get_the_ID()] = get_the_title();
    endwhile;

    wp_reset_query();


    $product = ($lego[$nr]);

    echo "Alle details van " . $product . ":" . "<br>";

    $dbretour = $wpdb->prefix . 'rnb_availability';
    $dbbadge = $wpdb->prefix . 'postmeta';


    $retour = $wpdb->get_col("SELECT return_datetime FROM $dbretour WHERE product_id = $nr ORDER BY `order_id` DESC");
    // $wpdb->get_col("SELECT post_title FROM wp_posts");
    $badge = $wpdb->get_col("SELECT meta_value FROM $dbbadge WHERE post_id = 3268 AND meta_key = '_text' ");


    echo "Datum Retour : " .  ($retour[0]) . "<br>";
    // echo "Huidige Badge : " .  ($badge[0]) . "<br>";


    $updated = $wpdb->update("$dbbadge", array('meta_value' => 'direct112', 'post_id' => 3268), array('%s'));

    if (false === $updated) {
        echo "er is een error, update niet uitgevoerd";
    } else {
        echo "er is geen error?";
    }

    // postmeta
    // <div style="font-family: 'Open Sans', sans-serif;">direct-411</div>
    // $newbadge = '<div style=' . "font-family:" . ' . "Open Sans". "," . "sans-serif";"">direct-112</div>';

    ?>

</body>

</html>