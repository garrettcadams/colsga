<div class="row map-filter-menu">
	<div class="col-auto text-left">
		<?php if( function_exists( 'lv_directoryReview' ) ) { ?>
			<div class="btn-group menu-item" data-menu-filter="rating">
				<button class="btn dropdown-toggle btn-sm" type="button"data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?php esc_html_e( "Rating", 'jvfrmtd' ); ?>
				</button>
				<div class="dropdown-menu">
					<div class="dropdown-item" data-value="high"><?php esc_html_e( "By High Rated", 'jvfrmtd'); ?></div>
					<div class="dropdown-item" data-value="low"><?php esc_html_e( "By Low Rated", 'jvfrmtd'); ?></div>
					<div class="dropdown-divider"></div>
					<?php
					for( $intRateNumeric=5; $intRateNumeric>=1;$intRateNumeric-- ) {
						printf( '<div class="dropdown-item" data-value="%1$s">%1$s</div>', $intRateNumeric );
					} ?>
				</div>
			</div>
		<?php } ?>

		<div class="btn-group menu-item" data-menu-filter="order">
			<button class="btn dropdown-toggle btn-sm" type="button"data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<?php esc_html_e( "Sort By", 'jvfrmtd' ); ?>
			</button>
			<div class="dropdown-menu">
				<div class="dropdown-item" data-value="name" data-type="desc">
					<?php esc_html_e( "Name", 'jvfrmtd'); ?>
					<span class="glyphicon glyphicon-arrow-up asc hidden"></span>
					<span class="glyphicon glyphicon-arrow-down desc"></span>
				</div>
				<div class="dropdown-item" data-value="date" data-type="desc">
					<?php esc_html_e( "Date", 'jvfrmtd'); ?>
					<span class="glyphicon glyphicon-arrow-up asc hidden"></span>
					<span class="glyphicon glyphicon-arrow-down desc"></span>
				</div>
			</div>
		</div>

		<div class="btn-group menu-item" data-menu-filter="reviewed">
			<button class="btn btn-sm" type="button" data-toggle="button" data-value="1" aria-haspopup="true" aria-expanded="false">
				<?php esc_html_e( "Most reviewed", 'jvfrmtd' ); ?>
			</button>
		</div>

		<div class="btn-group menu-item" data-menu-filter="favorite">
			<button class="btn btn-sm" type="button" data-toggle="button" data-value="1" aria-haspopup="true" aria-expanded="false">
				<?php esc_html_e( "Favorites", 'jvfrmtd' ); ?>
			</button>
		</div>

		<div class="btn-group menu-item" data-menu-filter="openhour">
			<button class="btn btn-sm" type="button" data-toggle="button" data-value="1" aria-haspopup="true" aria-expanded="false">
				<?php esc_html_e( "Open now", 'jvfrmtd' ); ?>
			</button>
		</div>

	</div>
	<div class="col text-right">
		<div class='btn-group module-switcher' data-toggle="buttons">
			<label class="btn active">
				<input type="radio" name="module_switcher" value="grid" autocomplete="off" checked="checked">
				<span class='fa fa-th'></span>
			</label>
			<label class="btn">
				<input type="radio" name="module_switcher" value="list" autocomplete="off">
				<span class='fa fa-list'></span>
			</label>
		</div>
	</div>
</div>
<div class="row more-taxo-wrap">
	<div class="col">
		<?php do_action( 'jvbpd_map_more_filters' ); ?>
	</div>
</div>