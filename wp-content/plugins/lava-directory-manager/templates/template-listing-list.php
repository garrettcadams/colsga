<?php
/**
 *	Listings template loop
 *
 */
?>
<div class="lava-directory-manager-listing-item item-{post_id}">
	<div class="lava-image">
		<a href="{permalink}">
			<div class="lava-thb" style="width:100%;height:100px;{thumbnail}"></div>
			<strong>{author-name}</strong>
		</a>
	</div>
	<div class="description">
		<a href="{permalink}"> <h1>{post-title}</h1> </a>
		<ul>
			<li class="meta-type"><span>{listing_category}</span></li>
			<li class="meta-location"><span>{listing_location}</span></li>
		</ul>
	</div>
</div>