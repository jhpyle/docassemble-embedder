<?php
/*
   Plugin Name: Docassemble interview embedder
   Plugin URI: https://docassemble.org
   Description: This plugin allows you to insert a docassemble interview into a Wordpress site.
   Version: 0.0.0
   Author: Jonathan Pyle
   Author URI: https://github.com/jhpyle
   License: MIT
 */

class DocassembleSettingsPage
{
    private $options;

    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    public function add_plugin_page()
    {
        add_options_page(
            'Docassemble Settings',
            'Docassemble',
            'manage_options',
            'docassemble-settings-admin',
            array( $this, 'create_admin_page' )
        );
    }

    public function create_admin_page()
    {
        $this->options = get_option( 'docassemble_options' );
                ?>
                <div class="wrap">
                    <h1>Docassemble Settings</h1>
                    <form method="post" action="options.php">
                    <?php
        settings_fields( 'docassemble_options_group' );
        do_settings_sections( 'docassemble-settings-admin' );
        submit_button();
                    ?>
                    </form>
                </div>
                <?php
    }

    public function page_init()
    {
        register_setting(
            'docassemble_options_group',
            'docassemble_options',
            array( $this, 'sanitize' )
        );

        add_settings_section(
            'setting_section_id',
            'Docassemble embedder settings',
            array( $this, 'print_section_info' ),
            'docassemble-settings-admin'
        );

        add_settings_field(
            'server_url',
            'Docassemble server',
            array( $this, 'server_url_callback' ),
            'docassemble-settings-admin',
            'setting_section_id'
        );

        add_settings_field(
            'style',
            'Default style for embedded interviews',
            array( $this, 'style_callback' ),
            'docassemble-settings-admin',
            'setting_section_id'
        );

        add_settings_field(
            'mode',
            'Type of embedding',
            array( $this, 'mode_callback' ),
            'docassemble-settings-admin',
            'setting_section_id'
        );

        add_settings_field(
            'wide',
            'Whether interview should fill width (if direct embedding) ',
            array( $this, 'wide_callback' ),
            'docassemble-settings-admin',
            'setting_section_id'
        );

        add_settings_field(
            'hide',
            'Whether interview navigation bar should be hidden (if direct embedding)',
            array( $this, 'hide_callback' ),
            'docassemble-settings-admin',
            'setting_section_id'
        );

        add_settings_field(
            'shortcode',
            'Whether the docassemble widge is embedded using a short code (if direct embedding)',
            array( $this, 'shortcode_callback' ),
            'docassemble-settings-admin',
            'setting_section_id'
        );

        add_settings_field(
            'bootstrap',
            'Bootstrap CSS URL (if direct embedding)',
            array( $this, 'bootstrap_callback' ),
            'docassemble-settings-admin',
            'setting_section_id'
        );
    }

    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['server_url'] ) ){
            $new_input['server_url'] = $input['server_url'];
        }

        if( isset( $input['style'] ) ){
            $new_input['style'] = $input['style'];
        }

        if( isset( $input['mode'] ) ){
            $new_input['mode'] = $input['mode'];
        }

        if( isset( $input['wide'] ) ){
            $new_input['wide'] = $input['wide'];
        }
        else{
            $new_input['wide'] = FALSE;
        }

        if( isset( $input['hide'] ) ){
            $new_input['hide'] = $input['hide'];
        }
        else{
            $new_input['hide'] = FALSE;
        }

        if( isset( $input['shortcode'] ) ){
            $new_input['shortcode'] = $input['shortcode'];
        }
        else{
            $new_input['shortcode'] = FALSE;
        }

        if( isset( $input['bootstrap'] ) ){
            $new_input['bootstrap'] = $input['bootstrap'];
        }

        return $new_input;
    }

    public function print_section_info()
    {
        //print 'Docassemble global settings:';
    }

    public function server_url_callback()
    {
        printf(
            '<input type="text" id="server_url" name="docassemble_options[server_url]" value="%s" style="width: 100%%;"/>',
            isset( $this->options['server_url'] ) ? esc_attr( $this->options['server_url']) : 'https://demo.docassemble.org'
        );
    }

    public function style_callback()
    {
        printf(
            '<input type="text" id="style" name="docassemble_options[style]" value="%s" style="width: 100%%;"/>',
            isset( $this->options['style'] ) ? esc_attr( $this->options['style']) : 'border-style: solid; border-width: 1px; border-color: #aaa; width: 100%; min-height: 95vh;'
        );
    }

    public function mode_callback()
    {
        if (isset( $this->options['mode'] ) && $this->options['mode'] == 'div'){
            $div_checked = ' checked';
            $iframe_checked = '';
        }
        else{
            $div_checked = '';
            $iframe_checked = ' checked';
        }
        print '<input type="radio" name="docassemble_options[mode]" value="iframe"' . $iframe_checked . '> Iframe<br><input type="radio" name="docassemble_options[mode]" value="div"' . $div_checked . '> Direct embedding<br>Only choose direct embedding if you know what you are doing.';
    }

    public function wide_callback()
    {
        if (isset( $this->options['wide'] ) && $this->options['wide'] == 'wide'){
            $checked = ' checked';
        }
        else{
            $checked = '';
        }
        print '<input type="checkbox" name="docassemble_options[wide]" value="wide"' . $checked . '> Interview should fill the width of the container, as if on a small screen (recommended unless the content area is full width)';
    }

    public function hide_callback()
    {
        if (isset( $this->options['hide'] ) && $this->options['hide'] == 'hide'){
            $checked = ' checked';
        }
        else{
            $checked = '';
        }
        print '<input type="checkbox" name="docassemble_options[hide]" value="hide"' . $checked . '> Hide the navigation bar in the interview (recommended; also use the "question back button" feature in your interview)';
    }

    public function shortcode_callback()
    {
        if (isset( $this->options['shortcode'] ) && $this->options['shortcode'] == 'shortcode'){
            $checked = ' checked';
        }
        else{
            $checked = '';
        }
        print '<input type="checkbox" name="docassemble_options[shortcode]" value="shortcode"' . $checked . '> You plan to insert the docassemble widget using a short code';
    }

    public function bootstrap_callback()
    {
        printf(
            '<input type="text" id="bootstrap" name="docassemble_options[bootstrap]" value="%s" style="width: 100%%;"/>',
            isset( $this->options['bootstrap'] ) ? esc_attr( $this->options['bootstrap']) : '/static/bootstrap/css/bootstrap.min.css'
        );
    }
}

