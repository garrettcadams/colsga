<?php
class Lava_Contact_Single_Page extends WP_Widget
{
	public $post_type;

	private static $load_script;

	function __construct()
	{
		parent::__construct(
			'Lava_Contact_Single_Page',
			__( "[Lava] Author information", 'Lavacode' ),
			array(
				'description' => __( "This is for only single detail pages. This widget shows the requested information about the author of the listing.", 'Lavacode' ),
			)
		);

		$this->post_type = lava_directory()->core->slug;
		add_action( 'wp_footer', Array( __CLASS__, 'scripts' ) );
	}

	public function widget( $args, $instance )
	{
		global $post;

		self::$load_script	= true;

		if( empty( $post ) ) {
			echo '<h4>' . __( "Invalid post ID.", 'Lavacode') . '</h4>';
			return;
		}

		if( $post->post_type != $this->post_type )
			return;

		$instance = apply_filters( 'lava_single_contact_widget_instance', $instance, $this );

		$lava_contact_shortcode	= $lava_report_shortcode	= '';
		if( isset( $instance['contact_type'] ) && isset( $instance['contact_id'] ) )
		{
			switch( $instance[ 'contact_type' ] ) {
				case 'contact'	: $lava_contact_shortcode = '[contact-form-7 id=%s title="%s"]'; break;
				case 'ninja'	: $lava_contact_shortcode = '[ninja_forms id=%s title="%s"]'; break;
			}
		}

		if( isset( $instance['report_type'] ) && isset( $instance['report_id'] ) )
		{
			switch( $instance[ 'report_type' ] ) {
				case 'contact'	: $lava_report_shortcode = '[contact-form-7 id=%s title="%s"]'; break;
				case 'ninja'	: $lava_report_shortcode = '[ninja_forms id=%s title="%s"]'; break;
			}
		}
		$GLOBALS[ 'lava_contact_shortcode' ] = sprintf( $lava_contact_shortcode, $instance['contact_id'], __( 'Contact Form', 'Lavacode' ) );
		$GLOBALS[ 'lava_report_shortcode' ] = sprintf( $lava_report_shortcode, $instance['report_id'], __( 'Contact Form', 'Lavacode' ) );

		$output_filename		= basename( __FILE__ );

		if(
			! $template_file = locate_template(
				Array(
					$output_filename,
					lava_directory()->folder . '/' . $output_filename,
				)
			)
		){
			$template_file = dirname( __FILE__ ) . "/html/{$output_filename}";
		}
		ob_start();
			echo isset( $args[ 'before_widget' ] ) ? $args[ 'before_widget' ] : '';
			require_once $template_file;
			echo isset( $args[ 'after_widget' ] ) ?$args[ 'after_widget' ]  : '';
		ob_end_flush();
	}

	public static function scripts()
	{
		if( ! self::$load_script )
			return;
		wp_enqueue_script( 'lava-directory-manager-jquery-lava-msg-js' );
	}

	public function form( $instance )
	{
		$lava_wg_fields					= Array(

			'contact_widget_title'				=>
				Array(
					'label'				=> __( "Title", 'Lavacode' )
					, 'type'			=> 'text',
					//, 'value'			=> 'CONTACT',
				)
			, 'separate'

			, 'contact_type'				=>
				Array(
					'label'				=> __( "Form Type", 'Lavacode' )
					, 'type'			=> 'radio'
					, 'value'			=>
						Array(
							''			=> __( "None", 'Lavacode' )
							, 'ninja'	=> __( "Ninja Form", 'Lavacode' )
							, 'contact'	=> __( "Contact Form", 'Lavacode' )

						)
				)
			, 'contact_id'				=>
				Array(
					'label'				=> __( "Form ID", 'Lavacode' )
					, 'type'			=> 'number'
				)
			, 'contact_btn_label'				=>
				Array(
					'label'				=> __( "Button Label", 'Lavacode' )
					, 'type'			=> 'text'
				)				
			, 'separate'

			, 'report_type'				=>
				Array(
					'label'				=> __( "Report Type", 'Lavacode' )
					, 'type'			=> 'radio'
					, 'value'			=>
						Array(
							''			=> __( "None", 'Lavacode' )
							, 'ninja'	=> __( "Ninja Form", 'Lavacode' )
							, 'contact'	=> __( "Contact Form", 'Lavacode' )

						)
				)
			, 'report_id'				=>
				Array(
					'label'				=> __( "Form ID", 'Lavacode' )
					, 'type'			=> 'number'
				)
			, 'report_btn_label'				=>
				Array(
					'label'				=> __( "Button Label", 'Lavacode' )
					, 'type'			=> 'text'
				)				
		);

		$output_html			= Array();

		if( !empty( $lava_wg_fields ) )
		{
			foreach( $lava_wg_fields as $id => $options )
			{
				if( $options === 'separate' ) {
					$output_html[]	= "<hr>"; continue;
				}
				$values				= isset( $instance[ $id ] ) ? esc_attr( $instance[ $id ] ) : null;
				$output_html[]		= "<p>";
				$output_html[]		= "<label for=\"" . $this->get_field_id( $id ) . "\">{$options['label']}</label>";

				switch( $options['type'] )
				{
					case 'radio':
						if( !empty( $options['value'] ) )
							foreach( $options['value'] as $value => $label )
								$output_html[]	= "<input id=\"" . $this->get_field_id( $id ) . "\" name=\"" . $this->get_field_name( $id ) . "\" type=\"{$options['type']}\" value=\"{$value}\"" . checked( $values == $value, true, false ) . "> {$label}";
					break;

					case 'number':
					case 'text':
					default:
						$output_html[]	= "<input id=\"" . $this->get_field_id( $id ) . "\" name=\"" . $this->get_field_name( $id ) . "\" type=\"{$options['type']}\" value=\"{$values}\">";
				}

				$output_html[]	= "</p>";
			}

			echo @implode( "\n", $output_html );
		}
	}

	public function update( $new_instance, $old_instance ) { return $new_instance; }

} // class Foo_Widget