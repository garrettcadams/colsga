<?php
class WilokeMessage {
    /*
     * @array $aConfiguration: status, hasRemoveBtn, $msgIcon, $msg
     */
	public static function message($aConfiguration, $isReturn=false){
		$aConfiguration = wp_parse_args(
			$aConfiguration,
			array(
				'status'       => 'success',
				'hasRemoveBtn' => false,
				'hasMsgIcon'   => false,
				'msgIcon'      => 'la la-envelope-o',
				'msg'          => ''
			)
		);

		$wrapperClass = '';
		switch ($aConfiguration['status']){
			case 'success':
				$wrapperClass = 'alert_success__1nkos';
				break;
			case 'nothing':
				$wrapperClass = '';
				break;
			case 'danger':
				$wrapperClass = 'alert_danger__2ajVf';
				break;
			case 'warning':
				$wrapperClass = 'alert_warning__2IUiO';
				break;
			case 'info':
				$wrapperClass = 'alert_info__2dwkg';
				break;
			case 'grey':
				$wrapperClass = 'alert_dark__3ks';
				break;
		}

		if ( $isReturn ){
		    ob_start();
        }
		?>
		<div class="alert_module__Q4QZx <?php echo esc_attr($wrapperClass); ?>">
			<?php if ( $aConfiguration['hasMsgIcon'] ) : ?>
			<div class="alert_icon__1bDKL"><i class="<?php echo esc_attr($aConfiguration['msgIcon']); ?>"></i></div>
			<?php endif; ?>
			<div class="alert_content__1ntU3">
				<?php Wiloke::ksesHTML($aConfiguration['msg']); ?>
			</div>
			<?php if ( $aConfiguration['hasRemoveBtn'] ) : ?>
			<a class="alert_close__3PtGd" href="#" title="<?php echo esc_attr__('Remove', 'wilcity'); ?>"><i class="la la-times"></i></a>
			<?php endif; ?>
		</div>
		<?php
        if ( $isReturn ){
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
	}
}