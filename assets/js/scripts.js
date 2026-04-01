if (window.gsap) {
	gsap.registerPlugin(ScrollTrigger);
	var header = $('.header'),
		scrollPrev = 0;

	$(window).on("scroll load resize", function () {
		var scrolled = $(window).scrollTop();
		if (scrolled > 77) {
			header.addClass('fix');
		} else {
			header.removeClass('fix');
		}
		if (scrolled > 77 && scrolled > scrollPrev) {
			header.addClass('out');
		} else {
			header.removeClass('out');
		}
		scrollPrev = scrolled;
	});
}



jQuery(document).ready(function ($) {

	$(".langs-btn a").on('click tap', function (event) {
		event.preventDefault();
		$(this).closest('.header-langs').toggleClass("open");
	});
	$(".has-children > a").on('click tap', function (event) {
		event.preventDefault();
		$(this).parent().toggleClass("open");
	});




	$(".menu-button").on('click tap', function () {
		$(":root").find('body').toggleClass("mobile-open");
		$(this).find('.but-icon').toggleClass("is-active");
	});

	$(".filter-btn").on('click tap', function () {
		$(":root").find('body').toggleClass("filter-open");
	});
	$(".filter-close").on('click tap', function () {
		$(":root").find('body').toggleClass("filter-open");
	});

	$(".drop-btn").on('click tap', function () {
		if ($(this).parent().hasClass("open")) {
			$(":root").find('body').removeClass("menu-open");
			$(this).parent().removeClass("open");
		} else {
			$(":root").find('body').removeClass("menu-open");
			$(".menu-catalog").find('.open').removeClass("open");
			$(":root").find('body').addClass("menu-open");
			$(this).parent().toggleClass("open");
		}
	});

	$(document).mouseup(function (e) { // событие клика по веб-документу
		var div = $(".catalog-dropdown .dropdown-container"); // тут указываем ID элемента
		var div2 = $(".drop-btn"); // тут указываем ID элемента
		if (!div.is(e.target) && !div2.is(e.target) // если клик был не по нашему блоку
			&& div.has(e.target).length === 0) { // и не по его дочерним элементам
			$(":root").find('body').removeClass("menu-open");
			$(".menu-catalog").find('.open').removeClass("open");
		}
	});


	$(".search-btn").on('click tap', function () {
		$(this).parent().toggleClass("open");
	});
	$(".search-close").on('click tap', function () {
		$('.header-search').toggleClass("open");
	});
	if (window.Fancybox) {
		Fancybox.bind('[data-fancybox]', {});
	}

	$('body').on('hidden.bs.modal', function () {
		if ($('.modal.show').length > 0) {
			$('body').addClass('modal-open');
		}
	});

	$('body').on('click tap', '.open-mobile-choose-size', function () {

		let obj, objModal, productId, sizesHtml;

		objModal = $('#mobile-buy');
		obj = $(this).closest('[data-product-container]');
		productId = obj.attr('data-product-id');
		sizesHtml = obj.find('.item-sizes').html();

		objModal.find('.item-sizes').html(sizesHtml);
		objModal.find('[data-product-container]').attr('data-product-id', productId).attr('data-product-size', 0);
		objModal.find('.item-sizes .s-item.active').trigger('click');
		objModal.modal('show');
	});

	$('.forgot').on('click tap', function () {
		$('.modal').modal('hide');
		$('#forgot').modal('show');
	});
	$('.to-login').on('click tap', function () {
		$('.modal').modal('hide');
		$('#login').modal('show');
	});
	$('.register').on('click tap', function () {
		$('.modal').modal('hide');
		$('#register').modal('show');
	});
	$('.quick').on('click tap', function () {
		$('.modal').modal('hide');
		$('#quick').modal('show');
	});

	var $banner = $('.header-banner').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		speed: 8000,
		autoplay: true,
		autoplaySpeed: 0,
		cssEase: 'linear',
		infinite: true,
		variableWidth: true,
		dots: false,
		arrows: false
	});

	$banner.on('mouseenter', function () {
		$banner.slick('slickPause');
	}).on('mouseleave', function () {
		$banner.slick('slickPlay');
	});
	$('.catalog-slider').each(function (idx, item) {
		var carouselId = "carousel" + idx;
		this.id = carouselId;
		var carouselId = $(this).parent().parent().find('.slider-navs');

		$(this).slick({
			slidesToShow: 4,
			slidesToScroll: 1,
			arrows: true,
			dots: false,
			appendArrows: carouselId,
			touchThreshold: 20,
			responsive: [
				{
					breakpoint: 1400,
					settings:
					{
						slidesToShow: 4,
					}
				},
				{
					breakpoint: 1200,
					settings:
					{
						slidesToShow: 3,
					}
				},
				{
					breakpoint: 992,
					settings:
					{
						slidesToShow: 2,
					}
				},
				{
					breakpoint: 767,
					settings:
					{
						slidesToShow: 1,
						variableWidth: true
					}
				},
			]
		});
	});
	$('.catalog-slider2').each(function (idx, item) {
		var carouselId = "carousel" + idx;
		this.id = carouselId;
		var carouselId = $(this).parent().parent().find('.slider-navs');

		$(this).slick({
			slidesToShow: 2,
			slidesToScroll: 1,
			arrows: true,
			dots: false,
			appendArrows: carouselId,
			touchThreshold: 20,
			responsive: [
				{
					breakpoint: 992,
					settings:
					{
						slidesToShow: 2,
					}
				},
				{
					breakpoint: 767,
					settings:
					{
						slidesToShow: 1,
						variableWidth: true
					}
				},
			]
		});
	});


	$('.blog-slider').each(function (idx, item) {
		var carouselId3 = "carousel3" + idx;
		this.id = carouselId3;
		var carouselId3 = $(this).parent().parent().find('.slider-navs');
		$(this).slick({
			slidesToShow: 2,
			slidesToScroll: 1,
			arrows: true,
			dots: false,
			focusOnSelect: true,
			appendArrows: carouselId3,
			touchThreshold: 30,
			responsive: [
				{
					breakpoint: 992,
					settings:
					{
						slidesToShow: 2,
					}
				},
				{
					breakpoint: 480,
					settings:
					{
						slidesToShow: 1,
					}
				},

			]
		});
	});

	$(".list-sort select").each(function () { // бежим по всем селектам
		$(this).select2({ // ини циализируем каждый отдельно
			closeOnSelect: true,
			allowHtml: true,
			minimumResultsForSearch: -1,
			allowClear: true,
			theme: 'default sort',
			dropdownParent: $(this).parent().parent().find('.sel-drop') // выбираем конкретный элемент с классом, относительно текущего селекта
		})
	});

	$('.detail-gallery').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		mobileFirst: true,
		arrows: false,
		dots: false,

		touchThreshold: 100,
		responsive: [
			{
				breakpoint: 992,
				settings: 'unslick'
			}
		]
	});
	$('.thumb-slider').slick({
		slidesToShow: 4,
		slidesToScroll: 1,
		mobileFirst: true,
		arrows: false,
		dots: false,
		infinite: false,
		touchThreshold: 100,
		variableWidth: true,
		responsive: [
			{
				breakpoint: 992,
				settings: 'unslick'
			},
			{
				breakpoint: 600,
				settings:
				{
					variableWidth: false,
					slidesToShow: 5,
				}
			},
		]
	});

	$(window).on('resize', function () {
		$('.detail-gallery').slick('resize');
		$('.thumb-slider').slick('resize');
	});

	(function ($) {

		function npGetDeliveryType() {
			var el = document.querySelector('input[name="delivery_type"]:checked');
			if (!el) return 0;
			return parseInt(el.value, 10);
		}

		function npCleanTerm(term) {
			if (!term) return '';
			term = String(term);
			term = term.replace(/\s+/g, '');
			return term;
		}

		function npToggleWarehouseDisabled() {
			var city = $('.js-select-city').val();
			var wh = $('.js-select-warehouse');

			if (!wh.length) return;

			if (!city) {
				wh.prop('disabled', true);
				return;
			}

			wh.prop('disabled', false);
		}

		$(".js-select-city").each(function () {
			var $select = $(this);

			$select.select2({
				minimumInputLength: 0,
				closeOnSelect: true,
				placeholder: $select.attr("data-placeholder"),
				allowClear: true,
				theme: 'default',
				dropdownParent: $select.parent().find('.sel-drop'),
				ajax: {
					type: "POST",
					url: appVars.ajaxurl,
					delay: 250,
					dataType: 'json',
					data: function (params) {
						var term = '';
						if (params) {
							if (typeof params.term !== 'undefined') term = params.term;
						}

						return {
							action: 'front_search_location',
							search: npCleanTerm(term),
							delivery_type: npGetDeliveryType(),
							security: appVars.securitycode,
						};
					},
					processResults: function (res) {

						if (!res) return { results: [] };
						if (!res.data) return { results: [] };

						if (res.data.status == 3) {
							$.fn.showNotices(res.data.error.info);
						}

						if (res.data.status == 1) {
							return { results: res.data.items };
						}

						return { results: [] };
					}
				},
				language: {
					inputTooShort: function (args) {
						return 'Введіть ще ' + (args.minimum - args.input.length) + ' або більше символів';
					},
					noResults: function () {
						return 'Нічого не знайдено';
					},
					searching: function () {
						return 'Пошук…';
					},
					loadingMore: function () {
						return 'Завантаження ще…';
					}
				},
			});
		});

		$(".js-select-warehouse").each(function () {
			var $select = $(this);

			$select.select2({
				minimumInputLength: 0,
				closeOnSelect: true,
				placeholder: $select.attr("data-placeholder"),
				allowClear: true,
				theme: 'default',
				dropdownParent: $select.parent().find('.sel-drop'),
				ajax: {
					type: "POST",
					url: appVars.ajaxurl,
					delay: 250,
					dataType: 'json',
					data: function (params) {
						var term = '';
						if (params) {
							if (typeof params.term !== 'undefined') term = params.term;
						}

						var locationRef = $('select[name="location"]').val();

						return {
							action: 'front_search_warehouse',
							search: npCleanTerm(term),
							location_ref: locationRef,
							delivery_type: npGetDeliveryType(),
							security: appVars.securitycode,
						};
					},
					processResults: function (res) {

						if (!res) return { results: [] };
						if (!res.data) return { results: [] };

						if (res.data.status == 3) {
							$.fn.showNotices(res.data.error.info);
						}

						if (res.data.status == 1) {
							return { results: res.data.items };
						}

						return { results: [] };
					}
				},
				language: {
					inputTooShort: function (args) {
						return 'Введіть ще ' + (args.minimum - args.input.length) + ' або більше символів';
					},
					noResults: function () {
						return 'Нічого не знайдено';
					},
					searching: function () {
						return 'Пошук…';
					},
					loadingMore: function () {
						return 'Завантаження ще…';
					}
				},
			});
		});


		$(document).on('select2:select', '.js-select-city', function (e) {
			var t = '';
			if (e && e.params && e.params.data && e.params.data.text) {
				t = e.params.data.text;
			}
			$('input[name="location_text"]').val(t);
		});

		$(document).on('select2:select', '.js-select-warehouse', function (e) {
			var t = '';
			if (e && e.params && e.params.data && e.params.data.text) {
				t = e.params.data.text;
			}
			$('input[name="warehouse_text"]').val(t);
		});

		$(document).on('select2:clear', '.js-select-city', function () {
			$('input[name="location_text"]').val('');
		});

		$(document).on('select2:clear', '.js-select-warehouse', function () {
			$('input[name="warehouse_text"]').val('');
		});



		function npToggleDeliveryUI() {
			var el = document.querySelector('input[name="delivery_type"]:checked');
			var type = 0;

			if (el) {
				type = parseInt(el.value, 10);
			}

			var warehouseWrap = document.querySelector('.js-warehouse-wrap');
			var addressWraps = document.querySelectorAll('.js-address-wrap');

			if (warehouseWrap) {
				warehouseWrap.style.display = 'none';
			}

			addressWraps.forEach(function (n) {
				n.style.display = 'none';
			});

			if (type === 1) {
				if (warehouseWrap) {
					warehouseWrap.style.display = '';
				}
			}

			if (type === 2) {
				if (warehouseWrap) {
					warehouseWrap.style.display = '';
				}
			}

			if (type === 3) {
				addressWraps.forEach(function (n) {
					n.style.display = '';
				});
			}
		}

		/* function npUpdateWarehousePlaceholder() {
			var type = npGetDeliveryType();

			var text = 'Оберіть відділення';
			if (type === 2) {
				text = 'Оберіть поштомат';
			}

			var wh = $('.js-select-warehouse');
			if (!wh.length) return;

			// обновляем атрибут
			wh.attr('data-placeholder', text);

			// обновляем placeholder внутри select2, если он уже инициализирован
			var s2 = wh.data('select2');
			if (s2) {
				s2.options.options.placeholder = text;
			}

			// если ничего не выбрано — обновим текст в UI
			if (!wh.val()) {
				var rendered = wh.next('.select2').find('.select2-selection__rendered');
				if (rendered.length) {
					var ph = rendered.find('.select2-selection__placeholder');
					if (ph.length) {
						ph.text(text);
					} else {
						rendered.html('<span class="select2-selection__placeholder">' + text + '</span>');
					}
				}
			}
		} */







		$(document).on('change', '.js-select-city', function () {
			$('.js-select-warehouse').val(null).trigger('change');
			npToggleWarehouseDisabled();
			npToggleDeliveryUI();
		});

		$(document).on('change', 'input[name="delivery_type"]', function () {
			$('.js-select-city').val(null).trigger('change');
			$('.js-select-warehouse').val(null).trigger('change');
			npToggleWarehouseDisabled();
			npToggleDeliveryUI();
			// npUpdatePlaceholders();

		});

		npToggleWarehouseDisabled();
		npToggleDeliveryUI();
		// npUpdatePlaceholders();






		// если Select2 что-то сделает на focus — вернём placeholder
		$(document).on('focus', '.select2-container--open .select2-search__field', function () {
			var $select = $(this).closest('.select2-container').prev('select');
			var text = $select.attr('data-search-placeholder');
			if (text) $(this).attr('placeholder', text);
		});


		function npGetWhSearchText() {
			var type = npGetDeliveryType();
			if (type === 2) return 'Введіть поштомат *';
			return 'Введіть відділення *';
		}

		function npAttachSearchOverlay($select, text) {
			var s2 = $select.data('select2');
			if (!s2 || !s2.$dropdown || !s2.$dropdown.length) return;

			var $searchWrap = s2.$dropdown.find('.select2-search--dropdown');
			var $field = $searchWrap.find('.select2-search__field');
			if (!$searchWrap.length || !$field.length) return;

			// создаём/обновляем оверлей
			var $ph = $searchWrap.find('.np-s2-search-ph');
			if (!$ph.length) {
				$ph = $('<span class="np-s2-search-ph"></span>');
				$searchWrap.append($ph);
			}
			$ph.text(text);

			// показать/скрыть по факту ввода
			var toggle = function () {
				var v = ($field.val() || '').trim();
				$ph.toggle(!v);
			};

			// важно: Select2 может перерисовывать, поэтому вешаем неймспейс и обновляем
			$field.off('.npPh').on('input.npPh keyup.npPh change.npPh', toggle);

			// сразу применим (на focus тоже должен быть виден, если пусто)
			toggle();
		}

		// при открытии — ставим нужный текст именно в поле ввода поиска
		$(document).on('select2:open', '.js-select-city', function () {
			npAttachSearchOverlay($(this), 'Введіть місто *');
		});

		$(document).on('select2:open', '.js-select-warehouse', function () {
			npAttachSearchOverlay($(this), npGetWhSearchText());
		});

		// если меняешь тип доставки когда селект уже открыт — обновим оверлей сразу
		$(document).on('change', 'input[name="delivery_type"]', function () {
			var $open = $('.select2-container--open');
			if (!$open.length) return;

			// какой селект сейчас открыт: он находится прямо перед контейнером select2
			var $select = $open.prev('select');
			if (!$select.length) return;

			if ($select.hasClass('js-select-warehouse')) {
				npAttachSearchOverlay($select, npGetWhSearchText());
			}
		});
	})(jQuery);











	$('.op').each(function (idx) {
		var op = "op" + idx;
		this.id = op;
		if (window.gsap) {
			gsap.from(this, 1, { scrollTrigger: { trigger: "#" + op, start: "top-=50px bottom-=15%", end: "top bottom", scrub: true }, y: "50px", opacity: 0, ease: Quint.easeOut });
		}
	});

	var image = document.getElementsByClassName('parallax');
	if (window.simpleParallax) {
		new simpleParallax(image, {
			scale: 1.1
		});
	}
});





