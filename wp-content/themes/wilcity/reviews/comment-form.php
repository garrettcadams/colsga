<?php
global $post, $wilcityReviewConfiguration;
if ( !isset($wilcityReviewConfiguration['enableReview']) || !$wilcityReviewConfiguration['enableReview'] ){
    return '';
}
?>
<comment-form heading="<?php echo esc_attr__('Discussion', 'wilcity'); ?>" post-comment-text="<?php echo esc_attr__('Post Discussion', 'wilcity'); ?>" heading-icon="la la-comments" post-id="<?php echo esc_attr($post->ID); ?>" label="<?php echo esc_attr__('Discussion', 'wilcity'); ?>" is-user-logged-in="<?php echo is_user_logged_in() ? 'yes' : 'no'; ?>"></comment-form>
