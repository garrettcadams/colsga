<?php

$strTitleString = esc_html__( "Page Title", 'jvfrmtd' );
if( jvbpd_bp()->is_bp_page( Array( 'member' ), true ) ) {
	$strTitleString = esc_html__( "Total Member Title", 'jvfrmtd' );
}elseif( jvbpd_bp()->is_bp_page( Array( 'group' ), true ) ) {
	$strTitleString = esc_html__( "Total Group Title", 'jvfrmtd' );
}elseif( jvbpd_bp()->is_bp_page( Array( 'activity' ), true ) ) {
	$strTitleString = esc_html__( "Total Activity Title", 'jvfrmtd' );
}elseif( jvbpd_bp()->is_bp_page( Array( 'register' ) ) ) {
	$strTitleString = esc_html__( "Register Title", 'jvfrmtd' );
} ?>
<div class="page-header" style="background-color:#e8e8e8;">
	<div class="container">
		<div class="ph-inner">
			<div class="pull-left"><h1><?php echo esc_html( $strTitleString ); ?></h1></div>
			<div class="pull-right">
				<?php echo jvbpd_layout()->getBreadcrumb(); ?>
				<?php /*
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item active">Library</li>
				</ol> */ ?>
			</div>
		</div>
	</div><!-- container -->
</div><!--page-header-->