$.fn.setCursorPosition = function (obj, pos) {
	if (obj.get(0).setSelectionRange) {
		obj.get(0).setSelectionRange(pos, pos);
	} else if (obj.get(0).createTextRange) {
		var range = obj.get(0).createTextRange();
		range.collapse(true);
		range.moveEnd('character', pos);
		range.moveStart('character', pos);
		range.select();
	}
};

$(".date").mask("99.99.9999");

$(".phone").blur(function () {
	if ($(this).val() != '') {
		$(this).parent().addClass('ok');
	} else {
		$(this).parent().removeClass('focus');
		$(this).parent().removeClass('ok');
		$(this).parent().removeClass('err');
	}
});

$(".phone").click(function () {

	let val = $(this).val();

	if (val == '+38 (0__) ___-__-__')
		$.fn.setCursorPosition($(this), 6);

}).mask("+38 (099) 999-99-99");

$(".input").focus(function () {
	$(this).parent().parent().addClass('focus');
	$(this).parent().parent().removeClass('error');
});

$(".input").blur(function () {
	var comment = $(this).html();
	if ($(this).val() != '' || comment.length != 0) {
		$(this).parent().parent().addClass('ok');
		$(this).parent().parent().removeClass('focus');
	} else {
		$(this).parent().parent().removeClass('focus');
		$(this).parent().parent().removeClass('ok');
	}
});

