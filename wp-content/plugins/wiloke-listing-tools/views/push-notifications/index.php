<div id="wiloke-push-notification-settings">
	<div class="semantic-tabs ui top attached tabular menu">
		<div class="active item" data-tab="firebase-settings">Firebase Settings</div>
		<div class="item" data-tab="admin-push-notification-settings">Push Notification To Admin</div>
		<div class="item" data-tab="customer-push-notification-settings">Push Notifications to Customers</div>
	</div>

	<?php require_once plugin_dir_path(__FILE__) . 'firebase-settings.php'; ?>
	<?php require_once plugin_dir_path(__FILE__) . 'admin-settings.php'; ?>
	<?php require_once plugin_dir_path(__FILE__) . 'customer-settings.php'; ?>
</div>