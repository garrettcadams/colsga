<?php
/**
 *	Map template loop
 *
 */

?>

<div id="lavaPost-{post_id}" class="lava-list-item">
	<div class="lv-item-thumbnail">

		<img src="{thumbnail_url}" width="80" height="80">

	</div><!-- lv-item-thumbnail -->

	<div class="lv-item-name-wrap">

		<div class="item-name">
			<a href="{permalink}">{post_title}</a>
		</div><!-- item-name -->

		<div class="lv-item-info">
			<span>{item-type}</span>
			<span>{item-city}</span>
		</div>

	</div><!-- lv-item-name-wrap -->

	<div class="item-author">{post_author}</div>
</div>