$(".textarea").focus(function () {
	$(this).parent().parent().addClass('focus');
	$(this).parent().parent().removeClass('error');
});

$(".textarea").blur(function () {
	var comment = $(this).html();
	if ($(this).val() != '' || comment.length != 0) {
		$(this).parent().parent().addClass('ok');
		$(this).parent().parent().removeClass('focus');
	} else {
		$(this).parent().parent().removeClass('focus');
		$(this).parent().parent().removeClass('ok');
	}
});
if ($(".sticky-cart").length) {
	(function () {
		var a = document.querySelector('.sticky-cart'), b = null, P = 48;  // если ноль заменить на число, то блок будет прилипать до того, как верхний край окна браузера дойдёт до верхнего края элемента. Может быть отрицательным числом
		window.addEventListener('scroll', Ascroll, false);
		document.body.addEventListener('scroll', Ascroll, false);
		function Ascroll() {
			if (b == null) {
				var Sa = getComputedStyle(a, ''), s = '';
				for (var i = 0; i < Sa.length; i++) {
					if (Sa[i].indexOf('overflow') == 0 || Sa[i].indexOf('padding') == 0 || Sa[i].indexOf('border') == 0 || Sa[i].indexOf('outline') == 0 || Sa[i].indexOf('box-shadow') == 0 || Sa[i].indexOf('background') == 0) {
						s += Sa[i] + ': ' + Sa.getPropertyValue(Sa[i]) + '; '
					}
				}
				b = document.createElement('div');
				b.style.cssText = s + ' box-sizing: border-box; width: ' + a.offsetWidth + 'px;';
				a.insertBefore(b, a.firstChild);
				var l = a.childNodes.length;
				for (var i = 1; i < l; i++) {
					b.appendChild(a.childNodes[1]);
				}
				a.style.height = b.getBoundingClientRect().height + 'px';
				a.style.padding = '0';
				a.style.border = '0';
			}
			var Ra = a.getBoundingClientRect(),
				R = Math.round(Ra.top + b.getBoundingClientRect().height - document.querySelector('.sticky-stop').getBoundingClientRect().top);  // селектор блока, при достижении верхнего края которого нужно открепить прилипающий элемент;  Math.round() только для IE; если ноль заменить на число, то блок будет прилипать до того, как нижний край элемента дойдёт до футера
			if ((Ra.top - P) <= 0) {
				if ((Ra.top - P) <= R) {
					b.className = 'stop';
					b.style.top = - R + 'px';
				} else {
					b.className = 'sticky';
					b.style.top = P + 'px';
				}
			} else {
				b.className = '';
				b.style.top = '';
			}
			window.addEventListener('resize', function () {
				a.children[0].style.width = getComputedStyle(a, '').width
			}, false);
		}
	})()

}

if ($(".thumb-slider").length) {
	(function () {
		var a = document.querySelector('.thumb-slider'), b = null, P = 20;  // если ноль заменить на число, то блок будет прилипать до того, как верхний край окна браузера дойдёт до верхнего края элемента. Может быть отрицательным числом
		window.addEventListener('scroll', Ascroll, false);
		document.body.addEventListener('scroll', Ascroll, false);
		function Ascroll() {
			if (b == null) {
				var Sa = getComputedStyle(a, ''), s = '';
				for (var i = 0; i < Sa.length; i++) {
					if (Sa[i].indexOf('overflow') == 0 || Sa[i].indexOf('padding') == 0 || Sa[i].indexOf('border') == 0 || Sa[i].indexOf('outline') == 0 || Sa[i].indexOf('box-shadow') == 0 || Sa[i].indexOf('background') == 0) {
						s += Sa[i] + ': ' + Sa.getPropertyValue(Sa[i]) + '; '
					}
				}
				b = document.createElement('div');
				b.style.cssText = s + ' box-sizing: border-box; width: ' + a.offsetWidth + 'px;';
				a.insertBefore(b, a.firstChild);
				var l = a.childNodes.length;
				for (var i = 1; i < l; i++) {
					b.appendChild(a.childNodes[1]);
				}
				a.style.height = b.getBoundingClientRect().height + 'px';
				a.style.padding = '0';
				a.style.border = '0';
			}
			var Ra = a.getBoundingClientRect(),
				R = Math.round(Ra.top + b.getBoundingClientRect().height - document.querySelector('.sticky-stop').getBoundingClientRect().top);  // селектор блока, при достижении верхнего края которого нужно открепить прилипающий элемент;  Math.round() только для IE; если ноль заменить на число, то блок будет прилипать до того, как нижний край элемента дойдёт до футера
			if ((Ra.top - P) <= 0) {
				if ((Ra.top - P) <= R) {
					b.className = 'stop';
					b.style.top = - R + 'px';
				} else {
					b.className = 'sticky';
					b.style.top = P + 'px';
				}
			} else {
				b.className = '';
				b.style.top = '';
			}
			window.addEventListener('resize', function () {
				a.children[0].style.width = getComputedStyle(a, '').width
			}, false);
		}
	})()

}
if ($(".info-sticky").length) {
	(function () {
		var a = document.querySelector('.info-sticky'), b = null, P = 20;  // если ноль заменить на число, то блок будет прилипать до того, как верхний край окна браузера дойдёт до верхнего края элемента. Может быть отрицательным числом
		window.addEventListener('scroll', Ascroll, false);
		document.body.addEventListener('scroll', Ascroll, false);
		function Ascroll() {
			if (b == null) {
				var Sa = getComputedStyle(a, ''), s = '';
				for (var i = 0; i < Sa.length; i++) {
					if (Sa[i].indexOf('overflow') == 0 || Sa[i].indexOf('padding') == 0 || Sa[i].indexOf('border') == 0 || Sa[i].indexOf('outline') == 0 || Sa[i].indexOf('box-shadow') == 0 || Sa[i].indexOf('background') == 0) {
						s += Sa[i] + ': ' + Sa.getPropertyValue(Sa[i]) + '; '
					}
				}
				b = document.createElement('div');
				b.style.cssText = s + ' box-sizing: border-box; width: ' + a.offsetWidth + 'px;';
				a.insertBefore(b, a.firstChild);
				var l = a.childNodes.length;
				for (var i = 1; i < l; i++) {
					b.appendChild(a.childNodes[1]);
				}
				a.style.height = b.getBoundingClientRect().height + 'px';
				a.style.padding = '0';
				a.style.border = '0';
			}
			var Ra = a.getBoundingClientRect(),
				R = Math.round(Ra.top + b.getBoundingClientRect().height - document.querySelector('.sticky-stop').getBoundingClientRect().top);  // селектор блока, при достижении верхнего края которого нужно открепить прилипающий элемент;  Math.round() только для IE; если ноль заменить на число, то блок будет прилипать до того, как нижний край элемента дойдёт до футера
			if ((Ra.top - P) <= 0) {
				if ((Ra.top - P) <= R) {
					b.className = 'stop';
					a.className = 'stop info-container info-sticky';
					b.style.top = - R + 'px';
				} else {
					b.className = 'sticky';
					a.className = 'info-container info-sticky';
					b.style.top = P + 'px';
				}
			} else {
				b.className = '';
				b.style.top = '';
			}
			window.addEventListener('resize', function () {
				a.children[0].style.width = getComputedStyle(a, '').width
			}, false);
		}
	})()

}
if ($(".inner-menu").length) {
	(function () {
		var a = document.querySelector('.inner-menu'), b = null, P = 120;  // если ноль заменить на число, то блок будет прилипать до того, как верхний край окна браузера дойдёт до верхнего края элемента. Может быть отрицательным числом
		window.addEventListener('scroll', Ascroll, false);
		document.body.addEventListener('scroll', Ascroll, false);
		function Ascroll() {
			if (b == null) {
				var Sa = getComputedStyle(a, ''), s = '';
				for (var i = 0; i < Sa.length; i++) {
					if (Sa[i].indexOf('overflow') == 0 || Sa[i].indexOf('padding') == 0 || Sa[i].indexOf('border') == 0 || Sa[i].indexOf('outline') == 0 || Sa[i].indexOf('box-shadow') == 0 || Sa[i].indexOf('background') == 0) {
						s += Sa[i] + ': ' + Sa.getPropertyValue(Sa[i]) + '; '
					}
				}
				b = document.createElement('div');
				b.style.cssText = s + ' box-sizing: border-box; width: ' + a.offsetWidth + 'px;';
				a.insertBefore(b, a.firstChild);
				var l = a.childNodes.length;
				for (var i = 1; i < l; i++) {
					b.appendChild(a.childNodes[1]);
				}
				a.style.height = b.getBoundingClientRect().height + 'px';
				a.style.padding = '0';
				a.style.border = '0';
			}
			var Ra = a.getBoundingClientRect(),
				R = Math.round(Ra.top + b.getBoundingClientRect().height - document.querySelector('.sticky-stop').getBoundingClientRect().top);  // селектор блока, при достижении верхнего края которого нужно открепить прилипающий элемент;  Math.round() только для IE; если ноль заменить на число, то блок будет прилипать до того, как нижний край элемента дойдёт до футера
			if ((Ra.top - P) <= 0) {
				if ((Ra.top - P) <= R) {
					b.className = 'stop';
					b.style.top = - R + 'px';
				} else {
					b.className = 'sticky';
					b.style.top = P + 'px';
				}
			} else {
				b.className = '';
				b.style.top = '';
			}
			window.addEventListener('resize', function () {
				a.children[0].style.width = getComputedStyle(a, '').width
			}, false);
		}
	})()

}


