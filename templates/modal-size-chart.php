<div class="modal fade" id="rozmir" tabindex="-1" aria-labelledby="rozmirLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content h-100">
			<div class="modal-rozmir">
				<div class="rozmir-top d-flex align-start justify-content-between">
					<div class=" d-md-flex align-items-center ">
						<div class="rozmir-title">розмірна сітка</div>
						<div class="rozmir-anons">Оберіть розмір відповідно своїх параметрів</div>
					</div>
					<button type="button" class="close d-flex align-items-center justify-content-center" data-dismiss="modal"
						aria-label="Close"><span class="ic icon-close"></span></button>
				</div>
				<!-- <table class="rozmir-table">
					<thead>
						<tr>
							<th>Розмір <br />EUR / INT</th>
							<th>Обхват <br />грудей</th>
							<th>Обхват <br />талії</th>
							<th>Обхват <br />стегон</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>30 / XXS</td>
							<td>74 см</td>
							<td>56 см</td>
							<td>84 см</td>
						</tr>
						<tr>
							<td>32 / XXS</td>
							<td>76 см</td>
							<td>60 см</td>
							<td>86 см</td>
						</tr>
						<tr>
							<td>34 / XS</td>
							<td>80 см</td>
							<td>64 см</td>
							<td>88 см</td>
						</tr>
						<tr>
							<td>36 / S</td>
							<td>84 см</td>
							<td>68 см</td>
							<td>90 см</td>
						</tr>
						<tr>
							<td>38 / M</td>
							<td>88 см</td>
							<td>72 см</td>
							<td>92 см</td>
						</tr>
						<tr>
							<td>40 / L</td>
							<td>92 см</td>
							<td>76 см</td>
							<td>94 см</td>
						</tr>
						<tr>
							<td>44 / XXL</td>
							<td>96 см</td>
							<td>80 см</td>
							<td>96 см</td>
						</tr>
						<tr>
							<td>46 / 3XL</td>
							<td>100 см</td>
							<td>84 см</td>
							<td>99 см</td>
						</tr>
					</tbody>
				</table> -->


				<?php
				// Хелпер рендера ACF Table
				if (!function_exists('e_render_acf_table')) {
					function e_render_acf_table($table)
					{
						if (!$table) {
							return false;
						}

						echo '<table class="rozmir-table">';

						if (!empty($table['caption'])) {
							echo '<caption>' . $table['caption'] . '</caption>';
						}

						if (!empty($table['header'])) {
							echo '<thead><tr>';
							foreach ($table['header'] as $th) {
								$c = '';
								if (isset($th['c'])) {
									$c = $th['c'];
								}
								echo '<th>' . $c . '</th>';
							}
							echo '</tr></thead>';
						}

						echo '<tbody>';
						if (!empty($table['body'])) {
							foreach ($table['body'] as $tr) {
								echo '<tr>';
								if ($tr) {
									foreach ($tr as $td) {
										$c = '';
										if (isset($td['c'])) {
											$c = $td['c'];
										}
										echo '<td>' . $c . '</td>';
									}
								}
								echo '</tr>';
							}
						}
						echo '</tbody>';

						echo '</table>';
						return true;
					}
				}

				// 1) Берём ID термина из ACF-поля товара
				$product_group_id = get_field('product_group');

				// 2) Получаем термин таксы product_group
				$term = null;
				if ($product_group_id) {
					$maybe_term = get_term((int) $product_group_id, 'product_group');
					if ($maybe_term) {
						if (!is_wp_error($maybe_term)) {
							$term = $maybe_term;
						}
					}
				}

				// 3) Пытаемся отрендерить таблицу из термина
				$rendered = false;
				if ($term) {
					$ctx = 'product_group_' . $term->term_id; // контекст ACF для терминов
					$size_table = get_field('size_table', $ctx); // Post Object или ID
					$size_table_id = 0;

					if ($size_table) {
						if (is_object($size_table)) {
							$size_table_id = $size_table->ID;
						} else {
							$size_table_id = (int) $size_table;
						}
					}

					if ($size_table_id) {
						$table = get_field('size_table_field', $size_table_id); // ACF Table
						if ($table) {
							$rendered = e_render_acf_table($table);
						}
					}
				}

				// 4) ELSE: если для группы не назначена таблица — выводим дефолт из options
				if (!$rendered) {
					$size_table_default = get_field('size_table_default', 'option'); // ACF Table
					if ($size_table_default) {
						e_render_acf_table($size_table_default);
					}
				}
				?>


				<!--<div class="rozmir-calculate">
					<div class="calculate-top d-md-flex align-items-center justify-content-between">
						<div class="calculate-title">Калькулятор розміру</div>
						<ul class="nav"  role="tablist">
							<li role="presentation">
								<a href="#calc-0" data-toggle="tab" class="cat-tab nav-link active">Жіночий</a>
							</li>
							<li role="presentation">
								<a href="#calc-1"  data-toggle="tab" class="cat-tab nav-link">Чоловічий</a>
							</li>
						</ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane fade show active" id="calc-0" >
							<div class="d-md-flex justify-content-between">
								<div class="calc-form">
									<form action="">
										<div class="calc-container">
											<label class="label">Обхват грудей</label>
											<input type="text" class="input" placeholder="90">
										</div>
										<div class="calc-container">
											<label class="label">Обхват талії</label>
											<input type="text" class="input" placeholder="60">
										</div>
										<div class="calc-container">
											<label class="label">Обхват бедер</label>
											<input type="text" class="input" placeholder="90">
										</div>
										<div class="calc-container">
											<input type="submit" class="btn-black w-100 submit" value="Розрахувати">
										</div>
										<div class="calc-result d-flex align-items-center">
											<div class="result-anons">Оптимальний розмір для ваших параметрів становить</div>
											<div class="result">S</div>
										</div>
									</form>
								</div>
								<div class="calc-image">
									<div class="image-container">
										<img src="<?php echo get_template_directory_uri(); ?>/images/calc1.jpg" alt="">
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="calc-1" >
							<div class="d-md-flex justify-content-between">
								<div class="calc-form">
									<form action="">
										<div class="calc-container">
											<label class="label">Обхват грудей</label>
											<input type="text" class="input" placeholder="90">
										</div>
										<div class="calc-container">
											<label class="label">Обхват талії</label>
											<input type="text" class="input" placeholder="60">
										</div>
										<div class="calc-container">
											<label class="label">Обхват бедер</label>
											<input type="text" class="input" placeholder="90">
										</div>
										<div class="calc-container">
											<input type="submit" class="btn-black w-100 submit" value="Розрахувати">
										</div>
										<div class="calc-result d-flex align-items-center">
											<div class="result-anons">Оптимальний розмір для ваших параметрів становить</div>
											<div class="result">S</div>
										</div>
									</form>
								</div>
								<div class="calc-image">
									<div class="image-container">
										<img src="<?php echo get_template_directory_uri(); ?>/images/calc1.jpg" alt="">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>-->

			</div>
		</div>
	</div>
</div>