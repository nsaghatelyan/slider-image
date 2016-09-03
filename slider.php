<?php

/*
Plugin Name: Slider
Plugin URI: http://huge-it.com/slider
Description: Slider Huge-IT is an awesome WordPress Slider Plugin with many nice features. Just need to install and build slider in a few minutes.
Version: 3.1.91
Author: Huge-IT
Author URI: http://huge-it.com/
License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action('media_buttons_context', 'hugeit_slider_add_my_custom_button' );
function hugeit_slider_add_my_custom_button($context) {
  
  $img = plugins_url( '/images/post.button.png' , __FILE__ );
  $container_id = 'huge_it_slider';

  $title = 'Select Huge IT Slider to insert into post';

  $context .= '<a class="button thickbox" title="Select slider to insert into post"    href="?page=sliders_huge_it_slider&task=add_shortcode_post&TB_iframe=1&width=400&inlineId='.$container_id.'">
		<span class="wp-media-buttons-icon" style="background: url('.$img.'); background-repeat: no-repeat; background-position: left bottom;"></span>
	Add Slider
	</a>';
  
  return $context;
}

add_filter('media_view_strings', 'hugeit_slider_remove_media_tab' );
function hugeit_slider_remove_media_tab($strings) {
	return $strings;
}
//todo: hanel
add_action('init', 'hugeit_sldier_do_output_buffer' );
function hugeit_sldier_do_output_buffer() {
	if (isset($_GET['page']) && $_GET['page'] === 'sliders_huge_it_slider') {
		ob_start();
	}
}

$ident = 1;

add_action('admin_head', 'hugeit_slider_huge_it_ajax_func' );
function hugeit_slider_huge_it_ajax_func()
{
    ?>
    <script>
        var huge_it_ajax = '<?php echo admin_url("admin-ajax.php"); ?>';
    </script>
<?php
}

add_shortcode('huge_it_slider', 'huge_it_slider_images_list_shotrcode');
function huge_it_slider_images_list_shotrcode($atts)
{
    extract(shortcode_atts(array(
        'id' => 'no huge_it slider',
    ), $atts));
	hugeit_slider_add_style_to_header($atts['id']);
	add_action('wp_footer', 'hugeit_slider_add_style_to_header' );
        
    wp_register_script( 'bxSlider',plugins_url("js/jquery.bxslider.js", __FILE__) ,array ('jquery'), '1.0.0', true);
	wp_enqueue_script('bxSlider');
	wp_register_script( 'bxSliderSetup',plugins_url("js/bxslider.setup.js", __FILE__) ,array ('jquery'), '1.0.0', true);
	wp_enqueue_script('bxSliderSetup');

	wp_register_style( 'bxSlidercss',plugins_url("style/jquery.bxslider.css", __FILE__));
	wp_enqueue_style('bxSlidercss');
        
    return hugeit_slider_images_list($atts['id']);
}

add_filter('posts_request', 'hugeit_slider_after_search_results' );
function hugeit_slider_after_search_results($query)
{
    global $wpdb;
    if (isset($_REQUEST['s']) && $_REQUEST['s']) {
        $serch_word = sanitize_text_field(($_REQUEST['s']));
		$gen_string_slider_search = hugeit_slider_gen_string_slider_search($serch_word, $wpdb->prefix . 'posts.post_content') . " " . $wpdb->prefix . "posts.post_content";
        $query = str_replace($wpdb->prefix . "posts.post_content" ,$gen_string_slider_search ,$query);
    }
    return $query;
}

function hugeit_slider_gen_string_slider_search($serch_word, $wordpress_query_post) {
    $string_search = '';

    global $wpdb;
    if ($serch_word) {
        $rows_slider = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "huge_itslider_sliders WHERE (description LIKE %s) OR (name LIKE %s)", '%' . $serch_word . '%', "%" . $serch_word . "%"));

        $count_cat_rows = count($rows_slider);

        for ($i = 0; $i < $count_cat_rows; $i++) {
            $string_search .= $wordpress_query_post . ' LIKE \'%[huge_it_slider id="' . $rows_slider[$i]->id . '" details="1" %\' OR ' . $wordpress_query_post . ' LIKE \'%[huge_it_slider id="' . $rows_slider[$i]->id . '" details="1"%\' OR ';
        }
		
        $rows_slider = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "huge_itslider_sliders WHERE (name LIKE %s)","'%" . $serch_word . "%'"));
        $count_cat_rows = count($rows_slider);
        for ($i = 0; $i < $count_cat_rows; $i++) {
            $string_search .= $wordpress_query_post . ' LIKE \'%[huge_it_slider id="' . $rows_slider[$i]->id . '" details="0"%\' OR ' . $wordpress_query_post . ' LIKE \'%[huge_it_slider id="' . $rows_slider[$i]->id . '" details="0"%\' OR ';
        }

        $rows_single = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "huge_itslider_images WHERE name LIKE %s","'%" . $serch_word . "%'"));

        $count_sing_rows = count($rows_single);
        if ($count_sing_rows) {
            for ($i = 0; $i < $count_sing_rows; $i++) {
                $string_search .= $wordpress_query_post . ' LIKE \'%[huge_it_slider_Product id="' . $rows_single[$i]->id . '"]%\' OR ';
            }

        }
    }
    return $string_search;
}


function hugeit_slider_images_list($id)
{
    require_once("slider_front_end.html.php");
    require_once("slider_front_end.php");
    if (isset($_GET['product_id'])) {
        if (isset($_GET['view']) && esc_html($_GET['view']) == 'huge_itslider') {
            return hugeit_slider_show_published_images_1($id);
        }
    } else {
        return hugeit_slider_show_published_images_1($id);
    }
}

add_action('admin_menu', 'huge_it_slider_options_panel');
function huge_it_slider_options_panel()
{
    $page_cat = add_menu_page('Theme page title', 'Huge IT Slider', 'delete_pages', 'sliders_huge_it_slider', 'hugeit_slider_page', plugins_url('images/sidebar.icon.png', __FILE__));
    add_submenu_page('sliders_huge_it_slider', 'Sliders', 'Sliders', 'delete_pages', 'sliders_huge_it_slider', 'hugeit_slider_page' );
    $page_option = add_submenu_page('sliders_huge_it_slider', 'General Options', 'General Options', 'manage_options', 'Options_slider_styles', 'hugeit_slider_options_slider_styles' );
	add_submenu_page( 'sliders_huge_it_slider', 'Licensing', 'Licensing', 'manage_options', 'huge_it_slider_Licensing', 'huge_it_slider_Licensing');
	add_submenu_page('sliders_huge_it_slider', 'Featured Plugins', 'Featured Plugins', 'manage_options', 'huge_it_slider_featured_plugins', 'huge_it_slider_featured_plugins');
	
	add_action('admin_print_styles-' . $page_cat, 'huge_it_slider_admin_script');
    add_action('admin_print_styles-' . $page_option, 'huge_it_slider_option_admin_script');
	
}
function huge_it_slider_Licensing() {
	?>
	<div style="width:95%">
		<p>
			This plugin is the LITE version of the Slider. If you want to customize to the styles and colors of your
			website,than you need to buy Full License. Purchasing Full License will add possibility to customize the
			general options of the Slider.
		</p>
		<br/><br/>
		<a href="http://huge-it.com/slider/" class="button-primary" target="_blank">Purchase a License</a>
		<br/><br/><br/>
		<p>After the purchasing the commercial version follow this steps:</p>
		<ol>
			<li>Deactivate Huge IT slider Plugin</li>
			<li>Delete Huge IT slider Plugin</li>
			<li>Install the downloaded commercial version of the plugin</li>
		</ol>
	</div>
<?php
}

function huge_it_slider_featured_plugins() {
	?>

<style>
.element {
	position: relative;
	width:93%; 
	margin:5px 0 5px 0;
	padding:2%;
	clear:both;
	overflow: hidden;
	border:1px solid #DEDEDE;
	background:#F9F9F9;
}
.element > div {
	display:table-cell;
}
.element div.left-block {
	padding-right:10px;
}
.element div.left-block .main-image-block {
	clear:both; 
}
.element div.left-block .thumbs-block {
	position:relative;
	margin-top:10px;
}
.element div.left-block .thumbs-block ul {
	width:240px; 
	height:auto;
	display:table;
	margin:0;
	padding:0;
	list-style:none;
}
.element div.left-block .thumbs-block ul li {
	margin:0 3px 0 2px;
	padding:0;
	width:75px; 
	height:75px; 
	float:left;
}
.element div.left-block .thumbs-block ul li a {
	display:block;
	width:75px; 
	height:75px; 
}
.element div.left-block .thumbs-block ul li a img {
	width:75px; 
	height:75px; 
}
.element div.right-block {
	vertical-align:top;
    width: 100%;
}
.element div.right-block > div {
	width:100%;
	padding-bottom:10px;
	margin-top:10px;
}
.element div.right-block > div:last-child {
	background:none;
}
.element div.right-block .title-block  {
	margin-top:3px;
}
.element div.right-block .title-block h3 {
	margin:0;
	padding:0;
	font-weight:normal;
	font-size:18px !important;
	line-height:18px !important;
	color:#0074A2;
}
.element div.right-block .description-block p,.element div.right-block .description-block * {
	margin:0;
	padding:0;
	font-weight:normal;
	font-size:14px;
	color:#555555;
}
.element div.right-block .description-block h1,
.element div.right-block .description-block h2,
.element div.right-block .description-block h3,
.element div.right-block .description-block h4,
.element div.right-block .description-block h5,
.element div.right-block .description-block h6,
.element div.right-block .description-block p, 
.element div.right-block .description-block strong,
.element div.right-block .description-block span {
	padding:2px !important;
	margin:0 !important;
}
.element div.right-block .description-block ul,
.element div.right-block .description-block li {
	padding:2px 0 2px 5px;
	margin:0 0 0 8px;
}
.element .button-block {
	position:relative;
}
.element div.right-block .button-block a,.element div.right-block .button-block a:link,.element div.right-block .button-block a:visited {
	position:relative;
	display:inline-block;
	padding:6px 12px;
	background:#2EA2CD;
	color:#FFFFFF;
	font-size: 14px;
	text-decoration:none;
}
.element div.right-block .button-block a:hover,.pupup-elemen.element div.right-block .button-block a:focus,.element div.right-block .button-block a:active {
	background:#0074A2;
	color:#FFFFFF;
}
.button-block a {
	float: right;
}
.description-block p {
	text-align: justify !important;
}
@media only screen and (max-width: 767px) {
	.element > div {
		display:block;
		width:100%;
		clear:both;
	}
	.element div.left-block {
		padding-right:0;
	}
	.element div.left-block .main-image-block {
		clear:both;
		width:100%; 
	}
	.element div.left-block .main-image-block img {
		width:100% !important;  
		height:auto;
	}
	.element div.left-block .thumbs-block ul {
		width:100%; 
	}
}
</style>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
            <a href="http://huge-it.com/wordpress-gallery/" target="_blank">
			     <img src="<?php echo plugins_url( 'images/Gallery.png' , __FILE__ ); ?>">
            </a>
		</div>
	</div>
	<div class="right-block">
        <div class="title-block"><a href="http://huge-it.com/wordpress-gallery/" target="_blank"><h3>WordPress Image Gallery</h3></a></div>
		<div class="description-block">
			<p>Huge-IT Gallery images is perfect for using for creating various galleries within various views, to creating various sliders with plenty of styles, beautiful lightboxes with it’s options for any taste. The product allows adding descriptions and titles for each image of the Gallery. It is rather useful wherever using with various pages and posts, as well as within custom location.</p>
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/wordpress-gallery/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
            <a href="http://huge-it.com/portfolio-gallery/" target="_blank">
			     <img src="<?php echo plugins_url( 'images/portfolio.png' , __FILE__ ); ?>">
            </a>
		</div>
	</div>
	<div class="right-block">
        <div class="title-block"><a href="http://huge-it.com/portfolio-gallery/" target="_blank"><h3>WordPress Portfolio/Gallery</h3></a></div>
		<div class="description-block">
			<p>Portfolio Gallery is perfect for using for creating various portfolios or gallery within various views. The product allows adding descriptions and titles for each portfolio gallery. It is rather useful whever using with various pages and posts, as well as within custom location.</p>
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/portfolio-gallery/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
            <a href="http://huge-it.com/forms/" target="_blank">
			     <img src="<?php echo plugins_url( 'images/form.png' , __FILE__ ); ?>">
            </a>
		</div>
	</div>
	<div class="right-block">
        <div class="title-block"><a href="http://huge-it.com/forms/" target="_blank"><h3>WordPress Forms</h3></a></div>
		<div class="description-block">
			<p>Forms are one of the most important elements of WordPress website because without Forms Builder you will not be able to always keep in touch with your visitors</p>
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/forms/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
			<a href="http://huge-it.com/product-catalog/" target="_blank">
				<img src="<?php echo plugins_url( 'images/catalog.png' , __FILE__ ); ?>">
			</a>
		</div>
	</div>
	<div class="right-block">
		<div class="title-block"><a href="http://huge-it.com/product-catalog/" target="_blank"><h3>WordPress Product Catalog</h3></a></div>
		<div class="description-block">
			<p>Product Catalog introduces incomparable Product Catalog demonstration. Just use it to better lean about all advantages of Product Catalog.</p>
		</div>
		<div class="button-block">
			<a href="http://huge-it.com/product-catalog/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
			<a href="http://huge-it.com/wordpress-responsive-slider/" target="_blank">
				<img src="<?php echo plugins_url( 'images/responsive-slider.png' , __FILE__ ); ?>">
			</a>
		</div>
	</div>
	<div class="right-block">
		<div class="title-block"><a href="http://huge-it.com/wordpress-responsive-slider/" target="_blank"><h3>WordPress Responsive Slider</h3></a></div>
		<div class="description-block">
			<p>Create responsive wordpress slider in minutes. Bring awesome image slider, showcase post sliders for your WordPress website, display images and photos in beautiful sliders.</p>
		</div>
		<div class="button-block">
			<a href="http://huge-it.com/wordpress-responsive-slider/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
            <a href="http://huge-it.com/lightbox/" target="_blank">
			     <img src="<?php echo plugins_url( 'images/lightbox.png' , __FILE__ ); ?>">
            </a>
		</div>
	</div>
	<div class="right-block">
        <div class="title-block"><a href="http://huge-it.com/lightbox/" target="_blank"><h3>WordPress Lightbox</h3></a></div>
		<div class="description-block">
			<p>Lightbox is a perfect tool for viewing photos. It is created especially for simplification of using, permits you to view larger version of images and giving an interesting design. With the help of slideshow and various styles, betray a unique image to your website.</p>
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/lightbox/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
            <a href="http://huge-it.com/wordpress-video-gallery/" target="_blank">
                    <img src="<?php echo plugins_url( 'images/player.png' , __FILE__ ); ?>">
            </a>
		</div>
	</div>
	<div class="right-block">
        <div class="title-block"><a href="http://huge-it.com/wordpress-video-gallery/" target="_blank"><h3>WordPress Video Gallery</h3></a></div>
		<div class="description-block">
			<p>Video Gallery plugin was created and specifically designed to show your video files in unusual splendid ways. It has 5 good-looking views. Each are made in different taste so that you can choose any of them, according to the style of your website.</p>
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/wordpress-video-gallery/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
            <a href="http://huge-it.com/share-buttons/" target="_blank">
			     <img src="<?php echo plugins_url( 'images/share.png' , __FILE__ ); ?>">
            </a>
		</div>
	</div>
	<div class="right-block">
        <div class="title-block"><a href="http://huge-it.com/share-buttons/" target="_blank"><h3>WordPress Share Buttons</h3></a></div>
			<p>Social network is one of the popular places where people get information about everything in the world. Adding social share buttons into your blog or website page is very necessary and useful element for "socialization" of the project.</p>
		<div class="description-block">
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/share-buttons/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
            <a href="http://huge-it.com/google-map/" target="_blank">
			     <img src="<?php echo plugins_url( 'images/google-map.png' , __FILE__ ); ?>">
            </a>
		</div>
	</div>
	<div class="right-block">
        <div class="title-block"><a href="http://huge-it.com/google-map/" target="_blank"><h3>WordPress Google Map</h3></a></div>
			<p>Huge-IT Google Map. One more perfect tool from Huge-IT. Improved Google Map, where we have our special contribution. Most simple and effective tool for rapid creation of individual Google Map in posts and pages.</p>
		<div class="description-block">
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/google-map/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
            <a href="http://huge-it.com/colorbox/" target="_blank">
			     <img src="<?php echo plugins_url( 'images/colorbox.png' , __FILE__ ); ?>">
            </a>
		</div>
	</div>
	<div class="right-block">
        <div class="title-block"><a href="http://huge-it.com/colorbox/" target="_blank"><h3>WordPress Colorbox</h3></a></div>
			<p>Huge-It Colorbox is the most spellbinding plugin in WordPress that implement Lightbox-effect look of the images and videos (when you click on the thumbnail of the image/video it nicely opens and increases in the same window with a beautiful effect).</p>
		<div class="description-block">
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/colorbox/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
            <a href="http://huge-it.com/video-player/" target="_blank">
			     <img src="<?php echo plugins_url( 'images/video-player.png' , __FILE__ ); ?>">
            </a>
		</div>
	</div>
	<div class="right-block">
        <div class="title-block"><a href="http://huge-it.com/video-player/" target="_blank"><h3>WordPress Video Player</h3></a></div>
			<p>Inserting video on a page is a perfect way to supplement website with media content and expand the user’s interest in your site. Huge-IT Video Player is extremely necessary video tool for your sites, which provides a wide range of different file formats.</p>
		<div class="description-block">
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/video-player/" target="_blank">View Plugin</a>
		</div>
	</div>

</div>
	<?php
}

function huge_it_slider_admin_script()
{
	wp_enqueue_media();
	wp_enqueue_style( "jquery_ui", plugins_url( "style/jquery-ui.css", __FILE__ ), false );
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) || ICL_PLUGIN_INACTIVE ) {
		wp_enqueue_script( "jquery" );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script('jquery-ui-sortable');
	}

	wp_enqueue_script( "simple_slider_js", plugins_url( "js/simple-slider.js", __FILE__ ), false );
	wp_enqueue_style( "simple_slider_css", plugins_url( "style/simple-slider.css", __FILE__ ), false );
	wp_enqueue_style( "admin_css", plugins_url( "style/admin.style.css", __FILE__ ), false );
	wp_enqueue_script( "admin_js", plugins_url( "js/admin.js", __FILE__ ), false );
	wp_enqueue_script( 'param_block2', plugins_url( "elements/jscolor/jscolor.js", __FILE__ ) );
}

function huge_it_slider_option_admin_script() {
	wp_enqueue_script('jquery');

	if ( ! wp_script_is( 'jQuery.ui' ) ) {
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
	}
	wp_enqueue_script( "simple_slider_js", plugins_url( "js/simple-slider.js", __FILE__ ), false );

	wp_enqueue_style( "simple_slider_css", plugins_url( "style/simple-slider.css", __FILE__ ), false );
	wp_enqueue_style( "admin_css", plugins_url( "style/admin.style.css", __FILE__ ), false );
	wp_enqueue_script( "admin_js", plugins_url( "js/admin.js", __FILE__ ), false );
	wp_enqueue_script( 'param_block2', plugins_url( "elements/jscolor/jscolor.js", __FILE__ ) );
}

function hugeit_slider_page() {
    require_once("sliders.php");
    require_once("sliders.html.php");

	if ( ! function_exists( 'hugeit_slider_print_html_nav' ) ) {
		require_once( "slider_function/html_slider_func.php" );
	}

	if ( isset( $_GET["task"] ) ) {
		$task = sanitize_text_field( $_GET["task"] );
	} else {
		$task = '';
	}
	if ( isset( $_GET["id"] ) ) {
		$id = absint( $_GET["id"] );
	} else {
		$id = 0;
	}
	global $wpdb;
	switch ( $task ) {

		case 'add_cat':
			if ( ! isset( $_GET['hugeit_slider_new_slider_nonce'] ) || ! wp_verify_nonce( $_GET['hugeit_slider_new_slider_nonce'] ) ) {
				die( 'Wrong nonce.' );
			}
			hugeit_slider_add_slider();
			break;
		case 'add_shortcode_post':
			hugeit_slider_add_shortcode_post();
			break;
		case 'popup_posts':
			if ( $id ) {
				hugeit_slider_popup_posts( $id );
			}
			break;
		case 'popup_video':
			if ( $id ) {
				hugeit_slider_popup_video( $id );
			} else {
				$id = $wpdb->get_var( "SELECT MAX( id ) FROM " . $wpdb->prefix . "huge_itslider_sliders" );
				hugeit_slider_popup_video( $id );
			}
			break;
		case 'edit_cat':
			if ( ! isset( $_GET['hugeit_slider_edit_slide_nonce'] ) || ! wp_verify_nonce( $_GET['hugeit_slider_edit_slide_nonce'] ) ) {
				die( 'Wrong nonce.' );
			}
			if ( $id ) {
				hugeit_slider_edit_slider( $id );
			} else {
				$id = $wpdb->get_var( "SELECT MAX( id ) FROM " . $wpdb->prefix . "huge_itslider_sliders" );
				hugeit_slider_edit_slider( $id );
			}
			break;

		case 'save':
			if ( $id ) {
				hugeit_slider_apply_cat( $id );
			}
		case 'apply':
			if ( ! isset( $_REQUEST['hugeit_slider_apply_form'] ) || ! wp_verify_nonce( $_REQUEST['hugeit_slider_apply_form'] ) ) {
				die( 'Wrong nonce.' );
			}
			if ( $id ) {
				hugeit_slider_apply_cat( $id );
				hugeit_slider_edit_slider( $id );
			}
			break;
		case 'remove_cat':
			if ( ! isset( $_GET['hugeit_slider_remove_slide_nonce'] ) || ! wp_verify_nonce( $_GET['hugeit_slider_remove_slide_nonce'] ) ) {
				die( 'Wrong nonce.' );
			}
			hugeit_slider_remove_slider( $id );
			hugeit_slider_show_slider();
			break;
		default:
			hugeit_slider_show_slider();
			break;
	}
}
function hugeit_slider_add_shortcode_post() {
	?>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#hugeitsliderinsert').on('click', function() {

			jQuery('#save-buttom').click();

			var id = jQuery('#huge_it_slider-select option:selected').val();
			if (window.parent.tinyMCE || window.parent.tinyMCE.activeEditor) {
				window.parent.send_to_editor('[huge_it_slider id="' + id + '"]');
			}
			tb_remove();
		})
	});
</script>
<style>
#wpadminbar,.auto-fold #adminmenu, .auto-fold #adminmenu li.menu-top, .auto-fold #adminmenuback, .auto-fold #adminmenuwrap {
	display: none;
}

#wpcontent {
	margin-top: -55px;
}

.wp-core-ui .button {margin:0 0 0 10px !important;}

#slider-unique-options-list li {
	clear:both;
	margin:10px 0 5px 0;
}

#slider-unique-options-list li label {width:130px;}

#save-buttom {display:none;}
 
h3 {
	margin:30px 0 15px 0;
}
</style>
<div class="clear"></div>
<h3>Select the Slider</h3>
<div id="huge_it_slider">
	<?php
	global $wpdb;
	$query    = "SELECT * FROM " . $wpdb->prefix . "huge_itslider_sliders";
	$firstrow = $wpdb->get_row( $query );
	if ( isset( $_POST["hugeit_slider_id"] ) ) {
		$id = absint( $_POST["hugeit_slider_id"] );
	} else {
		$id = ( $firstrow->id );
	}
	if ( isset( $_GET["htslider_id"] ) && absint( $_GET["htslider_id"] ) == absint( $_POST["hugeit_slider_id"] ) ) {
		if ( isset( $_GET["hugeit_save"] ) ) {
			$hugeit_save = absint( $_GET["hugeit_save"] );
			if ( $hugeit_save == 1 ) {

				$post_sl_width            = sanitize_text_field( $_POST["sl_width"] );
				$post_sl_height           = sanitize_text_field( $_POST["sl_height"] );
				$post_pause_on_hover      = sanitize_text_field( $_POST["pause_on_hover"] );
				$post_slider_effects_list = sanitize_text_field( $_POST["slider_effects_list"] );
				$post_sl_pausetime        = sanitize_text_field( $_POST["sl_pausetime"] );
				$post_sl_changespeed      = sanitize_text_field( $_POST["sl_changespeed"] );
				$post_sl_position         = sanitize_text_field( $_POST["sl_position"] );
				$post_sl_loading_icon     = sanitize_text_field( $_POST["sl_loading_icon"] );
				$post_show_thumb          = sanitize_text_field( $_POST["show_thumb"] );

				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itslider_sliders SET  sl_width = '%s'  WHERE id = %d ", $post_sl_width, $id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itslider_sliders SET  sl_height = '%s'  WHERE id = %d ", $post_sl_height, $id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itslider_sliders SET  pause_on_hover = '%s'  WHERE id = %d ", $post_pause_on_hover, $id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itslider_sliders SET  slider_list_effects_s = '%s'  WHERE id = %d ", $post_slider_effects_list, $id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itslider_sliders SET  description = '%s'  WHERE id = %d ", $post_sl_pausetime, $id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itslider_sliders SET  param = '%s'  WHERE id = %d ", $post_sl_changespeed, $id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itslider_sliders SET  sl_position = '%s'  WHERE id = %d ", $post_sl_position, $id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itslider_sliders SET  sl_loading_icon = '%s' WHERE id = %d ", $post_sl_loading_icon, $id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itslider_sliders SET  show_thumb = '%s' WHERE id = %d ", $post_show_thumb, $id ) );/*add*/

			}
		}
	}
	// $table_name = $wpdb->prefix."huge_itslider_sliders";
	$shortcodesliders = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "huge_itslider_sliders  ORDER BY id ASC" );
	$query            = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itslider_sliders WHERE id= %d", $id );
	$row              = $wpdb->get_row( $query );
	$container_id     = 'huge_it_slider'; ?>
