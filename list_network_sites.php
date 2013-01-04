<?php
/**
 * @package List_Network_Sites
 * @version 0.1.1
 */
/*
Plugin Name: List Network Sites
Plugin URI: https://github.com/patproct/List-Network-Sites
Description: This plugin features a widget that will display a list of the sites in the Network in which it is installed.
Author: Patrick Proctor
Version: 0.1.1
Author URI: https://patrickjproctor.com/
*/

/**
* List Network Sites
*/
class LNSWidget extends WP_Widget
{
	function __construct() {
		parent::__construct(
			'lns_widget', // Base ID
			'LNS Widget', // Name
			array( 'description' => __('LNS Widget', 'text_domain'), ) // Args
		);
	}
	/**
	 * Front-end display of widget.
	 *
	 * @see LNSWidget::widget()
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
		echo __( $this->get_all_sites(), 'text_domain' );
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see LNSWidget::update()
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
	 * @see LNSWidget::form()
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
	
	/**
	  * Get all sites
	  *
	  * @see LNSWidget::get_all_sites()
	  *
	  * @param none
	  */
	public function get_all_sites() {

		global $wpdb;

		// Query all blogs from multi-site install
		$blogs = $wpdb->get_results("SELECT blog_id,domain,path FROM wp_blogs where blog_id > 0 ORDER BY blog_id ASC");
		
		// Start unordered list
		$list = '<ul style="padding:0;">';
		
		// For each blog search for blog name in respective options table
		foreach( $blogs as $blog ) {
			$blog_id = ($blog->blog_id <= 1) ? '' : $blog->blog_id.'_';
			// Query for name from options table
			$blogname = $wpdb->get_results("SELECT option_value FROM wp_".$blog_id ."options WHERE option_name='blogname' ");
			foreach( $blogname as $name ) { 

				// Create bullet with name linked to blog home page
				$list .= '<li>';
				$list .= '<a title="Visit '.$name->option_value.'" href="http://';
				$list .= $blog->domain;
				$list .= $blog -> path;
				$list .= '">';
				$list .= $name->option_value;
				$list .= '</a></li>';

			}
		}

		// End unordered list
		$list .= '</ul>';
		return $list;
	}
}

function registerLNSWidget() {
	// register LNSWidget widget
	register_widget('LNSWidget');
}

add_action('admin_notices','get_all_sites');
add_action( 'widgets_init', create_function( '', 'register_widget( "LNSwidget" );' ) );
?>