<?php
class jvbpd_login1 extends Jvbpd_Shortcode_Parse
{

	public $user;
	public $user_id			= 0;
	public $user_name		= '';
	public $user_avatar		= '';
	public $is_logged_in	= false;
	public $mypage			= '#';
	public $editProfile			= '#';
	public $submit_item		= '#';

	public function output( $attr, $content='' )
	{
		$this->fixCount			= 6;
		parent::__construct( $attr ); ob_start();
		$this->sHeader();
		$this->initialize();
		$strClassName				= $this->is_logged_in ? ' logged-in' : false;
		?>
		<div id="<?php echo $this->sID; ?>" class="shortcode-container fadein">
			<div class="shortcode-header">
				<div class="shortcode-title">
					<?php echo $this->title; ?>
				</div>
				<div class="shortcode-nav"></div>
			</div>
			<div class="shortcode-output<?php echo $strClassName; ?>">
				<?php $this->render(); ?>
			</div>
		</div>
		<?php
		$this->sFooter(); return ob_get_clean();
	}

	public function initialize()
	{
		$this->is_logged_in	= is_user_logged_in();

		if( ! $this->is_logged_in )
			return;

		$this->user_id			= get_current_user_id();
		$this->user				= new WP_User( $this->user_id );
		$this->user_name	= $this->user->display_name;

		$this->logout_page	= wp_logout_url( home_url( '/' ) );

		//if( function_exists( 'jvbpd_getCurrentUserPage' ) ) {
			$this->mypage		= bp_loggedin_user_domain();
			$this->editProfile = $this->mypage.'settings';
			//$this->submit_item	= jvbpd_getCurrentUserPage( 'add-lv_listing');
		//}

		$this->user_avatar	= str_replace(
			'>', 'data-no-lazy>',
			get_avatar( $this->user_id, 60 )
		);
	}

	public function getCommentCount(){
		global $wpdb;
		return $wpdb->get_var(
			$wpdb->prepare( "select count(*) from $wpdb->comments as cmt left join $wpdb->posts as post on cmt.comment_post_ID = post.ID where post.post_type=%s and post.post_author=%s", 'lv_listing', $this->user_id )
		);
	}

	public function render()
	{

		if( $this->is_logged_in ) {
			$this->logout_form();
		}else{
			$this->login_form();
		}
	}

	public function login_form() {
		wp_login_form(
			Array(
				'form_id'		=> $this->sID . '_login_form'
			)
		);
		printf(
			"<p class=\"login-lostpassword\"><a href=\"%s\">%s</a></p>",
			wp_lostpassword_url( home_url( '/' ) ),
			__( "Lost your password?", 'jvfrmtd' )
		);
	}

	public function logout_form()
	{
		?>
		<div class="panel panel-default" style="z-index: 1;">
			<div class="panel-body"><?php echo $this->panel_body(); ?></div>
			<div class="panel-footer"><?php $this->panel_footer();?></div>
		</div><!-- /.panel -->
		<?php
	}

	public function panel_body()
	{
		?>
		<div class="meta-container">
			<div class="col-md-4 meta-avatar">
				<a href="<?php echo $this->mypage; ?>">
					<?php echo $this->user_avatar; ?>
				</a>
			</div>
			<div class="col-md-7 meta-wrap">
				<div class="meta-display-name">
					<strong><?php echo $this->user_name; ?></strong>					
				</div>
				<div class="meta-posts">
					<i class="fa fa-caret-right"></i>
					<label><?php _e( "Posts", 'jvfrmtd' ); ?></label>
					<div class="bedge"><?php echo count_user_posts( $this->user_id , 'lv_listing' ); ?></div>
				</div>
				<div class="meta-comments">
					<i class="fa fa-caret-right"></i>
					<label><?php _e( "Topics", 'jvfrmtd' ); ?></label>
					<div class="bedge"><?php echo intVal( $this->getCommentCount() ); ?></div>
				</div>
			</div>
		</div><!-- /.row -->
		<?php
	}

	public function panel_footer() {
		$arrFooterMeta		= Array(
			Array(
				'label'		=> __( "My Page", 'jvfrmtd' ),
				'icon'		=> 'fa fa-bell-o',
				'href'		=> $this->mypage,
			),
			Array(
				'label'		=> __( "Settings", 'jvfrmtd' ),
				'icon'		=> 'fa fa-bell-o',
				'href'		=> $this->editProfile,
			),
			Array(
				'label'		=> __( "Logout", 'jvfrmtd' ),
				'icon'		=> 'fa fa-bell-o',
				'href'		=> $this->logout_page,
			)
		);
		
		echo '<div class="row meta-addons">';
		foreach( $arrFooterMeta as $footerMeta ) {
			echo "
				<div class=\"col-md-4\">
					<a href=\"{$footerMeta[ 'href' ]}\">
						<i class=\"{$footerMeta[ 'icon' ]}\"></i>
						<span>{$footerMeta[ 'label' ]}</span>
					</a>
				</div>
			";
		}
		echo '</div>';
	}
}