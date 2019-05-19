<?php
/*
   Plugin Name: Docassemble interview embedder
   Plugin URI: https://docassemble.org
   Description: This plugin allows you to insert a docassemble interview into a Wordpress site.
   Version: 1.0
   Author: Jonathan Pyle
   Author URI: https://github.com/jhpyle
   License: MIT
 */

class Docassemble_Widget extends WP_Widget {

    // Main constructor
    public function __construct() {
		parent::__construct(
			'docassemble_widget',
			__( 'Docassemble widget', 'text_domain' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
    }
    
    // The widget form (for the backend )
    public function form( $instance ) {
        $defaults = array(
            'title'      => '',
            'server_url' => 'https://demo.docassemble.org',
            'interview'  => 'docassemble.demo:data/questions/questions.yml',
            'style'      => 'border-style: solid; border-width: 1px; border-color: #aaa; width: 100%; height: 95vh;',
        );
        
        // Parse current settings with defaults
        extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>
    <p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'text_domain' ); ?></label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'server_url' ) ); ?>"><?php _e( 'Docassemble server URL:', 'text_domain' ); ?></label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'server_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'server_url' ) ); ?>" type="text" value="<?php echo esc_attr( $server_url ); ?>" />
    </p>
    <p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'interview' ) ); ?>"><?php _e( 'Interview name:', 'text_domain' ); ?></label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'interview' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'interview' ) ); ?>" type="text" value="<?php echo esc_attr( $interview ); ?>" />
    </p>
    <p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>"><?php _e( 'Iframe CSS style:', 'text_domain' ); ?></label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>" type="text" value="<?php echo esc_attr( $style ); ?>" />
    </p>
    
<?php
}

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title']      = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
        $instance['server_url'] = isset( $new_instance['server_url'] ) ? wp_strip_all_tags( $new_instance['server_url'] ) : '';
        $instance['interview']  = isset( $new_instance['interview'] ) ? wp_strip_all_tags( $new_instance['interview'] ) : '';
        $instance['style']      = isset( $new_instance['style'] ) ? wp_strip_all_tags( $new_instance['style'] ) : '';
        return $instance;
    }

    // Display the widget
    public function widget( $args, $instance ) {
        extract( $args );

        $title      = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
        $server_url = isset( $instance['server_url'] ) ? $instance['server_url'] : '';
        $interview  = isset( $instance['interview'] ) ?$instance['interview'] : '';
        $style      = isset( $instance['style'] ) ? $instance['style'] : '';

        // WordPress core before_widget hook (always include )
        echo $before_widget;

        // Display the widget
        echo '<div class="widget-text wp_widget_plugin_box">';

        // Display widget title if defined
        if ( $title ) {
            echo $before_title . $title . $after_title;
        }
        echo '<iframe style="' . $style . '" src="' . $server_url . '/interview?i=' . urlencode($interview) . '"></iframe>';
        echo '</div>';

        echo $after_widget;
    }
}

// Register the widget
function docassemble_register_widget() {
    register_widget( 'Docassemble_Widget' );
}
add_action( 'widgets_init', 'docassemble_register_widget' );
