<?php
/**
 * Plugin Name: Random Maze Widget
 * Plugin URI: http://thomasmottl.com
 * Description: A widget that displays a randomly generated maze for the user to play.
 * Version: 1.0
 * Author: Thomas Mottl
 * Author URI: http://thomasmottl.com
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
class MazeWidget extends WP_Widget {

	public function __construct() {
		parent::__construct('rmaze', 'Random Maze', array('description' => __('Creates a randomly generated maze for the user.', 'text_domain') ) );
	}

 	public function form( $instance ) {
		if (isset( $instance['title'])) {
			$title = $instance['title'];
		} else {
			$title = __( 'Random Maze', 'text_domain' );
		}
		if (isset( $instance['size'])) {
			$size = $instance['size'];
		} else {
			$size = __( '16x16', 'text_domain' );
		}
		if (isset( $instance['padding'])) {
			$padding = $instance['padding'];
		} else {
			$padding = __( '8', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p><p>
		<label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Size (9x9 to 50x50):' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'size' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>" type="text" value="<?php echo esc_attr( $size ); ?>" />
		</p><p>
		<label for="<?php echo $this->get_field_id( 'padding' ); ?>"><?php _e( 'Line Distance (2 to 15):' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'padding' ); ?>" name="<?php echo $this->get_field_name( 'padding' ); ?>" type="text" value="<?php echo esc_attr( $padding ); ?>" />
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {

		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		$size = trim($new_instance['size']);
		$oldsize = trim($old_instance['size']);
		$pattern = '/([0-9]{1,2})x([0-9]{1,2})/i';


		$padding_new = trim($new_instance['padding']);
		$padding_old = trim($old_instance['padding']);

		
		if(preg_match($pattern, $size, $matches)!==false) {
			$x = intval($matches[1]);
			$y = intval($matches[2]);
			$padding_new = intval($padding_new);
			if($x>8 && $y>8 && $x<51 && $y<51 && $padding_new>1 && $padding_new<16) {
				$instance['size'] = $size;
				$instance['padding'] = $padding_new;
			} else if(preg_match($pattern, $oldsize)) {
				$instance['size'] = $oldsize;
				$instance['padding'] = $padding_old;
			} else {
				$instance['size'] = '16x16';
				$instance['padding'] = '8';
			}
		} else if(preg_match($pattern, $oldsize)!==false) {
			$instance['size'] = $oldsize;
			$instance['padding'] = $padding_old;
		} else {
			$instance['size'] = '16x16';
			$instance['padding'] = '8';

		}
		return $instance;
	}

	public function widget( $args, $instance ) {
		
		extract($args);

		$title = apply_filters('widget_title', $instance['title'] );
                $size = trim($instance['size']);
                $padding = trim($instance['padding']);
		if($size=='')
			$size = '16x16';
		if($padding=='')
			$padding = '8';

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		echo '<img alt="A randomly generated maze" src="'.plugin_dir_url( __FILE__ ).'mazer.php?size='.$size.'x'.$padding.'" />';
		echo $after_widget;
	}

}
add_action('widgets_init', 'add_maze_widget');
function add_maze_widget() {
	register_widget('MazeWidget');
}