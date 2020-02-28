<div id="wiloke-add-listing-fields">
    <div class="semantic-tabs ui top attached tabular menu">
        <div class="active item" data-tab="generalsettings">General Settings</div>
        <div class="item" data-tab="fieldsettings">Field Settings</div>
        <div class="item" data-tab="single-event-content">Single Content</div>
        <div class="item" data-tab="search-form">Search Form</div>
        <div class="item" data-tab="hero-search-form">Hero Search Form</div>
        <div class="item" data-tab="wiloke-schema-markup">Schema Markup</div>
    </div>
    <?php
        require_once plugin_dir_path(__FILE__) . 'general.php';
        require_once plugin_dir_path(__FILE__) . 'fields.php';
        require_once plugin_dir_path(__FILE__) . '../listing-settings/search-form.php';
        require_once plugin_dir_path(__FILE__) . 'content.php';
        require_once plugin_dir_path(__FILE__) . '../listing-settings/hero-search-form.php';
        require_once plugin_dir_path(__FILE__) . '../listing-settings/schema-markup.php';
    ?>
</div>
