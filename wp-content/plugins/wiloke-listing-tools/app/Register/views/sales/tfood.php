<?php if ( $this->pagination !== 1 && $this->pagination !== 0 ) : ?>
	<tfoot>
	<tr>
		<th colspan="8">
			<div class="ui center pagination menu" style="padding-top: 0 !important;">
				<?php for ($i = 1; $i <= $this->pagination; $i++ ) :
					$activated = $this->paged === $i ? 'active' : '';
					$aRequest['page']  = $this->slug;
					$url = admin_url('admin.php');
					$aArgs['paged'] = $i;
					$aArgs['page']  = $this->slug;
					$aArgs['posts_per_page']  = $this->postPerPages;
					$aArgs['gateway']  = $this->gateway;

					foreach ($aRequest as $key => $val){
						$aArgs[$key] = $val;
					}

					$url = add_query_arg(
                        $aArgs,
                        $url
                    );
					?>
					<a class="<?php echo esc_attr($activated); ?> item" href="<?php echo esc_url($url); ?>"><?php echo esc_html($i); ?></a>
				<?php endfor; ?>
			</div>
		</th>
	</tr>
	</tfoot>
<?php endif; ?>