jQuery(document).ready(function ($) {

	$.fn.initCarouselSlider = function () {

		$('.media-slider:not(.slick-initialized)').each(function (idx, item) {

			this.id = "carousel2" + idx;

			$(this).slick({
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: true,
				dots: false,
				focusOnSelect: true,
				touchThreshold: 200,
				responsive: [{
					breakpoint: 1200,
					settings:
					{
						arrows: false,
						dots: true,
					}
				}]
			});
		});
	}
	$(document).initCarouselSlider();
	$.fn.showNotices = function (messages) {

		if (messages.length == 0)
			return false;

		$(messages).each(function (index, el) {

			$('#notice').append(el);

			let addedElement = $('#notice').children().last();

			setTimeout(function () {
				addedElement.fadeOut(1000, function () {
					addedElement.remove();
				});
			}, 7000);
		});
	}

	$('#front-load-more-posts').on('click', function (event) {
		event.preventDefault();

		if ($(this).hasClass('disabled')) return false;
		$(this).addClass('disabled');

		let params, obj, objClick;

		params = new Object();
		objClick = $(this);
		obj = objClick.closest('.blog-items');

		params.offset = obj.find('.blog-item').length;

		$.ajax({
			type: "POST",
			url: appVars.ajaxurl,
			beforeSend: function () {
				objClick.toggleClass('active', true);
			},
			data: {
				action: 'front_load_more_posts',
				data: params,
				security: appVars.securitycode,
			},
			success: function (res) {
				objClick.toggleClass('active', false);
				objClick.toggleClass('disabled', false);

				if (res.data.status == 1)
					obj.find('.blog-item:last').after(res.data.html);

				if (res.data.is_hidden_button)
					objClick.remove();

				if (res.data.status == 3)
					$.fn.showNotices(res.data.error.info);
			}
		});
	});

	$.fn.productFilterTriggerClick = function () {
		$('#front-load-products').trigger('click');
	}

	$.fn.productFilterSyncUrl = function (obj) {
		var $selected = obj.find('input[name="color"][type="checkbox"]:checked').first();
		var selectedSlug = ($selected.attr('data-term-slug') || '').trim();

		var path = window.location.pathname || '/';
		var hasTrailingSlash = /\/$/.test(path);
		var segments = path.split('/').filter(function (s) { return s.length > 0; });

		var colorIndex = -1;
		var materialIndex = -1;

		segments.forEach(function (seg, i) {
			if (/^color[-_]/.test(seg)) colorIndex = i;
			if (/^material_/.test(seg)) materialIndex = i;
		});

		if (colorIndex !== -1) {
			segments.splice(colorIndex, 1);
			if (materialIndex !== -1 && materialIndex > colorIndex) {
				materialIndex -= 1;
			}
		}

		if (selectedSlug) {
			var colorSegment = 'color-' + selectedSlug;
			if (materialIndex !== -1) {
				segments.splice(materialIndex, 0, colorSegment);
			} else {
				segments.push(colorSegment);
			}
		}

		var newPath = '/' + segments.join('/');
		if (newPath !== '/' || hasTrailingSlash) {
			newPath = newPath.replace(/\/?$/, '/');
		}

		var newRelativeUrl = newPath + window.location.search + window.location.hash;
		var currentRelativeUrl = window.location.pathname + window.location.search + window.location.hash;
		if (newRelativeUrl !== currentRelativeUrl) {
			window.history.replaceState({}, '', newRelativeUrl);
		}
	}

	$.fn.productFilterParams = function (obj) {

		let params, brandIds, materialIds, sizeIds, colorIds;

		params = new Object();

		params.audience_category = obj.find('input[name="audience_category"]').val();
		params.product_category = obj.find('input[name="product_category"]').val();
		params.brand_id = obj.find('input[name="brand_id"]').val();
		params.search = obj.find('input[name="search"]').val();
		params.highlighted = obj.find('input[name="highlighted"]').val();
		params.sort = obj.find('select[name="sort"]').val();

		if (obj.find('.price-min').length)
			params.price_min = obj.find('.price-min').text();

		if (obj.find('.price-max').length)
			params.price_max = obj.find('.price-max').text();

		brandIds = new Object();
		obj.find('input[name="brand"][type="checkbox"]:checked').each(function (i, el) {
			brandIds[i] = $(el).attr('data-term-id');
		});

		materialIds = new Object();
		obj.find('input[name="material"][type="checkbox"]:checked').each(function (i, el) {
			materialIds[i] = $(el).attr('data-term-id');
		});

		sizeIds = new Object();
		obj.find('input[name="size"][type="checkbox"]:checked').each(function (i, el) {
			sizeIds[i] = $(el).attr('data-term-id');
		});

		colorIds = new Object();
		obj.find('input[name="color"][type="checkbox"]:checked').each(function (i, el) {
			colorIds[i] = $(el).attr('data-term-id');
		});



		params.brand_ids = brandIds;
		params.material_ids = materialIds;
		params.size_ids = sizeIds;
		params.color_ids = colorIds;

		return params;
	}

	$.fn.applyFilterFacets = function (obj, facets) {

		// reset: показываем всё и снимаем disabled-стили
		obj.find('input[type="checkbox"][name="brand"], input[type="checkbox"][name="color"], input[type="checkbox"][name="material"], input[type="checkbox"][name="size"]').each(function () {
			var $inp = $(this);
			$inp.prop('disabled', false).closest('label').removeClass('is-disabled');

			// показать контейнер
			var $wrap = $inp.closest('.checkbox, .color');
			if ($wrap.length) $wrap.removeClass('d-none');
		});

		if (!facets) return;

		var hasAny =
			(facets.brand_ids && facets.brand_ids.length) ||
			(facets.color_ids && facets.color_ids.length) ||
			(facets.material_ids && facets.material_ids.length) ||
			(facets.size_ids && facets.size_ids.length);

		if (!hasAny) return;

		var sets = {
			brand: new Set((facets.brand_ids || []).map(String)),
			color: new Set((facets.color_ids || []).map(String)),
			material: new Set((facets.material_ids || []).map(String)),
			size: new Set((facets.size_ids || []).map(String)),
		};

		['brand', 'color', 'material', 'size'].forEach(function (name) {
			var allowed = sets[name];

			obj.find('input[name="' + name + '"][type="checkbox"]').each(function () {
				var $inp = $(this);
				var id = String($inp.attr('data-term-id') || '');
				var ok = allowed.has(id);

				var $label = $inp.closest('label');
				var $wrap = $inp.closest('.checkbox, .color');

				if (!ok) {
					// если НЕ выбран — скрываем
					if (!$inp.prop('checked')) {
						if ($wrap.length) $wrap.addClass('d-none');
						$inp.prop('disabled', true);
						$label.addClass('is-disabled');
					} else {
						// выбранное не прячем, но можно подсветить как "оставлено"
						$label.removeClass('is-disabled');
						if ($wrap.length) $wrap.removeClass('d-none');
					}
				} else {
					// ok => показываем
					if ($wrap.length) $wrap.removeClass('d-none');
					$inp.prop('disabled', false);
					$label.removeClass('is-disabled');
				}
			});
		});
	};

	$('#front-load-products').on('click', function (event) {
		event.preventDefault();

		if ($(this).hasClass('disabled')) return false;
		$(this).addClass('disabled');

		let params, obj, objContainer, objClick, moreButton;

		params = new Object();
		objClick = $(this);
		obj = objClick.closest('#JS-product-filter');
		objContainer = obj.find('.products-container');
		moreButton = $('#front-load-more-products');

		params = $.fn.productFilterParams(obj);

		$.ajax({
			type: "POST",
			url: appVars.ajaxurl,
			beforeSend: function () {
				objContainer.toggleClass('d-none', true);
				obj.find('.items-load').toggleClass('d-none', false);
			},
			data: {
				data: params,
				action: 'front_get_products_by_filter',
				security: appVars.securitycode,
			},
			success: function (res) {
				obj.find('.items-load').toggleClass('d-none', true);
				objContainer.toggleClass('d-none', false);
				objClick.toggleClass('disabled', false);

				if (res.data.status == 4 || res.data.status == 1) {
					objContainer.html(res.data.html);
					obj.find('#products-found').text(res.data.products_found);

					$.fn.initCarouselSlider();

					if (res.data.facets) {
						$.fn.applyFilterFacets(obj, res.data.facets);
					}

					$.fn.productFilterSyncUrl(obj);
				}

				if (res.data.status == 4 || res.data.is_hidden_button)
					moreButton.toggleClass('d-none', true);

				if (!res.data.is_hidden_button)
					moreButton.toggleClass('d-none', false);

				if (res.data.status == 3)
					$.fn.showNotices(res.data.error.info);
			}
		});
	});

	$('#front-load-more-products').on('click', function (event) {
		event.preventDefault();

		if ($(this).hasClass('disabled')) return false;
		$(this).addClass('disabled');

		let params, obj, objContainer, objClick;

		params = new Object();
		objClick = $(this);
		obj = objClick.closest('#JS-product-filter');
		objContainer = obj.find('.products-container');

		params = $.fn.productFilterParams(obj);

		params.offset = objContainer.find('.product-item').length;

		$.ajax({
			type: "POST",
			url: appVars.ajaxurl,
			beforeSend: function () {
				objClick.toggleClass('active', true);
			},
			data: {
				data: params,
				action: 'front_get_products_by_filter',
				security: appVars.securitycode,
			},
			success: function (res) {
				objClick.toggleClass('active', false);
				objClick.toggleClass('disabled', false);

				if (res.data.status == 1) {
					objContainer.append(res.data.html);

					$.fn.initCarouselSlider();
				}

				if (res.data.status == 4 || res.data.is_hidden_button)
					objClick.toggleClass('d-none', true);

				if (res.data.status == 3)
					$.fn.showNotices(res.data.error.info);
			}
		});
	});

	$('#JS-product-filter').on('change', 'select[name="sort"]', function () {
		$.fn.productFilterTriggerClick();
	});

	$('#JS-product-filter').on('change', 'input[name="color"][type="checkbox"]', function () {
		var $changed = $(this);
		var $filter = $changed.closest('#JS-product-filter');

		// URL supports a single color segment, keep one active color in UI.
		if ($changed.prop('checked')) {
			$filter.find('input[name="color"][type="checkbox"]').not($changed).prop('checked', false);
		}
	});

	$('body').on('click', '.front-add-product-to-cart', function (event) {
		event.preventDefault();

		if ($(this).hasClass('disabled')) return false;
		$(this).addClass('disabled');

		let params, obj, objClick;

		params = new Object();
		objClick = $(this);
		obj = objClick.closest('[data-product-container]');

		if (obj.find('.sizes-container .active').length == 0) {
			obj.find('.sizes-container').addClass('failer');
			objClick.toggleClass('disabled', false);
			return false;
		}

		params.product_id = obj.attr('data-product-id');
		params.product_size = obj.attr('data-product-size');
		params.quantity = obj.find('.quant-input').length ? obj.find('.quant-input').val() : obj.attr('data-product-quantity');

		$.ajax({
			type: "POST",
			url: appVars.ajaxurl,
			beforeSend: function () {
				$('.preloader').toggleClass('d-none', false);
			},
			data: {
				data: params,
				action: 'front_add_product_to_cart',
				security: appVars.securitycode,
			},
			success: function (res) {
				objClick.toggleClass('disabled', false);
				$('.preloader').toggleClass('d-none', true);

				if (res.data.status == 1) {

					// ✅ GA4 add_to_cart (только при успешном добавлении)
					(function pushAddToCartGA4() {
						var currency = obj.attr('data-currency') || 'UAH';

						var qty = parseInt(obj.attr('data-product-quantity'), 10);
						if (!qty || qty < 1) qty = 1;

						var price = parseFloat(obj.attr('data-product-price'));
						var name = (obj.attr('data-product-name') || '').trim();

						// если нет данных — не шлём кривой эвент (NaN / пустое имя)
						if (!isFinite(price) || !name) return;

						var item = {
							item_id: String(obj.attr('data-product-id') || ''),
							item_name: name,
							item_variant: String(obj.attr('data-product-size-name') || obj.attr('data-product-size') || ''),

							quantity: qty,
							price: price
						};

						window.dataLayer = window.dataLayer || [];
						window.dataLayer.push({ ecommerce: null });
						window.dataLayer.push({
							event: 'add_to_cart',
							ecommerce: {
								currency: currency,
								value: +(price * qty).toFixed(2),
								items: [item]
							}
						});
					})();

					// твой текущий код
					$.fn.showNotices(res.data.error.info);

					$('#mobile-buy').modal('hide');
					$('body').find('[data-cart-product-count]').text(res.data.cart_product_count);
					$('body').find('[data-product-id="' + params.product_id + '"] [data-laptop-button]').replaceWith(res.data.added_product_html);
					$('body').find('[data-product-id="' + params.product_id + '"] [data-mobile-button]').replaceWith(res.data.mobile_added_product_html);
					$('body').find('[data-product-id="' + params.product_id + '"] .sizes-container:not(.single-product-sizes-container, .mobile-buy-sizes-container)').toggleClass('d-none', true);
				}

				if (res.data.status == 3)
					$.fn.showNotices(res.data.error.info);
			}

		});
	});

	$('body').on('click', '.front-delete-product-from-cart', function (event) {
		event.preventDefault();

		if ($(this).hasClass('disabled')) return false;
		$(this).addClass('disabled');

		let params, obj, objClick;

		params = new Object();
		objClick = $(this);
		obj = objClick.closest('[data-cart-product-container]');

		params.product_id = obj.attr('data-product-id');
		params.product_size = obj.attr('data-product-size');
		params.post_id = appVars.postID;

		$.ajax({
			type: "POST",
			url: appVars.ajaxurl,
			beforeSend: function () {
				$('.preloader').toggleClass('d-none', false);
			},
			data: {
				data: params,
				action: 'front_delete_product_from_cart',
				security: appVars.securitycode,
			},
			success: function (res) {
				objClick.toggleClass('disabled', false);
				$('.preloader').toggleClass('d-none', true);

				if (res.data.status == 1) {
					$.fn.showNotices(res.data.error.info);
					$('.modal-cart').html(res.data.html);
					$('body').find('[data-cart-product-count]').text(res.data.cart_product_count);
					$('body').find('[data-product-id="' + params.product_id + '"] [data-laptop-button]').replaceWith(res.data.add_product_html);
					$('body').find('[data-product-id="' + params.product_id + '"] [data-mobile-button]').replaceWith(res.data.mobile_add_product_html);
					$('body').find('[data-product-id="' + params.product_id + '"] .sizes-container:not(.single-product-sizes-container)').toggleClass('d-none', false);
				}

				if (res.data.status == 3)
					$.fn.showNotices(res.data.error.info);
			}
		});
	});

	$('body').on('click', '.front-add-product-to-wishlist', function (event) {
		event.preventDefault();

		if ($(this).hasClass('disabled')) return false;
		$(this).addClass('disabled');

		let params, obj, objClick;

		params = new Object();
		objClick = $(this);
		obj = objClick.closest('[data-product-container]');

		params.product_id = obj.attr('data-product-id');
		params.is_single = obj.attr('data-is-single');

		$.ajax({
			type: "POST",
			url: appVars.ajaxurl,
			data: {
				action: 'front_add_product_to_wishlist',
				data: params,
				security: appVars.securitycode,
			},
			success: function (res) {
				objClick.toggleClass('disabled', false);

				if (res.data.status == 1) {
					$.fn.showNotices(res.data.error.info);
					objClick.replaceWith(res.data.html);
					$('body').find('[data-count-wishlist]').text(res.data.count_wishlist);
				}

				if (res.data.status == 3)
					$.fn.showNotices(res.data.error.info);
			}
		});
	});

	$('body').on('click', '.front-delete-product-from-wishlist', function (event) {
		event.preventDefault();

		if ($(this).hasClass('disabled')) return false;
		$(this).addClass('disabled');

		let params, obj, objClick;

		params = new Object();
		objClick = $(this);
		obj = objClick.closest('[data-product-container]');

		params.product_id = obj.attr('data-product-id');
		params.is_single = obj.attr('data-is-single');

		$.ajax({
			type: "POST",
			url: appVars.ajaxurl,
			data: {
				action: 'front_delete_product_from_wishlist',
				data: params,
				security: appVars.securitycode,
			},
			success: function (res) {
				objClick.toggleClass('disabled', false);

				if (res.data.status == 1) {
					$.fn.showNotices(res.data.error.info);
					objClick.replaceWith(res.data.html);
					$('body').find('[data-count-wishlist]').text(res.data.count_wishlist);
				}

				if (res.data.status == 3)
					$.fn.showNotices(res.data.error.info);
			}
		});
	});

	$('#front-load-wishlist-products').on('click', function (event) {
		event.preventDefault();

		if ($(this).hasClass('disabled')) return false;
		$(this).addClass('disabled');

		let params, obj, objContainer, objClick, moreButton;

		params = new Object();
		objClick = $(this);
		obj = objClick.closest('#JS-product-wishlist');
		objContainer = obj.find('.products-container');
		moreButton = $('#front-load-more-wishlist-products');

		params.offset = 0;

		$.ajax({
			type: "POST",
			url: appVars.ajaxurl,
			beforeSend: function () {
				objContainer.toggleClass('d-none', true);
				obj.find('.items-load').toggleClass('d-none', false);
			},
			data: {
				data: params,
				action: 'front_get_wishlist_products',
				security: appVars.securitycode,
			},
			success: function (res) {
				obj.find('.items-load').toggleClass('d-none', true);
				objContainer.toggleClass('d-none', false);
				objClick.toggleClass('disabled', false);

				if (res.data.status == 4 || res.data.status == 1) {
					objContainer.html(res.data.html);

					$.fn.initCarouselSlider();
				}

				if (res.data.status == 4 || res.data.is_hidden_button)
					moreButton.toggleClass('d-none', true);

				if (!res.data.is_hidden_button)
					moreButton.toggleClass('d-none', false);

				if (res.data.status == 3)
					$.fn.showNotices(res.data.error.info);
			}
		});
	});

	$('#front-load-more-wishlist-products').on('click', function (event) {
		event.preventDefault();

		if ($(this).hasClass('disabled')) return false;
		$(this).addClass('disabled');

		let params, obj, objContainer, objClick;

		params = new Object();
		objClick = $(this);
		obj = objClick.closest('#JS-product-wishlist');
		objContainer = obj.find('.products-container');

		params.offset = objContainer.find('.product-item').length;

		$.ajax({
			type: "POST",
			url: appVars.ajaxurl,
			beforeSend: function () {
				objClick.toggleClass('active', true);
			},
			data: {
				data: params,
				action: 'front_get_wishlist_products',
				security: appVars.securitycode,
			},
			success: function (res) {
				objClick.toggleClass('active', false);
				objClick.toggleClass('disabled', false);

				if (res.data.status == 1) {
					objContainer.append(res.data.html);

					$.fn.initCarouselSlider();
				}

				if (res.data.status == 4 || res.data.is_hidden_button)
					objClick.toggleClass('d-none', true);

				if (res.data.status == 3)
					$.fn.showNotices(res.data.error.info);
			}
		});
	});

	if ($('#JS-product-wishlist').length) {
		setTimeout(function () {
			$('#front-load-wishlist-products').trigger('click');
		}, 1000);
	}


	$(function () {
		$.ajax({
			type: "POST",
			url: appVars.ajaxurl,
			data: {
				action: 'front_get_wishlist_count',
				security: appVars.securitycode,
			},
			success: function (res) {
				if (!res || !res.data) {
					return;
				}

				if (typeof res.data.count_wishlist !== 'undefined') {
					$('[data-count-wishlist]').text(res.data.count_wishlist);
				}
			}
		});
	});


	$('body').on('click', '.front-set-chosen-size', function (e) {

		const objClick = $(this);

		// недоступные — игнор
		if (objClick.hasClass('no-available') || objClick.is('[aria-disabled="true"], :disabled')) {
			e.preventDefault();
			return;
		}

		let params = {};
		const obj = objClick.closest('.sizes-container');

		obj.removeClass('failer');
		obj.find('[data-size-id]').removeClass('active');

		params.size_id = objClick.addClass('active').attr('data-size-id');

		obj.closest('[data-product-container]').attr('data-product-size', params.size_id);

		$.ajax({
			type: "POST",
			url: appVars.ajaxurl,
			data: {
				data: params,
				action: 'front_set_chosen_size',
				security: appVars.securitycode,
			},
			success: function (res) {
				if (res && res.data && res.data.status == 3) {
					$.fn.showNotices(res.data.error.info);
				}
			}
		});
	});

	$('body').on('click', '.front-set-chosen-size', function () {
		if ($(this).hasClass('no-available')) return false;

		let sizeSlug = $(this).attr('data-size-slug');

		if (!sizeSlug) return false;

		let url = new URL(window.location.href);
		url.searchParams.set('size', sizeSlug);

		window.history.replaceState({}, '', url.toString());
	});

	$('body').on('click', '.quant-button', function (event) {
		event.preventDefault();

		var obj, number, old, now, unix, last_unix;

		obj = $(this).parent();
		number = $(this).attr('data-number');
		old = obj.find('input').val();

		now = parseInt(old) + parseInt(number);
		if (now <= 0)
			return false;

		obj.find('input').val(now);

		var container = obj.closest('[data-product-container]');
		if (container.length) container.attr('data-product-quantity', now);

		if (!$(this).hasClass('quant-change-postponed')) {
			obj.find('input').trigger('change');
			return false;
		}


		unix = parseInt(new Date().getTime() / 1);
		obj.attr('data-unix', unix);
		setTimeout(function (unix) {
			last_unix = obj.attr('data-unix');
			if (unix != last_unix) return false;
			obj.find('input').trigger('change');
		}, 1500, unix);
	});

	$('body').on('change', '.quant-input', function () {
		var container = $(this).closest('[data-product-container]');
		if (!container.length) return;

		var v = parseInt($(this).val(), 10);
		if (!v || v < 1) v = 1;

		$(this).val(v);
		container.attr('data-product-quantity', v);
	});

	$.fn.checkQuantity = function (obj, quantity) {

		if (/[^0-9]/.test(quantity) || quantity <= 0) {
			obj.val(obj.attr('data-quantity'));
			return false;
		}

		obj.attr('data-quantity', quantity);
	};

	$('body').on('change', '[data-product-container] input[name="quant"]', function () {

		let quantity = $(this).val();

		let check = $.fn.checkQuantity($(this), quantity);
		if (check === false) return;

		$(this).closest('[data-product-container]').attr('data-product-quantity', quantity);
	});

	$('body').on('change', '[data-cart-product-container] input[name="quant"]', function () {

		let params, obj, objClick, check;

		params = new Object();
		objClick = $(this);
		obj = objClick.closest('[data-cart-product-container]');

		params.product_id = obj.attr('data-product-id');
		params.product_size = obj.attr('data-product-size');
		params.quantity = objClick.val();
		params.post_id = appVars.postID;

		check = $.fn.checkQuantity(objClick, params.quantity);
		if (check === false) return;

		$.ajax({
			type: "POST",
			url: appVars.ajaxurl,
			beforeSend: function () {
				$('.preloader').toggleClass('d-none', false);
			},
			data: {
				data: params,
				action: 'front_change_product_quantity_in_cart',
				security: appVars.securitycode,
			},
			success: function (res) {
				$('.preloader').toggleClass('d-none', true);

				if (res.data.status == 1) {
					$.fn.showNotices(res.data.error.info);
					$('.modal-cart').html(res.data.html);
				}

				if (res.data.status == 3)
					$.fn.showNotices(res.data.error.info);
			}
		});
	});

	$('body').on('click', '.front-get-cart', function (event) {
		event.preventDefault();

		if ($(this).hasClass('disabled')) return false;
		$(this).addClass('disabled');

		let params, objClick;

		params = new Object();
		objClick = $(this);

		$.ajax({
			type: "POST",
			url: appVars.ajaxurl,
			beforeSend: function () {
				$('.preloader').toggleClass('d-none', false);
			},
			data: {
				data: params,
				action: 'front_get_cart',
				security: appVars.securitycode,
			},
			success: function (res) {
				objClick.toggleClass('disabled', false);
				$('.preloader').toggleClass('d-none', true);

				if (res.data.status == 1) {
					$('.modal-cart').html(res.data.html);
					$('[data-target="#cart"]').trigger('click');
					$('body').find('[data-cart-product-count]').text(res.data.cart_product_count);
				}

				if (res.data.status == 3)
					$.fn.showNotices(res.data.error.info);
			}
		});
	});

	$('body').on('click', '#front-place-order', function (event) {
		event.preventDefault();

		if ($(this).hasClass('disabled')) return false;
		$(this).addClass('disabled');

		let params, objClick, obj;

		params = new Object();
		objClick = $(this);
		obj = objClick.closest('.order-container');

		params.firstname = obj.find('input[name="firstname"]').val();
		params.lastname = obj.find('input[name="lastname"]').val();
		params.email = obj.find('input[name="email"]').val();
		params.phone = obj.find('input[name="phone"]').val();
		params.comment = obj.find('textarea[name="comment"]').val();
		params.payment_type = obj.find('input[name="payment_type"]:checked').length ? obj.find('input[name="payment_type"]:checked').val() : 0;
		params.delivery_type = obj.find('input[name="delivery_type"]:checked').length ? obj.find('input[name="delivery_type"]:checked').val() : 0;

		// Ref (id) + text отдельно
		params.location = obj.find('select[name="location"]').val() ? obj.find('select[name="location"]').val() : '';
		params.warehouse = obj.find('select[name="warehouse"]').val() ? obj.find('select[name="warehouse"]').val() : '';

		// тексты для писем
		params.location_text = obj.find('input[name="location_text"]').val() ? obj.find('input[name="location_text"]').val() : '';
		params.warehouse_text = obj.find('input[name="warehouse_text"]').val() ? obj.find('input[name="warehouse_text"]').val() : '';

		// адресная доставка
		params.address_full = obj.find('input[name="address_full"]').val() ? obj.find('input[name="address_full"]').val() : '';
		params.address_express = obj.find('input[name="address_express"]').is(':checked') ? 1 : 0;


		$.ajax({
			type: "POST",
			url: appVars.ajaxurl,
			beforeSend: function () {
				$('.preloader').toggleClass('d-none', false);
			},
			data: {
				data: params,
				action: 'front_place_order',
				security: appVars.securitycode,
			},
			success: function (res) {
				objClick.toggleClass('disabled', false);
				$('.preloader').toggleClass('d-none', true);

				if (res.data.status == 1) {

					let paymentForm = $('#payment-form');

					paymentForm.html(res.data.html);

					setTimeout(function () {
						paymentForm.find('form').submit();
					}, 100);
				}

				if (res.data.status == 2)
					window.location.replace(res.data.href);

				if (res.data.status == 3)
					$.fn.showNotices(res.data.error.info);
			}
		});
	});

	$.fn.initJqSlider = function (el) {

		// нет jQuery UI slider → тихо выходим
		if (!$.fn.slider) {
			return;
		}

		let valueMin, valueMax, min, max;

		valueMin = $(el).attr('data-value-min');
		valueMax = $(el).attr('data-value-max');
		min = $(el).attr('data-min');
		max = $(el).attr('data-max');

		$(el).find('.jq-ui-slider').slider({
			range: true,
			values: [valueMin, valueMax],
			min: parseFloat(min),
			max: parseFloat(max),
			step: 1,
			stop: function () {
				var obj = $(this).closest('.jq-ui-slider');
				var objParent = obj.closest('.filter-slider');

				objParent.find('.price-min').text(obj.slider('values', 0));
				objParent.find('.price-max').text(obj.slider('values', 1));
			},
			slide: function () {
				var obj = $(this).closest('.jq-ui-slider');
				var objParent = obj.closest('.filter-slider');

				objParent.find('.price-min').text(obj.slider('values', 0));
				objParent.find('.price-max').text(obj.slider('values', 1));
			}
		});
	};


	$('.filter-slider').each(function (index, el) {
		$.fn.initJqSlider(el);
	});

	$.fn.openSubscribeModal = function () {

		$('#subscribe').modal('show');

		$.ajax({
			type: "POST",
			url: appVars.ajaxurl,
			data: {
				action: 'front_open_subscribe_modal',
				security: appVars.securitycode,
			},
			success: function (res) { }
		});
	}

	if ($('#subscribe').length) {
		setTimeout(function () {
			$.fn.openSubscribeModal();
		}, 18000);
	}





});









