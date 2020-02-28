<?php
global $post;
if ( $post->post_author == get_current_user_id() || !\WilokeListingTools\Models\PostModel::isClaimed($post->ID) ){
    return '';
}
?>
<div class="listing-detail_rightItem__2CjTS wilcity-single-tool-inbox">
    <message-btn btn-name="<?php esc_html_e('Message', 'wilcity'); ?>"></message-btn>
</div>
