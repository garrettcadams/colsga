<?php
if (!function_exists ('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

class Jvbpd_Import {

    const REMOTE_SITE = 'http://myjavo.com/demo/';

	public $name = '';
	public $prefix = '';
	public $dirFiles = '';
    public $message = "";
    public $attachments = false;
	public static $hInstance =null;

    function __construct() {
		$this->setVariables();
		$this->register_hooks();
        add_action( 'jvbpd_admin_help_submenus', array( &$this, 'jvbpd_admin_import' ) );
    }

	function setVariables() {
        if( !class_exists( 'jvbpd_admin_helper')) {
            return;
        }
		$this->name = $this->name = jvbpd_admin_helper::$instance->name;
		$this->prefix = sanitize_title( $this->name );
		$this->dirFiles = sprintf( '%s/files', jvbpdCore()->import_path );
	}

	function register_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
	}

	public function admin_enqueue() {
		?>
		<style type="text/css">
			#jv-metaboxes-general{ letter-spacing:1px; }
			#jv-metaboxes-general .text-uppercase{ text-transform:uppercase; margin:0; font-size:17px;}
			#jv-metaboxes-general .jv-main-box-wrap{ border:solid 1px #ddd; background-color:#fff; padding:20px 25px; display:inline-block; }
			#jv-metaboxes-general .jv-main-box-wrap .jv-metaboxes-half-left{width:35%; float:left;}
			#jv-metaboxes-general .jv-main-box-wrap .jv-metaboxes-half-right{width:65%; float:right; margin-top:40px;}
			#jv-metaboxes-general ul.ul-li-letter-spacing li{ letter-spacing:1px; }
			#jv-metaboxes-general .big-font{
				display:block;
				margin:20px 0;
				font-size:17px;
			}
			#jv-metaboxes-general .normal-font{ font-size:1em; }
			#jv-metaboxes-general input[type="submit"][name="import"]{
				line-height:1;
				padding:10px 20px;
				height:auto;
				font-size:1.5em;
				letter-spacing:1px;
				text-transform:uppercase;
			}
			#jv-metaboxes-general .import_load{
				margin:0;
				margin-top:10px;
			}
			#jv-metaboxes-general .jv-progress-bar-wrapper{
				display:block;
				margin:10px;
			}
			#jv-metaboxes-general .jv-progress-bar-wrapper > .progress-bar-wrapper,
			#jv-metaboxes-general .jv-progress-bar-wrapper > .progress-value{
				display:inline-block;
				vertical-align:middle;
			}
			.jvfrm-demo-select-label{	float:left; margin-right:10px; line-height:30px;}

			#jv-metaboxes-general .jvfrm-page-form-section-holder>p,
			#jv-metaboxes-general .jvfrm-page-form-section-holder .jvfrm-demo-select-box-wrapper,
			#jv-metaboxes-general .jvfrm-page-form-section-holder .jvfrm-page-form-section{margin-bottom:1em;}
		</style>
		<?php
	}

    function init_jvbpd_import() {
        if(isset($_REQUEST['import_option'])) {
            $import_option = $_REQUEST['import_option'];
            $folder = "demo/";
            if (!empty($_POST['example']))
                $folder = $_POST['example']."/";
            if($import_option == 'content'){
                $this->import_content('proya_content.xml');
            }elseif($import_option == 'custom_sidebars') {
                $this->import_custom_sidebars('custom_sidebars.txt');
            } elseif($import_option == 'widgets') {
                $this->import_widgets('widgets.txt','custom_sidebars.txt');
            } elseif($import_option == 'options'){
                $this->import_options('jvbpd_themes_settings.txt');
            }elseif($import_option == 'menus'){
                $this->import_menus('menus.txt');
            }elseif($import_option == 'settingpages'){
                $this->import_settings_pages('settingpages.txt');
            }elseif($import_option == 'complete_content'){
                $this->import_content('proya_content.xml');
                $this->import_options($folder.'jvbpd_themes_settings.txt');
                $this->import_widgets($folder.'widgets.txt','custom_sidebars.txt');
                $this->import_menus($folder.'menus.txt');
                $this->import_settings_pages($folder.'settingpages.txt');
                $this->message = __("Content imported successfully", 'jvfrmtd');
            }
        }
    }

    public function import_content($file){
		ob_start();
		require_once( $this->dirFiles . '/../class.wordpress-importer.php' );
		$jvbpd_import = new WP_Import();
		set_time_limit(0);
		$jvbpd_import->fetch_attachments = $this->attachments;

		$returned_value = $jvbpd_import->import( $this->dirFiles . '/' . $file );
		if(is_wp_error($returned_value)){
			$this->message = __("An Error Occurred During Import", 'jvfrmtd');
		}
		else {
			$this->message = __("Content imported successfully", 'jvfrmtd');
		}
		ob_get_clean();
    }

    public function import_widgets($file, $file2){
        $this->import_custom_sidebars($file2);
        $options = $this->file_options($file);
        foreach ((array) $options['widgets'] as $jvbpd_widget_id => $jvbpd_widget_data) {
            update_option( 'widget_' . $jvbpd_widget_id, $jvbpd_widget_data );
        }
        $this->import_sidebars_widgets($file);
        $this->message = __("Widgets imported successfully", 'jvfrmtd');
    }

    public function import_sidebars_widgets($file){
        $jvbpd_sidebars = get_option("sidebars_widgets");
        unset($jvbpd_sidebars['array_version']);
        $data = $this->file_options($file);
        if ( is_array($data['sidebars']) ) {
            $jvbpd_sidebars = array_merge( (array) $jvbpd_sidebars, (array) $data['sidebars'] );
            unset($jvbpd_sidebars['wp_inactive_widgets']);
            $jvbpd_sidebars = array_merge(array('wp_inactive_widgets' => array()), $jvbpd_sidebars);
            $jvbpd_sidebars['array_version'] = 2;
            wp_set_sidebars_widgets($jvbpd_sidebars);
        }
    }

    public function import_custom_sidebars($file){
        $options = $this->file_options($file);
        update_option( 'jvbpd_sidebars', $options);
        $this->message = __("Custom sidebars imported successfully", 'jvfrmtd');
    }

    public function import_options($file){
        $options = $this->file_options($file);
        update_option( 'jvbpd_themes_settings', $options);
        $this->message = __("Options imported successfully", 'jvfrmtd');
    }

    public function import_menus($file){
		global $wpdb;
		$jvbpd_terms_table = $wpdb->prefix . "terms";
		$this->menus_data = $this->file_options($file);
		$menu_array = array();
		foreach ($this->menus_data as $registered_menu => $menu_slug) {
			$term_rows = $wpdb->get_results("SELECT * FROM $jvbpd_terms_table where slug='{$menu_slug}'", ARRAY_A);
			if(isset($term_rows[0]['term_id'])) {
				$term_id_by_slug = $term_rows[0]['term_id'];
			} else {
				$term_id_by_slug = null;
			}
			$menu_array[$registered_menu] = $term_id_by_slug;
		}
		set_theme_mod('nav_menu_locations', array_map('absint', $menu_array ) );
    }

    public function import_settings_pages($file){
        $pages = $this->file_options($file);

        foreach($pages as $jvbpd_page_option => $jvbpd_page_id){
            update_option( $jvbpd_page_option, $jvbpd_page_id);
        }
    }
    public function file_options($file){
        $file_content = $this->get_contents( $file );
        if ($file_content) {
            $unserialized_content = unserialize(base64_decode($file_content));
            if ($unserialized_content) {
                return $unserialized_content;
            }
        }
        return false;
    }

    function get_contents( $path='' ) {
		return wp_remote_retrieve_body( wp_remote_get( self::REMOTE_SITE . $path ) );
    }

    function jvbpd_admin_import( $menus=Array() ) {

		$menus[] = Array(
			'slug' => 'import',
			'name' => esc_html__( "Demo Import", 'jvfrmtd' ),
			'func' => Array( &$this, 'jvbpd_generate_import_page' ),
		);

		return $menus;
    }

    function jvbpd_generate_import_page() {
		do_action( 'jvbpd_admin_helper_page_header' );
		do_action( 'jvbpd_admin_helper_import_header' );
		?>
        <div id="jv-metaboxes-general" class="wrap jvfrm-page jvfrm-page-info">
		<div class="jvfrm-page-form">

			<div class="jvfrm-page-form-section-holder clearfix jv-main-box-wrap">
                <h2 class="jvfrm-page-title text-uppercase"><?php _e('Javo One-Click Import', 'jvfrmtd') ?></h2>
                <p>&nbsp;</p>
				<!--<h3 class="jvfrm-page-section-title text-uppercase"><?php esc_html_e('Import Demo Content','jvfrmtd'); ?></h3>-->
				<div class="jv-metaboxes-half-left">
					<div class="jvfrm-page-form-section">
						<div class="jvfrm-field-desc">
							<!--<h4 class="text-uppercase"><?php esc_html_e('Demo Site', 'jvfrmtd'); ?></h4>-->
						</div>
						<div class="jvfrm-section-content">
							<div class="container-fluid">
								<div class="jvfrm-demo-select-box-wrapper">
									<div class="jvfrm-demo-select-label"><?php esc_html_e('Select a Demo','jvfrmtd'); ?></div>
									<div class="jvfrm-demo-select-box">
										<select name="import_example" id="import_example" class="form-control jvfrm-form-element">
											<option value="listopia-demo">Demo All</option>
										</select>
									</div>
								</div>
								<div class="row next-row">
									<div class="col-lg-3">
										<img id="demo_site_img" src="<?php echo get_stylesheet_directory_uri() . '/screenshot.png' ?>" width="250">
									</div>
								</div>

							</div>
						</div>
					</div>
					<div class="jvfrm-page-form-section" >
						<div class="jvfrm-section-content">
							<div class="container-fluid">
								<div class="row">
									<div class="col-lg-3">
										<?php esc_html_e( "Import Type", 'jvfrmtd' ); ?>
										<select name="import_option" id="import_option" class="form-control jvfrm-form-element">
											<option value="">Please Select</option>
											<option value="complete_content">All</option>
											<option value="content">Content</option>
											<!--<option value="widgets">Widgets</option>-->
											<!-- option value="menus">Menus</option -->
											<option value="options">Options</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="jvfrm-page-form-section media-check" >
						<!--<div class="jvfrm-field-desc">
							<h4 class="text-uppercase"><?php esc_html_e('Import attachments', 'jvfrmtd'); ?></h4>
						</div>-->
						<div class="jvfrm-section-content">
							<label>
								<?php esc_html_e( "Do you want to import media files?", 'jvfrmtd' ); ?>
								<input type="checkbox" value="1" class="jvfrm-form-element" name="import_attachments" id="import_attachments">
							</label>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-3">
							<div class="form-button-section clearfix">
								<input type="button" class="button button-primary" value="Import" name="import" id="import_demo_data" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-3"></div>

					</div>

					<div class="import_load">
						<span><?php _e('The import process may take some time. Please be patient.', 'jvfrmtd') ?> </span><br />
						<div class="jv-progress-bar-wrapper html5-progress-bar">
							<div class="progress-bar-wrapper">
								<progress id="progressbar" value="0" max="100"></progress>
							</div>
							<div class="progress-value">0%</div>
							<div class="progress-bar-message"></div>
						</div>
					</div>
				</div>

				<div class="jv-metaboxes-half-right">
					<div class="alert alert-warning" style="border:solid 1px #ddd; background-color:#f4f4f4; padding:0 20px 10px;">
						<strong class="text-uppercase big-font">Important notes:</strong>
						<ul class="ul-li-letter-spacing ul-li-font-size-1-2">
							<li>Please note that import process will take time needed to download all attachments from demo web site.</li>
							<li class="warning">
								<ul>
									<li> <h4 class="text-uppercase big-font">Tips, If you have problems with Import.</h4></li>
									<li class="normal-font"> 1. Cause : Low uploading size (a common cause. 99%). Solution : Increase your uploading size / memory in your server.</li>
									<li class="normal-font"> 2. Solution : Deactivate all of  <a href="<?php echo admin_url( 'plugins.php' ); ?>" target="_blank">( Go to Deactivate )</a></li>
									<li class="normal-font"> 3. Solution : Import demo xml files directly.</li>
									<li class="normal-font"> If you still have problems, please contact <a href="https://javothemes.com/support/">javo support team</a>.</li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
        </div>
        <script type="text/javascript">
			var $j = window.jQuery;
			var ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
            $j(document).ready(function() {
				/**
				$j('#import_example').on('change', function (e) {
					var optionSelected = $j("option:selected", this).val();
					$j('#demo_site_img').attr('src', '<?php echo get_template_directory_uri() . '/css/admin/images/demos/' ?>' + optionSelected + '.jpg' );
				}); */

                $j(document).on('click', '#import_demo_data', function(e) {
                    e.preventDefault();
                    if ($j( "#import_option" ).val() == "") {
                    	alert('Please select Import Type.');
                    	return false;
                    }
                    if (confirm('Are you sure, you want to import Demo Data now?')) {
                        $j('.import_load').css('display','block');
                        var progressbar = $j('#progressbar')
                        var import_opt = $j( "#import_option" ).val();
                        var import_expl = $j( "#import_example" ).val();
                        var p = 0;
                        if(import_opt == 'content'){
                            for(var i=1;i<10;i++){
                                var str;
                                if (i < 10) str = 'demo_content_0'+i+'.xml';
                                else str = 'demo_content_'+i+'.xml';
                                jQuery.ajax({
                                    type: 'POST',
                                    url: ajaxurl,
                                    data: {
                                        action: 'jvbpd_dataImport',
                                        xml: str,
                                        example: import_expl,
                                        import_attachments: ($j("#import_attachments").is(':checked') ? 1 : 0)
                                    },
                                    success: function(data, textStatus, XMLHttpRequest){
                                        p+= 10;
                                        $j('.progress-value').html((p) + '%');
                                        progressbar.val(p);
                                        if (p == 90) {
                                            str = 'demo_content_10.xml';
                                            jQuery.ajax({
                                                type: 'POST',
                                                url: ajaxurl,
                                                data: {
                                                    action: 'jvbpd_dataImport',
                                                    xml: str,
                                                    example: import_expl,
                                                    import_attachments: ($j("#import_attachments").is(':checked') ? 1 : 0)
                                                },
                                                success: function(data, textStatus, XMLHttpRequest){
                                                    p+= 10;
                                                    $j('.progress-value').html((p) + '%');
                                                    progressbar.val(p);
                                                    $j('.progress-bar-message').html('<div class="alert alert-success"><strong>Import is completed</strong></div>');
                                                },
                                                error: function(MLHttpRequest, textStatus, errorThrown){
                                                }
                                            });
                                        }
                                    },
                                    error: function(MLHttpRequest, textStatus, errorThrown){
                                    }
                                });
                            }
                        } else if(import_opt == 'widgets') {
                            jQuery.ajax({
                                type: 'POST',
                                url: ajaxurl,
                                data: {
                                    action: 'jvbpd_widgetsImport',
                                    example: import_expl
                                },
                                success: function(data, textStatus, XMLHttpRequest){
                                    $j('.progress-value').html((100) + '%');
                                    progressbar.val(100);
                                },
                                error: function(MLHttpRequest, textStatus, errorThrown){
                                }
                            });
                            $j('.progress-bar-message').html('<div class="alert alert-success"><strong>Import is completed</strong></div>');
                        } else if(import_opt == 'options'){
                            jQuery.ajax({
                                type: 'POST',
                                url: ajaxurl,
                                data: {
                                    action: 'jvbpd_optionsImport',
                                    example: import_expl
                                },
                                success: function(data, textStatus, XMLHttpRequest){
                                    $j('.progress-value').html((100) + '%');
                                    progressbar.val(100);
                                },
                                error: function(MLHttpRequest, textStatus, errorThrown){
                                }
                            });
                            $j('.progress-bar-message').html('<div class="alert alert-success"><strong>Import is completed</strong></div>');
                        } else if(import_opt == 'menus'){
                            jQuery.ajax({
                                type: 'POST',
                                url: ajaxurl,
                                data: {
                                    action: 'jvbpd_menusImport',
                                    example: import_expl
                                },
                                success: function(data, textStatus, XMLHttpRequest){
                                    $j('.progress-value').html((100) + '%');
                                    progressbar.val(100);
                                },
                                error: function(MLHttpRequest, textStatus, errorThrown){
                                }
                            });
                            $j('.progress-bar-message').html('<div class="alert alert-success"><strong>Import is completed</strong></div>');
                        } else if(import_opt == 'complete_content'){
                            for(var i=1;i<10;i++){
                                var str;
                                if (i < 10) str = 'demo_content_0'+i+'.xml';
                                else str = 'demo_content_'+i+'.xml';
                                jQuery.ajax({
                                    type: 'POST',
                                    url: ajaxurl,
                                    data: {
                                        action: 'jvbpd_dataImport',
                                        xml: str,
                                        example: import_expl,
                                        import_attachments: ($j("#import_attachments").is(':checked') ? 1 : 0)
                                    },
                                    success: function(data, textStatus, XMLHttpRequest){
                                        p+= 10;
                                        $j('.progress-value').html((p) + '%');
                                        progressbar.val(p);
                                        if (p == 90) {
                                            str = 'demo_content_10.xml';
                                            jQuery.ajax({
                                                type: 'POST',
                                                url: ajaxurl,
                                                data: {
                                                    action: 'jvbpd_dataImport',
                                                    xml: str,
                                                    example: import_expl,
                                                    import_attachments: ($j("#import_attachments").is(':checked') ? 1 : 0)
                                                },
                                                success: function(data, textStatus, XMLHttpRequest){
                                                    jQuery.ajax({
                                                        type: 'POST',
                                                        url: ajaxurl,
                                                        data: {
                                                            action: 'jvbpd_otherImport',
                                                            example: import_expl
                                                        },
                                                        success: function(data, textStatus, XMLHttpRequest){
                                                            $j('.progress-value').html((100) + '%');
                                                            progressbar.val(100);
                                                            $j('.progress-bar-message').html('<div class="alert alert-success">Import is completed.</div>');
                                                        },
                                                        error: function(MLHttpRequest, textStatus, errorThrown){
                                                        }
                                                    });
                                                },
                                                error: function(MLHttpRequest, textStatus, errorThrown){
                                                }
                                            });
                                        }
                                    },
                                    error: function(MLHttpRequest, textStatus, errorThrown){
                                    }
                                });
                            }
                        }
                    }
                    return false;
                });
            });
        </script>
		<?php
		do_action( 'jvbpd_admin_helper_page_footer' );
		do_action( 'jvbpd_admin_helper_import_footer' );
	}

	public static function getInstance() {
		if( is_null( self::$hInstance ) ) {
			self::$hInstance = new self;
		}
		return self::$hInstance;
	}

}

