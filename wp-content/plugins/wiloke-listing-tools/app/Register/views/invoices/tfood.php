<tfoot>
<tr>
	<th colspan="10">
		<div class="ui pagination menu" style="padding-top: 0 !important;">
			<?php for ($i = 1; $i <= $this->pagination; $i++ ) :
				$activated = $this->paged === $i ? 'active' : '';
				$aRequest['paged'] = $i;
				$aRequest['page']  = $this->slug;
				$request = '';
				foreach ($aRequest as $key => $val){
					$request .= empty($request) ? $key.'='.$val :  '&'.$key.'='.$val;
				}
				?>
				<a class="<?php echo esc_attr($activated); ?> item" href="<?php echo esc_url(admin_url('admin.php?'.$request)); ?>"><?php echo esc_html($i); ?></a>
			<?php endfor; ?>
		</div>
	</th>
</tr>
</tfoot>