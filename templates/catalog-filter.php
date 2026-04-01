<?php

if (empty($args['objs']))
	return;

?>

<div class="filter-block">
	<button type="button" class="filter-name w-100 d-flex align-items-center justify-content-between"
		data-toggle="collapse" href="#filter-<?php echo esc_attr($args['name']); ?>" role="button"
		aria-expanded="false"><!--collapsed-->
		<span class="value"><?php echo esc_html($args['title']); ?></span>
		<span class="ic icon-plus"></span>
	</button>
	<div class="collapse show" id="filter-<?php echo esc_attr($args['name']); ?>">
		<div class="block-container">

			<?php foreach ($args['objs'] as $obj): ?>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="<?php echo esc_attr($args['name']); ?>"
							data-term-id="<?php echo $obj->term_id; ?>">
						<span><?php echo esc_html($obj->name); ?></span>
					</label>
				</div>
			<?php endforeach; ?>

		</div>
	</div>
</div>