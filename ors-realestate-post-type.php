<?php
/*
Plugin Name: Real Estate Post Type
Plugin URI: https://github.com/orangeroomsoftware/WP-Real-Estate-Post-Type
Version: 1.0
Author: <a href="http://www.orangeroomsoftware.com/">Orange Room Software</a>
Description: A post type for Real Estate Sales
*/

define('REALESTATE_PLUGIN_URL', '/wp-content/plugins/' . basename(dirname(__FILE__)) );
define('REALESTATE_PLUGIN_DIR', dirname(__FILE__));

#
# Theme Admin Options
#
require_once ( REALESTATE_PLUGIN_DIR . '/plugin-options.php' );

#
# Theme supporting filters
#
add_theme_support( 'post-thumbnails' );
add_filter( 'get_the_excerpt', 'do_shortcode' );
add_filter( 'the_excerpt', 'do_shortcode' );
add_filter( 'widget_text', 'do_shortcode' );

#
# Site Stylesheet
#
add_action('wp_print_styles', 'ors_realestate_template_stylesheets', 10);
function ors_realestate_template_stylesheets() {
  wp_enqueue_style('realestate-template-style', REALESTATE_PLUGIN_URL . "/style.css", 'ors-realestate', null, 'all');
}

#
# Admin Stylesheet
#
add_action('admin_print_styles', 'ors_realestate_admin_stylesheets', 10);
function ors_realestate_admin_stylesheets() {
  wp_enqueue_style('realestate-admin-style', REALESTATE_PLUGIN_URL . "/admin-style.css", 'ors-realestate-admin', null, 'all');
}

#
# Admin Javascript
#
add_action('admin_print_scripts', 'ors_realestate_plugin_admin_script', 5);
function ors_realestate_plugin_admin_script() {
  wp_register_script( 'ors_realestate_plugin_admin_script', REALESTATE_PLUGIN_URL . "/admin-script.js", 'jquery', time() );
  wp_enqueue_script('ors_realestate_plugin_admin_script');
}

#
# First time activation
#
register_activation_hook( __FILE__, 'activate_realestate_post_type' );
function activate_realestate_post_type() {
  create_realestate_post_type();
  flush_rewrite_rules();
  add_option( 'ors-realestate-global-features', '2 Car Garage|4 Car Garage|Air Conditioning|Alarm|Assigned Parking|Ceiling Fan|Central Heating|Covered Parking|Den/Office|Dining Area|Dining Room|Dishwasher|Disposal|Enclosed Patios|Evaporative Cooler|Family Room|Fenced Back Yard|Fireplace|Full Kitchen|Game Room|Garage|Generous Closet Areas|Interior Storage|Living Room|Loft|Microwave|Patio|RV Parking|Refrigerator|Separate Dining Room|Spa|Sprinklers|Storage Shed|Stove/Oven|Swimming Pool|Utility Room|Washer/Dryer|Washer/Dryer Hookup|Central Vac', '', true );
  add_option( 'ors-realestate-global-options', 'Pool Service|Pest Control Service|Yard Service|Sewer and Trash|Playground|Small Pets Considered|Tennis Court|Garbage Pickup|Satellite TV|Water', '', true );
}

#
# Custom post type def
#
add_action( 'init', 'create_realestate_post_type' );
function create_realestate_post_type() {
  $labels = array(
    'name' => _x('Real Estates', 'post type general name'),
    'singular_name' => _x('Real Estate', 'post type singular name'),
    'add_new' => _x('Add New', 'realestate'),
    'add_new_item' => __('Add New Real Estate'),
    'edit_item' => __('Edit Real Estate'),
    'new_item' => __('New Real Estate'),
    'view_item' => __('View Real Estate'),
    'search_items' => __('Search Real Estates'),
    'not_found' =>  __('No Real Estate found'),
    'not_found_in_trash' => __('No Real Estate found in Trash'),
    'parent_item_colon' => '',
    'menu_name' => 'Real Estates'

  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'query_var' => true,
    'capability_type' => 'post',
    'has_archive' => true,
    'hierarchical' => false,
    'menu_position' => 6,
    'supports' => array('title', 'location', 'gallery', 'thumbnail', 'editor', 'tags'),
    'menu_icon' => REALESTATE_PLUGIN_URL . '/icon.png',
    'rewrite' => array(
      'slug' => 'realestates',
      'with_front' => false
    )
  );

  register_post_type( 'realestate', $args );
}

