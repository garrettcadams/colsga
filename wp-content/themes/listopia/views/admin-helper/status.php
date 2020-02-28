<?php
function jvbpd_get_demoStatus(){
	global $wpdb;
	return $wpdb->get_var(
		$wpdb->prepare(
			"select ID from $wpdb->posts where post_type='%s' and post_status='%s'",
			apply_filters( 'jvbpd_core_post_type', 'post' ),
			'publish'
		)
	);
}

$arrGuideSections	= Array(
	'plugin-actived'		=> Array(
		'label'				=> esc_html__( "Active Requested Plugins", 'jvbpd' ),
		'active'			=> true,
		'passed'			=> apply_filters(
			'jvbpd_helper_require_plugins_pass',
			class_exists( 'Vc_Manager' ) &&
			class_exists( 'Ultimate_VC_Addons' )
			),
		'plugins' => apply_filters(
			'jvbpd_helper_require_plugins',
			Array(
				'Vc_Manager' => esc_html__( "WPBakery Visual Composer", 'jvbpd' ),
				'Ultimate_VC_Addons' => esc_html__( "Ultimate Addons for Visual Composer", 'jvbpd' )
			)
		)
	),
	'demo-installed'	=> Array(
		'label'				=> esc_html__( "Demo Installation", 'jvbpd' )
		, 'passed'			=> jvbpd_get_demoStatus()
	),
	'permalink'			=> Array(
		'label'				=> esc_html__( "Permalink Setup", 'jvbpd' )
		, 'passed'			=> strstr( get_option( 'permalink_structure' ), 'postname' )
	),
	'widget-installed'	=> Array(
		'label'				=> esc_html__( "Widget Installation", 'jvbpd' )
		, 'passed'			=> is_active_sidebar( 'lava-lv_listing-single-sidebar' )
	),
	'menu-installed'	=> Array(
		'label'				=> esc_html__( "Menu Setup", 'jvbpd' )
		, 'passed'			=> has_nav_menu( 'primary' )
	),
	'static-installed'	=> Array(
		'label'				=> esc_html__( "Front Page Setup", 'jvbpd' )
		, 'passed'			=> get_option( 'page_on_front' )
	),
	'setting-updated'	=> Array(
		'label'				=> esc_html__( "Theme Settings Setup", 'jvbpd' )
		, 'passed'			=> get_option( 'jvbpd_themes_settings' )
	),
); ?>

