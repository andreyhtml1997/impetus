<?php

$title 	= get_the_title();
$link	= get_the_permalink();

?>

<div class="modal fade" id="share" tabindex="-1" aria-labelledby="shareLabel" aria-hidden="true">
	<div class="modal-dialog h-100">
		<div class="modal-content h-100 align-items-center justify-content-center text-center">
			<button type="button" class="close d-flex align-items-center justify-content-center" data-dismiss="modal" aria-label="Close"><span class="ic icon-close"></span></button>
			<div class="modal-form">
				<div class="modal-title">Поділитись обраним товаром</div>
				<div class="share row no-gutters">
					<a href="<?php echo esc_url( get_share_link_by( 'facebook', $link, $title ) ); ?>" class="item col d-flex align-items-center justify-content-center" title="Share on Facebook" target="_blank">
						<span class="ic icon-fbb"></span>
					</a>
					<a href="<?php echo esc_url( get_share_link_by( 'whatsapp', $link, $title ) ); ?>" class="item col d-flex align-items-center justify-content-center" title="Share on WhatsApp" target="_blank">
						<span class="ic icon-wtt"></span>
					</a>
					<a href="<?php echo esc_url( get_share_link_by( 'telegram', $link, $title ) ); ?>" class="item col d-flex align-items-center justify-content-center" title="Share on Telegram" target="_blank">
						<span class="ic icon-tgg"></span>
					</a>
					<a href="<?php echo esc_url( get_share_link_by( 'viber', $link, $title ) ); ?>" class="item col d-flex align-items-center justify-content-center" title="Share on Viber" target="_blank">
						<span class="ic icon-vbb"></span>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>