#
# Meta Box for WordPress Admin Real Estate Editor
#
add_action( 'add_meta_boxes', 'add_custom_realestate_meta_boxes' );
function add_custom_realestate_meta_boxes() {
  add_meta_box("realestate_meta", 'Real Estate Information', "custom_realestate_meta_boxes", "realestate", "normal", "high");
}

function custom_realestate_meta_boxes() {
  global $post;
  $custom_data = get_post_custom($post->ID);

  $features = array_filter(explode('|', $custom_data['features'][0]), 'strlen');
  sort($features);
  $options = array_filter(explode('|', $custom_data['options'][0]), 'strlen');
  sort($options);

  $global_features = explode('|', get_option('ors-realestate-global-features'));
  $global_options = explode('|', get_option('ors-realestate-global-options'));

  ?>
  <div class="group">
    <p>
      Availability Status:<br>
      <input type="radio" name="realestate_meta[available]" value="Available Now" <?php echo ($custom_data['available'][0] == 'Available Now') ? 'checked' : ''; ?>>
      <label>Available Now</label>

      <input type="radio" name="realestate_meta[available]" value="Coming Soon" <?php echo ($custom_data['available'][0] == 'Coming Soon') ? 'checked' : ''; ?>>
      <label>Coming Soon</label>
    </p>
    <p>
      Property Type:<br>
      <input type="radio" name="realestate_meta[property_type]" value="Residential" <?php echo ($custom_data['property_type'][0] == 'Residential') ? 'checked' : ''; ?>>
      <label>Residential</label>
      <input type="radio" name="realestate_meta[property_type]" value="Commercial" <?php echo ($custom_data['property_type'][0] == 'Commercial') ? 'checked' : ''; ?>>
      <label>Commercial</label>
    </p>
  </div>

  <p>
    Price:<br>
    $<input type="text" name="realestate_meta[price]" value="<?php echo $custom_data['price'][0]; ?>" size="4">
  </p>

  <p>
    <label>Street:</label><br>
    <input name="realestate_meta[street]" value="<?php echo $custom_data['street'][0]; ?>" size="60">
  </p>
  <div class="group">
    <p>
      <label>City:</label><br>
      <input name="realestate_meta[city]" value="<?php echo $custom_data['city'][0]; ?>" size="40">
    </p>
    <p>
      <label>State:</label><br>
      <input name="realestate_meta[state]" value="<?php echo $custom_data['state'][0]; ?>" size="2">
    </p>
    <p>
      <label>ZIP:</label><br>
      <input name="realestate_meta[zip]" value="<?php echo $custom_data['zip'][0]; ?>" size="5">
    </p>
  </div>

  <div class="group">
    <p>
      Structure Size:<br>
      <input type="text" name="realestate_meta[home_size]" value="<?php echo $custom_data['home_size'][0]; ?>" size="4" class="numeric">sq ft
    </p>
    <p>
      Lot Size:<br>
      <input type="text" name="realestate_meta[lot_size]" value="<?php echo $custom_data['lot_size'][0]; ?>" size="4" class="numeric">sq ft
    </p>
    <p>
      Bedrooms:<br>
      <input type="text" name="realestate_meta[bedrooms]" value="<?php echo $custom_data['bedrooms'][0]; ?>" size="2" class="numeric">
    </p>
    <p>
      Bathrooms:<br>
      <input type="text" name="realestate_meta[bathrooms]" value="<?php echo $custom_data['bathrooms'][0]; ?>" size="2" class="numeric">
    </p>
  </div>

  <p>
    Features:<br>
    <input type="hidden" id="features-data" name="realestate_meta[features]" value="<?php echo implode('|', $features); ?>">
    <ul id="features" class="bundle">
      <?php foreach ( $global_features as $value ) { if (empty($value)) continue; ?>
      <li><input type="checkbox" value="<?php echo $value; ?>" <?php echo in_array($value, $features) ? 'checked="checked"' : ''; ?>> <?php echo $value; ?></li>
      <?php } ?>
    </ul>
    <input type="text" id="add-feature-text" name="add-feature" value="" size="20">
    <input type="button" id="add-feature-button" value="Add">
  </p>

  <p>
    Optional:<br>
    <input type="hidden" id="options-data" name="realestate_meta[options]" value="<?php echo $custom_data['options'][0]; ?>">
    <ul id="options" class="bundle">
      <?php foreach ( $global_options as $value ) { if (empty($value)) continue; ?>
      <li><input type="checkbox" value="<?php echo $value; ?>" <?php echo in_array($value, $options) ? 'checked="checked"' : ''; ?>> <?php echo $value; ?></li>
      <?php } ?>
    </ul>
    <input type="text" id="add-option-text" name="add-option" value="" size="20">
    <input type="button" id="add-option-button" value="Add">
  </p>

  <?php
}

