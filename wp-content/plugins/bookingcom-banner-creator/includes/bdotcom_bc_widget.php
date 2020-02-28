<?php
/**
 * WIDGET SECTION
 * ----------------------------------------------------------------------------
 */
// use widgets_init action hook to execute custom function
add_action( 'widgets_init', 'bdotcom_register_widgets' );
//register our widget
function bdotcom_register_widgets( ) {
                register_widget( 'bdotcom_widget' );
}
//Since WP version 4.3 use construct for widget
/*if ( version_compare( BDOTCOM_BC_WP_VERSION, '4.3', '<' ) ) {
                class bdotcom_widget extends WP_Widget {
                                //process the new widget
                                function bdotcom_widget( ) {
                                                $widget_ops = array(
                                                                 'classname' => 'bdotcom_widget',
                                                                'description' => BDOTCOM_BC_PLUGIN_WIDGET_DESCR 
                                                );
                                                $this->WP_Widget( 'bdotcom_widget', BDOTCOM_BC_PLUGIN_NAME, $widget_ops );
                                }
                                function form( $instance ) {
                                                //here we need just the post/map aid and we're done, we can create the shortcode       
                                                //Create da loop through the maps post type    
                                                $loop = new WP_Query( array(
                                                                 'post_type' => 'bdotcom_bm' 
                                                ) );
                                                if ( $loop->have_posts() ) {
?>
            
                                                                <select name="<?php echo $this->get_field_name( 'bdotcom_bannerid' ); ?>">
                                                                                <option value="no_choice" <?php
                                                                                                if ( isset( $instance[ 'bdotcom_bannerid' ] ) ) {
                                                                                                                selected( $instance[ 'bdotcom_bannerid' ], 'no_choice' );
                                                                                                } //isset( $instance[ 'bdotcom_bannerid' ] )
                                                                                                ?>><?php _e("Select one banner...", BDOTCOM_BC_TEXT_DOMAIN ); ?>
                                                                                </option>
            
                                                                <?php
                                                                                while ( $loop->have_posts() ) {
                                                                                                $loop->the_post();
                                                                ?>
                                                                                                <option value="<?php echo get_the_ID(); ?>" 
                                                                                                                <?php
                                                                                                                if ( isset( $instance[ 'bdotcom_bannerid' ] ) ) {
                                                                                                                                selected( $instance[ 'bdotcom_bannerid' ], get_the_ID() );
                                                                                                                } //isset( $instance[ 'bdotcom_bannerid' ] )
                                                                                                                ?>> <?php the_title(); ?> </option>
                                
                                                                <?php
                                                                                } //while ( $loop->have_posts() )
                                                                ?>
                            
                                                                </select>         
           
                                 <?php
                                                } //if ( $loop->have_posts() )     
                                } //function form ( $instance )
                                function update( $new_instance, $old_instance ) {
                                                $instance                 = $old_instance;
                                                $instance[ 'bdotcom_bannerid' ] = $new_instance[ 'bdotcom_bannerid' ];
                                                return $instance;
                                }
                                //display the widget only if a map was selected
                                function widget( $args, $instance ) {
                                                if ( $instance[ 'bdotcom_bannerid' ] != 'no_choice' ) {
                                                                extract( $args );
                                                                echo $before_widget;
                                                                $bdotcom_mbe_width = get_post_meta( $instance[ 'bdotcom_bannerid' ], '_bdotcom_mbe_width', true );
                                                                $bdotcom_mbe_width = !empty( $bdotcom_mbe_width ) ? ' width="' . $bdotcom_mbe_width . '"' : '';
                                                                // Use the shortcode to generate the map                
                                                                echo do_shortcode( '[bdotcom_bm bannerid="' . $instance[ 'bdotcom_bannerid' ] . '" ]' );
                                                                echo $after_widget;
                                                } //$instance[ 'bdotcom_bannerid' ] != 'no_choice'
                                }
                }
} //version_compare( BDOTCOM_BC_PLUGIN_VERSION, '4.3', '<' )
else {*/
                class bdotcom_widget extends WP_Widget {
                                //process the new widget
                                function __construct( ) {
                                                parent::__construct( 'bdotcom_widget', // Base ID
                                                                BDOTCOM_BC_PLUGIN_NAME, // Name
                                                                array(
                                                                 'description' => BDOTCOM_BC_PLUGIN_WIDGET_DESCR,
                                                                'classname' => 'bdotcom_widget' 
                                                ) // Args
                                                                );
                                }
                                function form( $instance ) {
                                                //here we need just the post/map aid and we're done, we can create the shortcode       
                                                //Create da loop through the maps post type    
                                                $loop = new WP_Query( array(
                                                                 'post_type' => 'bdotcom_bm' 
                                                ) );
                                                if ( $loop->have_posts() ) {
?>
            
                                                                <select name="<?php
                                                                                echo $this->get_field_name( 'bdotcom_bannerid' ); ?>">
                                                                                <option value="no_choice" <?php
                                                                                if ( isset( $instance[ 'bdotcom_bannerid' ] ) ) {
                                                                                                selected( $instance[ 'bdotcom_bannerid' ], 'no_choice' );
                                                                                } //isset( $instance[ 'bdotcom_bannerid' ] )
                                                                                ?>><?php _e("Select one banner...", BDOTCOM_BC_TEXT_DOMAIN ); ?></option>
            
                                                                <?php
                                                                                while ( $loop->have_posts() ) {
                                                                                                $loop->the_post();
                                                                ?>
                                                                                                <option value="<?php echo get_the_ID(); ?>" <?php
                                                                                                if ( isset( $instance[ 'bdotcom_bannerid' ] ) ) {
                                                                                                                selected( $instance[ 'bdotcom_bannerid' ], get_the_ID() );
                                                                                                } //isset( $instance[ 'bdotcom_bannerid' ] )
                                                                                                ?>> <?php the_title(); ?> </option>
                
                                                                <?php
                                                                                } //while ( $loop->have_posts() )
                                                                ?>
            
                                                                </select>         
           
                                <?php
                                                } //if ( $loop->have_posts() )     
                                } //function form ( $instance )
                                function update( $new_instance, $old_instance ) {
                                                $instance                 = $old_instance;
                                                $instance[ 'bdotcom_bannerid' ] = $new_instance[ 'bdotcom_bannerid' ];
                                                return $instance;
                                }
                                //display the widget only if a map was selected
                                function widget( $args, $instance ) {
                                                if ( $instance[ 'bdotcom_bannerid' ] != 'no_choice' ) {
                                                                extract( $args );
                                                                echo $before_widget;                                                              
                                                               
                                                                // Use the shortcode to generate the map                
                                                                echo do_shortcode( '[bdotcom_bm bannerid="' . $instance[ 'bdotcom_bannerid' ] . '" ]' );
                                                                echo $after_widget;
                                                } //$instance[ 'bdotcom_bannerid' ] != 'no_choice'
                                }
                }
//}
?>