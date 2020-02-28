<?php
/**
 *
 *
 * @since	1.0
 */
class moduleBpGrid extends Jvbpd_Module {

	public $current_component = '';
	public $component_action = '';

	public function __construct( $post, $param=Array() ) {
		$this->lghTitle			= 5;
		parent::__construct( $post, $param );

		if( function_exists( 'bp_is_current_component' ) && bp_is_active( 'groups' ) && bp_is_current_component( BuddyPress()->groups->slug ) ) {
			$this->current_component = 'groups';
		}

		if( function_exists( 'bp_is_current_component' ) && bp_is_active( 'members' ) && bp_is_current_component( BuddyPress()->members->slug ) ) {
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

	public function getBpGroupMember() {
		if( $this->current_component != 'groups' ) {
			return;
		}
		?>
		<span <?php bp_group_class( $classese = array('bp-img-num badge badge-info label-notice-num counter')); ?>><?php echo bp_get_group_member_count_int(); ?></span>
		<?php
	}

	public function get_component_action() {
		ob_start();
		do_action( $this->component_action );
		return ob_get_clean();
	}

	public function output() {
		ob_start();
		?>
		<div <?php $this->classes();
		//default : null
		//small-box
		//bg-box
		?>>
			<?php $this->before(); ?>
			<div class="thumb-wrap">
				<a href="<?php echo $this->permalink; ?>" class="jv-module12-link">
					<?php echo $this->thumbnail( 'large', true ); ?>
					<div class="author">
						<?php echo $this->avatar; ?>
						<div class="user-online-wrap">
							<span class="heartbit"></span>
							<span class="dot"></span>
							<?php $this->getBpGroupMember(); ?>
						</div><!-- .user-online-wrap -->
					</div><!-- .author -->
				</a>
				<div class="bp-btn-actions">
					<?php do_action( $this->component_action ); ?>
				</div><!-- .bp-btn-actions -->
			</div><!-- /.thumb-wrap -->

			<div class="author-meta">
				<h4 class="media-heading meta-title"><?php echo $this->title; ?></h4>
				<?php if( $this->current_component == 'members' ) { ?>
					<i class="jv-icon2-clock2"></i>
					<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_last_active( array( 'relative' => false ) ) ); ?>"></span>
				<?php } ?>
			</div>
			<div class="caption">

				<?php echo $this->bpExcerpt(); ?>

				<?php if($this->excerpt != '' ){ ?>
				<div class="jv-excerpt-wrap"><?php echo $this->excerpt; ?></div>
				<?php } ?>
				<?php echo $this->moreInfo(); ?>
			</div><!-- /.media-body -->
			<?php echo $this->excerpt; ?>
				<?php echo $this->addtionalInfo(); ?>
			<?php //$this->hover_layer();?>

			<?php
			$content = $this->get_component_action();
			if( !empty( $content ) ) { ?>
				<div class="dot-options">
					<div class="dropdown">
					  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="icon-ellipsis-vertical"></i>
					  </button>
					  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
						<?php echo $content; ?>
					  </div>
					</div>
				</div>
			<?php
			} // if( !empty( $content ) )
			$this->after(); ?>
		</div><!-- /.media -->
		<?php
		return ob_get_clean();
	}
}