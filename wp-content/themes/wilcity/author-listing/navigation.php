<?php
$authorID = get_query_var('author');
$authorPageUrl = get_author_posts_url($authorID);
$mode = get_query_var('mode');

use WilokeListingTools\Frontend\User;
use WilokeListingTools\Controllers\AuthorPageController;
?>
<!-- detail-navtop_module__zo_OS -->
<div class="detail-navtop_module__zo_OS js-detail-navtop">
	<div class="container">
		<nav class="detail-navtop_nav__1j1Ti">

			<!-- list_module__1eis9 list-none -->
			<ul class="list_module__1eis9 list-none list_horizontal__7fIr5">
                <?php if ( !empty(User::getField('description', $authorID)) ) : ?>
                <li class="<?php echo esc_attr(AuthorPageController::navigationWrapperClass($mode, 'about|empty')); ?>">
					<a class="list_link__2rDA1 text-ellipsis color-primary--hover" href="<?php echo esc_url($authorPageUrl.AuthorPageController::getAuthorMode('about')); ?>">
						<span class="list_icon__2YpTp"><i class="la la-user"></i></span>
						<span class="list_text__35R07"><?php esc_html_e('About', 'wilcity'); ?></span>
					</a>
				</li>
                <?php endif; ?>

                <?php
                $aPostTypes = AuthorPageController::getAuthorPostTypes($authorID);
                if ( !empty($aPostTypes) ):
                    foreach ($aPostTypes as $postType => $aPostTypeInfo) :
                ?>
                    <li class="<?php echo esc_attr(AuthorPageController::navigationWrapperClass($mode, $postType)); ?>">
                        <a class="list_link__2rDA1 text-ellipsis color-primary--hover" href="<?php echo esc_url($authorPageUrl.$postType); ?>">
                            <span class="list_icon__2YpTp"><i class="<?php echo esc_attr($aPostTypeInfo['icon']); ?>"></i></span>
                            <span class="list_text__35R07"><?php echo absint($aPostTypeInfo['totalPosts']) > 1 ? esc_html($aPostTypeInfo['name']) : esc_html($aPostTypeInfo['singular_name']); ?> (<?php echo esc_html($aPostTypeInfo['totalPosts']); ?>)</span>
                        </a>
                    </li>
                <?php
                    endforeach;
                endif;
                ?>

			</ul>
		</nav>

        <?php if ( is_user_logged_in() && ( get_current_user_id() == $authorID ) ) : ?>
		<div class="detail-navtop_right__KPAlw">
			<a class="wil-btn wil-btn--primary wil-btn--round wil-btn--md wil-btn--block" href="<?php echo esc_url(\WilokeListingTools\Framework\Helpers\GetWilokeSubmission::getField('dashboard_page', true)); ?>">
                <i class="la la-home"></i> <?php esc_html_e('Dashboard', 'wilcity'); ?>
			</a>
		</div>
        <?php endif; ?>

	</div>
</div><!-- End / detail-navtop_module__zo_OS -->