<div class="about-wrap jv-admin-wrap">
    <h1><?php echo esc_html($objTheme->name); ?> <?php esc_html_e( "Default Setting / System Status", 'jvbpd' ); ?></h1>
    <div class="about-text" style="margin-bottom: 32px;">

        <p>
			<?php esc_html_e( "Please have a look at default settings and system status. default settings is for you should complete (100%) to use our theme properly.", 'jvbpd' ); ?>
        </p>
    </div>

	<div class=" jv-default-setting-status-wrap">
		<h3 class="jv-default-setting-status-title">
			<?php esc_html_e( "Default Settings Status", 'jvbpd' ); ?>
			<div class="jv-default-setting-status-progress">
				<span><?php esc_html_e( "Loading", 'jvbpd' ); ?></span> <?php esc_html_e( "Completed", 'jvbpd' ); ?>
			</div>
		</h3>
		<div><?php esc_html_e( "These default settings should be installed or actived to make this theme work properly.", 'jvbpd' ); ?></div>
		<?php
		if( false && !empty( $arrGuideSections ) ) : foreach( $arrGuideSections as $section => $sectionMeta ) {
			$strFileName	= JVBPD_ADM_DIR . "/templates/step-{$section}.php";
			$isComplete		= $sectionMeta[ 'passed' ] ? esc_html__( "Completed", 'jvbpd' ) : esc_html__( "Incompleted", 'jvbpd' );
			$actived_class	= $sectionMeta[ 'passed' ] ? 'active' : 'active update';
			$isActived			= isset( $sectionMeta[ 'active' ] ) ? ' collapse' : null;
			echo "
				<table class=\"widefat plugins jv-default-setting-status-table{$isActived}\">
					<thead>
						<tr class='{$actived_class}'>
							<th width=\"80%\" class='check-column'><i></i>{$sectionMeta[ 'label' ]}</th>
							<th width=\"20%\" class='action-links'><i></i>{$isComplete}</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan='2'>";
							if( file_exists( $strFileName ) )
								require_once $strFileName;
							echo "
							</td>
						</tr>
					</tbody>
				</table>";
		} endif;
		?>
	</div>




	<?php
    /*  ----------------------------------------------------------------------------
        Theme config
     */

    // Theme name
    default_settings_status::add(esc_html__('Theme config','jvbpd'), array(
        'check_name' => esc_html__('Theme name','jvbpd'),
        'tooltip' => '',
        'value' =>  $objTheme->get( 'Name' ),
        'status' => 'info'
    ));

    // Theme version
    default_settings_status::add(esc_html__('Theme config','jvbpd'), array(
        'check_name' => esc_html__('Theme version','jvbpd'),
        'tooltip' => '',
        'value' =>  $objTheme->get( 'Version' ),
        'status' => 'info'
    ));

    /*  ----------------------------------------------------------------------------
        Server status
     */


    // server info
    default_settings_status::add(esc_html__('php.ini configuration','jvbpd'), array(
        'check_name' => esc_html__('Server software','jvbpd'),
        'tooltip' => '',
        'value' =>  function_exists('jvbpd_core_get_sortware') ? jvbpd_core_get_sortware() : esc_html__("Unknown", 'jvbpd'),
        'status' => 'info'
    ));

    // php version
    default_settings_status::add(esc_html__('php.ini configuration','jvbpd'), array(
        'check_name' => esc_html__('PHP Version','jvbpd'),
        'tooltip' => '',
        'value' => phpversion(),
        'status' => 'info'
    ));

    // post_max_size
    default_settings_status::add(esc_html__('php.ini configuration','jvbpd'), array(
        'check_name' => esc_html__('post_max_size','jvbpd'),
        'tooltip' => '',
        'value' =>  ini_get('post_max_size') . '<span class="jv-status-small-text"> - '. esc_html__('You cannot upload images, themes and plugins that have a size bigger than this value.','jvbpd').'</span>',
        'status' => 'info'
    ));

    // php time limit
    $max_execution_time = ini_get('max_execution_time');
    if ($max_execution_time == 0 or $max_execution_time >= 60) {
        default_settings_status::add(esc_html__('php.ini configuration','jvbpd'), array(
            'check_name' => esc_html__('max_execution_time','jvbpd'),
            'tooltip' => '',
            'value' =>  $max_execution_time,
            'status' => 'green'
        ));
    } else {
        default_settings_status::add(esc_html__('php.ini configuration','jvbpd'), array(
            'check_name' => esc_html__('max_execution_time','jvbpd'),
            'tooltip' => '',
            'value' =>  $max_execution_time . '<span class="jv-status-small-text"> - '. esc_html__('the execution time should be bigger than 60 if you plan to use the demos','jvbpd') .'</span>',
            'status' => 'yellow'
        ));
    }


    // php max input vars
    $max_input_vars = ini_get('max_input_vars');
    if ($max_input_vars == 0 or $max_input_vars >= 2000) {
        default_settings_status::add(esc_html__('php.ini configuration','jvbpd'), array(
            'check_name' => esc_html__('max_input_vars','jvbpd'),
            'tooltip' => '',
            'value' =>  $max_input_vars,
            'status' => 'green'
        ));
    } else {
        default_settings_status::add(esc_html__('php.ini configuration','jvbpd'), array(
            'check_name' => esc_html__('max_input_vars','jvbpd'),
            'tooltip' => '',
            'value' =>  $max_input_vars . '<span class="jv-status-small-text"> - '.esc_html__('the max_input_vars should be bigger than 2000, otherwise it can cause incomplete saves in the menu panel in WordPress','jvbpd').'</span>',
            'status' => 'yellow'
        ));
    }

    /*  ----------------------------------------------------------------------------
        WordPress
    */
    // home url
    default_settings_status::add(esc_html__('WordPress and plugins','jvbpd'), array(
        'check_name' => esc_html__('WP Home URL','jvbpd'),
        'tooltip' => '',
        'value' => esc_url( home_url( '/' ) ),
        'status' => 'info'
    ));

    // site url
    default_settings_status::add(esc_html__('WordPress and plugins','jvbpd'), array(
        'check_name' => esc_html__('WP Site URL','jvbpd'),
        'tooltip' => '',
        'value' => site_url(),
        'status' => 'info'
    ));

    // home_url == site_url
    if (home_url() != site_url()) {
        default_settings_status::add(esc_html__('WordPress and plugins','jvbpd'), array(
            'check_name' => esc_html__('Home URL - Site URL','jvbpd'),
            'tooltip' => esc_html__('Home URL not equal to Site URL, this may indicate a problem with your WordPress configuration.','jvbpd'),
            'value' => esc_html__('Home URL != Site URL','jvbpd'). '<span class="jv-status-small-text">'.esc_html__('Home URL not equal to Site URL, this may indicate a problem with your WordPress configuration.','jvbpd').'</span>',
            'status' => 'yellow'
        ));
    }

    // version
    default_settings_status::add(esc_html__('WordPress and plugins','jvbpd'), array(
        'check_name' => esc_html__('WP version','jvbpd'),
        'tooltip' => '',
        'value' => get_bloginfo('version'),
        'status' => 'info'
    ));


    // is_multisite
    default_settings_status::add(esc_html__('WordPress and plugins','jvbpd'), array(
        'check_name' => esc_html__('WP multisite enabled','jvbpd'),
        'tooltip' => '',
        'value' => is_multisite() ? esc_html__('Yes','jvbpd') : esc_html__('No','jvbpd'),
        'status' => 'info'
    ));


    // language
    default_settings_status::add(esc_html__('WordPress and plugins','jvbpd'), array(
        'check_name' => esc_html__('WP Language','jvbpd'),
        'tooltip' => '',
        'value' => get_locale(),
        'status' => 'info'
    ));



    // memory limit
    $memory_limit = default_settings_status::wp_memory_notation_to_number(WP_MEMORY_LIMIT);
    if ( $memory_limit < 67108864 ) {
        default_settings_status::add(esc_html__('WordPress and plugins','jvbpd'), array(
            'check_name' => esc_html__('WP Memory Limit','jvbpd'),
            'tooltip' => '',
            'value' => size_format( $memory_limit ) . esc_html__('/request','jvbpd'). '<span class="jv-status-small-text">- '. esc_html__('We recommend setting memory to at least 64MB. The theme is well tested with a 40MB/request limit, but if you are using multiple plugins that may not be enough. See:','jvbpd'). '<a href="http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">'.esc_html__('Increasing memory allocated to PHP','jvbpd').'</a></span>',
            'status' => 'yellow'
        ));
    } else {
        default_settings_status::add(esc_html__('WordPress and plugins','jvbpd'), array(
            'check_name' => esc_html__('WP Memory Limit','jvbpd'),
            'tooltip' => '',
            'value' => size_format( $memory_limit ) . '/request',
            'status' => 'green'
        ));
    }


    // wp debug
    if (defined('WP_DEBUG') and WP_DEBUG === true) {
        default_settings_status::add(esc_html__('WordPress and plugins','jvbpd'), array(
            'check_name' => esc_html__('WP_DEBUG','jvbpd'),
            'tooltip' => '',
            'value' => esc_html__('WP_DEBUG is enabled','jvbpd'),
            'status' => 'yellow'
        ));
    } else {
        default_settings_status::add(esc_html__('WordPress and plugins','jvbpd'), array(
            'check_name' => esc_html__('WP_DEBUG','jvbpd'),
            'tooltip' => '',
            'value' => esc_html__('False','jvbpd'),
            'status' => 'green'
        ));
    }






    // caching
    $caching_plugin_list = array(
        'wp-super-cache/wp-cache.php' => array(
            'name' => esc_html__('WP super cache','jvbpd'),
            'status' => 'green',
        ),
        'w3-total-cache/w3-total-cache.php' => array(
            'name' => esc_html__('W3 total cache (we recommend WP super cache)','jvbpd'),
            'status' => 'yellow',
        ),
        'wp-fastest-cache/wpFastestCache.php' => array(
            'name' => esc_html__('WP Fastest Cache (we recommend WP super cache)','jvbpd'),
            'status' => 'yellow',
        ),
    );
    $active_plugins = get_option('active_plugins');
    $caching_plugin = esc_html__('No caching plugin detected','jvbpd');
    $caching_plugin_status = 'yellow';
    foreach ($active_plugins as $active_plugin) {
        if (isset($caching_plugin_list[$active_plugin])) {
            $caching_plugin = $caching_plugin_list[$active_plugin]['name'];
            $caching_plugin_status = $caching_plugin_list[$active_plugin]['status'];
            break;
        }
    }
    default_settings_status::add(esc_html__('WordPress and plugins','jvbpd'), array(
        'check_name' => esc_html__('Caching plugin','jvbpd'),
        'tooltip' => '',
        'value' =>  $caching_plugin,
        'status' => $caching_plugin_status
    ));

    default_settings_status::render_tables();
    ?>