$('body').on('click', '.submit-promo', function (e) {
	e.preventDefault();
	var $wrap = $(this).closest('.cart-promo');
	var $msgBox = $wrap.find('[data-promo-msg]');
	var code = $.trim($wrap.find('[name="promo_code"]').val());
	var post_id = $('#promo-fallback').data('postid') || 0;

	if (!code) { $msgBox.html('<div class="promo-msg promo-msg--error">Вкажіть промокод</div>'); return; }

	$('.preloader').toggleClass('d-none', false);

	$.ajax({
		type: 'POST',
		url: appVars.ajaxurl,
		data: { action: 'apply_promo_code', code: code, post_id: post_id, security: appVars.securitycode },
		success: function (res) {
			$('.preloader').toggleClass('d-none', true);
			if (res && res.success && res.data && res.data.status == 1) {
				// перерисуем весь блок корзины (со скидкой)
				$('.modal-cart').html(res.data.html);
				$('body').find('[data-cart-product-count]').text(res.data.cart_product_count);
			} else {
				var msg = (res && res.data && res.data.error && res.data.error.info) || 'Промокод не знайдено';
				$msgBox.html('<div class="promo-msg promo-msg--error">' + msg + '</div>');
			}
		},
		error: function () {
			$('.preloader').toggleClass('d-none', true);
			$msgBox.html('<div class="promo-msg promo-msg--error">Помилка з’єднання. Спробуйте ще раз.</div>');
		}
	});
});