#
# Save Real Estate Post
#
add_action( 'save_post', 'save_realestate_postdata' );
function save_realestate_postdata( $post_id ) {
  if ( get_post_type() != 'realestate' ) return;

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    return;

  // Check permissions
  if ( 'page' == $_POST['post_type'] ) {
    if ( !current_user_can( 'edit_page', $post_id ) )
      return;
  } else {
    if ( !current_user_can( 'edit_post', $post_id ) )
      return;
  }

  // Page Meta
  $custom_data = $_POST['realestate_meta'];
  foreach ($custom_data as $key=>$value) {
    update_post_meta($post_id, $key, $value);
  }

  // Global Features and Options
  $features = explode('|', $custom_data['features']); sort($features);
  $options = explode('|', $custom_data['options']); sort($options);
  $global_features = explode('|', get_option('ors-realestate-global-features'));
  $global_options = explode('|', get_option('ors-realestate-global-options'));
  $global_features = array_filter(array_unique(array_merge($global_features, $features)), 'strlen');
  $global_options = array_filter(array_unique(array_merge($global_options, $options)), 'strlen');
  sort($global_features);
  sort($global_options);
  update_option('ors-realestate-global-features', implode('|', $global_features));
  update_option('ors-realestate-global-options', implode('|', $global_options));
}

#
# Real Estate Admin Listings
#
add_filter("manage_edit-realestate_columns", "realestate_edit_columns");
function realestate_edit_columns($columns) {
  $columns["thumbnail"]      = "Photo";
  $columns["title"]          = "Property Title";
  $columns["available"]      = "Availability";
  $columns["price"]          = "Price";
  $columns["property_type"]  = "Type";
  $columns["bed-bath"]       = "Bed/Bath";
  $columns["street"]         = "Street";
  $columns["city"]           = "City";
  $columns["author"]         = "Author";
  $columns["date"]           = "Date Added";
  return $columns;
}

add_action("manage_posts_custom_column",  "realestate_custom_columns");
function realestate_custom_columns($column){
  if ( get_post_type() != 'realestate' ) return;

  global $post;
  $custom = get_post_custom();

  switch ($column) {
    case "thumbnail":
      if ( has_post_thumbnail( $post->ID ) ) {
        the_post_thumbnail(array(50,50));
      }
      break;
    case "price":
      echo '$' . number_format($custom["price"][0]);
      break;
    case "available":
      echo $custom["available"][0];
      break;
    case "property_type":
      echo $custom["home_size"][0] . 'sqft ' . $custom["property_type"][0];
      break;
    case "bed-bath":
      echo $custom["bedrooms"][0] . '/' . $custom["bathrooms"][0];
      break;
    case "street":
      echo $custom["street"][0];
      break;
    case "city":
      echo $custom["city"][0];
      break;
  }
}

