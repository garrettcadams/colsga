<?php
function wilcityRenderSidebarHeader($title, $icon, $isReturn = false){
    if ( $isReturn ){
        ob_start();
    }
	?>
	<header class="content-box_header__xPnGx clearfix">
		<div class="wil-float-left">
			<h4 class="content-box_title__1gBHS"><i class="<?php echo esc_attr($icon); ?>"></i><span><?php echo esc_html($title);?></span></h4>
		</div>
	</header>
	<?php
    if ( $isReturn ){
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}