$('body').on('click', '.js-remove-promo', function (e) {
	e.preventDefault();
	var post_id = $('#promo-fallback').data('postid') || 0;
	$('.preloader').toggleClass('d-none', false);
	$.ajax({
		type: 'POST',
		url: appVars.ajaxurl,
		data: { action: 'remove_promo_code', post_id: post_id, security: appVars.securitycode },
		success: function (res) {
			$('.preloader').toggleClass('d-none', true);
			if (res && res.success && res.data && res.data.status == 1) {
				$('.modal-cart').html(res.data.html);
				$('body').find('[data-cart-product-count]').text(res.data.cart_product_count);
			}
		},
		error: function () {
			$('.preloader').toggleClass('d-none', true);
		}
	});
});




document.addEventListener('DOMContentLoaded', function () {
	const btn = document.querySelector('#front-place-order');
	if (btn) {
		btn.setAttribute('data-fb-event', 'InitiateCheckout');
		btn.setAttribute('data-fb-params', JSON.stringify({ step: 'checkout' }));
	}
});









document.addEventListener('DOMContentLoaded', function () {
	const copyBlocks = document.querySelectorAll('.copy-container-js');

	if (!copyBlocks.length) {
		return;
	}

	copyBlocks.forEach(function (block) {
		block.addEventListener('click', function () {
			var textToCopy = block.getAttribute('data-copy-content');

			if (!textToCopy) {
				return;
			}

			function onSuccess() {
				block.classList.add('copied');

				// снимаем класс через 3 секунды
				setTimeout(function () {
					block.classList.remove('copied');
				}, 3000);
			}

			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(textToCopy).then(function () {
					onSuccess();
				}).catch(function () {
					copyWithFallback(textToCopy, block, onSuccess);
				});
			} else {
				copyWithFallback(textToCopy, block, onSuccess);
			}
		});
	});

	function copyWithFallback(text, block, callback) {
		var tempInput = document.createElement('input');
		tempInput.value = text;
		document.body.appendChild(tempInput);
		tempInput.select();
		try {
			document.execCommand('copy');
			if (typeof callback === 'function') {
				callback();
			}
		} catch (e) {
			console.error('Copy failed', e);
		}
		document.body.removeChild(tempInput);
	}
});