#
# Custom Query for this post type to sort by price
#
# Don't use this sort in Admin
if ( !is_admin() ) add_filter( 'posts_clauses', 'ors_realestate_query' );
function ors_realestate_query($clauses) {
  if ( !strstr($clauses['where'], 'realestate') or is_single() ) return $clauses;

  global $wpdb, $ors_realestate_cookies;
  $clauses['fields'] .= ", CAST((select {$wpdb->postmeta}.meta_value from {$wpdb->postmeta} where {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID and {$wpdb->postmeta}.meta_key = 'price' order by meta_id desc limit 1) as decimal) as price";
  $clauses['fields'] .= ", CAST((select {$wpdb->postmeta}.meta_value from {$wpdb->postmeta} where {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID and {$wpdb->postmeta}.meta_key = 'home_size' order by meta_id desc limit 1) as decimal) as home_size";
  $clauses['fields'] .= ", CAST((select {$wpdb->postmeta}.meta_value from {$wpdb->postmeta} where {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID and {$wpdb->postmeta}.meta_key = 'bedrooms' order by meta_id desc limit 1) as decimal) as bedrooms";
  $clauses['fields'] .= ", CAST((select {$wpdb->postmeta}.meta_value from {$wpdb->postmeta} where {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID and {$wpdb->postmeta}.meta_key = 'bathrooms' order by meta_id desc limit 1) as decimal) as bathrooms";
  $clauses['fields'] .= ", (select {$wpdb->postmeta}.meta_value from {$wpdb->postmeta} where {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID and {$wpdb->postmeta}.meta_key = 'property_type') as property_type";

  $clauses['having'] = array();
  $clauses['orderby'] = '';

  if ( isset($ors_realestate_cookies['property_type']) and $ors_realestate_cookies['property_type'] != '' ) {
    $clauses['where'] .= " and property_type = '{$ors_realestate_cookies['property_type']}'";
  }

  if ( isset($ors_realestate_cookies['text_search']) and $ors_realestate_cookies['text_search'] != '' ) {
    $clauses['where'] .= " and ({$wpdb->posts}.post_title like '%{$ors_realestate_cookies['text_search']}%'";
    $clauses['where'] .= " or {$wpdb->posts}.post_content like '%{$ors_realestate_cookies['text_search']}%')";
  }

  $search_params = array('bedrooms', 'bathrooms');
  foreach ($search_params as $param) {
    if ( isset($ors_realestate_cookies[$param]) and $ors_realestate_cookies[$param] != '' ) {
      $clauses['having'][] = "$param = '$ors_realestate_cookies[$param]'";
    }
  }
  if ( !empty($clauses['having']) ) {
    $clauses['where'] .= ' HAVING ' . implode(' and ', $clauses['having']);
  }

  $order_params = array('price' => 'price_near', 'home_size' => 'size_near');
  foreach ($order_params as $field => $param) {
    if ( isset($ors_realestate_cookies[$param]) and $ors_realestate_cookies[$param] != '' ) {
      $clauses['orderby'] .= ", ABS({$ors_realestate_cookies[$param]} - $field)";
    }
  }
  if ( $clauses['orderby'] == '' ) $clauses['orderby'] = 'price ASC';
  else $clauses['orderby'] = substr($clauses['orderby'], 2);

  # print "<pre>" . print_r($clauses, 1) . "</pre>";
  return $clauses;
}

add_action( 'init', 'ors_realestate_set_cookies');
function ors_realestate_set_cookies() {
  global $ors_realestate_cookies;
  $search_params = array(
    'price_near',
    'size_near',
    'bedrooms',
    'bathrooms',
    'text_search'
  );

  foreach ($search_params as $param) {
    if ( isset($_POST[$param]) ) {
      if ( $_POST['clear'] == 'Clear' ) $_POST[$param] = '';
      $ors_realestate_cookies[$param] = $_POST[$param];
      setcookie(
        $param,
        $_POST[$param],
        time() + 3600, COOKIEPATH, COOKIE_DOMAIN, false
      );
    }

    elseif ( isset($_COOKIE[$param]) ) {
      $ors_realestate_cookies[$param] = $_COOKIE[$param];
    }
  }
}

/*
 * Get the current URL
 */
if ( !function_exists( 'get_current_url' ) ) {
  function get_current_url() {
    return $_SERVER["REQUEST_URI"];
  }
}

/*
 * Fix 404 pages
 */
add_action( 'template_redirect', 'ors_realestate_404_fix', 0 );
function ors_realestate_404_fix() {
  global $ors_realestate_search;

  if ( !have_posts() && strstr(get_current_url(), 'realestates') ) {
    $search_params = array(
      'price_near',
      'size_near',
      'bedrooms',
      'bathrooms',
      'text_search'
    );

    foreach ($search_params as $param) {
      setcookie(
        $param,
        null, -1, COOKIEPATH, COOKIE_DOMAIN, false
      );
    }

    wp_redirect( "/realestates/?nf=1" );
    exit;
  }
}

#
# Fix the content
#

/* Templates */
add_filter( 'archive_template', 'ors_realestate_archive_template' ) ;
function ors_realestate_archive_template( $archive_template ) {
  global $post;

  if ( is_post_type_archive ( 'realestate' ) ) {
   $archive_template = REALESTATE_PLUGIN_DIR . '/archive-realestate.php';
  }

  return $archive_template;
}

add_filter( 'single_template', 'ors_realestate_single_template' );
function ors_realestate_single_template($single_template) {
  global $post;

  if ($post->post_type == 'realestate') {
    $single_template = REALESTATE_PLUGIN_DIR . '/single-realestate.php';
  }

  return $single_template;
}

