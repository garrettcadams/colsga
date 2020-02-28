<?php
use Elementor\Icons_Manager;
$settings = $this->get_settings();
?>
<div class="elementor-jvbpd-price-table jvbpd-elements">
	<div class="price-table featured-table">
		<div class="<?php echo "price-table__heading ".$this->randor_css['header']; ?>">
			<div class="price-table__icon">
				<div class="price-table__icon-box">
					<?php
					if(
						isset($settings['__fa4_migrated']['_icon']) ||
						(empty($settings['icon']) && Icons_Manager::is_migration_allowed())
					) {
						Icons_Manager::render_icon( $settings['_icon'], Array('aria-hidden' => 'true') );
					}else{
						printf('<i %s></i>', esc_attr( $settings['icon']));
					}
					/* <i class="<?php echo $settings['icon'] ?>"></i></div> */ ?>
				</div>
			</div>
			<h2 class="price-table__title"><?php echo $settings['title'] ?></h2>
			<h4 class="price-table__subtitle"><?php echo $settings['subtitle'] ?></h4>
		</div>

		<?php
		$list = $settings['features_list'];
		if ( $list ) {
			echo '<div class="price-table__features">';
			foreach ( $list as $item ) {
				switch ($item['item_included']) {
				case 'item-included':
				    $include_icon = $settings['included_bullet_icon'];
				    break;
				case 'item-excluded':
				    $include_icon = $settings['excluded_bullet_icon'];
				    break;
				}
				echo '<div class="price-feature price-feature-13fjygw '. $item['item_included'] .'">';
				echo '<div class="price-feature__inner"><i class="item-bullet '. $include_icon .'"></i><span class="price-feature__text">'. $item['item_text'] .'</span></div>';
				echo '</div>'; //wrap
			}
			echo '</div>';
		}
		?>
		<div class="price-table__price">
			<span class="price-table__price-prefix"><?php echo $settings['price_prefix'] ?></span>
			<span class="price-table__price-val"><?php echo $settings['price'] ?></span>
			<span class="price-table__price-suffix"><?php echo $settings['price_suffix'] ?></span>
		</div>
		<div class="price-table__action">
			<a class="elementor-button elementor-size-md price-table-button" href="<?php echo $settings['button_url'] ?>"><?php echo $settings['button_text'] ?></a>
		</div>
		<?php
		if ($settings['featured']=='yes'){
			//echo '<img src="http://localhost/frm/wp-content/uploads/sites/2/2017/11/price.png" alt="" class="price-table__badge">';
		}
		?>
	</div>
	<?php
	if ($settings['featured']=='yes'){
	echo '<div class="eapps-price-table-column-ribbon-container">';
  echo '<div class="eapps-price-table-column-ribbon">';
  echo 'Popular';
  echo '</div>';
  echo '</div>';
	}?>
</div>
