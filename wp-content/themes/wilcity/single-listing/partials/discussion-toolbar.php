<?php global $wilcityoDiscussion; ?>
<div class="dropdown_module__J_Zpj">
	<div class="dropdown_threeDots__3fa2o wilcity-discussion-toggle-toolbar-<?php echo esc_attr($wilcityoDiscussion->ID); ?>" data-toggle-button="dropdown" data-body-toggle="true"><span class="dropdown_dot__3I1Rn"></span><span class="dropdown_dot__3I1Rn"></span><span class="dropdown_dot__3I1Rn"></span></div>
	<div class="dropdown_itemsWrap__2fuze" data-toggle-content="dropdown">
		<!-- list_module__1eis9 list-none -->
		<ul class="list_module__1eis9 list-none list_small__3fRoS list_abs__OP7Og arrow--top-right ">
			<li class="list_item__3YghP">
                <a class="wilcity-edit-discussion text-ellipsis color-primary--hover" href="#" data-id="<?php echo esc_attr($wilcityoDiscussion->ID); ?>">
                    <span class="list_icon__2YpTp"><i class="la la-edit"></i></span>
                    <span class="list_text__35R07"><?php esc_html_e('Edit', 'wilcity'); ?></span>
                </a>
            </li>
			<li class="list_item__3YghP">
                <a class="list_link__2rDA1 text-ellipsis color-primary--hover wilcity-delete-discussion wilcity-delete-discussion-<?php echo esc_attr($wilcityoDiscussion->ID); ?>" href="#" data-id="<?php echo esc_attr($wilcityoDiscussion->ID); ?>">
                    <span class="list_icon__2YpTp"><i class="la la-trash"></i></span>
                    <span class="list_text__35R07"><?php esc_html_e('Delete', 'wilcity'); ?></span>
                </a>
            </li>
		</ul><!-- End /  list_module__1eis9 list-none -->
	</div>
</div>