#
# Create a Sidebar for above the Real Estate content
#
if ( function_exists('register_sidebar') ) {
  // Register widget zones for Real Estate Post Type
  register_sidebar( array('name' => 'Above Real Estate Archives',  'id' => 'above-realestate-archive',  'before_widget' => '', 'after_widget' => '') );
  register_sidebar( array('name' => 'Above Real Estate Single',  'id' => 'above-realestate-single',  'before_widget' => '', 'after_widget' => '') );
}

#
# Search Box
#
add_filter( 'loop_start', 'ors_realestate_search_box' );
function ors_realestate_search_box() {
  if ( get_post_type() != 'realestate' ) return;
  if ( is_single() ) return;

  global $ors_realestate_cookies;

  ?>
  <div id='ors-realestate-search-box'>
    <form action="/realestates/" method="POST" class="form-inline" role="form">
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-addon">Price Near</div>
          <input name="price_near" class="form-control price_near" type="number" value="<?php echo $ors_realestate_cookies['price_near'] ?>">
        </div>
      </div>
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-addon">Size Near</div>
          <input name="size_near" class="form-control size_near" type="number" value="<?php echo $ors_realestate_cookies['size_near'] ?>">
        </div>
      </div>
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-addon">Text</div>
          <input name="text_search" class="form-control text_search" type="text" value="<?php echo $ors_realestate_cookies['text_search'] ?>">
        </div>
      </div>
      <input type="hidden" name="post_type" value="realestate">
      <button type="submit" name="submit" class="btn btn-primary">Search</button>
      <button type="submit" name="clear" class="btn btn-danger">Reset</button>
    </form>
  </div>
  <?php
  if ($_GET['nf'] == 1) {
    ?>
    <div class="not-found">No realestates found. Your search was reset.</div>
    <?php
  }
}

# Formats the post titles
add_filter( 'the_title', 'realestate_title_filter' );
function realestate_title_filter($content) {
  if ( !in_the_loop() or get_post_type() != 'realestate' ) return $content;

  foreach ( get_post_custom() as $key => $value ) {
    $custom[$key] = $value[0];
  }

  if ( $custom['available'] == 'Coming Soon' ) $visible = false; else $visible = true;

  $output  = '';
  if ($custom['price'])
    $output .= '<span class="price">$' . number_format($custom['price']) . '</span>';

  if ( $visible ) $output .= '<span class="title">' . $content . '</span>';
  else $output .= '<span class="title">Coming Soon</span>';

  $output .= '<span class="property-type">' . $custom['property_type'] . '</span>';

  return $output;
}

# Formats the excerpt
add_filter('the_excerpt', 'realestate_excerpt_filter');
function realestate_excerpt_filter($content) {
  if ( get_post_type() != 'realestate' ) return $content;

  foreach ( get_post_custom() as $key => $value ) {
    $custom[$key] = $value[0];
  }

  if ( $custom['available'] == 'Coming Soon' ) $visible = false; else $visible = true;

  $address = $custom['street'] . ", " . $custom['city'] . ", " . $custom['state'] . "  " . $custom['zip'];

  $output = '';

  if ( !has_post_thumbnail( $post->ID ) ) {
    $output .= '<a href="' . get_permalink() . '"><img width="150" height="150" src="' . REALESTATE_PLUGIN_URL . '/nophoto.png" class="attachment-thumbnail wp-post-image realestate-photo" alt="No Photo" title="' . $address . '"></a>';
  }

  if ( $custom['available'] ) {
    $output .= "<div class='availability burst-8 " . preg_replace('/\-{2}+/','',preg_replace('/[^A-Za-z0-9]/','-',strtolower(strip_tags($custom['available'])))) . "'>" . ucwords($custom['available']) . "</div>";
  }

  $output .= "<ul class='meta'>";
  if ( $visible ) $output .= "  <li>Address: " . $address . '</li>';

  $stats = array();
  if ( $custom['home_size'] )
    $stats[] = "{$custom['home_size']} Square Foot";
  if ( $custom['bedrooms'] )
    $stats[] = "{$custom['bedrooms']} Bedrooms";
  if ( $custom['bathrooms'] )
    $stats[] = "{$custom['bathrooms']} Bathrooms";

  if ( count($stats) >= 1 )
    $output .= "  <li>" . implode(", ", $stats) . "</li>";

  $output .= "</ul>";

  return $output;
}

