<?php
$arrPages = jvbpd_tso()->getPages(); ?>
<div class="jvbpd_ts_tab javo-opts-group-tab hidden" tar="lvdr">
	<h2> <?php esc_html_e( "Page Settings", 'jvfrmtd' ); ?> </h2>
	<table class="form-table">
	<tr><th>
		<?php esc_html_e( "Post pages", 'jvfrmtd' );?>
		<span class="description"></span>
	</th><td>
		<?php
			foreach( Array(
				'single_post_template' => Array(
					'label' => esc_html__( "Default Single Post Detail template", 'listopia' ),
					'description' => sprintf(
						'To create more headers, Core >> page builder ( <a href="%1$s" target="_blank">Go page builder</a> )',
						esc_url( add_query_arg( Array( 'post_type' => 'jvbpd-listing-elmt', ), admin_url( 'edit.php' ) ) )
					),
					'options' => jvbpdCore()->admin->getElementorSinglePostID(),
				),
				//'archive_post_template' => Array(
				//	'label' => esc_html__( "Default Post Archive template", 'listopia' ),
				//	'options' => jvbpdCore()->admin->getElementorArchiveID(),
				//),
			) as $elementor_single_key => $elementor_single_meta ) {
				?>
				<h4><?php echo esc_html( $elementor_single_meta[ 'label' ] ); ?></h4>
				<fieldset class="inner">
					<select name="jvbpd_ts[<?php echo $elementor_single_key; ?>]">
						<?php
						foreach( $elementor_single_meta[ 'options' ] as $template_id  ) {
							printf(
								'<option value="%1$s"%3$s>%2$s</option>', $template_id, get_the_title( $template_id ),
								selected( $template_id == jvbpd_tso()->get( $elementor_single_key, '' ), true, false )
							);
						} ?>
					</select>
					<?php
					if( isset( $elementor_single_meta[ 'description' ] ) ) { ?>
						<div>
							<span class="description"><?php echo $elementor_single_meta[ 'description' ]; ?></span>
						</div>
						<?php
					} ?>
				</fieldset>
				<?php
			}
		?>

	</td></tr>
        <?php
        if( function_exists( 'lava_directory' ) ) { ?>
            <tr>
                <th>
                    <?php esc_html_e("Listing pages", 'jvfrmtd'); ?>
                    <span class="description"></span>
                </th>
                <td>
                    <h4><?php esc_html_e("Add listing", 'jvfrmtd'); ?></h4>
                    <fieldset class="inner">
                        <select name="jvbpd_ts[add_listing]">
                            <option value=""><?php esc_html_e("Select a page", 'jvfrmtd'); ?></option>
                            <?php
                            foreach( $arrPages as $objPage) {
                                printf(
                                    '<option value="%1$s"%3$s>%2$s</option>',
                                    $objPage->ID, $objPage->post_title,
                                    selected(jvbpd_tso()->get('add_listing') == $objPage->ID, true, false)
                                );
                            } ?>
                        </select>
                    </fieldset>
                    <?php
                    if (function_exists('jvbpdCore')) :
                        foreach( Array(
                                     'single_lv_listing_template' => Array(
                                         'label' => esc_html__("Default Single Listing template", 'listopia'),
                                         'description' => sprintf(
                                             'To create more headers, Core >> page builder ( <a href="%1$s" target="_blank">Go page builder</a> )',
                                             esc_url(add_query_arg(Array('post_type' => 'jvbpd-listing-elmt',), admin_url('edit.php')))
                                         ),
                                         'options' => jvbpdCore()->admin->getElementorSingleID(),
                                     ),
                                     'archive_lv_listing_template' => Array(
                                         'label' => esc_html__("Default Listing Archive template", 'listopia'),
                                         'options' => jvbpdCore()->admin->getElementorArchiveID(),
                                     ),
                                 ) as $elementor_single_key => $elementor_single_meta) {
                            ?>
                            <h4><?php echo esc_html($elementor_single_meta['label']); ?></h4>
                            <fieldset class="inner">
                                <select name="jvbpd_ts[<?php echo $elementor_single_key; ?>]">
                                    <?php
                                    foreach( $elementor_single_meta['options'] as $template_id) {
                                        printf(
                                            '<option value="%1$s"%3$s>%2$s</option>', $template_id, get_the_title($template_id),
                                            selected($template_id == jvbpd_tso()->get($elementor_single_key, ''), true, false)
                                        );
                                    } ?>
                                </select>
                                <?php
                                if (isset($elementor_single_meta['description'])) { ?>
                                    <div>
                                        <span class="description"><?php echo $elementor_single_meta['description']; ?></span>
                                    </div>
                                    <?php
                                } ?>
                            </fieldset>
                            <?php
                        }
                    endif; ?>

                </td>
            </tr>
            <tr>
            <th>
                <?php esc_html_e("Shortcode", 'jvfrmtd'); ?>
                <span class="description"></span>
            </th>
            <td>
                <h4><?php esc_html_e("Header search shortcode result page", 'jvfrmtd'); ?></h4>
                <fieldset class="inner">
                    <select name="jvbpd_ts[search_sesult_page]">
                        <option value=""><?php esc_html_e("Select a page", 'jvfrmtd'); ?></option>
                        <?php
                        foreach ($arrPages as $objPage) {
                            printf(
                                '<option value="%1$s"%3$s>%2$s</option>',
                                $objPage->ID, $objPage->post_title,
                                selected(jvbpd_tso()->get('search_sesult_page') == $objPage->ID, true, false)
                            );
                        } ?>
                    </select>
                </fieldset>
            </td></tr><?php
        } // condition for lava_directory class
        if( function_exists( 'lava_WCP' ) ) { ?>
            <tr>
                <th>
                    <?php esc_html_e("Product pages", 'jvfrmtd'); ?>
                    <span class="description"></span>

                </th>
                <td>
                    <?php
                    foreach( Array(
                        'single_product_template' => Array(
                            'label' => esc_html__("Default Single Product Template", 'listopia'),
                            'options' => lava_WCP()->template->getSelectProductTemplates(),
                        ),
                    ) as $elementor_single_key => $elementor_single_meta) {
                        ?>
                        <h4><?php echo esc_html($elementor_single_meta['label']); ?></h4>
                        <fieldset class="inner">
                            <select name="jvbpd_ts[<?php echo $elementor_single_key; ?>]">
                                <?php
                                foreach( $elementor_single_meta['options'] as $template_label => $template_id ) {
                                    printf(
                                        '<option value="%1$s"%3$s>%2$s</option>', $template_id, (''==$template_id? $template_label: get_the_title($template_id)),
                                        selected($template_id == jvbpd_tso()->get($elementor_single_key, ''), true, false)
                                    );
                                } ?>
                            </select>
                            <?php
                            if (isset($elementor_single_meta['description'])) { ?>
                                <div>
                                    <span class="description"><?php echo $elementor_single_meta['description']; ?></span>
                                </div>
                                <?php
                            } ?>
                        </fieldset>
                    <?php } ?>
            </td></tr><?php
        } // condition for WC Page Builder class
        ?>

	</table>
</div>