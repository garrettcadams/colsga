<!-- Left navbar-header -->
<div class="navbar-default sidebar vertical-sidebar left-sidebar fixed" role="navigation">
    <div class="sidebar-nav navbar-collapse collapse show">
		<div class="sidebar-swicher-wrap bg-secondary p-4">
			<a href="javascript:void(0)" class="dashboard-sidebar-switcher no-hover-effect"><i class=" jvbpd-icon2-arrow-right"></i></a>
		</div>
		<div class="left-sidebar-brand"></div>
		<?php
		wp_nav_menu([
			'menu'            => 'sidebar_left',
			'theme_location'  => 'sidebar_left',
			'container'       => 'div',
			'container_id'    => 'jv-slidebar-left-container',
			'container_class' => 'sidebar-nav navbar-collapse collapse show',
			'menu_id'         => 'jv-slidebar-left',
			'menu_class'      => 'nav jvbpd-nav flex-column',
			'echo'          => true,
			'depth'           => 3,
			'fallback_cb'     => 'jvnavwalker::fallback',
			'walker'          => new jvnavwalker()
		]); ?>
    </div>
</div>
<!-- Left navbar-header end -->