<form action="?page=sliders_huge_it_slider&task=add_shortcode_post&TB_iframe=1&width=400&inlineId=<?php echo $container_id; ?>&hugeit_save=1&htslider_id=<?php echo $id; ?>" method="post" name="adminForm" id="adminForm">
	<?php if ( count( $shortcodesliders ) ) {
		echo "<select id='huge_it_slider-select' onchange='this.form.submit()' name='hugeit_slider_id'>";
		foreach ( $shortcodesliders as $shortcodeslider ) {
			$selected = '';
			if ( $shortcodeslider->id == $_POST["hugeit_slider_id"] ) {
				$selected = 'selected="selected"';
			}
			echo "<option " . $selected . " value='" . $shortcodeslider->id . "'>" . $shortcodeslider->name . "</option>";
		}
		echo "</select>";
		echo "<button class='button primary' id='hugeitsliderinsert'>Insert Slider</button>";
	} else {
		echo "No slideshows found", "huge_it_slider";
	}
	$container_id = 'huge_it_slider';
	?>
</div>
	<div id="" class="meta-box-sortables ui-sortable">
		<div id="slider-unique-options" class="">
			<h3 class="hndle"><span>Current Slider Options</span></h3>
			<ul id="slider-unique-options-list">
				<li>
					<label for="sl_width">Width</label>
					<input type="text" name="sl_width" id="sl_width" value="<?php echo $row->sl_width; ?>"
					       class="text_area"/>
				</li>
				<li>
					<label for="sl_height">Height</label>
					<input type="text" name="sl_height" id="sl_height" value="<?php echo $row->sl_height; ?>"
					       class="text_area"/>
				</li>
				<li>
					<label for="pause_on_hover">Pause on Hover</label>
					<input type="hidden" value="off" name="pause_on_hover"/>
					<input type="checkbox" name="pause_on_hover" value="on"
					       id="pause_on_hover" <?php if ( $row->pause_on_hover == 'on' ) { echo 'checked="checked"';} ?> />
				</li>
				<li>
					<label for="slider_effects_list">Effects</label>
					<select name="slider_effects_list" id="slider_effects_list">
						<option <?php if ( $row->slider_list_effects_s == 'none' ) {echo 'selected';} ?> value="none">None</option>
						<option <?php if ( $row->slider_list_effects_s == 'cubeH' ) {echo 'selected';} ?> value="cubeH">Cube Horizontal</option>
						<option <?php if ( $row->slider_list_effects_s == 'cubeV' ) {echo 'selected';} ?> value="cubeV">Cube Vertical</option>
						<option <?php if ( $row->slider_list_effects_s == 'fade' ) {echo 'selected';} ?> value="fade">Fade</option>
						<option <?php if ( $row->slider_list_effects_s == 'sliceH' ) {echo 'selected';} ?> value="sliceH">Slice Horizontal</option>
						<option <?php if ( $row->slider_list_effects_s == 'sliceV' ) {echo 'selected';} ?> value="sliceV">Slice Vertical</option>
						<option <?php if ( $row->slider_list_effects_s == 'slideH' ) {echo 'selected';} ?> value="slideH">Slide Horizontal</option>
						<option <?php if ( $row->slider_list_effects_s == 'slideV' ) {echo 'selected';} ?> value="slideV">Slide Vertical</option>
						<option <?php if ( $row->slider_list_effects_s == 'scaleOut' ) {echo 'selected';} ?> value="scaleOut">Scale Out</option>
						<option <?php if ( $row->slider_list_effects_s == 'scaleIn' ) {echo 'selected';} ?> value="scaleIn">Scale In</option>
						<option <?php if ( $row->slider_list_effects_s == 'blockScale' ) {echo 'selected';} ?> value="blockScale">Block Scale</option>
						<option <?php if ( $row->slider_list_effects_s == 'kaleidoscope' ) {echo 'selected';} ?> value="kaleidoscope">Kaleidoscope</option>
						<option <?php if ( $row->slider_list_effects_s == 'fan' ) {echo 'selected';} ?> value="fan">Fan</option>
						<option <?php if ( $row->slider_list_effects_s == 'blindH' ) {echo 'selected';} ?> value="blindH">Blind Horizontal</option>
						<option <?php if ( $row->slider_list_effects_s == 'blindV' ) {echo 'selected';} ?> value="blindV">Blind Vertical</option>
						<option <?php if ( $row->slider_list_effects_s == 'random' ) {echo 'selected';} ?> value="random">Random</option>
					</select>
				</li>

				<li>
					<label for="sl_pausetime">Pause Time</label>
					<input type="text" name="sl_pausetime" id="sl_pausetime" value="<?php echo $row->description; ?>"
					       class="text_area"/>
				</li>
				<li>
					<label for="sl_changespeed">Change Speed</label>
					<input type="text" name="sl_changespeed" id="sl_changespeed" value="<?php echo $row->param; ?>"
					       class="text_area"/>
				</li>
				<li>
					<label for="slider_position">Slider Position</label>
					<select name="sl_position" id="slider_position">
						<option <?php if ( $row->sl_position == 'left' ) {echo 'selected';} ?> value="left">Left</option>
						<option <?php if ( $row->sl_position == 'right' ) {echo 'selected';} ?> value="right">Right</option>
						<option <?php if ( $row->sl_position == 'center' ) {echo 'selected';} ?> value="center">Center</option>
					</select>
				</li>
				<li>
					<label for="sl_loading_icon">Loading Icon</label>
					<select id="sl_loading_icon" name="sl_loading_icon">
						<option <?php if ( $row->sl_loading_icon == 'on' ) {echo 'selected';} ?> value="on">On</option>
						<option <?php if ( $row->sl_loading_icon == 'off' ) {echo 'selected';} ?> value="off">Off</option>
					</select>
				</li>
				<li>
					<label for="show_thumb">Navigate By</label>
					<input type="hidden" value="off" name="show_thumb"/>
					<select id="show_thumb" name="show_thumb">
						<option <?php if ( $row->show_thumb == 'dotstop' ) {echo 'selected';} ?> value="dotstop">Dots</option>
						<option <?php if ( $row->show_thumb == 'thumbnails' ) {echo 'selected';} ?> value="thumbnails">Thumbnails</option>
						<option <?php if ( $row->show_thumb == 'nonav' ) {echo 'selected';} ?> value="nonav">No Navigation</option>
					</select>
				</li>

			</ul>
			<input type="submit" value="Save Slider" id="save-buttom" class="button button-primary button-large">
		</div>
	</div>
	</form>
