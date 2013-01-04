<?php
/**
 * @package List_Network_Sites
 * @version 0.1.0
 */
/*
Plugin Name: List Network Sites
Plugin URI: http://github.com/patproct/
Description: This plugin should do what I tell it.
Author: Matt Mullenweg
Version: 0.1.0
Author URI: http://github.com/patproct/
*/

/**
* List Network Sites
*/
class LNSWidget extends WP_Widget
{
	function __construct() {
		parent::__construct(
			'lns_widget', // Base ID
			'LNS_Widget', // Name
			array( 'description' => __('LNS_Widget', 'text_domain'), ) // Args
		);
	}
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		echo __( 'Hello, World!', 'text_domain' );
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
}


function get_all_sites() {

	global $wpdb;

	// Query all blogs from multi-site install
	$blogs = $wpdb->get_results("SELECT blog_id,domain,path FROM wp_blogs where blog_id > 1 ORDER BY path");

	// Start unordered list
	echo '<ul>';

	// For each blog search for blog name in respective options table
	foreach( $blogs as $blog ) {

		// Query for name from options table
		$blogname = $wpdb->get_results("SELECT option_value FROM wp_".$blog->blog_id ."_options WHERE option_name='blogname' ");
		foreach( $blogname as $name ) { 

			// Create bullet with name linked to blog home pag
			echo '<li>';
			echo '<a href="http://';
			echo $blog->domain;
			echo $blog -> path;
			echo '">';
			echo $name->option_value;
			echo '</a></li>';

		}
	}

	// End unordered list
	echo '</ul>';
}

add_action('admin_notices','get_all_sites');
?>