# Formats the content of the post
add_filter('the_content', 'realestate_content_filter');
function realestate_content_filter($content) {
  if ( get_post_type() != 'realestate' ) return $content;

  foreach ( get_post_custom() as $key => $value ) {
    $custom[$key] = $value[0];
  }

  if ( $custom['available'] == 'Coming Soon' ) $visible = false; else $visible = true;

  $address = $custom['street'] . ", " . $custom['city'] . ", " . $custom['state'] . "  " . $custom['zip'];

  $features = array_filter(explode('|', $custom['features']), 'strlen');
  $options = array_filter(explode('|', $custom['options']), 'strlen');

  $output = '';

  $output .= get_option('ors-realestate-gallery-shortcode') . '<br/>';

  $output .= $content;

  $output .= "<ul class='meta'>";
  if ( $visible ) $output .= "  <li>Address: " . $address . '</li>';
  $output .= "  <li>" . $custom['bedrooms'] . ' Bedrooms ';
  $output .= "  " . $custom['bathrooms'] . ' Bath</li>';
  if ( $custom['home_size'] )
    $output .= "  <li>{$custom['home_size']} Square Foot {$custom['property_type']}</li>";
  if ( $custom['lot_size'] )
    $output .= "  <li>{$custom['lot_size']} Square Foot Lot</li>";
  $output .= "</ul>";

  if ( is_array($features) and !empty($features[0]) ) {
    $output .= "<div class='features'>";
    $output .= "Features:<br>";
    $output .= '<ul>';
    foreach ( $features as $value ) {
      $output .= '  <li>' . $value . '</li>';
    }
    $output .= '</ul></div>';
  }

  if ( is_array($options) and !empty($options[0]) ) {
    $output .= "<div class='options'>";
    $output .= "Optional:<br>";
    $output .= '<ul>';
    foreach ( $options as $value ) {
      $output .= '  <li>' . $value . '</li>';
    }
    $output .= '</ul></div>';
  }

  if ( $inquiry = get_option('ors-realestate-inquiry-form') ) {
    $output .= '<div class="inquiry-form">';
    $output .= '<h2>Send Email Inquiry</h2>';
    $output .= $inquiry;
    $output .= '</div>';
    $output .= '<script type="text/javascript" charset="utf-8">jQuery(function() { jQuery(".inquiry-form form input[name*=subject]").val("'."Real Estate Inquiry for {$address}".'"); });</script>';
  }

  if ( $tell_a_friend = get_option('ors-realestate-tell-a-friend-form') ) {
    $output .= '<div class="inquiry-form">';
    $output .= '<h2>Tell-A-Friend</h2>';
    $output .= $tell_a_friend;
    $output .= '</div>';
  }

  $output .= '<a class="back-button" href="' . $_SERVER['HTTP_REFERER'] . '">◄ Back to Listings</a>';

  return $output;
}

/* short code */

function by_property_type($clauses = '') {
  global $wpdb, $current_property_type;

  $clauses['where'] .= "and (select {$wpdb->postmeta}.meta_value from {$wpdb->postmeta} where {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID and {$wpdb->postmeta}.meta_key = 'property_type') = '{$current_property_type}'";

  return $clauses;
}

add_shortcode( 'realestates', 'realestates_func' );
function realestates_func( $atts ) {
  global $wpdb, $current_property_type;

  $current_property_type  = $atts['type'];
  $args  = array(
    'posts_per_page'  => $atts['limit'],
    'orderby'         => 'rand',
    'post_type'       => 'realestate',
    'post_status'     => 'publish',
    'suppress_filters' => false
  );

  add_filter( 'posts_clauses', 'by_property_type' );
  $posts = get_posts($args);

  foreach ( $posts as $post ) {
    setup_postdata( $post );

    foreach ( get_post_custom($post->ID) as $key => $value ) {
      $custom[$key] = $value[0];
    }

    $output  = '<div id="ors-realestate" class="shortcode">';
    $output .= '<a href="' . get_permalink($post->ID) . '" rel="bookmark" title="' . the_title_attribute() . '">';
    $output .= "<header>$post->post_title</header>";

    if ( !has_post_thumbnail( $post->ID ) ) {
      $output .= '<img width="150" height="150" src="' . REALESTATE_PLUGIN_URL . '/nophoto.png" class="attachment-thumbnail wp-post-image realestate-photo" alt="No Photo" title="' . $address . '">';
    } else {
      $output .= get_the_post_thumbnail($post->ID, 'thumbnail');
    }

    $output .= "</a></div>";
  }

  remove_filter( 'posts_clauses', 'ors_realestate_query' );
  wp_reset_postdata();

  return $output;
}