(function ($) {
	if (typeof appVars === 'undefined') return;

	function esAjax(action, extra) {
		return $.ajax({
			url: appVars.ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: $.extend(
				{ action: action, security: appVars.securitycode },
				extra || {}
			)
		});
	}

	function isValidEmail(email) {
		return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
	}


	// fallback: ловим email на submit любой формы
	/* document.addEventListener('submit', function (e) {
		var f = e.target;
		if (!f || f.tagName !== 'FORM') return;

		var email = '';
		var fields = f.querySelectorAll('input, textarea');

		fields.forEach(function (el) {
			if (email) return;

			var v = (el.value || '').trim();
			if (!v) return;

			if (isValidEmail(v)) email = v;
		});

		if (!email) return;

		esAjax('front_es_set_email', { email: email });
	}, true); */


	// touch
	function touch() { esAjax('front_es_touch'); }
	$(touch);

	var lastTouch = 0;
	function throttledTouch() {
		var now = Date.now();
		if (now - lastTouch < 30000) return;
		lastTouch = now;
		touch();
	}
	$(document).on('click keydown scroll', throttledTouch);
	document.addEventListener('visibilitychange', function () {
		if (document.visibilityState === 'hidden') touch();
	});

	// email capture (включая name="your-email")
	// email capture (works with widgets / shadow DOM)
	var emailTimer = null;
	var lastSent = '';

	function pickEmailFromEvent(e) {
		var path = (e && e.composedPath) ? e.composedPath() : [e.target];

		for (var i = 0; i < path.length; i++) {
			var el = path[i];
			if (!el) continue;

			// ищем элемент, у которого вообще есть value
			var val = '';
			try { val = (el.value || '').toString().trim(); } catch (err) { val = ''; }
			if (!val) continue;

			if (!isValidEmail(val)) continue;

			// не обязателен, но снижает ложные срабатывания
			var meta = '';
			try {
				meta =
					((el.type || '') + ' ' +
						(el.name || '') + ' ' +
						(el.id || '') + ' ' +
						(el.placeholder || '') + ' ' +
						(el.autocomplete || '') + ' ' +
						(el.getAttribute ? (el.getAttribute('aria-label') || '') : '')).toLowerCase();
			} catch (err2) { meta = ''; }

			// если вообще нет подсказок что это email — всё равно можно отправлять,
			// но я оставил мягкий фильтр
			/* if (meta && meta.indexOf('mail') === -1 && meta.indexOf('email') === -1 && meta.indexOf('пошт') === -1 && meta.indexOf('почт') === -1 && (el.type || '').toLowerCase() !== 'email' && (el.autocomplete || '').toLowerCase() !== 'email') {
				// если хочешь максимально “как раньше” — просто убери этот if целиком
				continue;
			} */

			return val;
		}

		return '';
	}

	function sendEmail(val) {
		if (!val) return;
		if (val === lastSent) return;
		lastSent = val;

		esAjax('front_es_set_email', { email: val });
	}

	// capture phase — важно для виджетов
	['input', 'change', 'blur'].forEach(function (evt) {
		document.addEventListener(evt, function (e) {
			var val = pickEmailFromEvent(e);
			if (!val) return;

			clearTimeout(emailTimer);
			emailTimer = setTimeout(function () {
				sendEmail(val);
			}, 300);
		}, true);
	});

	// fallback на submit: если виджет генерит обычный <form>
	document.addEventListener('submit', function (e) {
		var form = e.target;
		if (!form || form.tagName !== 'FORM') return;

		try {
			var els = form.querySelectorAll('input, textarea');
			for (var i = 0; i < els.length; i++) {
				var v = (els[i].value || '').toString().trim();
				if (v && isValidEmail(v)) {
					sendEmail(v);
					break;
				}
			}
		} catch (err) { }
	}, true);


})(jQuery);



