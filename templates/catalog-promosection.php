<?php
/*
$promotion_videos = get_field('promotion_videos', 'option');
if (empty($promotion_videos))
	return;

$random_key = array_rand($promotion_videos);
$random_video = $promotion_videos[$random_key];
*/
?>

<!-- <div class="col-12 col-lg-8 op">
	<div class="catalog-video">
		<video autoplay loop muted playsinline>
			<source src="<?php echo esc_url($random_video); ?>">
		</video>
	</div>
</div> -->





<?php
// templates/catalog-promosection.php

$audience = '';
if (isset($args['audience'])) {
	$audience = $args['audience'];
}

if ($audience !== 'men' && $audience !== 'women' && $audience !== 'kids') {
	return; // нет аудитории — не выводим секцию
}

// Подбор поля по аудитории
$field_key = '';
if ($audience === 'men')
	$field_key = 'promotion_videos_man';
if ($audience === 'women')
	$field_key = 'promotion_videos_woman';
if ($audience === 'kids')
	$field_key = 'promotion_videos_kids';

// Получаем массив видео из Options
$videos = get_field($field_key, 'option');
if (empty($videos))
	return;

// Случайный элемент
$random_key = array_rand($videos);
$item = $videos[$random_key];

$url = '';
$type = 'video/mp4';

// ACF file (массив) или просто строка-URL, а также частые варианты репитера
if (is_array($item)) {
	if (!empty($item['url'])) {
		$url = $item['url'];
		if (!empty($item['mime_type']))
			$type = $item['mime_type'];
	} else {
		if (!empty($item['file']) && !empty($item['file']['url'])) {
			$url = $item['file']['url'];
			if (!empty($item['file']['mime_type']))
				$type = $item['file']['mime_type'];
		}
		if (!$url) {
			if (!empty($item['video']) && !empty($item['video']['url'])) {
				$url = $item['video']['url'];
				if (!empty($item['video']['mime_type']))
					$type = $item['video']['mime_type'];
			}
		}
	}
} else {
	$url = $item;
}

// Если MIME не определён — по расширению
if ($url) {
	$ext = strtolower(pathinfo($url, PATHINFO_EXTENSION));
	if ($ext === 'webm')
		$type = 'video/webm';
	if ($ext === 'ogv')
		$type = 'video/ogg';
	if ($ext === 'ogg')
		$type = 'video/ogg';
	if ($ext === 'mp4')
		$type = 'video/mp4';
	if ($ext === 'm4v')
		$type = 'video/mp4';
}

if (!$url)
	return;
?>

<!-- <div class="col-12 col-lg-8 op">
	<div class="catalog-video">
		<video autoplay loop muted playsinline>
			<source src="<?php echo esc_url($url); ?>" type="<?php echo $type; ?>">
		</video>
	</div>
</div> -->