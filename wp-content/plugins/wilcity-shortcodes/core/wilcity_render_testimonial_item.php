<?php
function wilcity_render_testimonial_item($icon, $oTestimonial){
	$oTestimonial = is_array($oTestimonial) ? (object)$oTestimonial : $oTestimonial;
    ?>
    <!-- quote_module__3GP7P wil-text-center -->
    <div class="quote_module__3GP7P wil-text-center">
        <div class="quote_icon__27ALW"><i class="<?php echo esc_attr($icon); ?>"></i></div>
        <div class="quote_content__1qqMu"><?php Wiloke::ksesHTML($oTestimonial->testimonial); ?></div>
        
        <!-- utility-box-1_module__MYXpX -->
        <div class="utility-box-1_module__MYXpX wil-align-center">
            <?php if ( !empty($oTestimonial->avatar) ) : ?>
	            <?php $avatarUrl = is_numeric($oTestimonial->avatar) ? wp_get_attachment_image_url($oTestimonial->avatar, array(100, 100)) : $oTestimonial->avatar; ?>
                <div class="utility-box-1_avatar__DB9c_ rounded-circle" style="background-image: url(<?php echo esc_url($avatarUrl); ?>);">
                    <img src="<?php echo esc_url($avatarUrl); ?>" alt="<?php echo esc_attr($oTestimonial->name); ?>"/>
                </div>
            <?php endif; ?>
            <div class="utility-box-1_body__8qd9j">
                <div class="utility-box-1_group__2ZPA2">
                    <h3 class="utility-box-1_title__1I925"><?php echo esc_html($oTestimonial->name); ?></h3>
                </div>
                <?php if ( !empty($oTestimonial->profesional) ) : ?>
                <div class="utility-box-1_description__2VDJ6"><?php echo esc_html($oTestimonial->profesional); ?></div>
                <?php endif; ?>
            </div>
        </div><!-- End / utility-box-1_module__MYXpX -->
    </div><!-- End / quote_module__3GP7P wil-text-center -->
    <?php
}