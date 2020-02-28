<?php
/**
 * Type C - Dashboard
 * My Dashboard > Favorites
 *
 */
if( ! function_exists( 'lv_directory_favorite' ) || ! class_exists( 'lvDirectoryFavorite_button' ) ) {
	die;
}

 ?>
	<!-- Content Start -->
<div class="jv-user-content">

<div class="card listing-card">
	<div class="card-header"><h4 class="card-title"><?php esc_html_e( "My Favorite Listings", 'jvfrmtd' ); ?></h4></div><!-- card-header -->
	<ul class="list-group list-group-flush">
		<?php jvbpdCore()->template_instance->load_template( '../dir/mypage/favorites/favorites-content' ); ?>
	</ul>
</div><!-- card -->
</div>
	<!-- Content End -->