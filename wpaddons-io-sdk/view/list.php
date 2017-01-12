<?php
/**
 * Addons
 *
 * @version 1.0
 * @author  wpAddons.io
 * @license https://opensource.org/licenses/gpl-license GNU Public License
 */
?>

<ul>
<?php foreach ( $addons->addons as $addon ) { ?>
	<li>
		<a href="<?php echo esc_url( $addon->url ); ?>" target="_blank"><?php echo esc_html( $addon->name ); ?></a> - <?php echo esc_html( $addon->description ); ?>
	</li>
<?php } ?>
</ul>