if(!function_exists('jvbpd_dataImport')) {
    function jvbpd_dataImport() {
        global $jvbpd_import;

        if ($_POST['import_attachments'] == 1) {
            $jvbpd_import->attachments = true;
		} else {
            $jvbpd_import->attachments = false;
		}

        $folder = "demo/";
        if( !empty( $_POST['example'] ) ) {
            $folder = $_POST['example'] . '/';
		}
        $jvbpd_import->import_content( $folder . $_POST['xml'] );
        die();
    }

    add_action('wp_ajax_jvbpd_dataImport', 'jvbpd_dataImport');
}

if(!function_exists('jvbpd_widgetsImport')) {
    function jvbpd_widgetsImport() {
        global $jvbpd_import;

        $folder = "demo/";
        if (!empty($_POST['example']))
            $folder = $_POST['example']."/";

        $jvbpd_import->import_widgets($folder.'widgets.txt',$folder.'custom_sidebars.txt');
        die();
    }

    add_action('wp_ajax_jvbpd_widgetsImport', 'jvbpd_widgetsImport');
}

if(!function_exists('jvbpd_menusImport')) {
	function jvbpd_menusImport() {
		global $jvbpd_import;

		$folder = "demo/";
		if (!empty($_POST['example']))
			$folder = $_POST['example']."/";

		$jvbpd_import->import_menus($folder.'menus.txt');
		die();
	}

    add_action('wp_ajax_jvbpd_menusImport', 'jvbpd_menusImport');
}

if(!function_exists('jvbpd_optionsImport'))
{
    function jvbpd_optionsImport()
    {
        global $jvbpd_import;

        $folder = "demo/";
        if (!empty($_POST['example']))
            $folder = $_POST['example']."/";

        $jvbpd_import->import_options($folder.'jvbpd_themes_settings.txt');
        $jvbpd_import->import_settings_pages($folder.'settingpages.txt');

        die();
    }

    add_action('wp_ajax_jvbpd_optionsImport', 'jvbpd_optionsImport');
}

if(!function_exists('jvbpd_otherImport'))
{
    function jvbpd_otherImport()
    {
        global $jvbpd_import;

        $folder = "demo/";
        if (!empty($_POST['example']))
            $folder = $_POST['example']."/";

        $jvbpd_import->import_options($folder.'jvbpd_themes_settings.txt');
        $jvbpd_import->import_widgets($folder.'widgets.txt',$folder.'custom_sidebars.txt');
        $jvbpd_import->import_menus($folder.'menus.txt');
        $jvbpd_import->import_settings_pages($folder.'settingpages.txt');

        die();
    }

    add_action('wp_ajax_jvbpd_otherImport', 'jvbpd_otherImport');
}