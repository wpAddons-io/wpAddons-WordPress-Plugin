<?php
/**
 * Addons
 *
 * @version 1.0
 * @author  wpAddons.io
 * @license https://opensource.org/licenses/gpl-license GNU Public License
 */
?>

<div id="the-list">

<?php foreach ( $addons->addons as $addon ) { ?>
	<div class="plugin-card">
		<div class="plugin-card-top">
			<div class="name column-name">
				<h3>
					<a href="<?php echo esc_url( $addon->url ); ?>" target="_blank">
					<?php echo $addon->name; ?>
					<img src="<?php echo esc_attr( $addon->icon ) ?>" class="plugin-icon" alt="">
					</a>
				</h3>
			</div>
			<div class="desc column-description">
				<p><?php echo $addon->description; ?></p>
			</div>
		</div>
	</div>
<?php } ?>

</div>