if (is_admin()){
    $docassemble_settings_page = new DocassembleSettingsPage();
}

class Docassemble_Widget extends WP_Widget {

    public function __construct() {
                parent::__construct(
                        'docassemble_widget',
                        __( 'Docassemble widget', 'text_domain' ),
                        array(
                                'customize_selective_refresh' => true,
                        )
                );
        $global = get_option('docassemble_options');
        $shortcode = isset( $global['shortcode'] ) ? ($global['shortcode'] == 'shortcode' ? TRUE : FALSE) : FALSE;
        $mode      = isset( $global['mode'] ) ? $global['mode'] : 'iframe';
        if ( $mode == 'div' and ($shortcode or is_active_widget(false, false, $this->id_base)) ){
            add_action( 'init', array(&$this, 'docassemble_load_assets'));
        }
     }

    public function form( $instance ) {
        $global = get_option('docassemble_options');
        $defaults = array(
            'title'      => '',
            'interview'  => 'docassemble.demo:data/questions/questions.yml',
            'style'      => isset($global['style']) ? $global['style'] : 'border-style: solid; border-width: 1px; border-color: #aaa; width: 100%; max-height: 95vh;',
        );
        extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>
    <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'text_domain' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
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
        $instance['interview']  = isset( $new_instance['interview'] ) ? wp_strip_all_tags( $new_instance['interview'] ) : '';
        $instance['style']      = isset( $new_instance['style'] ) ? wp_strip_all_tags( $new_instance['style'] ) : '';
        return $instance;
    }

    public function widget( $args, $instance ) {
        extract( $args );
        $global = get_option('docassemble_options');

        $title      = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
        $mode       = isset( $global['mode'] ) ? $global['mode'] : 'iframe';
        $shortcode  = isset( $global['shortcode'] ) ? ($global['shortcode'] == 'shortcode' ? TRUE : FALSE) : FALSE;
        $wide       = isset( $global['wide'] ) ? ($global['wide'] ? ' dawide' : '') : '';
        $hide       = isset( $global['hide'] ) ? ($global['hide'] ? ' dahide-navbar' : '') : '';
        $server_url = isset( $global['server_url'] ) ? $global['server_url'] : 'https://demo.docassemble.org';
        $interview  = isset( $instance['interview'] ) ? $instance['interview'] : 'docassemble.demo:data/questions/questions.yml';
        $style      = isset( $instance['style'] ) ? $instance['style'] : (isset($global['style']) ? $global['style'] : 'border-style: solid; border-width: 1px; border-color: #aaa; width: 100%; max-height: 95vh;');

        echo $before_widget;

        echo '<div class="widget-text wp_widget_plugin_box">';

        if ( $title ) {
            echo $before_title . $title . $after_title;
        }
        if ($mode == 'iframe'){
            echo '<iframe style="' . $style . '" src="' . $server_url . '/interview?i=' . urlencode($interview) . '"></iframe>';
        }
        else{
            echo '<div id="dablock" class="dajs-embedded' . $wide . $hide . '" style="' . $style . '"></div>';
            wp_enqueue_script('da-launch', add_query_arg( array('i' => $interview, 'js_target' => 'dablock'), $server_url . '/interview'), array(), null, true);
        }
        echo '</div>';

        echo $after_widget;
    }

    public function docassemble_load_assets(){
        $global = get_option('docassemble_options');
        $server_url = isset( $global['server_url'] ) ? $global['server_url'] : 'https://demo.docassemble.org';
        $bootstrap = isset( $global['bootstrap'] ) ? $global['bootstrap'] : '/static/bootstrap/css/bootstrap.min.css';
        if ($bootstrap != ''){
            if (substr($bootstrap, 0, 1) == '/'){
                wp_enqueue_style('da-bootstrap-css', $server_url . $bootstrap, array(), null, 'all');
            }
            else{
                wp_enqueue_style('da-bootstrap-css', $bootstrap, array(), null, 'all');
            }
        }
        wp_enqueue_style('da-bundle-css', $server_url . '/static/app/bundle.css', array(), null, 'all');
        wp_enqueue_script('da-bundle-js', $server_url .'/static/app/bundlenojquery.js', array('jquery'), null, true);
    }
}

function docassemble_register_widget() {
    register_widget( 'Docassemble_Widget' );
}

add_action( 'widgets_init', 'docassemble_register_widget' );