</div>



<?php
class default_settings_status {

   static $system_status = array();

   static function add($section, $status_array) {
	   self::$system_status[$section] []= $status_array;
   }

   static function render_tables() {
	   foreach (self::$system_status as $section_name => $section_statuses) {
			?>
			<table class="widefat jv-system-status-table" cellspacing="0">
				<thead>
					<tr>
					   <th colspan="4"><?php echo esc_html( $section_name ); ?></th>
					</tr>
				</thead>
				<tbody>
			<?php

				foreach ($section_statuses as $status_params) {
					?>
					<tr>
						<td class="jv-system-status-name"><?php echo esc_html( $status_params['check_name'] ); ?></td>
						<td class="jv-system-status-help"></td>
						<td class="jv-system-status-status">
							<?php
								switch ($status_params['status']) {
									case 'green':
										echo '<div class="jv-system-status-led jv-system-status-green jv-tooltip" data-position="right" title="'.esc_attr__('Green status: this check passed our system status test!','jvbpd').'"></div>';
										break;
									case 'yellow':
										echo '<div class="jv-system-status-led jv-system-status-yellow jv-tooltip" data-position="right" title="'.esc_attr__('Yellow status: this setting may affect the backend of the site. The front end should still run as expected. We recommend that you fix this.','jvbpd').'"></div>';
										break;
									case 'red' :
										echo '<div class="jv-system-status-led jv-system-status-red jv-tooltip" data-position="right" title="'.esc_attr__('Red status: the site may not work as expected with this option.','jvbpd').'"></div>';
										break;
									case 'info':
										echo '<div class="jv-system-status-led jv-system-status-info jv-tooltip" data-position="right" title="'.esc_attr__('Info status: this is just for information purposes and easier debug if a problem appears','jvbpd').'">i</div>';
										break;
								}
							?>
						</td>
						<td class="jv-system-status-value"><?php echo ent2ncr( $status_params['value'] ); ?></td>
					</tr>
					<?php
				}
			?>
				</tbody>
			</table>
			<?php
	   }
   }

   static function wp_memory_notation_to_number( $size ) {
	   $l   = substr( $size, -1 );
	   $ret = substr( $size, 0, -1 );
	   switch ( strtoupper( $l ) ) {
		   case 'P':
			   $ret *= 1024;
		   case 'T':
			   $ret *= 1024;
		   case 'G':
			   $ret *= 1024;
		   case 'M':
			   $ret *= 1024;
		   case 'K':
			   $ret *= 1024;
	   }
	   return $ret;
   }

}