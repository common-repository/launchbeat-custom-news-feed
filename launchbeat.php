<?php
/*
Plugin Name: Launchbeat
Plugin URI: http://launchbeat.com/wordpress
Description: Allows you to integrate a launchbeat feed with your blog
Version: 1.1
License: GPL
Author: Launchbeat.com
Author URI: http://launchbeat.com
*/

function get_launchbeat() 
{
  	for($i = 0 ; $i < func_num_args(); $i++) 
	{
	    	$args[] = func_get_arg($i);
    	}

  	if (!isset($args[0])) $feedName = get_option('launchbeat_display_name'); else $feedName = $args[0];
  	if (!isset($args[1])) $feedLength = get_option('launchbeat_display_length'); else $feedLength = $args[1];
        
	if (!function_exists('MagpieRSS')) 
	{
		include_once (ABSPATH . WPINC . '/rss.php');
		error_reporting(E_ERROR);
	}

	$rss_url = 'http://www.launchbeat.com/xml.php?n=' . urlencode($feedName) . '&m=' . urlencode($feedLength);
	
	$rss = @ fetch_rss($rss_url);

	if ($rss) 
	{
		$items = array_slice($rss->items, 0, $feedLength);
		print "<ul>";
		foreach ($items as $item ) 
		{
			$title = htmlspecialchars(stripslashes($item['title']));
			$description = htmlspecialchars(stripslashes($item['description']));
			$url = $item['link'];
                	print "<li><a href=\"$url\" title=\"$description\">$title</a></li>";
		} 
		print "</ul>";
  	}
}

function widget_launchbeat_init() 
{
	if (!function_exists('register_sidebar_widget')) return;

	function widget_launchbeat($args) 
	{		
		extract($args);

		$options = get_option('widget_launchbeat');
		$title = $options['title'];
		$name = $options['name'];
		$length = $options['length'];

		echo $before_widget . $before_title . $title . $after_title;
		get_launchbeat($name, $length);
		echo $after_widget;
	}

	function widget_launchbeat_control() 
	{
		$options = get_option('widget_launchbeat');
		if ( !is_array($options) )
		{
			$options = array('title'=>'');
		}
		if ( $_POST['launchbeat-submit'] ) 
		{
			$options['title'] = strip_tags(stripslashes($_POST['launchbeat-title']));
			$options['name'] = strip_tags(stripslashes($_POST['launchbeat-name']));
			$options['length'] = strip_tags(stripslashes($_POST['launchbeat-length']));
			update_option('widget_launchbeat', $options);
		}

		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$name = htmlspecialchars($options['name'], ENT_QUOTES);	
		$length = htmlspecialchars($options['length'], ENT_QUOTES);
	
		echo '<p style="text-align:right;"><label for="launchbeat-title">Title: <input style="width: 200px;" id="gsearch-title" name="launchbeat-title" type="text" value="'.$title.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="launchbeat-name">Feed name: <input style="width: 200px;" id="gsearch-title" name="launchbeat-name" type="text" value="'.$name.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="launchbeat-length">Articles: ';
		echo '<select name="launchbeat-length">';
		echo '<option '; if($length == '5') { echo 'selected '; } echo 'value="5">5</option>';
		echo '<option '; if($length == '10') { echo 'selected '; } echo 'value="10">10</option>';
		echo '<option '; if($length == '20') { echo 'selected '; } echo 'value="20">20</option>';
		echo '<option '; if($length == '30') { echo 'selected '; } echo 'value="30">30</option>';
		echo '<option '; if($length == '50') { echo 'selected '; } echo 'value="50">50</option>';
		echo '<option '; if($length == '100') { echo 'selected '; } echo 'value="100">100</option>';
		echo '</select></p>';
		echo '<input type="hidden" id="launchbeat-submit" name="launchbeat-submit" value="1" />';
	}		

	register_sidebar_widget('Launchbeat', 'widget_launchbeat');
	register_widget_control('Launchbeat', 'widget_launchbeat_control', 300, 100);
}

function launchbeat_subpanel() 
{
	if (isset($_POST['save_launchbeat_options'])) 
	{
		$option_display_name = $_POST['display_name'];
		$option_display_length = $_POST['display_length'];
		update_option('launchbeat_display_name', $option_display_name);
		update_option('launchbeat_display_length', $option_display_length);
		?> <div class="updated"><p>Options changes saved.</p></div> <?php
	}
	?>

	<div class="wrap">
		<h2>Launchbeat Options</h2>

		<br/>Note 1: The plugin will attempt to cache the feed. Please don't be upset if it takes a few minutes for your feed changes to be reflected.
		<br/>
		<br/>Note 2: If you use launchbeat as a widget, the options below will be overridden by the widget control selections.<br/><br/>

		<form method="post">
		
		<fieldset class="options">
		<table>
		 <tr>
		  <td><p><strong><label for="launchbeat_name">Feed name</label>:</strong></p></td>
		  <td><input name="display_name" type="text" id="launchbeat_name" value="<?php echo get_option('launchbeat_display_name'); ?>" size="20" /></p></td>
                 </tr>
                <tr>
          	<td><p><strong>Number of Articles:</strong></p></td>
          	<td>
        	<select name="display_length" id="display_length">
        	  <option <?php if(get_option('launchbeat_display_length') == '5') { echo 'selected'; } ?> value="5">5</option>
		  <option <?php if(get_option('launchbeat_display_length') == '10') { echo 'selected'; } ?> value="10">10</option>
		  <option <?php if(get_option('launchbeat_display_length') == '20') { echo 'selected'; } ?> value="20">20</option>
		  <option <?php if(get_option('launchbeat_display_length') == '30') { echo 'selected'; } ?> value="30">30</option>
		  <option <?php if(get_option('launchbeat_display_length') == '50') { echo 'selected'; } ?> value="50">50</option>
		  <option <?php if(get_option('launchbeat_display_length') == '100') { echo 'selected'; } ?> value="100">100</option>
		</select>
           </td> 
         </tr>        
	<tr><td colspan=2><br/><a href="http://www.launchbeat.com">Hosted by launchbeat.com</a></td></tr>
         </table>
        </fieldset>
		<p><div class="submit"><input type="submit" name="save_launchbeat_options" value="<?php _e('Save Options', 'save_launchbeat_options') ?>"  style="font-weight:bold;" /></div></p>
        </form>       
    </div>

<?php } 

function fR_admin_menu() 
{
	if (function_exists('add_options_page')) 
	{
		add_options_page('launchbeat Options Page', 'Launchbeat', 8, basename(__FILE__), 'launchbeat_subpanel');
        }
}

add_action('admin_menu', 'fR_admin_menu'); 
add_action('plugins_loaded', 'widget_launchbeat_init');
?>
