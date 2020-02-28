<?php
global $wilcityArgs;
use WilokeListingTools\Frontend\User;

$authorID  = get_query_var( 'author' );
?>
<header class="author-hero_header__Td5ex">
	<div class="author-hero_img__2WNNZ pos-a-full bg-cover" style="background-image: url('<?php echo esc_url(User::getCoverImage($authorID)) ?>');">
        <img src="<?php echo esc_url(User::getCoverImage($authorID)) ?>" alt="<?php echo esc_attr(User::getField('display_name', $authorID)); ?>">
    </div>
	<div class="wil-overlay"></div>
</header>
