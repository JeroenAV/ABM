<?php

//absolute path to wp-load.php, or relative to this script
//e.g., ../wp-core/wp-load.php
include('../wp-load.php');

//grab the WPDB database object, using WP's database
//more info: http://codex.wordpress.org/Class_Reference/wpdb
global $wpdb;

//make a new DB object using a different database
//$mydb = new wpdb('username','password','database','localhost');

//basic functionality

//run any query

$check = $wpdb->query("SELECT * FROM wp_posts");
echo $check;

//run a query and get the results as an associative array
$wpdb->get_results("SELECT * FROM wp_posts");

//get a single variable
$wpdb->get_var("SELECT post_title FROM wp_posts WHERE ID = 1");

//get a row as an assoc. array
$wpdb->get_row("SELECT * FROM wp_posts WHERE ID = 1");

//get an entire column
$wpdb->get_col("SELECT post_title FROM wp_posts");

//insert data into a table… sql protection?
$wpdb->insert('wp_posts', array('post_title' => 'test', 'ID' => 5), array('%s', '%d'));

//update an existing row
$wpdb->update('wp_posts', array('post_title' => 'test2'), array('ID' => 5), array('%s'));

// The function takes the following arguments:

// $table The table name.
// $data A PHP array where the keys are the columns and the values are the the values to be inserted into those columns.
// $where A PHP array where the key is the column name, and the value is the value to be checked.


$wpdb->update(
	$ipn_table,
	array(
		'ip_address_01' => $ipONactivate
	),
	array(
		'payer_email' => $serial,
		'item_name'   => $product,
	)
);

$sql = "UPDATE {$ipn_table} SET ip_address_01='{$ipONactivate}' WHERE payer_email='{$serial}' AND item_name='{$product}'";



//escaping queries
$wpdb->query($wpdb->prepare("UPDATE INTO wp_posts set post_title = %s WHERE ID = %d", 'test2', 5));

//two steps to insert a post…

//define the post… all field optional
$post = array(
	'post_title' => 'test',
	'post_type' => 'station',
	'post_status' => 'publish',
	'post_author' => 'greg',
);

//insert
$id = wp_insert_post($post);

//store key/value pair
update_post_meta($id, 'expiration', '201101010');

//retrieve key/value pair
$meta = get_post_meta($id, 'expiration', true);

//search for posts by key/value pair
$posts = get_posts('expiration=20110110');

//associate taxonomy terms with a post
wp_set_post_terms($id, array('red', 'blue', 'green'), 'colors');

//query posts by taxonomy term
$posts = get_posts('color=red');

//wizards for creating taxonomy / post types
//will output a plugin that you just drop into /wp-content/plugins and activate
//http://themergency.com/generators/wordpress-custom-taxonomy/
//http://themergency.com/generators/wordpress-custom-post-types/

//Cache

//store a value in cache
wp_cache_set('unique_key', $data);

//retrieve value from cache
$data = wp_cache_get('unqiue_key');

/* additional things to do
1) Install W3 Total Cache to get DB and object caching
2) Use the front end / admin UI to browse / sort data
*/