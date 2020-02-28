<?php
/**
 *
 *
 * @since	1.0
 */
class moduleBpGridNoBG extends Jvbpd_Module {

	public $current_component = '';
	public $component_action = '';

	public function __construct( $post, $param=Array() ) {
		$this->lghTitle = 10;
		$this->lghContent = 30;
		parent::__construct( $post, $param );

		if( function_exists( 'bp_is_current_component' ) && bp_is_current_component( BuddyPress()->groups->slug ) ) {
			$this->current_component = 'groups';
		}

		if( function_exists( 'bp_is_current_component' ) && bp_is_current_component( BuddyPress()->members->slug ) ) {
			$this->current_component = 'members';
		}
		$this->component_action = sprintf( 'bp_directory_%s_actions', $this->current_component );
	}

	public function bpExcerpt() {
		$strExcerpt = false;
		if( $this->current_component == 'groups' ) {
			$strExcerpt = bp_group_description_excerpt();
		}
		if( $this->current_component == 'members' ) {
			$strExcerpt = bp_get_member_latest_update();
		}
		return false !== $strExcerpt ? '<span class="update">' . $strExcerpt . '</span>' : $strExcerpt;
	}

	public function output() {
		ob_start();
		$jvbpd_listing_category = $this->category();
		$jvbpd_listing_location = $this -> get_term('item_location');
		?>
		<div <?php $this->classes( 'media' ); ?>>
			<?php $this->before(); ?>
			<div class="media-left effect-wrap jv-thumbnail">
				<a href="<?php echo $this->permalink;?>"><?php echo $this->avatar; ?><div class="user-online-wrap"><span class="heartbit"></span><span class="dot"></span></div></a>
			</div><!-- /.media-left -->
			<div class="media-body">
				<h4 class="media-heading meta-title"><?php echo $this->title; ?></h4>
				<i class="jv-icon2-clock2"></i> <span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_last_active( array( 'relative' => false ) ) ); ?>"></span>

				<?php echo $this->bpExcerpt(); ?>

				<?php if($this->excerpt != '' ){ ?>
				<div class="jv-excerpt-wrap"><?php echo $this->excerpt; ?></div>
				<?php } ?>
				<?php echo $this->moreInfo(); ?>
			</div><!-- /.media-body -->
				<?php echo $this->excerpt; ?>
					<?php echo $this->addtionalInfo(); ?>
				<?php //$this->hover_layer();?>
			<div class="dot-options">
				<div class="dropdown">
				  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="icon-ellipsis-vertical"></i>
				  </button>
				  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
					<?php do_action( $this->component_action ); ?>
				  </div>
				</div>
			</div>
			<?php $this->after(); ?>
		</div><!-- /.media -->
		<?php
		return ob_get_clean();
	}
}