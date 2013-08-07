<?php 

class Widget_title extends WP_Widget { 

	/** constructor */
	function __construct() {
		parent::WP_Widget( /* Base ID */'widget_title', /* Name */'Widget_title', array( 'description' => 'Ein Widget, dass Titel in Überschriften-Tag anzeigen zu lassen.' ) );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$widgettitle = apply_filters( 'widget_title', $instance['widgettitle'] );
		$emailid = apply_filters( 'widget_title', $instance['emailid'] );
		if($widgettitle)
			echo '<h2>'.$widgettitle.'</h2>';
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['widgettitle'] = strip_tags($new_instance['widgettitle']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( $instance ) {$widgettitle = esc_attr( $instance[ 'widgettitle' ] );}
		else {$widgettitle = __( '', 'text_domain' );}
		?>
		<p>
        <label for="<?php echo $this->get_field_id('widgettitle'); ?>"><?php _e('Titel eingeben:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('widgettitle'); ?>" name="<?php echo $this->get_field_name('widgettitle'); ?>" type="text" value="<?php echo $widgettitle; ?>" />	
		</p>
		<?php 
	}
	
} // class Widget_title


add_action( 'widgets_init', 'twentyten_widgets_init' );
add_action( 'widgets_init', create_function( '', 'register_widget("Widget_title");' ) ); ?>