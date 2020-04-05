var icms = icms || {};

icms.showcase = (function ($) {

	this.cart_styles = {};
	this.indexs = {};
	this.sync1 = false;
	this.sync2 = false;
	this.light_gallery = false;
	this.seted_variant = false;
	this.price_round = true;
	this.list_pos = 'center';
	this.list_height = 200;

    this.onDocumentReady = function() {

        var list_grid = $('.showcase_list_grid').length ? $('.showcase_list_grid') : false;
		if (list_grid){
			icms.showcase.bulidListGrid(list_grid);
			$(window).on('resize', function (){
				icms.showcase.bulidListGrid(list_grid);
			});
		}
		
		$('.wd_sc_cart .wd_sc_cart_loader').addClass('sc_load_hide');
		var btn_cart = $('.sc_cart_btn');
		var item_id = btn_cart.data('item_id');
		btn_cart.on('click', function() {
			var item_id = $(this).data('item_id');
			var variant_id = $(this).attr('data-variant');
			if (!item_id){ return; }
			if (!parseInt(variant_id) && $('#sc_item_' + item_id + ' .sc_variations_select, #item_' + item_id + ' .sc_variations_select').length){
				icms.modal.alert('Выберите вариант', 'ui_error');console.log($('#sc_item_' + item_id + ' .sc_variations_select'));
				$(".tzSelect .selectBox").addClass('sc_error_empty');
				if ($(".tzSelect .selectBox").length){
					$('html, body').animate({
						scrollTop: ($(".sc_right_box").offset().top - 70)
					}, 800);
				} else if ($('#sc_item_' + item_id + ' .sc_variations_select').length){
					$('#sc_item_' + item_id + ' .sc_variations_select').addClass('sc_error_empty');
				}
				return;
			}
			cart_data.qty = $('#item_' + item_id + ' .sc_qty_count input').length ? parseInt($('#item_' + item_id + ' .sc_qty_count input').val()) : 1;
			if (!cart_data.qty){
				icms.modal.alert('Укажите количество', 'ui_error');
				$(".sc_buy_qty").addClass('sc_error_empty');
				 $('html, body').animate({
					scrollTop: ($(".sc_right_box").offset().top - 70)
				}, 800);
				return;
			}
			$(".tzSelect .selectBox").removeClass('sc_error_empty');
			if ($('#sc_item_' + item_id + ' .miw_preloader').length){ $('#sc_item_' + item_id + ' .miw_preloader').show(); }
			btn_cart.addClass("sc_cart_btn_loading");
			$('.fa', btn_cart).addClass("fa-spin");
			cart_data.variant_id = variant_id;
			/* Хук для разработчиков */
			if (typeof(icms.showcase.beforeAddCart) == 'function'){
				icms.showcase.beforeAddCart($(this), cart_data);
			}
			/* Хук для разработчиков */
			$.post('/showcase/add_to_cart/' + (variant_id ? item_id + 'v' + variant_id : item_id), cart_data, function(result){
				if(!result.error){
					if ($('#sc_item_' + item_id + ' .miw_preloader').length){ $('#sc_item_' + item_id + ' .miw_preloader').fadeOut(800); }
					btn_cart.removeClass("sc_cart_btn_loading");
					btn_cart.addClass("sc_cart_btn_loaded");
					$('.fa', btn_cart).removeClass("fa-spin");
					$('.sc_cart_btn_label', btn_cart).text('В корзине');
					/* Хук для разработчиков */
					if (typeof(icms.showcase.afterAddCart) == 'function'){
						icms.showcase.afterAddCart($(this), result);
					}
					/* Хук для разработчиков */
					icms.showcase.scRefreshCarts(false);
				}
			}, 'json');
		});
		
		$('#item_' + item_id + ' .sc_qty_count input').on('keyup keypress change', function(){
			icms.showcase.scSetQty(false, item_id, false);
		});
		
		$('.sc_variants_selector select.sc_variations_select').on('change', function() {
			icms.showcase.setListVariant($(this).find(':selected'), $(this).data('item_id'));
		});
		
    };
	
	this.scSetQty = function(btn, item_id, is_cart) {
		/* Хук для разработчиков */
		if (typeof(icms.showcase.beforeSetQty) == 'function'){
			icms.showcase.beforeSetQty(btn, item_id, is_cart);
		}
		/* Хук для разработчиков */
		var count = $('#item_' + item_id + ' .sc_qty_count input').length ? parseInt($('#item_' + item_id + ' .sc_qty_count input').val()) : false;
		if (count){
			if ($(btn).hasClass('sc_qty_btn_plus')){
				if ($(btn).data('max') && (count + 1) > parseInt($(btn).data('max'))){
					$('#item_' + item_id + ' .sc_qty_count input').val(parseInt($(btn).data('max')));
				} else {
					$('#item_' + item_id + ' .sc_qty_count input').val(count + 1);
				}
			} else if($(btn).hasClass('sc_qty_btn_minus')){
				if ((count - 1) < 1){
					$('#item_' + item_id + ' .sc_qty_count input').val(1);
				} else {
					$('#item_' + item_id + ' .sc_qty_count input').val(count - 1);
				}
			} else {
				if (parseInt($('.sc_qty_btn_plus').data('max'))){
					if ((count + 1) > parseInt($('.sc_qty_btn_plus').data('max'))){
						$('#item_' + item_id + ' .sc_qty_count input').val(parseInt($('.sc_qty_btn_plus').data('max')));
					}
				}
			}
			if (is_cart){
				icms.showcase.scRefreshCarts({item_id : item_id, qty : $('#item_' + item_id + ' .sc_qty_count input').val()});
			} else {
				var price = $('.is_scPrice span[itemprop="price"]').length ? parseInt($('.is_scPrice span[itemprop="price"]').attr('content')) : false;
				if (price){
					var current_price = $('.is_scPrice').attr('data-current') ? $('.is_scPrice').attr('data-current') : parseInt(price);
					var new_price = (current_price * parseInt($('#item_' + item_id + ' .sc_qty_count input').val()));
					var sub = $('.is_scPrice span[itemprop="price"] sub').length ? $('.is_scPrice span[itemprop="price"] sub')[0].outerHTML : '';
					$('.is_scPrice span[itemprop="price"]').html(toPriceFormat(new_price) + sub).attr('content', new_price).hide().fadeIn(800);
				}
			}
		} else {
			$('#item_' + item_id + ' .sc_qty_count input').val(0);
			return;
		}
		/* Хук для разработчиков */
		if (typeof(icms.showcase.afterSetQty) == 'function'){
			icms.showcase.afterSetQty(btn, item_id, is_cart);
		}
		/* Хук для разработчиков */
	};
	
	this.scRemoveCartItem = function(button, item_id){
		if(!confirm('Удалить запись?')){ return false; }
		button = $(button);
		$('.fa', button).removeClass("fa-close").addClass("fa fa-refresh fa-spin");
		$.post('/showcase/add_to_cart/' + item_id + '/1', false, function(result){
			if(!result.error){
				$('.fa', button).addClass("fa-close").removeClass("fa-refresh fa-spin");
				icms.showcase.scRefreshCarts(false);
			}
		}, 'json');
	};
	
	this.scRefreshCarts = function(data){
		/* Хук для разработчиков */
		if (typeof(icms.showcase.beforeRefreshCart) == 'function'){
			icms.showcase.beforeRefreshCart(data);
		}
		/* Хук для разработчиков */
		$('.wd_sc_cart .wd_sc_cart_loader').removeClass('sc_load_hide');
		$.post('/showcase/refresh_carts/' + (data ? 1 : 0), (data ? $.extend(data, {styles : icms.showcase.cart_styles}) : {styles : icms.showcase.cart_styles}), function(result){
			if(!result.error){
				if (Object.keys(icms.showcase.cart_styles).length && typeof result.html === 'object'){
					$.each(icms.showcase.cart_styles, function( index, value ) {
						if (typeof result.html[index] != "undefined"){
							var wd_sc_cart_list = $('.wd_sc_cart_list', result.html[index]).html();
							$('.sc_style_' + index + ' .wd_sc_cart_list').html(wd_sc_cart_list);
							if ($('h1#sc_cart_title').length && $(result.html[index]).eq(0).is("h1")){
								$('h1#sc_cart_title').html($(result.html[index]).eq(0).html());
							}
							if ($('.sc_cart_counter').length && $('.sc_cart_counter', result.html[index]).length){
								$('.sc_cart_counter').html($('.sc_cart_counter', result.html[index]).text());
							}
						}
					});
				} else {
					$('.wd_sc_cart .wd_sc_cart_list').html(result.html);
				}
				$('.wd_sc_cart .wd_sc_cart_loader').addClass('sc_load_hide');
			}
			/* Хук для разработчиков */
			if (typeof(icms.showcase.afterRefreshCart) == 'function'){
				icms.showcase.afterRefreshCart(data, result);
			}
			/* Хук для разработчиков */
		}, 'json');
	};

	this.imgCenter = function(el, img) {
		if (img.height() > el.height()){
			img.css('height', '100%');
			if (img.width() > el.width()){
				var center = ((img.width() - el.width()) / 2);
				img.css('left', '-' + center + 'px');
			}
		} else {
			var center = ((img.width() - el.width()) / 2);
			img.css('left', '-' + center);
		}
		$('.sc_photo_view').removeClass('is_preload');
	}

	this.bulidListGrid = function(list_grid) {
		var list_height = icms.showcase.list_height;
        if (list_grid.width() > 576){
			if (list_grid.width() > 864){
				list_grid.addClass('sc_three_col');
				$(".miw_photo", list_grid).attr('style', 'width:' + parseInt((list_grid.width() / 3)) + 'px;height:'+list_height+'px;');
			} else {
				list_grid.removeClass('sc_three_col sc_one_col');
				$(".miw_photo", list_grid).attr('style', 'width:' + parseInt((list_grid.width() / 2)) + 'px;height:'+list_height+'px;');
			}
		} else {
			if (list_grid.width() < 500){
				list_grid.addClass('sc_one_col');
				$(".miw_photo", list_grid).attr('style', 'width:' + parseInt((list_grid.width() - 2)) + 'px;height:'+list_height+'px;');
			} else {
				list_grid.removeClass('sc_three_col sc_one_col');
				$(".miw_photo", list_grid).attr('style', 'width:' + parseInt((list_grid.width() / 2)) + 'px;height:'+list_height+'px;');
			}
		}
		$(".miw_photo", list_grid).imgLiquid({
			verticalAlign: icms.showcase.list_pos,
			onItemFinish: function(index, container, img){
				container.parents('.my_default_list_item').find('.miw_preloader').fadeOut(800);
			}
		});
	};
	
	this.setListVariant = function(btn, item_id) {
		btn = $(btn);
		var title = btn.data('title');
		var price = btn.data('price');
		var photo = btn.data('photo');
		var sale = btn.data('sale');
		var url = btn.data('url');
		if (title || price || photo || url){
			$('.showcase_list_grid #sc_item_' + item_id + ' .miw_preloader').show();
			if (title){
				$('.showcase_list_grid #sc_item_' + item_id + ' .miw_description .miw_title a').text(title);
			}
			if (price && !sale){
				$('.showcase_list_grid #sc_item_' + item_id + ' .miw_price_box .sc_old_price').remove();
				$('.showcase_list_grid #sc_item_' + item_id + ' .miw_price_box .miw_price').text(price);
			}
			if (sale){
				$('.showcase_list_grid #sc_item_' + item_id + ' .miw_price_box .sc_old_price').remove();
				$('.showcase_list_grid #sc_item_' + item_id + ' .miw_price_box .miw_price').text(sale);
			}
			if (photo){
				$('.showcase_list_grid #sc_item_' + item_id + ' .miw_photo').css('background-image', 'url(' + photo + ')');
				$('.showcase_list_grid #sc_item_' + item_id + ' .miw_meta_photo').attr('href', photo);
			}
			if (url){
				$('.showcase_list_grid #sc_item_' + item_id + ' .miw_description .miw_title a').attr('href', url);
				$('.showcase_list_grid #sc_item_' + item_id + ' .miw_meta_link').attr('href', url);
			}
			$('.showcase_list_grid #sc_item_' + item_id + ' .miw_preloader').hide();
		}
		if (btn.val() && $('#sc_item_' + item_id + ' .sc_cart_btn').length){
			$('#sc_item_' + item_id + ' .sc_cart_btn').attr('data-variant', btn.val());
		}
	};

	this.setVariant = function(select) {
		/* Хук для разработчиков */
		if (typeof(icms.showcase.beforeSetVariant) == 'function'){
			icms.showcase.beforeSetVariant(select);
		}
		/* Хук для разработчиков */
		var variant_id = parseInt($(select).val());
		if (variant_id > 0){
			$('.sc_item_view_header .sc_item_view_loader').show();
			$.post('/showcase/set_variant/' + variant_id, false, function(result){
				/* Хук для разработчиков */
				if (typeof(icms.showcase.responseSetVariant) == 'function'){
					icms.showcase.responseSetVariant(result);
				}
				/* Хук для разработчиков */
				if(!result.error){
					$('.sc_cart_btn').attr('data-variant', variant_id);
					if (result.sale && parseFloat(result.sale) != 0){
						var sub = $('.is_scPrice span[itemprop="price"] sub').length ? $('.is_scPrice span[itemprop="price"] sub')[0].outerHTML : '';
						$('.is_scPrice span[itemprop="price"]').attr('content', result.sale).html(result.sale_round + sub).hide().fadeIn(800);
						$('.is_scPrice').attr('data-current', result.sale);
						if ($('.is_scPrice .is_scOldPrice').length){
							$('.is_scPrice .is_scOldPrice').text(result.price_round);
						} else {
							$('.is_scPrice').prepend('<s class="is_scOldPrice">' + result.price_round + '</s> ');
						}
					} else {
						var sub = $('.is_scPrice span[itemprop="price"] sub').length ? $('.is_scPrice span[itemprop="price"] sub')[0].outerHTML : '';
						var sub = $('.is_scPrice span[itemprop="price"] sub').length ? $('.is_scPrice span[itemprop="price"] sub')[0].outerHTML : '';
						$('.is_scPrice span[itemprop="price"]').attr('content', result.price).html(result.price_round + sub).hide().fadeIn(800);
						$('.is_scPrice').attr('data-current', result.price);
						if ($('.is_scPrice .is_scOldPrice').length){
							$('.is_scPrice .is_scOldPrice').remove();
						}
					}
					if (parseInt(result.in_stock)){
						$('.sc_inStock_box').html('<i class="fa fa-check-square-o"></i> Есть в наличии (' + result.in_stock + ')').removeClass('scis_not').addClass('scis_yes');
					} else {
						$('.sc_inStock_box').html('<i class="fa fa-warning"></i> Нет в наличии').removeClass('scis_yes').addClass('scis_not');
					}
					$('#sc_goods_artikul b').text(result.artikul).hide().fadeIn(800);
					$('h1, #breadcrumbs ul li:last-child span, head title').text(result.title).hide().fadeIn(800);
					$('.sc_buy_qty .sc_qty_btn_plus').data('max', result.in_stock);
					$('.sc_buy_qty .sc_qty_count input').val(1);
					if (icms.showcase.sync2 && result.photo){
						if ($('#sync2 .slick-slide:not(.slick-cloned) img[src="/upload/' + result.photo.small + '"]').length){
							var current = $('#sync2 .slick-slide:not(.slick-cloned) img[src="/upload/' + result.photo.small + '"]').parents('.item').data('slick-index');
							icms.showcase.sync2.slick('slickGoTo', current);
						} else {
							var new_slide = '<div class="item mgLiquidNoFill imgLiquid" data-big="/upload/' + result.photo.big + '" style="width:100%;height:400px"><div class="sc_gallery_selector" data-src="/upload/' + result.photo.big + '"><i class="fa fa-arrows-alt" aria-hidden="true"></i></div><img data-lazy="/upload/' + result.photo.big + '" /></div>';
							icms.showcase.light_gallery.append(new_slide);
							icms.showcase.light_gallery.data('lightGallery').destroy(true);
							icms.showcase.light_gallery.lightGallery({
								selector: '.item:not(.slick-cloned) .sc_gallery_selector',
								download: false
							});
							icms.showcase.sync1.slick('slickAdd', new_slide);
							icms.showcase.sync2.slick('slickAdd', '<div class="item"><img src="/upload/' + result.photo.small + '" /></div>');
							var current = $('#sync2 .slick-slide:not(.slick-cloned) img[src="/upload/' + result.photo.small + '"]').parents('.item').data('slick-index');
							icms.showcase.sync2.slick('slickGoTo', current);
						}
					}
					insertParam('variant', variant_id);
					$('.sc_item_view_header .sc_item_view_loader').hide();
				}
				/* Хук для разработчиков */
				if (typeof(icms.showcase.afterSetVariant) == 'function'){
					icms.showcase.afterSetVariant(select, result);
				}
				/* Хук для разработчиков */
			}, 'json');
		}
	};
	
	this.setDelivery = function(button, id){
		button = $(button);
		$('.sc_cart_delivery .wd_sclf_checkout').hide();
		$.post('/showcase/set_delivery/' + id, false, function(result){
			if(result.error){
				icms.modal.alert('Неизвестная ошибка', 'ui_error');
			} else {
				button.addClass('sc_activated');
				$('.sc_delivery_checkbox').prop('checked', false);
				$('.sc_delivery_checkbox', button).prop('checked', true);
				$('.sc_cart_delivery .wd_sclf_checkout').show();
			}
		}, 'json');
	};

	this.addTags = function(button, id){
		button = $(button);
		icms.modal.alert('В разработке...');
	};
	
	function insertParam(key, value){
		key = encodeURI(key); value = encodeURI(value);
		var kvp = document.location.search.substr(1).split('&');
		var i=kvp.length; var x; while(i--){
			x = kvp[i].split('=');
			if (x[0]==key){
				x[1] = value;
				kvp[i] = x.join('=');
				break;
			}
		}
		if (i < 0) { kvp[kvp.length] = [key,value].join('='); }
		window.history.replaceState(null, null, '?' + kvp.join('&'));
	}
	
	function toPriceFormat(price){

		if (!icms.showcase.price_round){ return price; }

		var decimal=0;
		var separator=' ';
		var decpoint = '.';
		var format_string = '#';
	 
		var r=parseFloat(price)
	 
		var exp10=Math.pow(10,decimal);
		r=Math.round(r*exp10)/exp10;
	 
		rr=Number(r).toFixed(decimal).toString().split('.');
	 
		b=rr[0].replace(/(\d{1,3}(?=(\d{3})+(?:\.\d|\b)))/g,"\$1"+separator);
	 
		r=(rr[1]?b+ decpoint +rr[1]:b);
		return format_string.replace('#', r);
	}

	return this;

}).call(icms.showcase || {},jQuery);