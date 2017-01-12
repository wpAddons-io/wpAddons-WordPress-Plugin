<?php
/**
 * Addons
 *
 * @version 1.0
 * @author  wpAddons.io
 * @license https://opensource.org/licenses/gpl-license GNU Public License
 */
?>

<div class="addons-wrap third">

<?php foreach ( $addons->addons as $addon ) { ?>
	<div class="addon">
		<div class="addon-cover">
			<a href="<?php echo esc_url( $addon->url ); ?>" target="_blank">
				<img src="<?php echo esc_attr( $addon->banner ) ?>" alt="">
			</a>
		</div>
		<div class="addon-content">
			<h3 class="addon-heading">
				<a href="<?php echo esc_url( $addon->url ); ?>" target="_blank">
					<?php echo $addon->name; ?>
				</a>
			</h3>
			<p><?php echo $addon->description; ?></p>
		</div>
	</div>
<?php } ?>

</div>