// category description collapse
(() => {
	const blocks = document.querySelectorAll('.category-description');

	blocks.forEach((block) => {
		const content = block.querySelector('.text-content');
		const btn = block.querySelector('.link-plus');

		if (!content || !btn) return;

		const LIMIT = 200;

		// измеряем реальную высоту
		content.classList.remove('text-content--collapse');
		const realHeight = content.scrollHeight;

		if (realHeight > LIMIT) {
			content.classList.add('text-content--collapse');
		} else {
			btn.classList.remove('d-inline-flex');
			btn.classList.add('d-none');
		}

		btn.addEventListener('click', () => {
			content.classList.remove('text-content--collapse');
			btn.classList.remove('d-inline-flex');
			btn.classList.add('d-none')
		});
	});
})();




(() => {
	function fillCf7Hidden() {
		const forms = document.querySelectorAll('.wpcf7 form');
		if (!forms.length) return;

		const pageUrl = window.location.href;

		// пробуем найти название товара: h1 на странице товара
		const h1 = document.querySelector('h1');
		const productTitle = h1 ? h1.textContent.trim() : '';

		forms.forEach((form) => {
			const urlInput = form.querySelector('input[name="page-url"]');
			if (urlInput) urlInput.value = pageUrl;

			const titleInput = form.querySelector('input[name="product-title"]');
			if (titleInput) titleInput.value = productTitle;
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', fillCf7Hidden);
	} else {
		fillCf7Hidden();
	}

	// если CF7 перерисовывает форму после отправки/валидации
	document.addEventListener('wpcf7init', fillCf7Hidden);
})();
