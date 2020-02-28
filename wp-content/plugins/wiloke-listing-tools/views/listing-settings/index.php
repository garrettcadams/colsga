<div id="wiloke-add-listing-fields">
    <div class="semantic-tabs ui top attached tabular menu">
        <div class="active item" data-tab="addlisting">Add Listing Fields</div>
        <div class="item" data-tab="listing-card">Listing Card</div>
        <div class="item" data-tab="reviewsetting">Review Settings</div>
        <div class="item" data-tab="single-highlightboxes-tab">Single Highlight Box</div>
        <div class="item" data-tab="single-nav">Single Navigation</div>
        <div class="item" data-tab="single-sidebar">Single Sidebar</div>
        <div class="item" data-tab="search-form">Search Form</div>
        <div class="item" data-tab="hero-search-form">Hero Search Form</div>
        <div class="item" data-tab="wiloke-schema-markup">Schema Markup</div>
    </div>

	<?php
        require_once plugin_dir_path(__FILE__) . 'addlisting.php';
        require_once plugin_dir_path(__FILE__) . 'listing-card.php';
        require_once plugin_dir_path(__FILE__) . 'reviews.php';
        require_once plugin_dir_path(__FILE__) . 'single-highlightbox.php';
        require_once plugin_dir_path(__FILE__) . 'single-nav.php';
        require_once plugin_dir_path(__FILE__) . 'single-sidebar.php';
        require_once plugin_dir_path(__FILE__) . 'search-form.php';
        require_once plugin_dir_path(__FILE__) . 'hero-search-form.php';
        require_once plugin_dir_path(__FILE__) . 'schema-markup.php';
	?>
</div>