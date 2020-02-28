<div class="searchform">
	<form class="form ui" action="<?php echo esc_url(admin_url('admin.php')); ?>" method="GET">
		<div class="equal width fields">
			<input type="hidden" name="paged" value="<?php echo esc_attr($this->paged); ?>">
			<input type="hidden" name="page" value="<?php echo esc_attr($this->slug); ?>">
			<div class="search-field field">
				<label for="payment_status"><?php esc_html_e('Status', 'wiloke-listing-tools'); ?></label>
				<select id="payment_status" class="ui dropdown" name="payment_status">
					<?php
					foreach (wilokeListingToolsRepository()->get('sales:status') as $status => $title) :
						$selected = isset($_REQUEST['payment_status']) && $_REQUEST['payment_status'] === $status ? 'selected' : '';
						?>
						<option value="<?php echo esc_attr($status); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($title); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
            <div class="search-field field">
                <label for="plan_type"><?php esc_html_e('Plan Type', 'wiloke-listing-tools'); ?></label>
                <select id="plan_type" class="ui dropdown" name="plan_type">
					<?php
					foreach (wilokeListingToolsRepository()->get('payment:planTypes') as $plan => $title) :
						$selected = isset($_REQUEST['plan_type']) && $_REQUEST['plan_type'] === $plan ? 'selected' : '';
						?>
                        <option value="<?php echo esc_attr($plan); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($title); ?></option>
					<?php endforeach; ?>
                </select>
            </div>

            <div class="search-field field">
                <label for="filter-by-date"><?php esc_html_e('Date', 'wiloke-listing-tools'); ?></label>
                <select id="filter-by-date" class="ui dropdown" name="date">
					<?php foreach ($this->aFilterByDate as $date => $title):
						$selected = isset($_REQUEST['date']) && $_REQUEST['date'] === $date ? 'selected' : '';
						?>
                        <option value="<?php echo esc_attr($date); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($title); ?></option>
					<?php endforeach; ?>
                </select>

                <div id="filter-by-period" class="transition hidden search-field two fields wiloke-has-dependency" data-dependency='<?php echo json_encode(array('name'=>'date', 'value'=>'period')); ?>'>
                    <div class="field">
                        <input class="wiloke_datepicker" type="text" name="from" value="" placeholder="<?php esc_html_e('Date Start', 'wiloke-listing-tools'); ?>">
                    </div>
                    <div class="field">
                        <input class="wiloke_datepicker" type="text" name="to" value="" placeholder="<?php esc_html_e('Date End', 'wiloke-listing-tools') ?>">
                    </div>
                </div>
            </div>

            <div class="search-field field">
                <label for="filter-by-gateway"><?php esc_html_e('Gateway', 'wiloke-listing-tools'); ?></label>
                <select id="filter-by-gateway" class="ui dropdown" name="gateway">
					<?php
					foreach (wilokeListingToolsRepository()->get('payment:gateways') as $gateway => $gatewayName) :
						$selected = $this->gateway === $gateway ? 'selected' : '';
						?>
                        <option value="<?php echo esc_attr($gateway); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($gatewayName); ?></option>
					<?php endforeach; ?>
                </select>
            </div>

			<div class="search-field field">
				<label for="posts_per_page"><?php esc_html_e('Posts Per Page', 'wiloke-listing-tools'); ?></label>
				<input id="posts_per_page" type="text" name="posts_per_page" value="<?php echo esc_attr($this->postPerPages); ?>">
			</div>
			<div class="search-field field">
				<label for="posts_per_page"><?php esc_html_e('Apply', 'wiloke-listing-tools'); ?></label>
				<input type="submit" class="button ui basic green" value="<?php esc_html_e('Filter', 'wiloke-listing-tools'); ?>">
			</div>
		</div>
	</form>
</div>