<?php
if( ! function_exists( 'WC' ) ) {
	return false;
} ?>
<div class="row">
	<div class="col-md-12">
		<div class="card jv-simple-table">
			<div class="card-header"><h4 class="card-title"><?php esc_html_e( "My Orders", 'jvfrmtd' ); ?></h4></div><!-- card-header -->
			<div class="card-block">
			<!-- Starting Content -->

			<div class="table-responsive">
				<?php
				$my_orders_columns = apply_filters( 'woocommerce_my_account_my_orders_columns', array(
					'order-number'  => __( 'Order', 'jvfrmtd' ),
					'order-date'    => __( 'Date', 'jvfrmtd' ),
					'order-status'  => __( 'Status', 'jvfrmtd' ),
					'order-total'   => __( 'Total', 'jvfrmtd' ),
					'order-actions' => '&nbsp;',
				) );

				$customer_orders = get_posts( apply_filters( 'woocommerce_my_account_my_orders_query', array(
					'meta_key'    => '_customer_user',
					'meta_value'  => get_current_user_id(),
					'post_type'   => wc_get_order_types( 'view-orders' ),
					'post_status' => array_keys( wc_get_order_statuses() )
				) ) ); ?>

				<table class="table product-overview" id="myTable">
					<thead>
						<tr>
							<?php foreach ( $my_orders_columns as $column_id => $column_name ) : ?>
								<th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
							<?php endforeach; ?>
						</tr>
					</thead>

					<tbody>
						<?php foreach ( $customer_orders as $customer_order ) :
							$order      = wc_get_order( $customer_order );
							$item_count = $order->get_item_count();
							?>
							<tr class="order">
								<?php foreach ( $my_orders_columns as $column_id => $column_name ) : ?>
									<td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
										<?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
											<?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>

										<?php elseif ( 'order-number' === $column_id ) : ?>
											<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
												<?php echo _x( '#', 'hash before order number', 'jvfrmtd' ) . $order->get_order_number(); ?>
											</a>

										<?php elseif ( 'order-date' === $column_id ) : ?>
											<time datetime="<?php echo date( 'Y-m-d', strtotime( $order->get_date_completed() ) ); ?>" title="<?php echo esc_attr( date( 'Y-m-d', strtotime( $order->get_date_completed() ) ) ); ?>"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->get_date_completed() ) ); ?></time>

										<?php elseif ( 'order-status' === $column_id ) : ?>
											<?php echo wc_get_order_status_name( $order->get_status() ); ?>

										<?php elseif ( 'order-total' === $column_id ) : ?>
											<?php echo sprintf( _n( '%s for %s item', '%s for %s items', $item_count, 'jvfrmtd' ), $order->get_formatted_order_total(), $item_count ); ?>

										<?php elseif ( 'order-actions' === $column_id ) : ?>
											<?php
												$actions = array(
													'pay'    => array(
														'url'  => $order->get_checkout_payment_url(),
														'name' => __( 'Pay', 'jvfrmtd' )
													),
													'view'   => array(
														'url'  => $order->get_view_order_url(),
														'name' => __( 'View', 'jvfrmtd' )
													),
													'cancel' => array(
														'url'  => $order->get_cancel_order_url( wc_get_page_permalink( 'myaccount' ) ),
														'name' => __( 'Cancel', 'jvfrmtd' )
													)
												);

												if ( ! $order->needs_payment() ) {
													unset( $actions['pay'] );
												}

												if ( ! in_array( $order->get_status(), apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order ) ) ) {
													unset( $actions['cancel'] );
												}

												if ( $actions = apply_filters( 'woocommerce_my_account_my_orders_actions', $actions, $order ) ) {
													foreach ( $actions as $key => $action ) {
														echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
													}
												}
											?>
										<?php endif; ?>
									</td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			</div><!-- .card-block -->

		</div><!-- /.jv-simple-table -->
	</div> <!-- col-md-12 -->
</div><!--/row-->