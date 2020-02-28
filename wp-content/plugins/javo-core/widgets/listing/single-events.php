<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) exit;

class jvbpd_single_events extends Widget_Base {

	public function get_name() { return 'jvbpd-single-events'; }

	public function get_title() { return 'Single Events'; }

	public function get_icon() { return 'eicon-button'; }

	public function get_categories() { return [ 'jvbpd-single-listing' ]; }

	protected function _register_controls() {

		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
        ) );
		$this->end_controls_section();
	}

	public function getContents() {
		$post = get_post();
		$objEvent = class_exists( '\jvbpd_core_events' ) ? new \jvbpd_core_events : false;
		$isParentSinglePage = true;
		$lava_user_posts = Array();
		if( function_exists( 'tribe_get_events' ) ) {
			$arrEventListQuery = Array(
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'post_parent' => get_the_ID(),
				'paged' => max( 1, get_query_var( 'paged' ) ),
			);
			if( ! $isParentSinglePage ) {
				$arrEventListQuery[ 'author' ] = $post->ID;
			}else{
				$arrEventListQuery[ 'start_date' ] = date( 'Y-m-d' );
				$arrEventListQuery[ 'meta_query' ] = Array(
					Array(
						'key' => '_EventHideFromUpcoming',
						'compare' => 'NOT EXISTS',
						'value' => '',
					)
				);
			}
			$lava_user_posts = tribe_get_events( $arrEventListQuery );
		}

		$arrDashboardTabs = Array(
			'title' => Array(
				'label' => esc_html__( "Title", 'Lavacode' ),
			),
			'posted_date' => Array(
				'label' => esc_html__( "Posted Date", 'Lavacode' ),
			),
			'parent' => Array(
				'label' => esc_html__( "Parent", 'Lavacode' ),
			),
			'post_status' => Array(
				'label' => esc_html__( "Status", 'Lavacode' ),
			),
		);

		if( $objEvent->hasMessage() ) {
			echo $objEvent->outputMessage();
		}
		if( !empty( $lava_user_posts ) ) {

			/*
			if( isset( $jvfrm_spot_event_list_args[ 'before' ] ) ) {
				echo $jvfrm_spot_event_list_args[ 'before' ];
			} */
			printf( '<ul class="mypage-my-events-wrap list-group list-group-flush" data-delete-comment="%s">', esc_html__( "Do you want to delete this item?", 'javospot' ) );
			foreach( $lava_user_posts as $lava_user_event ){
				//$lava_user_posts->the_post(); ?>
				<li class="list-group-item event-<?php echo $lava_user_event->ID; ?>" data-id="<?php echo $lava_user_event->ID; ?>">

				<?php
				if( !empty( $arrDashboardTabs ) ) { foreach( $arrDashboardTabs as $strKey => $arrMeta ) {
					switch( $strKey ) {
					case 'title' : ?>
					<div class="listing-thumb">
						<?php
						printf(
							'<a href="%1$s">%2$s</a>',
							get_permalink( $lava_user_event->ID ),
							get_the_post_thumbnail( $lava_user_event->ID, Array( 50, 50 ), Array( 'class' => 'img-circle' ) )
						); ?>
					</div>

					<div class="listing-content">
						<h5 class="title"><a href="<?php echo get_permalink( $lava_user_event->ID ); ?>" class="title" target="_blank"><?php echo $lava_user_event->post_title; ?></a></h5>
						<span class="meta-taxonomies"><i class="icon-folder"></i><?php echo join( ', ', wp_get_object_terms( $lava_user_event->ID, $objEvent->getEventCategoryName(), Array( 'fields' => 'names' ) ) ); ?></span>
							<?php break;
							case 'posted_date' :
								echo '<span class="time date"><i class="icon-calender"></i>'. date_i18n( get_option( 'date_format' ), strtotime( $lava_user_event->EventStartDate ) ) ;
								echo ' ~ '. date_i18n( get_option( 'date_format' ), strtotime( $lava_user_event->EventEndDate ) ) .'</span>';
								break;
							case 'parent' :
								$objParent = get_post( $lava_user_event->post_parent );
								if( ( $objParent instanceof WP_Post ) && ! $isParentSinglePage ) {
									printf(
										'<a href="%1$s">%2$s</a> ',
										get_permalink( $lava_user_event->post_parent ),
										$objParent->post_title
									);
								}
								break;
							case 'post_status' :
								$jv_this_post_status = $lava_user_event->post_status;
								echo '<span class="post-status label label-rounded label-info">'.  (  $jv_this_post_status == 'publish' ? esc_html__( 'PUBLISH','Lavacode' ) : esc_html__( 'PENDING','Lavacode' ) )  .'</span>';
								break;
						} // switch
					} ?>
					</div><!-- listing-content -->

					<div class="listing-action btn-box">
						<?php if( get_current_user_id() == $lava_user_event->post_author && ! $isParentSinglePage ) : ?>
							<div class="lava-action text-right">
								<a href="<?php /*echo jvfrm_spot_getUserPage( $lava_user_event->post_author, 'add-event/edit/' . $lava_user_event->ID );*/ ?>" class="edit action hidden">
									<?php _e( "Edit", 'Lavacode' ); ?>
								</a>
								<a href="javascript:" class="remove action">
									<?php _e( "Remove", 'Lavacode' ); ?>
								</a>
								<?php
								do_action(
									"lava_" . jvfrm_spot_core()->slug  . "_dashboard_actions_after"
									, get_the_ID()
									, lava_directory_manager_get_option( 'page_add_' . jvfrm_spot_core()->slug )
								) ; ?>
							</div><!-- lava-action -->
						<?php endif; ?>
					</div><!-- listing-action -->
				</li><!-- listing-block-body -->
				<?php
				} // if empty check
			} //while
			echo '</ul>';
			/*
			if( isset( $jvfrm_spot_event_list_args[ 'after' ] ) ) {
				echo $jvfrm_spot_event_list_args[ 'after' ];
			} */
		} //if
		else{

			if( ! $isParentSinglePage ) {
				printf(
					'<ul class="mypage-my-events-wrap list-group list-group-flush"><li class="list-group-item text-center">%s, <a href="%s">%s</a></li></ul>',
					esc_html__( "There is no data", 'javospot' ),
					'', //jvfrm_spot_getUserPage( get_current_user_id(), 'add-event' ),
					'' // esc_html__( "Add new Event?", 'javospot' )
				);
			}
		} ?>

		<form method="post" class="hidden delete-event-form">
			<input type="hidden" name="event_id" value="">
			<input type="hidden" name="action" value="<?php echo $objEvent->getEventAction( 'delete' ); ?>">
		</form>
		<div class="hidden delete-event-loading">
			<i class="fa fa-cog fa-spin fa-3x"></i>
		</div>
	<?php
	}


	protected function render() {
		$settings = $this->get_settings();
		function_exists( 'tribe_get_events' ) && $this->getContents();
    }
}