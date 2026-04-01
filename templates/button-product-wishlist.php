<?php if ( ! is_product_in_wishlist( $args['product_id'] ) ) : ?>

	<?php echo get_add_single_product_to_wishlist_html(); ?>

<?php else : ?>

	<?php echo get_delete_single_product_from_wishlist_html(); ?>

<?php endif; ?>