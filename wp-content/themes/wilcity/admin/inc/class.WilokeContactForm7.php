<?php
/**
 * WilokeContactForm Class
 *
 * @category Helper
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
	exit;
}

class WilokeContactForm7
{
	public function __construct()
	{
		add_action('wpcf7_admin_footer', array($this, 'wiloke_contactfom7_helper'));
		add_action('admin_enqueue_scripts', array($this, 'wiloke_enqueue_scripts'));
		add_action('wp_ajax_wiloke_contactform7_demo', array($this, 'wiloke_contactform7_demo'));
	}

	public function wiloke_enqueue_scripts()
	{
		wp_enqueue_style('wiloke_contactform7', get_template_directory_uri() . '/admin/source/css/contactform7.css', array(), null);
		wp_enqueue_script('wiloke_contactform7', get_template_directory_uri() . '/admin/source/js/contactform7.js', array('jquery'), null, true);
	}

    public function wiloke_contactform7_demo()
	{
		global $wiloke;
		if ( isset($wiloke->aConfigs['contactform7']['contactForm']) )
		{
			Wiloke::ksesHTML($wiloke->aConfigs['contactform7']['contactForm']);
		}else{
			echo 'There are no contact forms';
		}
		die();
	}

	public function wiloke_contactfom7_helper()
	{
		?>
		<div id="wilokecontactform7" class="contactform7">
			<div class="postbox-container">
				<div class="postbox">
					<h2 class="hndle">Contact form 7 Option</h2>
					<div class="inside">
						<p>Setup my contact form looks like the demo.</p>
						<button id="wiloke-import-contactform7" class="button button-primary">Setup</button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}