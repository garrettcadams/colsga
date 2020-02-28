<?php
// Register Custom Navigation Walker
?>
<!-- .quick-view -->
<div class="quick-view">
    <div class="quick-header">
        <a class="navbar-brand" href="<?php echo home_url( '/' ); ?>">
            <?php esc_html_e( "Site Menu", 'jvbpd' ); ?>
        </a>
        <span class="overlay-sidebar-opener"><i class="jvbpd-icon2-none"></i></span>
    </div>
    <div class="quick-view-body">
        <div class="navbar-default sidebar vertical-sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse collapse">
            <?php
            wp_nav_menu([
                'menu'            => 'sidebar_right',
                'theme_location'  => 'sidebar_right',
                'container'       => 'div',
                'container_id'    => 'jv-slidebar-right-container',
                'container_class' => 'sidebar-nav navbar-collapse collapse',
                'menu_id'         => 'jv-slidebar-right',
                'menu_class'      => 'nav jvbpd-nav',
                'echo'          => true,
                'depth'           => 3,
                'fallback_cb'     => 'jvnavwalker::fallback',
                'walker'          => new jvnavwalker()
            ]); ?>
        </div>
        </div>
    </div>
</div>
<!-- /.quick-view -->