<?php
}
function hugeit_slider_options_slider_styles() {
    require_once("slider_Options.php");
    require_once("slider_Options.html.php");

    hugeit_slider_show_styles();
}

/**
 * Huge IT Widget
 */
class Hugeit_Slider_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'hugeit_slider_widget',
			'Huge IT Slider',
			array( 'description' => __( 'Huge IT Slider', 'huge_it_slider' ), ) 
		);
	}

	public function widget( $args, $instance ) {

		extract($args);

		if (isset($instance['slider_id'])) {
			$slider_id = $instance['slider_id'];

			$title = apply_filters( 'widget_title', $instance['title'] );
/**
 * @var $before_widget
 * @var $after_title
 * @var $before_title
 * @var $after_widget
 */
			echo $before_widget;
			if ( ! empty( $title ) )
				echo $before_title . $title . $after_title;

			echo do_shortcode("[huge_it_slider id={$slider_id}]");
			echo $after_widget;
		}
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['slider_id'] = strip_tags( $new_instance['slider_id'] );
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	public function form( $instance ) {
		$selected_slider = 0;
		$title = "";
		$sliders = false;

		if (isset($instance['slider_id'])) {
			$selected_slider = $instance['slider_id'];
		}

		if (isset($instance['title'])) {
			$title = $instance['title'];
		}
		?>
		<p>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<label for="<?php echo $this->get_field_id('slider_id'); ?>"><?php _e('Select Slider:', 'huge_it_slider'); ?></label>
			<select id="<?php echo $this->get_field_id('slider_id'); ?>" name="<?php echo $this->get_field_name('slider_id'); ?>">

			<?php
			global $wpdb;
			$query="SELECT * FROM ".$wpdb->prefix."huge_itslider_sliders ";
			$rowwidget=$wpdb->get_results($query);
			foreach($rowwidget as $rowwidgetecho) : ?>
				<option <?php if($rowwidgetecho->id == $instance['slider_id']){ echo 'selected'; } ?> value="<?php echo $rowwidgetecho->id; ?>"><?php echo $rowwidgetecho->name; ?></option>
			<?php endforeach; ?>
			</select>
		</p>

		<?php 
	}
}
add_action('widgets_init', 'hugeit_slider_register_widget' );
function hugeit_slider_register_widget() {
    register_widget('Hugeit_Slider_Widget');
}

/***<add>***/

require_once 'slider2.php';