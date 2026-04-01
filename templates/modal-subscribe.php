<?php

//we store it in a cookie so as not to show it for the next 24 hours
$is_viewed_modal_subscribe = get_user_cookie_modal_subscribe();
if ($is_viewed_modal_subscribe)
	return;

$image_id = get_field('foto', 'option');
$title = get_field('zagolovok', 'option');
$description = get_field('anons', 'option');

?>

<!-- <div class="modal fade" id="subscribe" tabindex="-1" aria-labelledby="subscribeLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<button type="button" class="close d-flex align-items-center justify-content-center" data-dismiss="modal" aria-label="Close"><span class="ic icon-close"></span></button>
			<div class="modal-subscribe">
				<div class="sub-image">
					
					<?php if (!empty($image_id)): ?>
					<img src="<?php echo wp_get_attachment_image_url($image_id, 'full'); ?>" srcset="<?php echo wp_get_attachment_image_srcset($image_id, 'full'); ?>" sizes="<?php echo wp_get_attachment_image_sizes($image_id, 'full'); ?>" alt="Image">
					<?php endif; ?>

				</div>
				<div class="sub-info">
					
					<?php if (!empty($title)): ?>
					<div class="sub-title"><?php echo $title; ?></div>
					<?php endif; ?>
					
					<?php if (!empty($description)): ?>
					<div class="sub-anons"><?php echo $description; ?></div>
					<?php endif; ?>
					
					<div class="sub-form light">
					
						<?php echo do_shortcode('[contact-form-7 id="906ff74" title="Підписка модал"]'); ?>
					
					</div>
				</div>
			</div>
		</div>
	</div>
</div> -->