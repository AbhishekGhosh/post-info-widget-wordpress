<?php
/*
Plugin Name: Post Info Widget
Plugin URI: http://thecustomizewindows.com/
Description: Like the Executable PHP widget, it has some default functions. Heavily derived from the Executable PHP widget by Otto in WordPress.
Author: Abhishek_Ghosh
Version: 1.0
Author URI: http://thecustomizewindows.com/
License: GPL3

    Copyleft 2013  Dr. Abhishek Ghosh  (email : me@abhishekghosh.pro)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 3, 
    as published by the Free Software Foundation. 
    
    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    The license for this software can likely be found here: 
    http://www.gnu.org/licenses/gpl-3.0.html
    
*/

class Post_Info_Widget extends WP_Widget {

	function Post_Info_Widget() {
		$widget_ops = array('classname' => 'widget_execphp', 'description' => __('Arbitrary text, HTML, or PHP Code'));
		$control_ops = array('width' => 400, 'height' => 350);
		$this->WP_Widget('execphp', __('PHP Code'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
		$text = apply_filters( 'widget_execphp', $instance['text'], $instance );
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } 
			ob_start();
// Edit after this section 
// we asked your WordPress to get the title and description from your given text
// You can add any WordPress function to execute the result in front end
// $title, $text and others are given here for example
// the <!-- Post Info Widget Starts http://thecustomizewindows.com/ --> will showup in frontend as it is outside the loop
			eval('?>'.$text);
			$text = ob_get_contents();
			ob_end_clean();
			?>
	<!-- Post Info Widget Starts http://thecustomizewindows.com/ -->

			<div class="execphpwidget"><?php echo $instance['filter'] ? wpautop($text) : $text; ?></div>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( $new_instance['text'] ) );
		$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$text = format_to_edit($instance['text']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs.'); ?></label></p>
<?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("Post_Info_Widget");'));

// donate link on manage plugin page
add_filter('plugin_row_meta', 'execphp_donate_link', 10, 2);
function execphp_donate_link($links, $file) {
	if ($file == plugin_basename(__FILE__)) {
		$donate_link = 'URL here">Donate</a>';
		$links[] = $donate_link;
	}
	return $links;
}
