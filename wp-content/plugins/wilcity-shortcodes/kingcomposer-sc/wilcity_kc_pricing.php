<?php
$atts = shortcode_atts(
    array(
        'include'       => '',
        '_id'      => '',
        'items_per_row' => 'col-md-4',
        'extra_class'   => '',
        'listing_type'  => 'flexible',
        'toggle_nofollow' => 'disable',
        'css'           => ''
    ),
	$atts
);

wilcityPricing($atts);