<div class="items d-flex">

	<?php if ( ! empty( $args['instagram'] ) ) : ?>
	<a href="<?php echo esc_url( $args['instagram'] ); ?>" class="item d-flex align-items-center justify-content-center" target="_blank">
		<span class="ic icon-insta"></span>
	</a>
	<?php endif; ?>

	<?php if ( ! empty( $args['facebook'] ) ) : ?>
	<a href="<?php echo esc_url( $args['facebook'] ); ?>" class="item d-flex align-items-center justify-content-center" target="_blank">
		<span class="ic icon-fb"></span>
	</a>
	<?php endif; ?>

	<?php if ( ! empty( $args['telegram'] ) ) : ?>
	<a href="<?php echo esc_url( $args['telegram'] ); ?>" class="item d-flex align-items-center justify-content-center" target="_blank">
		<span class="ic icon-tg"></span>
	</a>
	<?php endif; ?>

	<?php if ( ! empty( $args['youtube'] ) ) : ?>
	<a href="<?php echo esc_url( $args['youtube'] ); ?>" class="item d-flex align-items-center justify-content-center" target="_blank">
		<span class="ic icon-you"></span>
	</a>
	<?php endif; ?>

</div>