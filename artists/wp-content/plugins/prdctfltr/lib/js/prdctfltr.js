/*!
 * imagesLoaded PACKAGED v4.1.4
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */

!function(e,t){"function"==typeof define&&define.amd?define("ev-emitter/ev-emitter",t):"object"==typeof module&&module.exports?module.exports=t():e.EvEmitter=t()}("undefined"!=typeof window?window:this,function(){function e(){}var t=e.prototype;return t.on=function(e,t){if(e&&t){var i=this._events=this._events||{},n=i[e]=i[e]||[];return n.indexOf(t)==-1&&n.push(t),this}},t.once=function(e,t){if(e&&t){this.on(e,t);var i=this._onceEvents=this._onceEvents||{},n=i[e]=i[e]||{};return n[t]=!0,this}},t.off=function(e,t){var i=this._events&&this._events[e];if(i&&i.length){var n=i.indexOf(t);return n!=-1&&i.splice(n,1),this}},t.emitEvent=function(e,t){var i=this._events&&this._events[e];if(i&&i.length){i=i.slice(0),t=t||[];for(var n=this._onceEvents&&this._onceEvents[e],o=0;o<i.length;o++){var r=i[o],s=n&&n[r];s&&(this.off(e,r),delete n[r]),r.apply(this,t)}return this}},t.allOff=function(){delete this._events,delete this._onceEvents},e}),function(e,t){"use strict";"function"==typeof define&&define.amd?define(["ev-emitter/ev-emitter"],function(i){return t(e,i)}):"object"==typeof module&&module.exports?module.exports=t(e,require("ev-emitter")):e.imagesLoaded=t(e,e.EvEmitter)}("undefined"!=typeof window?window:this,function(e,t){function i(e,t){for(var i in t)e[i]=t[i];return e}function n(e){if(Array.isArray(e))return e;var t="object"==typeof e&&"number"==typeof e.length;return t?d.call(e):[e]}function o(e,t,r){if(!(this instanceof o))return new o(e,t,r);var s=e;return"string"==typeof e&&(s=document.querySelectorAll(e)),s?(this.elements=n(s),this.options=i({},this.options),"function"==typeof t?r=t:i(this.options,t),r&&this.on("always",r),this.getImages(),h&&(this.jqDeferred=new h.Deferred),void setTimeout(this.check.bind(this))):void a.error("Bad element for imagesLoaded "+(s||e))}function r(e){this.img=e}function s(e,t){this.url=e,this.element=t,this.img=new Image}var h=e.jQuery,a=e.console,d=Array.prototype.slice;o.prototype=Object.create(t.prototype),o.prototype.options={},o.prototype.getImages=function(){this.images=[],this.elements.forEach(this.addElementImages,this)},o.prototype.addElementImages=function(e){"IMG"==e.nodeName&&this.addImage(e),this.options.background===!0&&this.addElementBackgroundImages(e);var t=e.nodeType;if(t&&u[t]){for(var i=e.querySelectorAll("img"),n=0;n<i.length;n++){var o=i[n];this.addImage(o)}if("string"==typeof this.options.background){var r=e.querySelectorAll(this.options.background);for(n=0;n<r.length;n++){var s=r[n];this.addElementBackgroundImages(s)}}}};var u={1:!0,9:!0,11:!0};return o.prototype.addElementBackgroundImages=function(e){var t=getComputedStyle(e);if(t)for(var i=/url\((['"])?(.*?)\1\)/gi,n=i.exec(t.backgroundImage);null!==n;){var o=n&&n[2];o&&this.addBackground(o,e),n=i.exec(t.backgroundImage)}},o.prototype.addImage=function(e){var t=new r(e);this.images.push(t)},o.prototype.addBackground=function(e,t){var i=new s(e,t);this.images.push(i)},o.prototype.check=function(){function e(e,i,n){setTimeout(function(){t.progress(e,i,n)})}var t=this;return this.progressedCount=0,this.hasAnyBroken=!1,this.images.length?void this.images.forEach(function(t){t.once("progress",e),t.check()}):void this.complete()},o.prototype.progress=function(e,t,i){this.progressedCount++,this.hasAnyBroken=this.hasAnyBroken||!e.isLoaded,this.emitEvent("progress",[this,e,t]),this.jqDeferred&&this.jqDeferred.notify&&this.jqDeferred.notify(this,e),this.progressedCount==this.images.length&&this.complete(),this.options.debug&&a&&a.log("progress: "+i,e,t)},o.prototype.complete=function(){var e=this.hasAnyBroken?"fail":"done";if(this.isComplete=!0,this.emitEvent(e,[this]),this.emitEvent("always",[this]),this.jqDeferred){var t=this.hasAnyBroken?"reject":"resolve";this.jqDeferred[t](this)}},r.prototype=Object.create(t.prototype),r.prototype.check=function(){var e=this.getIsImageComplete();return e?void this.confirm(0!==this.img.naturalWidth,"naturalWidth"):(this.proxyImage=new Image,this.proxyImage.addEventListener("load",this),this.proxyImage.addEventListener("error",this),this.img.addEventListener("load",this),this.img.addEventListener("error",this),void(this.proxyImage.src=this.img.src))},r.prototype.getIsImageComplete=function(){return this.img.complete&&this.img.naturalWidth},r.prototype.confirm=function(e,t){this.isLoaded=e,this.emitEvent("progress",[this,this.img,t])},r.prototype.handleEvent=function(e){var t="on"+e.type;this[t]&&this[t](e)},r.prototype.onload=function(){this.confirm(!0,"onload"),this.unbindEvents()},r.prototype.onerror=function(){this.confirm(!1,"onerror"),this.unbindEvents()},r.prototype.unbindEvents=function(){this.proxyImage.removeEventListener("load",this),this.proxyImage.removeEventListener("error",this),this.img.removeEventListener("load",this),this.img.removeEventListener("error",this)},s.prototype=Object.create(r.prototype),s.prototype.check=function(){this.img.addEventListener("load",this),this.img.addEventListener("error",this),this.img.src=this.url;var e=this.getIsImageComplete();e&&(this.confirm(0!==this.img.naturalWidth,"naturalWidth"),this.unbindEvents())},s.prototype.unbindEvents=function(){this.img.removeEventListener("load",this),this.img.removeEventListener("error",this)},s.prototype.confirm=function(e,t){this.isLoaded=e,this.emitEvent("progress",[this,this.element,t])},o.makeJQueryPlugin=function(t){t=t||e.jQuery,t&&(h=t,h.fn.imagesLoaded=function(e,t){var i=new o(this,e,t);return i.jqDeferred.promise(h(this))})},o.makeJQueryPlugin(),o});

(function($){
"use strict";

	function prdctfltr_sort_classes() {
		if ( prdctfltr.ajax_class == '' ) {
			prdctfltr.ajax_class = '.products';
		}
		if ( prdctfltr.ajax_category_class == '' ) {
			prdctfltr.ajax_category_class = '.product-category';
		}
		if ( prdctfltr.ajax_product_class == '' ) {
			prdctfltr.ajax_product_class = '.type-product';
		}
		if ( prdctfltr.ajax_pagination_class == '' ) {
			prdctfltr.ajax_pagination_class = '.woocommerce-pagination';
		}
		if ( prdctfltr.ajax_count_class == '' ) {
			prdctfltr.ajax_count_class = '.woocommerce-result-count';
		}
		if ( prdctfltr.ajax_orderby_class == '' ) {
			prdctfltr.ajax_orderby_class = '.woocommerce-ordering';
		}
	}
	prdctfltr_sort_classes();

	function mobile() {
		$('.prdctfltr_mobile').each( function() {
			$('head').append('<style>@media screen and (min-width: '+$(this).attr('data-mobile')+'px) {.prdctfltr_wc[data-id="'+$(this).prev().attr('data-id')+'"] {display:block;}.prdctfltr_wc[data-id="'+$(this).attr('data-id')+'"] {display:none;}}@media screen and (max-width: '+$(this).attr('data-mobile')+'px) {.prdctfltr_wc[data-id="'+$(this).prev().attr('data-id')+'"] {display:none;}.prdctfltr_wc[data-id="'+$(this).attr('data-id')+'"] {display:block;}}</style>');
		});
	}
	mobile();

	var pf_singlesc = false;
	if ( $('.prdctfltr_sc_products.prdctfltr_ajax '+prdctfltr.ajax_class).length == 1 && $('.prdctfltr_wc:not(.prdctfltr_step_filter)').length > 0 ) {
		$('body').addClass('prdctfltr-sc');
		pf_singlesc = 1;
	}
	else {
		prdctfltr.active_sc = '';
	}

	var pf_failsafe = false;
	function ajax_failsafe() {
		if ( prdctfltr.ajax_failsafe.length == 0 ) {
			return false;
		}
		if ( $('.prdctfltr_sc_products').length > 0 ) {
			return false;
		}
		if ( $('body').hasClass('prdctfltr-ajax') ) {
			pf_failsafe = false;
			if( $.inArray('wrapper', prdctfltr.ajax_failsafe) !== -1 ) {
				if ( $(prdctfltr.ajax_class).length < 1 ) {
					pf_failsafe = true;
				}
			}
			if( $.inArray('product', prdctfltr.ajax_failsafe) !== -1 ) {
				if ( $(prdctfltr.ajax_class+' '+prdctfltr.ajax_product_class).length < 1 && $(prdctfltr.ajax_class+' '+prdctfltr.ajax_category_class).length < 1 ) {
					pf_failsafe = true;
				}
			}

			if( $.inArray('pagination', prdctfltr.ajax_failsafe) !== -1 ) {
				if ( $(prdctfltr.ajax_pagination_class).length < 1 ) {
					pf_failsafe = true;
				}
			}

			if ( pf_failsafe === true ) {
				console.log('PF: AJAX Failsafe active.');
			}
		}
	}
	ajax_failsafe();

	prdctfltr.clearall = ( $.isArray(prdctfltr.clearall) === true ? prdctfltr.clearall : false );

	var archiveAjax = false;
	if ( $('body').hasClass('prdctfltr-ajax') && pf_failsafe === false ) {
		archiveAjax = true;
	}

	if ( archiveAjax === true || pf_singlesc ) {
		var pageFilters = {};
		var makeHistory = {};

		$('.prdctfltr_wc').each( function() {
			pageFilters[$(this).attr('data-id')] = $("<div />").append($(this).clone()).html();
		});

		if ( prdctfltr.rangefilters ) {
			pageFilters.ranges = prdctfltr.rangefilters;
		}

		pageFilters.products = $("<div />").append($(prdctfltr.ajax_class).clone()).html();
		pageFilters.pagination = $("<div />").append($(prdctfltr.ajax_pagination_class).clone()).html();
		pageFilters.count = $("<div />").append($(prdctfltr.ajax_count_class).clone()).html();
		pageFilters.orderby = $("<div />").append($(prdctfltr.ajax_orderby_class).clone()).html();
		pageFilters.title = $("<div />").append($('h1.page-title').clone()).html();
		pageFilters.desc = $("<div />").append($('.term-description:first, .page-description:first').clone()).html();
		pageFilters.loop_start = $('<ul class="products">');
		//pageFilters.loop_end = '</ul>';

		var historyId = guid();

		makeHistory[historyId] = pageFilters;
		history.replaceState({filters:historyId, archiveAjax:true, shortcodeAjax:false}, document.title, '');
	}

	function guid() {
		function s4() {
			return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
		}
		return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
	}

	var ajaxActive = false;

	$.expr[':'].Contains = function(a,i,m){
		return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
	};

	String.prototype.getValueByKey = function (k) {
		var p = new RegExp('\\b' + k + '\\b', 'gi');
		return this.search(p) != -1 ? decodeURIComponent(this.substr(this.search(p) + k.length + 1).substr(0, this.substr(this.search(p) + k.length + 1).search(/(&|;|$)/))) : "";
	};

	var startInit = false;
	function init_ranges() {

		$.each( prdctfltr.rangefilters, function(i, obj3) {

			var currTax = $('#'+i).attr('data-filter');

			if ( currTax !== 'price' ) {
				obj3.prettify_enabled = true;
				
				obj3.prettify = function (num) {
					return obj3.prettyValues[num];
				};
			}
			obj3.onChange = function (data) {
				startInit = true;
			};
			obj3.onFinish = function (data) {
				if ( startInit === true ) {
					startInit = false;

					//var currTax = $('#'+i).attr('data-filter');
					if ( data.min == data.from && data.max == data.to ) {

						var ourObj = prdctfltr_get_obj_580($('#'+i).closest('.prdctfltr_wc'));

						$.each( ourObj, function(i, obj) {

							$(obj).find('input[name="rng_min_'+currTax+'"]').val('');
							$(obj).find('input[name="rng_max_'+currTax+'"]').val('');
							$(obj).find('.prdctfltr_range input[data-filter="'+currTax+'"]:not(#'+i+')').each(function() {
								var range = $(this).data("ionRangeSlider");
								range.update({
									from: data.min,
									to: data.max
								});
							});

						});

						$('#'+i).closest('.prdctfltr_filter').find('input[name="rng_max_'+currTax+'"]:first').trigger('change');


					}
					else {

						var minVal = ( currTax == 'price' ?
							data.from :
							$(obj3.prettyValues[data.from]).text() );

						var maxVal = ( currTax == 'price' ?
							data.to :
							$(obj3.prettyValues[data.to]).text() );

						var ourObj = prdctfltr_get_obj_580($('#'+i).closest('.prdctfltr_wc'));

						$.each( ourObj, function(i, obj) {

							$(obj).find('input[name="rng_min_'+currTax+'"]').val(minVal);
							$(obj).find('input[name="rng_max_'+currTax+'"]').val(maxVal);

							$(obj).find('.prdctfltr_range input[data-filter="'+currTax+'"]:not(#'+i+')').each(function() {
								var range = $(this).data("ionRangeSlider");

								range.update({
									from: data.from,
									to: data.to
								});
							});

						});

						$('#'+i).closest('.prdctfltr_filter').find('input[name="rng_max_'+currTax+'"]:first').trigger('change');

					}

					var curr_filter = $('#'+i).closest('.prdctfltr_wc');
					if ( curr_filter.hasClass('prdctfltr_tabbed_selection') && curr_filter.hasClass('prdctfltr_click') ) {
						curr_filter.find('.prdctfltr_filter').each( function() {
							if ( $(this).find('input[type="hidden"]:first').length > 0 && $(this).find('input[type="hidden"]:first').val() !== '' ) {
								if ( !$(this).hasClass('prdctfltr_has_selection') ) {
									$(this).addClass('prdctfltr_has_selection');
								}
								
							}
							else {
								if ( $(this).hasClass('prdctfltr_has_selection') ) {
									$(this).removeClass('prdctfltr_has_selection');
								}
							}
						});
					}

					var ourObj = prdctfltr_get_obj_580(curr_filter);

					$.each( ourObj, function(i, obj) {
						var pfObj = $(obj).find('.prdctfltr_filter[data-filter="rng_'+currTax+'"]');
						pfObj.each( function(){
							check_selection_boxes($(this),'look');
						});
					});

				}
			};

			$('#'+i).ionRangeSlider(obj3);
			ranges[i] = $('#'+i).data('ionRangeSlider');

		});
	}
	var ranges = {};
	init_ranges();

	function reorder_selected(curr) {
		curr = ( curr == null ? $('.prdctfltr_wc') : curr );
		if( curr.find('label.prdctfltr_active').length == 0 ) {
			return;
		}
		curr.each( function() {
			var currEl = $(this);
			if ( $(this).hasClass('prdctfltr_selected_reorder') ) {
				currEl.find('.prdctfltr_filter.prdctfltr_attributes:not(.prdctfltr_hierarchy) .prdctfltr_checkboxes, .prdctfltr_filter.prdctfltr_vendor .prdctfltr_checkboxes, .prdctfltr_filter.prdctfltr_byprice .prdctfltr_checkboxes, .prdctfltr_filter.prdctfltr_orderby .prdctfltr_checkboxes').each( function() {
					var checkboxes = $(this);
					if ( checkboxes.find('label.prdctfltr_active').length > 0 ) {
						$(checkboxes.find('label.prdctfltr_active').get().reverse()).each( function () {
							var addThis = $(this);
							$(this).remove();
							if ( checkboxes.find('label.prdctfltr_ft_none:first').length>0 ) {
								checkboxes.find('label.prdctfltr_ft_none:first').after(addThis);
							}
							else {
								checkboxes.prepend(addThis);
							}
						});
					}
				});
			}
		});
	}
	reorder_selected();

	function reorder_adoptive(curr) {

		curr = ( curr == null ? $('.prdctfltr_wc') : curr );

		curr.each( function() {

			var currEl = $(this);

			if ( $(this).hasClass('prdctfltr_adoptive_reorder') ) {
				currEl.find('.prdctfltr_adoptive').each( function() {
					var filter = $(this);
					if ( filter.find('.pf_adoptive_hide').length > 0 ) {
						var checkboxes = filter.find('.prdctfltr_checkboxes');
						filter.find('.pf_adoptive_hide').each( function() {
							var addThis = $(this);
							$(this).remove();
							checkboxes.append(addThis);
						});
					}
				});
			}

		});

	}
	reorder_adoptive();

	$(document).on('click', '.pf_more:not(.pf_activated)', function() {
		var filter = $(this).closest('.prdctfltr_attributes, .prdctfltr_meta');
		var checkboxes = filter.find('.prdctfltr_checkboxes');
		var curr = filter.closest('.prdctfltr_wc');

		if ( curr.hasClass('pf_adptv_default') ) {
			var searchIn = '> label:not(.pf_adoptive_hide)';
		}
		else {
			var searchIn = '> label';
		}

		var displayType = checkboxes.find(searchIn+':first').css('display');

		checkboxes.find(searchIn).attr('style', 'display:'+displayType+' !important');
		checkboxes.find('.pf_more').html('<span>'+prdctfltr.localization.show_less+'</span>');
		checkboxes.find('.pf_more').addClass('pf_activated');

		if ( filter.closest('.prdctfltr_wc').hasClass('pf_mod_masonry') ) {
			filter.closest('.prdctfltr_filter_inner').isotope('layout');
		}
	});

	$(document).on('click', '.pf_more.pf_activated', function() {
		var filter = $(this).closest('.prdctfltr_attributes, .prdctfltr_meta');
		var checkboxes = filter.find('.prdctfltr_checkboxes');
		var curr = filter.closest('.prdctfltr_wc');
		if ( curr.hasClass('pf_adptv_default') ) {
			var searchIn = '> label:not(.pf_adoptive_hide)';
		}
		else {
			var searchIn = '> label';
		}
		checkboxes.each(function(){
			var max = parseInt(filter.attr('data-limit'));
			if (max !== 0 && $(this).find(searchIn).length > max+1) {

				$(this).find(searchIn+':gt('+max+')').attr('style', 'display:none !important');
				$(this).find('.pf_more').html('<span>'+prdctfltr.localization.show_more+'</span>').removeClass('pf_activated');

				if ( filter.closest('.prdctfltr_wc').hasClass('pf_mod_masonry') ) {
					filter.closest('.prdctfltr_filter_inner').isotope('layout');
				}
			}
		});
	});

	function set_select_index(curr) {

		curr = ( curr == null ? $('.prdctfltr_woocommerce') : curr );

		curr.each( function() {

			var curr_el = $(this);

			var selects = curr_el.find('.pf_select .prdctfltr_filter');
			if ( selects.length > 0 ) {
				var zIndex = selects.length;
				selects.each( function() {
					$(this).css({'z-index':zIndex});
					zIndex--;
				});
			}
		});

	}
	set_select_index();

	function init_search(curr) {

		curr = ( curr == null ? $('.prdctfltr_wc') : curr );

		curr.each( function() {

			var curr_el = $(this);

			curr_el.find('input.pf_search').each( function() {
				/*if ( curr_el.hasClass('prdctfltr_click_filter') ) {*/
					$(this).keyup( function () {
						if ($(this).next().is(':hidden')) {
							$(this).next().show();
						}
						if ($(this).val()==''){
							//$(this).next().hide();
						}
					});
				/*}*/
			});
		});
	}
	init_search();

	$(document).on( 'keydown', '.pf_search', function() {
		if(event.which==13) {
			$(this).next().trigger('click');
			return false;
		}
	});

	$(document).on( 'click', '.pf_search_trigger', function() {
		var wc = $(this).closest('.prdctfltr_wc');

		if ( $(this).prev().val() == '' ) {
			$('.prdctfltr_filter input[name="s"], .prdctfltr_add_inputs input[name="s"]').remove();
		}


		if ( !wc.hasClass('prdctfltr_click_filter') ) {
			wc.find('.prdctfltr_woocommerce_filter_submit').trigger('click');
		}
		else {
			var obj = wc.find('.prdctfltr_woocommerce_ordering');
			prdctfltr_respond_550(obj);
		}

		return false;
	});



	function is_touch_device() {
		return 'ontouchstart' in window || navigator.maxTouchPoints;
	}


	function prdctfltr_init_tooltips(curr) {
		if (is_touch_device()!==true) {
			curr = ( curr == null ? $('.prdctfltr_woocommerce') : curr );

			curr.each( function() {
				var curr_el = $(this);
				var fixedTooltips = false;
				if (curr_el.hasClass('prdctfltr_maxheight')) {
					fixedTooltips = true;
				}

				var $pf_tooltips = curr_el.find('.prdctfltr_filter.pf_attr_img label, .prdctfltr_terms_customized:not(.prdctfltr_terms_customized_select) label');

				$pf_tooltips
				.on('mouseenter', function() {
					var $this = $(this);
					var position = getCoords($this);

					if ($this.prop('hoverTimeout')) {
						$this.prop('hoverTimeout', clearTimeout($this.prop('hoverTimeout')));
					}

					$this.prop('hoverIntent', setTimeout(function() {
						if ( fixedTooltips===true ) {
							var toolTip = $this.find('.prdctfltr_tooltip');

							toolTip.css({'top':position.top-$this.innerHeight()/2-15+'px', 'left':position.left-1+$this.innerWidth()/2+'px', 'height':$this.height()});
							$('body').append('<div class="pf_fixtooltip">'+$('<div></div>').append(toolTip.clone()).html()+'</div>');
							setTimeout(function() {
								$('body > .pf_fixtooltip:last').addClass('prdctfltr_hover');
							},10);
							
						}
						else {
							$this.addClass('prdctfltr_hover');
						}

					}, 250));
				})
				.on('mouseleave', function() {
					var $this = $(this);

					if ($this.prop('hoverIntent')) {
						$this.prop('hoverIntent', clearTimeout($this.prop('hoverIntent')));
					}

					$this.prop('hoverTimeout', setTimeout(function() {
						if ( fixedTooltips===true ) {
							$('body > .prdctfltr_hover:first').removeClass('prdctfltr_hover').addClass('prdctfltr_removeme');
							setTimeout(function() {
								$('body > .prdctfltr_removeme:first').remove();
								$this.find('.prdctfltr_tooltip').removeAttr('style');
							},250);
						}
						else {
							$this.removeClass('prdctfltr_hover');
						}
					}, 250));
				});
			});
		}
	}
	prdctfltr_init_tooltips();

	function getCoords( elem ) {
		var box = elem[0].getBoundingClientRect();

		var body = document.body;
		var docEl = document.documentElement;

		var scrollTop = window.pageYOffset || docEl.scrollTop || body.scrollTop;
		var scrollLeft = window.pageXOffset || docEl.scrollLeft || body.scrollLeft;

		var clientTop = docEl.clientTop || body.clientTop || 0;
		var clientLeft = docEl.clientLeft || body.clientLeft || 0;

		var top  = box.top +  scrollTop - clientTop;
		var left = box.left + scrollLeft - clientLeft;

		return { top: Math.round(top), left: Math.round(left) };
	}

	function reorder_limit(curr) {

		curr = ( typeof curr == 'undefined' ? $('.prdctfltr_wc') : curr );

		curr.each( function() {

			var curr_el = $(this);

			if ( curr_el.hasClass('pf_adptv_default') ) {
				var searchIn = '> label:not(.pf_adoptive_hide)';
			}
			else {
				var searchIn = '> label';
			}

			curr_el.find('.prdctfltr_attributes, .prdctfltr_meta').each( function() {
				var filter = $(this);
				var checkboxes = filter.find('.prdctfltr_checkboxes');
				checkboxes.each(function(){
					var max = parseInt(filter.attr('data-limit'));
					if (max != 0 && $(this).find(searchIn).length > max+1) {
						$(this).find(searchIn+':gt('+max+')').attr('style', 'display:none !important').end().append($('<div class="pf_more"><span>'+prdctfltr.localization.show_more+'</span></div>'));
					}
				});
			});
		});

	}
	reorder_limit();

	function prdctfltr_init_scroll(curr) {

		curr = ( curr == null ? $('.prdctfltr_wc') : curr );
		var wrap = curr.find('.prdctfltr_filter_wrapper');

		if ( curr.hasClass('pf_mod_row') && curr.hasClass('prdctfltr_scroll_default') ) {
			if ( curr.find('.prdctfltr_filter').length > parseInt( wrap.attr('data-columns'), 10 ) ) {
				wrap.css('overflow-x', 'scroll');
			}
		}

		if ( curr.hasClass('prdctfltr_scroll_active') && curr.hasClass('prdctfltr_maxheight') ) {

			var wrapper = curr.find('.prdctfltr_filter:not(.prdctfltr_range,.prdctfltr_search) .prdctfltr_add_scroll');

			wrapper.mCustomScrollbar({
				axis:'y',
				scrollInertia:550,
				autoExpandScrollbar:true,
				advanced:{
					updateOnBrowserResize:true,
					updateOnContentResize:true
				}
			});

			if ( !curr.hasClass('prdctfltr_wc_widget') && curr.hasClass('pf_mod_row') && ( curr.find('.prdctfltr_checkboxes').length > $('.prdctfltr_filter_wrapper:first').attr('data-columns') ) ) {

				if ( curr.hasClass('prdctfltr_slide') ) {
					curr.find('.prdctfltr_woocommerce_ordering').show();
				}

				var curr_scroll_column = curr.find('.prdctfltr_filter:first').outerWidth();
				var curr_columns = curr.find('.prdctfltr_filter').length;

				curr.find('.prdctfltr_filter_inner').css('width', curr_columns*curr_scroll_column);
				curr.find('.prdctfltr_filter').css('width', curr_scroll_column);
				
				wrap.mCustomScrollbar({
					axis:'x',
					scrollInertia:550,
					scrollbarPosition:'outside',
					autoExpandScrollbar:true,
					advanced:{
						updateOnBrowserResize:true,
						updateOnContentResize:false
					}
				});

				if ( curr.hasClass('prdctfltr_slide') ) {
					curr.find('.prdctfltr_woocommerce_ordering').hide();
				}

			}

			if ( $('.prdctfltr-widget').length == 0 || $('.prdctfltr-widget .prdctfltr_error').length == 1 ) {
				curr.find('.prdctfltr_slide .prdctfltr_woocommerce_ordering').hide();
			}

		}
/*		else if ( curr.hasClass('prdctfltr_scroll_default') && curr.hasClass('prdctfltr_maxheight') ) {
		}
		else {
		}*/

	}

	function prdctfltr_cats_mode_700(curr) {

		curr = ( curr == null ? $('.prdctfltr_wc') : curr );

		curr.each(function(i,obj) {

			obj = $(obj);
			var checkFilters = obj.find('.prdctfltr_attributes');

			checkFilters.each(function(){

				var mode = false;

				if ( $(this).hasClass('prdctfltr_drill') ) {
					mode = 'drill';
				}
				if ( $(this).hasClass('prdctfltr_drillback') ) {
					mode = 'drillback';
				}
				if ( $(this).hasClass('prdctfltr_subonly') ) {
					mode = 'subonly';
				}
				if ( $(this).hasClass('prdctfltr_subonlyback') ) {
					mode = 'subonlyback';
				}
				if ( mode === false ) {
					return true;
				}

				var doIt = true;
				var checkCheckboxes = $(this).find('.prdctfltr_checkboxes');

				if ( mode == 'subonly' || mode == 'subonlyback' ) {
					if ( checkCheckboxes.find('label.prdctfltr_active').length > 1 ) {
						if ( checkCheckboxes.find('> label.prdctfltr_active').length > 1 ) {
							doIt = false;
						}
						var checkParents = '';
						checkCheckboxes.find('label.prdctfltr_active input[type="checkbox"]').each( function() {
							if ( checkParents == '' ) {
								checkParents = ( $(this).attr('data-parent') ? $(this).attr('data-parent') : '%toplevel' );
							}
							else {
								if ( $(this).attr('data-parent') !== checkParents ) {
									doIt = false;
								}
							}
						});

					}
				}


				if ( doIt === false ) {
					return;
				}

				var ourEl = checkCheckboxes.find('label.prdctfltr_active');

				if ( ourEl.length == 0 ) {
					if ( mode == 'drill' || mode == 'drillback' ) {
						checkCheckboxes.find('> .prdctfltr_sub').remove();
					}
				}
				else {
					ourEl.each( function() {

						if ( $(this).next().is('.prdctfltr_sub' ) ) {
							var subParent = $(this).next();
						}
						else {
							var subParent = $(this).closest('.prdctfltr_sub');
						}

						if ( subParent.length == 0 ) {
							if ( mode == 'drill' || mode == 'drillback' ) {
								checkCheckboxes.find('> .prdctfltr_sub').remove();
							}
						}
						else {

							if ( mode == 'drill' || mode == 'drillback' ) {
								subParent.find('.prdctfltr_sub').remove();
							}

							var subParentCon = $('<div></div>').append(subParent.clone()).html();
							if ( mode.indexOf('back') !== -1 && subParent.prev().is('label') ) {
								subParentCon += $('<div></div>').append(subParent.prev().addClass('prdctfltr_hiddenparent').clone()).html();
							}
						}

						if ( typeof subParentCon != 'undefined' ) {
							checkCheckboxes.empty();
							checkCheckboxes.append(subParentCon);
						}

					});

				}

			});

		});

	}

	function get_category_mode(setView) {

		if ( typeof setView == 'undefined' ) {
			prdctfltr_cats_mode_700();
		}
		else {
			prdctfltr_cats_mode_700(setView);
		}

	}
	get_category_mode();

	function prdctfltr_added_check(curr) {

		curr = ( curr == null ? $('.prdctfltr_wc:visible') : curr );

		curr.each(function(){
			var adds = {};
			var obj = $(this);
			//var currLength = obj.find('.prdctfltr_attributes').length;
			obj.find('.prdctfltr_attributes').each( function() {

				var attribute = $(this);
				var valOf = attribute.find('input[type="hidden"]:first');
				var makeVal = valOf.val();

				if ( typeof makeVal !== 'undefined' && makeVal !== '' ) {

					var vals = [];

					if ( makeVal.indexOf(',') > 0 ) {
						vals = makeVal.split(',');
					}
					else if ( makeVal.indexOf('+') > 0 ) {
						vals = makeVal.split('+');
					}
					else {
						vals[0] = makeVal;
					}

					var filter = $(this);

					var lenght = vals.length;

					$.each(vals, function(i, val23) {

						if ( curr.find('input[type="checkbox"][value="'+val23+'"]').length == 0 ) {

							var dataFilter = filter.attr('data-filter');

							if ( typeof adds[dataFilter] == 'undefined' ) {
								adds[dataFilter] = [];
							}
							if( $.inArray(val23, adds[dataFilter]) == -1 ) {
								adds[dataFilter].push(val23);
								valOf.val('');
							}
							obj.each( function() {
								var wrap = $(this);
									wrap.find('.prdctfltr_add_inputs').append('<input name="'+dataFilter+'" value="'+makeVal+'" class="pf_added_input" />');
							} );
						}

					});
				}

			});
		});
	}
	$(document).ready( function() {
		prdctfltr_added_check();
	});

	function prdctfltr_show_opened_cats(curr) {

		curr = ( curr == null ? $('.prdctfltr_woocommerce') : curr );

		curr.find('label.prdctfltr_active').each( function() {
			$(this).next().show();
			$(this).parents('.prdctfltr_sub').each( function() {
				$(this).show();
				if ( !$(this).prev().hasClass('prdctfltr_clicked') ) {
					$(this).prev().addClass('prdctfltr_clicked');
				}
			});
		});

	}

	function prdctfltr_all_cats(curr) {

		curr = ( curr == null ? $('.prdctfltr_wc') : curr );
		var searchIn = curr.is('prdctfltr_wc') ? '.prdctfltr_filter.prdctfltr_attributes.prdctfltr_expand_parents .prdctfltr_sub' : '.prdctfltr_expand_parents .prdctfltr_sub';

		curr.find(searchIn).each( function() {
			var curr = $(this);
			if ( !curr.is(':visible') ) {
				curr.show();
				if ( !curr.prev().hasClass('prdctfltr_clicked') ) {
					curr.prev().addClass('prdctfltr_clicked');
				}
			}
		});

	}

	function prdctfltr_make_clears(curr) {

		curr = ( curr == null ? $('.prdctfltr_wc') : curr );

		var clearActive = false;
		var currEls = curr.find('.prdctfltr_filter label.prdctfltr_active');
		var currElLength = currEls.length;

		var rangeEl = curr.find('input[name^="rng_m"]').filter(function() { return this.value !== ''; });

		var otherEl = curr.find('.prdctfltr_add_inputs input.pf_added_orderby');

		var btnEl = curr.find('.prdctfltr_buttons label.prdctfltr_active');

		if ( rangeEl.length > 0 ) {
			curr.each( function() {
				if ( !$(this).hasClass('pf_remove_clearall') ) {
					var currPf = $(this);
					currPf.find('.prdctfltr_buttons').append('<span class="prdctfltr_reset"><label><input name="reset_filter" type="checkbox" /><span>'+prdctfltr.localization.clearall+'</span></label></span>');
				}
			});
		}
		else if ( currElLength>0 ) {
			currEls.each( function() {

				var currEl = $(this);
				var currElPrnt = currEl.closest('.prdctfltr_filter');
				var currElFilter = currElPrnt.attr('data-filter');

				if ( prdctfltr.clearall[0] != null) {
					if ( $.inArray( currElFilter, prdctfltr.clearall ) > -1 ) {
						
					}
					else {
						clearActive = true;
					}
				}
				else {
					clearActive = true;
				}

				if ( !--currElLength ) {
					if ( clearActive === true ) {
						curr.each( function() {
							if ( !$(this).hasClass('pf_remove_clearall') ) {
								var currPf = $(this);
								currPf.find('.prdctfltr_buttons').append('<span class="prdctfltr_reset"><label><input name="reset_filter" type="checkbox" /><span>'+prdctfltr.localization.clearall+'</span></label></span>');
							}
						});
					}
				}

			});
		}
		else if ( btnEl.length>0 ) {
			curr.each( function() {
				if ( !$(this).hasClass('pf_remove_clearall') ) {
					var currPf = $(this);
					currPf.find('.prdctfltr_buttons').append('<span class="prdctfltr_reset"><label><input name="reset_filter" type="checkbox" /><span>'+prdctfltr.localization.clearall+'</span></label></span>');
				}
			});
		}
		else if ( otherEl.length>0 ) {
			curr.each( function() {
				if ( !$(this).hasClass('pf_remove_clearall') ) {
					var currPf = $(this);
					currPf.find('.prdctfltr_buttons').append('<span class="prdctfltr_reset"><label><input name="reset_filter" type="checkbox" /><span>'+prdctfltr.localization.clearall+'</span></label></span>');
				}
			});
		}
	}

	function prdctfltr_submit_form(curr_filter) {

		if ( curr_filter.hasClass('prdctfltr_click_filter') || $('.prdctfltr_wc input[name="reset_filter"]:checked').length > 0 ) {
			prdctfltr_respond_550( curr_filter.find('form') );
		}

	}

	$('.prdctfltr_wc').each( function() {

		var curr = $(this);


prdctfltr_filter_terms_init(curr);

		prdctfltr_init_scroll(curr);

		if ( curr.find('.prdctfltr_filter.prdctfltr_attributes.prdctfltr_expand_parents').length > 0 ) {
			prdctfltr_all_cats(curr);
		}
		prdctfltr_show_opened_cats(curr);

		if ( curr.hasClass('pf_mod_masonry') ) {
			curr.find('.prdctfltr_filter_inner').isotope({
				resizable: false,
				masonry: { }
			});
			if ( !curr.hasClass('prdctfltr_always_visible') ) {
				curr.find('.prdctfltr_woocommerce_ordering').hide();
			}
		}

		if ( curr.attr('class').indexOf('pf_sidebar_css') > 0 ) {
			if ( curr.hasClass('pf_sidebar_css_right') ) {
				$('body').css('right', '0px');
			}
			else {
				$('body').css('left', '0px');
			}
			if ( !$('body').hasClass('wc-prdctfltr-active-overlay') ) {
				$('body').addClass('wc-prdctfltr-active-overlay');
			}
		}

		if ( curr.hasClass('prdctfltr_step_filter') ) {
			var checkStep = curr.find('.prdctfltr_woocommerce_filter_submit');
			if ( curr.find('.prdctfltr_woocommerce_filter_submit').length>0 ) {
				curr.find('.prdctfltr_woocommerce_filter_submit').remove();
			}
			curr.find('.prdctfltr_buttons').prepend('<a class="button prdctfltr_woocommerce_filter_submit pf_stopajax" href="#">'+(prdctfltr.js_filters[curr.attr('data-id')].button_text==''?prdctfltr.localization.getproducts:prdctfltr.js_filters[curr.attr('data-id')].button_text)+'</a>');
			curr.closest('.prdctfltr_sc').addClass('prdctfltr_sc_step_filter');
		}

		if ( $(this).attr('data-loader') !== 'none' &&  $(this).attr('data-loader').substr(0, 4) !== 'css-' ) {
			pf_preload_image(prdctfltr.url+'lib/images/svg-loaders/'+$(this).attr('data-loader')+'.svg');
		}

		check_selection_boxes_wrapper(curr);
		prdctfltr_make_clears(curr);

	});

	function pf_preload_image(url) {
		var img = new Image();
		img.src = url;
	}

	$(document).on( 'change', 'input[name^="rng_"]', function() {
		var curr = $(this).closest('.prdctfltr_woocommerce');

		if ( curr.hasClass('prdctfltr_click_filter') ) {
			prdctfltr_respond_550(curr.find('.prdctfltr_woocommerce_ordering'));
		}
	});

	var stopAjax = false;
	$(document).on('click', '.prdctfltr_woocommerce_filter_submit', function() {

		if ( $(this).hasClass('pf_stopajax') ) {
			stopAjax = true;
		}

		var curr = $(this).closest('.prdctfltr_woocommerce_ordering');

		prdctfltr_respond_550(curr);

		return false;

	});

	$(document).on('click', '.prdctfltr_woocommerce_filter:not(.pf_ajax_loading)', function() {

		var curr_filter = $(this).closest('.prdctfltr_woocommerce');

		if (curr_filter.hasClass('pf_mod_masonry') && curr_filter.find('.prdctfltr_woocommerce_ordering:hidden').length > 0 ) {
			if (curr_filter.hasClass('prdctfltr_active')===false) {
				var curr_check = curr_filter.find('.prdctfltr_woocommerce_ordering');
				curr_check.show().find('.prdctfltr_filter_inner').isotope('layout');
				curr_check.hide();
			}
		}
		if ( !curr_filter.hasClass('prdctfltr_always_visible') ) {
			var curr = $(this).closest('.prdctfltr_woocommerce').find('.prdctfltr_woocommerce_ordering');

			if( $(this).hasClass('prdctfltr_active') ) {
				if ( curr_filter.attr('class').indexOf( 'pf_sidebar' ) == -1 ) {
					if ( curr_filter.hasClass( 'pf_fullscreen' ) ) {
						curr.stop(true,true).fadeOut(200, function() {
							curr.find('.prdctfltr_close_sidebar').remove();
						});
					}
					else {
						if ( !curr_filter.hasClass('prdctfltr_wc_widget') &&  !curr_filter.hasClass('prdctfltr_always_visible') ) {
							curr.stop(true,true).slideUp(200);
						}
					}
				}
				else {
					curr.stop(true,true).fadeOut(200, function() {
						curr.find('.prdctfltr_close_sidebar').remove();
					});
					if ( curr_filter.attr('class').indexOf( 'pf_sidebar_css' ) > 0 ) {
						if ( curr_filter.hasClass('pf_sidebar_css_right') ) {
							$('body').css({'right':'0px','bottom':'auto','top':'auto','left':'auto'});
						}
						else {
							$('body').css({'right':'auto','bottom':'auto','top':'auto','left':'0px'});
						}
						$('.prdctfltr_overlay').remove();
					}
				}
				$(this).removeClass('prdctfltr_active');
				$('body').removeClass('wc-prdctfltr-active');
			}
			else {
				$(this).addClass('prdctfltr_active');
				if ( curr_filter.attr('class').indexOf( 'pf_sidebar' ) == -1 ) {
					$('body').addClass('wc-prdctfltr-active');
					if ( curr_filter.hasClass( 'pf_fullscreen' ) ) {
						curr.prepend('<div class="prdctfltr_close_sidebar"><i class="prdctfltr-delete"></i> '+prdctfltr.localization.close_filter+'</div>');
						curr.stop(true,true).fadeIn(200);

						var curr_height = $(window).height() - curr.find('.prdctfltr_filter_inner').outerHeight() - curr.find('.prdctfltr_close_sidebar').outerHeight() - curr.find('.prdctfltr_buttons').outerHeight();

						if ( curr_height > 128 ) {
							var curr_diff = curr_height/2;
							curr_height = curr.outerHeight();
							curr.css({'padding-top':curr_diff+'px'});
						}
						else {
							curr_height = $(window).height() - curr.find('.prdctfltr_close_sidebar').outerHeight() - curr.find('.prdctfltr_buttons').outerHeight() -128;
						}
						curr_filter.find('.prdctfltr_filter_wrapper').css({'max-height':curr_height});
					}
					else {
						if ( !curr_filter.hasClass('prdctfltr_wc_widget') &&  !curr_filter.hasClass('prdctfltr_always_visible') ) {
							curr.stop(true,true).slideDown(200);
						}
					}
				}
				else {
					curr.prepend('<div class="prdctfltr_close_sidebar"><i class="prdctfltr-delete"></i> '+prdctfltr.localization.close_filter+'</div>');
					curr.stop(true,true).fadeIn(200);
					if ( curr_filter.attr('class').indexOf( 'pf_sidebar_css' ) > 0 ) {
						$('body').append('<div class="prdctfltr_overlay"></div>');
						if ( prdctfltr.rtl == '' ) {
							if ( curr_filter.hasClass('pf_sidebar_css_right') ) {
								$('body').css({'right':'160px','bottom':'auto','top':'auto','left':'auto'});
								$('.prdctfltr_overlay').css({'right':'310px'}).delay(200).animate({'opacity':0.33},200,'linear');
							}
							else {
								$('body').css({'right':'auto','bottom':'auto','top':'auto','left':'160px'});
								$('.prdctfltr_overlay').css({'left':'310px'}).delay(200).animate({'opacity':0.33},200,'linear');
							}
						}
						else {
							if ( curr_filter.hasClass('pf_sidebar_css_right') ) {
								$('body').css({'left':'160px','bottom':'auto','top':'auto','right':'auto'});
								$('.prdctfltr_overlay').css({'left':'310px'}).delay(200).animate({'opacity':0.33},200,'linear');
							}
							else {
								$('body').css({'left':'auto','bottom':'auto','top':'auto','right':'160px'});
								$('.prdctfltr_overlay').css({'right':'310px'}).delay(200).animate({'opacity':0.33},200,'linear');
							}
						}
					}
					$('body').addClass('wc-prdctfltr-active');
				}
			}
		}

		return false;
	});

	$(document).on('click', '.prdctfltr_overlay, .prdctfltr_close_sidebar', function() {

		if ( $(this).closest('.prdctfltr_woocommerce').length > 0 ) {
			$(this).closest('.prdctfltr_woocommerce').find('.prdctfltr_woocommerce_filter.prdctfltr_active').trigger('click');
		}
		else {
			$('.pf_sidebar_css .prdctfltr_woocommerce_filter.prdctfltr_active, .pf_sidebar_css_right .prdctfltr_woocommerce_filter.prdctfltr_active').trigger('click');
		}

	});

	$(document).on('click', '.pf_default_select .prdctfltr_widget_title, .prdctfltr_terms_customized_select .prdctfltr_widget_title', function() {

		var curr = $(this).closest('.prdctfltr_filter').find('.prdctfltr_add_scroll');

		if ( !curr.hasClass('prdctfltr_down') ) {
			$(this).find('.prdctfltr-down').attr('class', 'prdctfltr-up');
			curr.addClass('prdctfltr_down');
			curr.slideDown(100);
		}
		else {
			curr.slideUp(100);
			curr.removeClass('prdctfltr_down');
			$(this).find('.prdctfltr-up').attr('class', 'prdctfltr-down');
		}

	});

	var pf_select_opened = false;
	$(document).on('click', '.pf_select .prdctfltr_filter .prdctfltr_regular_title, .prdctfltr_terms_customized_select.prdctfltr_filter .prdctfltr_regular_title', function() {
		pf_select_opened = true;
		var curr = $(this).closest('.prdctfltr_filter').find('.prdctfltr_add_scroll');

		if ( !curr.hasClass('prdctfltr_down') ) {
			$(this).find('.prdctfltr-down').attr('class', 'prdctfltr-up');
			curr.addClass('prdctfltr_down');
			curr.slideDown(100, function() {
				pf_select_opened = false;
			});

			if ( !$('body').hasClass('wc-prdctfltr-select') ) {
				$('body').addClass('wc-prdctfltr-select');
			}
		}
		else {
			curr.slideUp(100, function() {
				pf_select_opened = false;

			});
			curr.removeClass('prdctfltr_down');
			$(this).find('.prdctfltr-up').attr('class', 'prdctfltr-down');
			if ( curr.closest('.prdctfltr_woocommerce').find('.prdctfltr_down').length == 0 ) {
				$('body').removeClass('wc-prdctfltr-select');
			}
		}

	});

	$(document).on( 'click', 'body.wc-prdctfltr-select', function(e) {

		var curr_target = $(e.target);

		if ( $('.prdctfltr_wc.pf_select .prdctfltr_down, .prdctfltr_terms_customized_select .prdctfltr_down').length > 0 && pf_select_opened === false && !curr_target.is('span, input, i') ) {
			$('.prdctfltr_wc.pf_select .prdctfltr_down, .prdctfltr_wc:not(.prdctfltr_wc_widget.pf_default_select) .prdctfltr_terms_customized_select .prdctfltr_down').each( function() {
				var curr = $(this);
				if ( curr.is(':visible') ) {
					curr.slideUp(100);
					curr.removeClass('prdctfltr_down');
					curr.closest('.prdctfltr_filter').find('span .prdctfltr-up').attr('class', 'prdctfltr-down');
				}
			});
			$('body').removeClass('wc-prdctfltr-select');
		}
	});

	$(document).on('click', 'span.prdctfltr_sale label, span.prdctfltr_instock label, span.prdctfltr_reset label', function() {

		var field = $(this).children('input:first');

		var curr_name = field.attr('name');
		var curr_filter = $(this).closest('.prdctfltr_wc');

		var ourObj = prdctfltr_get_obj_580(curr_filter);
		var pf_length = prdctfltr_count_obj_580(ourObj);

		if ( $('body').hasClass('prdctfltr-ajax') && field.attr('name') == 'reset_filter' ) {
			$.each( ourObj, function(i, obj) {
				if ( obj.find('.prdctfltr_buttons input[name="reset_filter"]').length==0 ) {
					obj.find('.prdctfltr_buttons').append('<input name="reset_filter" type="checkbox" checked />');
				}
			});
		}

		$.each( ourObj, function(i, obj) {

			obj = $(obj);

			var curr_obj = obj.find('.prdctfltr_buttons input[name="'+curr_name+'"]');
			if ( curr_obj.length>0 ) {
				curr_obj.each(function(i5,obj24){
					var obj25 = $(obj24);
					if ( !obj25.parent().hasClass('prdctfltr_active') ) {
						obj25.prop('checked', true).attr('checked', true).parent().addClass('prdctfltr_active');
						de_check_buttons(obj25,'notactive');
					}
					else {
						obj25.prop('checked', false).attr('checked', false).parent().removeClass('prdctfltr_active');
						de_check_buttons(obj25,'active');
					}
				});
			}

			if ( obj.find('.prdctfltr_filter.prdctfltr_instock').length>0 ) {
				obj.find('.prdctfltr_filter.prdctfltr_instock input[name="instock_products"]').remove();
			}

			if ( !--pf_length ) {
				prdctfltr_submit_form(curr_filter);
			}

		});

	});

	$(document).on('click', '.prdctfltr_byprice label', function() {

		var curr_chckbx = $(this).find('input[type="checkbox"]');
		var curr = curr_chckbx.closest('.prdctfltr_filter');
		var curr_var = curr_chckbx.val().split('-');
		var curr_filter = curr_chckbx.closest('.prdctfltr_wc');

		if ( curr_filter.hasClass('prdctfltr_tabbed_selection') ) {
			var currVal = curr.find('input[name="min_price"]').val()+'-'+curr.find('input[name="max_price"]').val();
			if ( currVal == curr_chckbx.val() ) {
				return false;
			}
		}

		var ourObj = prdctfltr_get_obj_580(curr_filter);
		var pf_length = prdctfltr_count_obj_580(ourObj);

		if ( curr_var[0] == '' && curr_var[1] == '' || curr_chckbx.closest('label').hasClass('prdctfltr_active') ) {

			$.each( ourObj, function(i, obj) {
				var pfObj = $(obj).find('.prdctfltr_filter.prdctfltr_byprice');
				pfObj.find('.prdctfltr_active input[type="checkbox"]').prop('checked',false).attr('checked',false).closest('label').removeClass('prdctfltr_active');
				pfObj.find('input[name="min_price"]').val('');
				pfObj.find('input[name="max_price"]').val('');
				if ( !--pf_length ) {
					prdctfltr_submit_form(curr_filter);
				}
			});

		}
		else {

			$.each( ourObj, function(i, obj) {
				var pfObj = $(obj).find('.prdctfltr_filter.prdctfltr_byprice');
				pfObj.find('.prdctfltr_active input[type="checkbox"]').prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');
				pfObj.find('input[name="min_price"]').val(curr_var[0]);
				pfObj.find('input[name="max_price"]').val(curr_var[1]);
				pfObj.find('input[value="'+curr_var[0]+'-'+curr_var[1]+'"][type="checkbox"]').prop('checked',true).attr('checked',true).change().closest('label').addClass('prdctfltr_active');
				if ( !--pf_length ) {
					prdctfltr_submit_form(curr_filter);
				}
			});

		}

		if ( curr_filter.hasClass('prdctfltr_tabbed_selection') && curr_filter.hasClass('prdctfltr_click') ) {
			curr_filter.find('.prdctfltr_filter').each( function() {
				if ( $(this).find('input[type="hidden"]:first').length > 0 && $(this).find('input[type="hidden"]:first').val() !== '' ) {
					if ( !$(this).hasClass('prdctfltr_has_selection') ) {
						$(this).addClass('prdctfltr_has_selection');
					}
					
				}
				else {
					if ( $(this).hasClass('prdctfltr_has_selection') ) {
						$(this).removeClass('prdctfltr_has_selection');
					}
				}
			});
		}

		if ( !curr_chckbx.closest('.prdctfltr_wc').hasClass('prdctfltr_wc_widget') && ( curr_chckbx.closest('.prdctfltr_wc').hasClass('pf_select') || curr.hasClass('prdctfltr_terms_customized_select') ) ) {

			if ( curr.hasClass('prdctfltr_terms_customized_select') && curr_chckbx.closest('.prdctfltr_wc').hasClass('prdctfltr_wc_widget') && curr_chckbx.closest('.prdctfltr_wc').hasClass('pf_default_select') ) {
				return false;
			}
			curr_chckbx.closest('.prdctfltr_filter').find('.prdctfltr_add_scroll').slideUp(250).removeClass('prdctfltr_down');
			curr_chckbx.closest('.prdctfltr_filter').find('.prdctfltr_regular_title i.prdctfltr-up').removeClass('prdctfltr-up').addClass('prdctfltr-down');

		}


		$.each( ourObj, function(i, obj) {
			var pfObj = $(obj).find('.prdctfltr_filter[data-filter="price"]');
			pfObj.each( function(){
				check_selection_boxes($(this),'look');
			});
		});


		return false;

	});

	$(document).on('click', '.prdctfltr_filter:not(.prdctfltr_byprice) label', function(event) {

		if( $(event.target).is('input') ) {
			return false;
		}

		var curr_chckbx = $(this).find('input[type="checkbox"]');
		var curr = curr_chckbx.closest('.prdctfltr_filter');
		var curr_var = curr_chckbx.val();
		var curr_filter = curr.closest('.prdctfltr_wc');

		if ( curr_filter.hasClass('pf_adptv_unclick') ) {
			if ( curr_chckbx.parent().hasClass( 'pf_adoptive_hide' ) ) {
				return false;
			}
		}

		prdctfltr_check_580(curr, curr_chckbx, curr_var, curr_filter);

		return false;

	});

	var shortcodeAjax = false;
	var prodcutsWrapper = false;
	var hasFilter = false;
	var hasProducts = false;
	var isAjax = false;
	var isStep = false;
	var hasWidget = false;

	function resetVars() {
		shortcodeAjax = false;
		prodcutsWrapper = false;
		hasFilter = false;
		hasProducts = false;
		isAjax = false;
		isStep = false;
		hasWidget = false;

	}

	function prdctfltr_get_obj_580(filter) {
		var ourObj = {};
		resetVars();

		if ( filter.closest('.prdctfltr_sc').length>0 ) {
			var scWrap = filter.closest('.prdctfltr_sc');
			var scMode = scWrap.is('.prdctfltr_sc_filter') ? 'sc_filter' : 'sc_shortcode' ;
			if ( scWrap.find('.prdctfltr_wc').length>0 ) {
				hasFilter = true;
			}
			if ( scWrap.find(prdctfltr.ajax_class).length>0 ) {
				hasProducts = true;
			}
			if ( scWrap.hasClass('prdctfltr_ajax') ) {
				isAjax = true;
				shortcodeAjax = true;
			}
			if ( scWrap.find('.prdctfltr_wc').hasClass('prdctfltr_step_filter') ) {
				isStep = true;
			}
			if ( $('.prdctfltr_wc_widget').length>0 ) {
				hasWidget = true;
			}
		}
		else if ( filter.closest('.prdctfltr_wcsc').length>0 ) {

		}
		else if ( archiveAjax === true ) {
			
		}
		else if ( filter.closest('.prdctfltr_wc_widget').length>0 ) {
			hasWidget = true;
			if( $('.prdctfltr_sc:not(.prdctfltr_sc_step_filter)').length>0 ) {
				if ( $('.prdctfltr_sc:not(.prdctfltr_sc_step_filter)').find(prdctfltr.ajax_class).length>0 ) {
					var scWrap = $('.prdctfltr_sc:not(.prdctfltr_sc_step_filter)');
					var scMode = scWrap.is('.prdctfltr_sc_filter') ? 'sc_filter' : 'sc_shortcode' ;
					hasFilter = true;
					hasProducts = true;
					prodcutsWrapper = scWrap;
					shortcodeAjax = prodcutsWrapper.hasClass('prdctfltr_ajax');
				}
			}
		}

		if ( isStep ) {
			scWrap.find('.prdctfltr_wc').each( function() {
				ourObj[$(this).attr('data-id')] = $(this);
			});
		}
		else if ( hasProducts && hasFilter ) {
			prodcutsWrapper = scWrap;
			if ( hasWidget ) {
				scWrap.find('.prdctfltr_wc:not(.prdctfltr_step_filter)').each( function() {
					ourObj[$(this).attr('data-id')] = $(this);
				});
				$('.prdctfltr_wc_widget:not(.prdctfltr_step_filter)').each( function() {
					ourObj[$(this).attr('data-id')] = $(this);
				});
			}
			else {
				scWrap.find('.prdctfltr_wc:not(.prdctfltr_step_filter)').each( function() {
					ourObj[$(this).attr('data-id')] = $(this);
				});
			}
		}
		else {
			$('.prdctfltr_wc:not([data-id="'+filter.attr('data-id')+'"]):not(.prdctfltr_step_filter)').each( function() {
				if ( $(this).closest('.prdctfltr_sc_products').length==0 ) {
					ourObj[$(this).attr('data-id')] = $(this);
				}
			});
			ourObj[filter.attr('data-id')] = $('.prdctfltr_wc[data-id="'+filter.attr('data-id')+'"]');
		}

		return ourObj;

	}

	function prdctfltr_count_obj_580(ourObj) {
		var pf_length = 0;
		var i;
		for (i in ourObj) {
			if (ourObj.hasOwnProperty(i)) {
				pf_length++;
			}
		}
		return pf_length;
	}

	function prdctfltr_check_parent_helper_590(termParent, pfObj) {
		if ( termParent ) {
			var found = pfObj.find('input[value="'+termParent+'"]');
			if ( found.length > 0 ) {
				pfObj.find('input[value="'+termParent+'"][type="checkbox"]').prop('checked',true).attr('checked',true).change().closest('label').addClass('prdctfltr_active');
			}
			else {
				//pfObj.find('label:first').insertBefore('<label style="display:none;"><input type="checkbox" value="'+termParent+'" checked /></label>');
			}
		}
	}

	function prdctfltr_check_580(curr, curr_chckbx, curr_var, curr_filter) {

		var ourObj = prdctfltr_get_obj_580(curr_filter);
		var pf_length = prdctfltr_count_obj_580(ourObj);

		var field = curr.children('input[type="hidden"]:first');

		var curr_name = field.attr('name');
		var curr_val = field.val();

		if ( curr_filter.hasClass('prdctfltr_tabbed_selection') ) {
			if ( curr_val == curr_chckbx.val() ) {
				return false;
			}
		}

		if ( $('.pf_added_input[name="'+curr_name+'"]').length > 0 ) {
			$('.pf_added_input[name="'+curr_name+'"]').remove();
		}

		if ( curr.hasClass('prdctfltr_selection') ) {
			var checkLength = pf_length;
			$.each( ourObj, function(i, obj) {
				var pfObj1 = $(obj).find('.prdctfltr_filter:not(.prdctfltr_range):not([data-filter="'+curr_name+'"]) label.prdctfltr_active');
				if ( pfObj1.length>0 ) {
					$.each( pfObj1, function(i3, ob5) {
						$('.pf_added_input[name="'+$(ob5).closest('.prdctfltr_filter').attr('data-filter')+'"]').remove();
						$(ob5).removeClass('prdctfltr_active').find('input[type="checkbox"]').prop('checked',false).attr('checked',false).change().closest('.prdctfltr_filter').find('input[type="hidden"]').val('');
					});
				}
				var pfObj = $(obj).find('.prdctfltr_filter.prdctfltr_range input[type="hidden"][val!=""]');
				if ( pfObj.length>0 ) {
					$.each( pfObj, function(i2, obj4) {
						$('.pf_added_input[name="'+$(obj4).attr('name')+'"]').remove();
						$(obj4).closest('.prdctfltr_filter').find('input[type="hidden"]').val('');
					});
				}

				if ( !--checkLength ) {
					$.each( ourObj, function(i4, obj47) {

						$(obj47).find('.prdctfltr_buttons input[name="sale_products"], .prdctfltr_buttons input[name="instock_products"]').each(function() {
							$(this).prop('checked', false).attr('checked', false).closest('label').removeClass('prdctfltr_active');
							de_check_buttons($(this),'active');
						});

						$(obj47).find('input.pf_search').val('');
						$(obj47).find('input[id^="prdctfltr_rng_"]').each(function(){
							var setRng = $(this).data('ionRangeSlider');
							ranges[$(this).attr('id')].update({
								from: setRng.options.min,
								to: setRng.options.max
							});
						});

						$(obj47).find('.prdctfltr_filter').each( function() {
							check_selection_boxes($(this),'init');
						});

					});
				}


			});
		}

		if ( !curr.hasClass('prdctfltr_multi') ) {

			if ( curr_var == '' || curr_chckbx.closest('label').hasClass('prdctfltr_active') ) {

				var termParent = curr_chckbx.attr('data-parent');

				$.each( ourObj, function(i, obj) {
					var pfObj = $(obj).find('.prdctfltr_filter[data-filter="'+curr_name+'"]');
					pfObj.find('.prdctfltr_active input[type="checkbox"]').prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');

					if ( termParent ) {
						prdctfltr_check_parent_helper_590(termParent, pfObj);
						pfObj.find('input[name="'+curr_name+'"]').val(termParent);
					}
					else {
						pfObj.find('input[name="'+curr_name+'"]').val('');
					}

					if ( !--pf_length ) {
						//pfClearSure = curr_name;
						prdctfltr_submit_form(curr_filter);
					}
				});

			}
			else {

				$.each( ourObj, function(i, obj) {
					var pfObj = $(obj).find('.prdctfltr_filter[data-filter="'+curr_name+'"]');
					pfObj.find('.prdctfltr_active input[type="checkbox"]').prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');
					pfObj.find('input[name="'+curr_name+'"]').val(curr_var);
					pfObj.find('input[value="'+curr_var+'"][type="checkbox"]').prop('checked',true).attr('checked',true).change().closest('label').addClass('prdctfltr_active');
					if ( !--pf_length ) {
						prdctfltr_submit_form(curr_filter);
					}
				});

			}

			if ( curr_chckbx.closest('.prdctfltr_wc').hasClass('pf_select') || curr.hasClass('prdctfltr_terms_customized_select') ) {
				if ( curr.hasClass('prdctfltr_terms_customized_select') && curr_chckbx.closest('.prdctfltr_wc').hasClass('prdctfltr_wc_widget') && curr_chckbx.closest('.prdctfltr_wc').hasClass('pf_default_select') ) {
					return false;
				}
				curr_chckbx.closest('.prdctfltr_filter').find('.prdctfltr_add_scroll').slideUp(250).removeClass('prdctfltr_down');
				curr_chckbx.closest('.prdctfltr_filter').find('.prdctfltr_regular_title i.prdctfltr-up').removeClass('prdctfltr-up').addClass('prdctfltr-down');
			}

		}
		else {

			if ( curr_chckbx.val() !== '' ) {

				if ( curr_chckbx.closest('label').hasClass('prdctfltr_active') ) {

					if ( curr.hasClass('prdctfltr_merge_terms') ) {
						var curr_settings = ( curr_val.indexOf('+') > 0 ? curr_val.replace('+' + curr_var, '').replace(curr_var + '+', '') : '' );

						$.each(prdctfltr.js_filters, function(n18,obj43){
							if ( typeof obj43.adds !== 'undefined' && obj43.adds[curr_name] !== null ) {
								var check = prdctfltr.js_filters[n18].adds[curr_name];
								prdctfltr.js_filters[n18].adds[curr_name] = ( typeof check !== 'undefined' && check.indexOf('+') > 0 ? check.replace('+' + curr_var, '').replace(curr_var + '+', '') : '' );
							}
						});
					}
					else {
						var curr_settings = ( curr_val.indexOf(',') > 0 ? curr_val.replace(',' + curr_var, '').replace(curr_var + ',', '') : '' );

						$.each(prdctfltr.js_filters, function(n18,obj43){
							if ( typeof obj43.adds !== 'undefined' && obj43.adds[curr_name] !== null ) {
								var check = prdctfltr.js_filters[n18].adds[curr_name];
								prdctfltr.js_filters[n18].adds[curr_name] = ( typeof check !== 'undefined' && check.indexOf(',') > 0 ? check.replace(',' + curr_var, '').replace(curr_var + ',', '') : '' );
							}
						});
					}

					var termParent = curr_chckbx.attr('data-parent');

					$.each( ourObj, function(i, obj) {
						var pfObj = $(obj).find('.prdctfltr_filter[data-filter="'+curr_name+'"]');
						pfObj.find('input[name="'+curr_name+'"]').val(curr_settings);
						pfObj.find('input[value="'+curr_var+'"][type="checkbox"]').prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');

						if ( termParent ) {
							if ( curr_settings == '' ) {
								prdctfltr_check_parent_helper_590(termParent, pfObj);
								pfObj.find('input[name="'+curr_name+'"]').val(termParent);
							}
						}

						if ( !--pf_length ) {
							prdctfltr_submit_form(curr_filter);
						}

					});

				}
				else {

					$('.prdctfltr_filter[data-filter="'+curr_name+'"] .prdctfltr_sub[data-sub="'+curr_var+'"]').find('.prdctfltr_active input[type="checkbox"]').each( function() {

						var checkVal = $(this).val();
						if ( curr.hasClass('prdctfltr_merge_terms') ) {
							if ( curr_val.indexOf('+') > 0 ) {
								curr_val = curr_val.replace('+' + checkVal, '').replace(checkVal + '+', '');
							}
							else {
								curr_val = curr_val.replace(checkVal, '');
							}
						}
						else {
							if ( curr_val.indexOf(',') > 0 ) {
								curr_val = curr_val.replace(',' + checkVal, '').replace(checkVal + ',', '');
							}
							else {
								curr_val = curr_val.replace(checkVal, '');
							}
						}
						$(this).prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');
					});

					if ( curr.hasClass('prdctfltr_merge_terms') ) {

						if ( curr.closest('.prdctfltr_wc').find('.prdctfltr_filter[data-filter="'+curr_name+'"]').length>1 ) {
							curr.find('.prdctfltr_active').each(function(){
								var val12 = $(this).find('input[type="checkbox"]').val();
								if ( curr_val.indexOf('+') > 0 ) {
									curr_val = curr_val.replace('+' + val12, '').replace(val12 + '+', '');
								}
								else {
									curr_val = curr_val.replace(val12, '');
								}
								$(this).find('input[type="checkbox"]').prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');
							});
						}

						var curr_settings = ( curr_val == '' ? curr_var : curr_val + '+' + curr_var );
					}
					else {
						var curr_settings = ( curr_val == '' ? curr_var : curr_val + ',' + curr_var );
					}

					var termParent = curr_chckbx.attr('data-parent');

					$.each( ourObj, function(i, obj) {
						var pfObj = $(obj).find('.prdctfltr_filter[data-filter="'+curr_name+'"]');
						pfObj.find('input[name="'+curr_name+'"]').val(curr_settings);
						pfObj.find('input[value="'+curr_var+'"][type="checkbox"]').prop('checked',true).attr('checked',true).change().closest('label').addClass('prdctfltr_active');

						if ( termParent ) {
							if ( pfObj.find('input[value="'+termParent+'"][type="checkbox"]:checked').length > 0 ) {
								pfObj.find('input[value="'+termParent+'"][type="checkbox"]:checked').prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');
								if ( curr_settings.indexOf(termParent) > -1 ) {
									if ( curr.hasClass('prdctfltr_merge_terms') ) {
										var makeNew = ( curr_settings.indexOf('+') > 0 ? curr_settings.replace('+' + termParent, '').replace(termParent + '+', '') : '' );
									}
									else {
										var makeNew = ( curr_settings.indexOf(',') > 0 ? curr_settings.replace(',' + termParent, '').replace(termParent + ',', '') : '' );
									}
									pfObj.find('input[name="'+curr_name+'"]').val(makeNew);
								}
							}
							else {
								var remTermParent = pfObj.find('input[value="'+termParent+'"][type="checkbox"]').attr('data-parent');
								if ( remTermParent ) {
									while ( remTermParent !== false ) {
										pfObj.find('input[value="'+remTermParent+'"][type="checkbox"]:checked').prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');
										if ( curr_settings.indexOf(remTermParent) > -1 ) {
											if ( curr.hasClass('prdctfltr_merge_terms') ) {
												var makeNew = ( curr_settings.indexOf('+') > 0 ? curr_settings.replace('+' + remTermParent, '').replace(remTermParent + '+', '') : '' );
											}
											else {
												var makeNew = ( curr_settings.indexOf(',') > 0 ? curr_settings.replace(',' + remTermParent, '').replace(remTermParent + ',', '') : '' );
											}
											pfObj.find('input[name="'+curr_name+'"]').val(makeNew);
										}
										remTermParent = ( pfObj.find('input[value="'+remTermParent+'"][type="checkbox"]').attr('data-parent') ? pfObj.find('input[value="'+remTermParent+'"][type="checkbox"]').attr('data-parent') : false );
									}
								}
							}
						}

						if ( !--pf_length ) {
							prdctfltr_submit_form(curr_filter);
						}
					});

				}
			}
			else {

				$.each( ourObj, function(i, obj) {
					var pfObj = $(obj).find('.prdctfltr_filter[data-filter="'+curr_name+'"]');

					if ( pfObj.find('label.prdctfltr_active input[data-parent]').length>0 ) {
						if ( pfObj.find('label.prdctfltr_active input[data-parent]').length == pfObj.find('label.prdctfltr_active input[data-parent="'+pfObj.find('label.prdctfltr_active input[data-parent]:first').attr('data-parent')+'"]').length ) {
							pfObj.find('input[name="'+curr_name+'"]').val(pfObj.find('label.prdctfltr_active input[data-parent]:first').attr('data-parent'));
							pfObj.find('input[type="checkbox"]').prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');
						}
					}
					else {
						pfObj.find('input[name="'+curr_name+'"]').val('');
						pfObj.find('input[type="checkbox"]').prop('checked',false).attr('checked',false).change().closest('label').removeClass('prdctfltr_active');
					}

					if ( !--pf_length ) {
						prdctfltr_submit_form(curr_filter);
					}
				});

			}

		}

		if ( curr_filter.hasClass('prdctfltr_tabbed_selection') && curr_filter.hasClass('prdctfltr_click') ) {
			curr_filter.find('.prdctfltr_filter').each( function() {
				if ( $(this).find('input[type="hidden"]:first').length > 0 && $(this).find('input[type="hidden"]:first').val() !== '' ) {
					if ( !$(this).hasClass('prdctfltr_has_selection') ) {
						$(this).addClass('prdctfltr_has_selection');
					}
				}
				else {
					if ( $(this).hasClass('prdctfltr_has_selection') ) {
						$(this).removeClass('prdctfltr_has_selection');
					}
				}
			});
		}


		$.each( ourObj, function(i, obj) {
			var pfObj = $(obj).find('.prdctfltr_filter[data-filter="'+curr_name+'"]');
			pfObj.each( function(){
				check_selection_boxes($(this),'look');
			});
		});

	}

	function check_selection_boxes_wrapper(curr) {

		curr.find('.prdctfltr_filter').each( function() {
			check_selection_boxes($(this),'init');
		});

		curr.find('.prdctfltr_buttons:first label.prdctfltr_active').each(function() {
			check_buttons($(this),'init');
		});

	}

	function de_check_buttons(curr,mode) {

		var collectors = prdctfltr.js_filters[curr.closest('.prdctfltr_wc').attr('data-id')].collectors;
		var collectorStyle = prdctfltr.js_filters[curr.closest('.prdctfltr_wc').attr('data-id')].collector_style;

		if ( mode == 'active' ) {

			$.each( collectors, function(i,e) {
				switch(e){

					case 'collector':
						var wrap = curr.closest('.prdctfltr_woocommerce_ordering');

						var collector = wrap.find('.prdctfltr_collector');
						if( collector.find('.prdctfltr_title_remove[data-key="'+curr.attr('name')+'"]').length>0 ) {
							collector.find('.prdctfltr_title_remove[data-key="'+curr.attr('name')+'"]').closest('.prdctfltr_title_selected').remove();
						}

					break;

					case 'topbar':
						var wrap = curr.closest('.prdctfltr_wc');

						var collector = wrap.find('.prdctfltr_topbar');
						if( collector.find('.prdctfltr_title_remove[data-key="'+curr.attr('name')+'"]').length>0 ) {
							collector.find('.prdctfltr_title_remove[data-key="'+curr.attr('name')+'"]').closest('.prdctfltr_title_selected').remove();
						}

					break;

					default:

					break;
				}

			});
		}
		else {

			var input = '<span class="prdctfltr_title_selected"><span class="prdctfltr_title_added prdctfltr_title_remove" data-key="'+curr.attr('name')+'"><i class="prdctfltr-check"></i></span> <span class="prdctfltr_selected_title">'+curr.parent().text()+'</span><span class="prdctfltr_title_selected_separator"></span></span>';

			$.each( collectors, function(i,e) {
				switch(e){

					case 'collector':
						var wrap = curr.closest('.prdctfltr_woocommerce_ordering');

						if (wrap.find('.prdctfltr_collector').length==0) {
							wrap.prepend('<div class="prdctfltr_collector prdctfltr_collector_'+collectorStyle+'"></div>');
							wrap.find('.prdctfltr_collector').html(input);
						}
						else {
							var collector = wrap.find('.prdctfltr_collector');
							if( collector.find('.prdctfltr_title_remove[data-key="'+curr.attr('name')+'"]').length>0 ) {
								collector.find('.prdctfltr_title_remove[data-key="'+curr.attr('name')+'"]').closest('.prdctfltr_title_selected').remove();
							}
							wrap.find('.prdctfltr_collector').append(input);
						}
					break;

					case 'topbar':

						var wrap = curr.closest('.prdctfltr_wc');

						if (wrap.find('.prdctfltr_topbar').length==0) {
							wrap.find('.prdctfltr_woocommerce_filter_title').after('<div class="prdctfltr_topbar"></div>');
							wrap.find('.prdctfltr_topbar').html(input);
						}
						else {
							var collector = wrap.find('.prdctfltr_topbar');
							if( collector.find('.prdctfltr_title_remove[data-key="'+curr.attr('name')+'"]').length>0 ) {
								collector.find('.prdctfltr_title_remove[data-key="'+curr.attr('name')+'"]').closest('.prdctfltr_title_selected').remove();
							}
							wrap.find('.prdctfltr_topbar').append(input);
						}

					break;

					default:

					break;
				}

			});

		}

		if ( curr.closest('.prdctfltr_wc').hasClass('pf_mod_masonry') ) {
			curr.closest('.prdctfltr_filter_inner').isotope('layout');
		}

	}

	function check_buttons(curr,mode) {

		var collectors = prdctfltr.js_filters[curr.closest('.prdctfltr_wc').attr('data-id')].collectors;
		var collectorStyle = prdctfltr.js_filters[curr.closest('.prdctfltr_wc').attr('data-id')].collector_style;

		var input = '<span class="prdctfltr_title_selected">'+(mode=='init'?'<a href="#" class="prdctfltr_title_remove" data-key="'+curr.find('input:first').attr('name')+'"><i class="prdctfltr-delete"></i></a>':'<span class="prdctfltr_title_added prdctfltr_title_remove" data-key="'+curr.find('input:first').attr('name')+'"><i class="prdctfltr-check"></i></span>')+' <span class="prdctfltr_selected_title">'+curr.text()+'</span><span class="prdctfltr_title_selected_separator"></span></span>';

		$.each( collectors, function(i,e) {
			switch(e){

				case 'collector':
					var wrap = curr.closest('.prdctfltr_woocommerce_ordering');

					if (wrap.find('.prdctfltr_collector').length==0) {
						wrap.prepend('<div class="prdctfltr_collector prdctfltr_collector_'+collectorStyle+'"></div>');
						wrap.find('.prdctfltr_collector').html(input);
					}
					else {
						var collector = wrap.find('.prdctfltr_collector');
						if( collector.find('.prdctfltr_title_remove[data-key="'+curr.find('input:first').attr('name')+'"]').length>0 ) {
							collector.find('.prdctfltr_title_remove[data-key="'+curr.find('input:first').attr('name')+'"]').closest('.prdctfltr_title_selected').remove();
						}
						wrap.find('.prdctfltr_collector').append(input);
					}
				break;

				case 'topbar':

					var wrap = curr.closest('.prdctfltr_wc');

					if (wrap.find('.prdctfltr_topbar').length==0) {
						wrap.find('.prdctfltr_woocommerce_filter_title').after('<div class="prdctfltr_topbar"></div>');
						wrap.find('.prdctfltr_topbar').html(input);
					}
					else {
						var collector = wrap.find('.prdctfltr_topbar');
						if( collector.find('.prdctfltr_title_remove[data-key="'+curr.find('input:first').attr('name')+'"]').length>0 ) {
							collector.find('.prdctfltr_title_remove[data-key="'+curr.find('input:first').attr('name')+'"]').closest('.prdctfltr_title_selected').remove();
						}
						wrap.find('.prdctfltr_topbar').append(input);
					}

				break;

				default:

				break;
			}

		});

		if ( curr.closest('.prdctfltr_wc').hasClass('pf_mod_masonry') ) {
			curr.closest('.prdctfltr_filter_inner').isotope('layout');
		}

	}


	function get_input_delete(selectedTerms, mode, curr, slug) {
		return '<span class="prdctfltr_title_selected">'+(mode=='init'?'<a href="#" class="prdctfltr_title_remove" data-key="'+curr.attr('data-filter')+'"'+slug+'><i class="prdctfltr-delete"></i></a>':'<span class="prdctfltr_title_added prdctfltr_title_remove" data-key="'+curr.attr('data-filter')+'"'+slug+'><i class="prdctfltr-check"></i></span>')+' <span class="prdctfltr_selected_title">'+selectedTerms+'</span><span class="prdctfltr_title_selected_separator"></span></span>';
	}
		
	function check_selection_boxes(curr,mode) {
		/*if ( curr.hasClass('prdctfltr_terms_customized_select') && curr_chckbx.closest('.prdctfltr_wc').hasClass('prdctfltr_wc_widget') && curr_chckbx.closest('.prdctfltr_wc').hasClass('pf_default_select') ) {
			return false;
		}*/

		var selectedTerms = [];
		var selectedItms = [];
		curr.find('label.prdctfltr_active').each(function() {
			if ( $(this).find('.prdctfltr_customization_search').length>0 ) {
				selectedTerms.push($(this).find('.prdctfltr_customization_search').text());
			}
			else if ( $(this).find('.prdctfltr_customize_name').length>0 ) {
				selectedTerms.push($(this).find('.prdctfltr_customize_name').text());
			}
			else {
				selectedTerms.push($(this).find('span:first').contents().filter(function(){return 3==this.nodeType;}).text());
			}
			if ( $(this).closest('.prdctfltr_filter').hasClass('prdctfltr_attributes') || $(this).closest('.prdctfltr_filter').hasClass('prdctfltr_meta') ) {
				selectedItms.push($(this).find('input[type="checkbox"]:first').val() );
			}
		});

		if ( typeof selectedTerms[0] == 'undefined' && curr.hasClass('prdctfltr_range') ) {
			var rngData = curr.find('[id^="prdctfltr_rng_"]:first').data('ionRangeSlider');

			if ( typeof rngData !== 'undefined' ) {
				if ( ( rngData.result.from==rngData.options.min && rngData.result.to == rngData.options.max ) === false ) {
					if ( curr.attr('data-filter') == 'rng_price' ) {
						selectedTerms.push(rngData.options.prefix+rngData.result.from+rngData.options.postfix+' &longleftrightarrow; '+rngData.options.prefix+rngData.result.to+rngData.options.postfix);
					}
					else {
						selectedTerms.push(rngData.options.prefix+rngData.options.prettyValues[rngData.result.from]+rngData.options.postfix+' &longleftrightarrow; '+rngData.options.prefix+rngData.options.prettyValues[rngData.result.to]+rngData.options.postfix);
					}

				}
			}
		}

		if ( typeof selectedTerms[0] !== 'undefined' ) {

			var col = prdctfltr.js_filters[curr.closest('.prdctfltr_wc').attr('data-id')];

			var collectors = typeof col!=='undefined'?col.collectors:[];
			var collectorStyle = typeof col!=='undefined'?col.collector_style:[];

			var slug = '';
			if ( curr.hasClass('prdctfltr_attributes') || curr.hasClass('prdctfltr_meta') ) {
				if ( 1==1 && typeof selectedTerms[1] !== 'undefined' ) {
					var input = '';
					$.each(selectedItms, function(o23,k23) {
						slug = ' data-slug="'+selectedItms[o23]+'"';
						input += get_input_delete( selectedTerms[o23], mode, curr, slug );
					});
				}
				else {
					var value = curr.find('input[type="hidden"]:first').val();
					var parent = curr.find('input[type="hidden"]:first').attr('data-parent');
					slug = ' data-slug="'+(typeof parent!=='undefined'?parent+'>':'')+value+'"';
					var input = get_input_delete( selectedTerms.join(', '), mode, curr, slug );
				}
			}
			else {
				var input = '<span class="prdctfltr_title_selected">'+(mode=='init'?'<a href="#" class="prdctfltr_title_remove" data-key="'+curr.attr('data-filter')+'"'+slug+'><i class="prdctfltr-delete"></i></a>':'<span class="prdctfltr_title_added prdctfltr_title_remove" data-key="'+curr.attr('data-filter')+'"'+slug+'><i class="prdctfltr-check"></i></span>')+' <span class="prdctfltr_selected_title">'+selectedTerms.join(', ')+'</span><span class="prdctfltr_title_selected_separator"></span></span>';
			}

			$.each( collectors, function(i,e) {
				switch(e){
					case 'intitle':
						curr.find('.prdctfltr_regular_title .prdctfltr_title_selected, .prdctfltr_widget_title  .prdctfltr_title_selected').remove();
						curr.find('.prdctfltr_regular_title, .prdctfltr_widget_title').prepend(input);
					break;

					case 'aftertitle':
						curr.find('.prdctfltr_aftertitle').remove();
						curr.find('.prdctfltr_add_scroll').before('<div class="prdctfltr_aftertitle prdctfltr_collector_'+collectorStyle+'">'+input+'</div>');
					break;

					case 'collector':
						var wrap = curr.closest('.prdctfltr_woocommerce_ordering');

						if (wrap.find('.prdctfltr_collector').length==0) {
							wrap.prepend('<div class="prdctfltr_collector prdctfltr_collector_'+collectorStyle+'"></div>');
							wrap.find('.prdctfltr_collector').html(input);
						}
						else {
							var collector = wrap.find('.prdctfltr_collector');
							if( collector.find('.prdctfltr_title_remove[data-key="'+curr.attr('data-filter')+'"]').length>0 ) {
								collector.find('.prdctfltr_title_remove[data-key="'+curr.attr('data-filter')+'"]').closest('.prdctfltr_title_selected').remove();
							}
							wrap.find('.prdctfltr_collector').append(input);
						}
					break;

					case 'topbar':

						var wrap = curr.closest('.prdctfltr_wc');

						if (wrap.find('.prdctfltr_topbar').length==0) {
							wrap.find('.prdctfltr_woocommerce_filter_title').after('<div class="prdctfltr_topbar"></div>');
							wrap.find('.prdctfltr_topbar').html(input);
						}
						else {
							var collector = wrap.find('.prdctfltr_topbar');
							if( collector.find('.prdctfltr_title_remove[data-key="'+curr.attr('data-filter')+'"]').length>0 ) {
								collector.find('.prdctfltr_title_remove[data-key="'+curr.attr('data-filter')+'"]').closest('.prdctfltr_title_selected').remove();
							}
							wrap.find('.prdctfltr_topbar').append(input);
						}

					break;

					default:

					break;
				}

			});

		}
		else if ( typeof selectedTerms[0] == 'undefined' ) {
			if ( curr.closest('.prdctfltr_wc').find('.prdctfltr_attributes[data-filter="'+curr.attr('data-filter')+'"] label.prdctfltr_active').length == 0 ) {
				curr.find('.prdctfltr_title_selected').remove();
				curr.closest('.prdctfltr_wc').find('.prdctfltr_collector .prdctfltr_title_remove[data-key="'+curr.attr('data-filter')+'"]').closest('.prdctfltr_title_selected').remove();
				curr.closest('.prdctfltr_wc').find('.prdctfltr_topbar .prdctfltr_title_remove[data-key="'+curr.attr('data-filter')+'"]').closest('.prdctfltr_title_selected').remove();
			}
		}

		if ( curr.closest('.prdctfltr_wc').hasClass('pf_mod_masonry') ) {
			curr.closest('.prdctfltr_filter_inner').isotope('layout');
		}

	}

	//var pfClearSure = false;

	function clear_filters_after(filter) {
		filter.nextAll('.prdctfltr_filter').each(function() {
			$(this).find('input[type="hidden"]').val('');
		});
	}
	function clicked_remove(obj,mode,term) {

		switch ( term ) {
			case 's':
			case 'pf_search':
			case 'search_products':
				var srchStr = 'input[name="s"],input[name="search_products"]';
			break;

			case 'price':
				var srchStr = 'input[name="min_price"],input[name="max_price"]';
			break;
			
			default:
				var srchStr = 'input[name="'+term+'"]';
			break;
		}

		if ( mode === true ) {
			obj.closest('.prdctfltr_sc_products').find('.prdctfltr_filter, .prdctfltr_buttons').find(srchStr).remove();
			if ( $('.prdctfltr_wc_widget').length>0 ) {
				$('.prdctfltr_wc_widget').find('.prdctfltr_filter, .prdctfltr_buttons').find(srchStr).remove();
			}
			$('.prdctfltr_add_inputs').find(srchStr).remove();
		}
		else {
			$('.prdctfltr_filter, .prdctfltr_add_inputs, .prdctfltr_buttons').find(srchStr).remove();
		}

	}

	$(document).on('click', 'a.prdctfltr_title_remove', function() {

		var filter = $(this).attr('data-key');
		//pfClearSure = filter;

		var filterWrap = $(this).closest('.prdctfltr_filter');

		if ( $(this).closest('.prdctfltr_filter').hasClass('prdctfltr_has_selection') ) {
			clear_filters_after($(this).closest('.prdctfltr_filter'));
		}

		var mode = $(this).closest('.prdctfltr_sc_products').length>0;

		if ( filter == 's' || filter == 'search_products' || filter == 'pf_search' ) {
			clicked_remove($(this),mode,filter);
		}
		else if ( filter == 'price' ) {
			clicked_remove($(this),mode,filter);
		}
		else if ( filter == 'orderby' || filter == 'sale_products' || filter == 'instock_products' ) {
			clicked_remove($(this),mode,filter);
		}
		else if ( filter == 'vendor' || filter == 'instock' || filter == 'products_per_page' ) {
			clicked_remove($(this),mode,filter);
		}
		else if ( filter.substr(0,4) !== 'rng_' ) {

			if ( $(this).closest('.prdctfltr_sc_products').length > 0 ) {
				var curr_els = $(this).closest('.prdctfltr_sc_products').find('input[name="'+filter+'"]');
				if ( $('.prdctfltr_wc_widget').length>0 ) {
					curr_els.push($('.prdctfltr_wc_widget').find('input[name="'+filter+'"]'));
				}
			}
			else {
				var curr_els = $('.prdctfltr_filter, .prdctfltr_add_inputs').find('input[name="'+filter+'"]');
			}

			var selectedString = $(this).attr('data-slug');
			if ( selectedString.indexOf( '>' ) > 0 ) {
				var termParent = selectedString.substr(0, selectedString.indexOf( '>' ));
				selectedString = selectedString.substr(selectedString.indexOf( '>' )+1);
			}

			var cur_vals = [];
			if ( selectedString.indexOf(',') > 0 ) {
				cur_vals = selectedString.split(',');
			}
			else if ( selectedString.indexOf('+') > 0 ) {
				cur_vals = selectedString.split('+');
			}
			else {
				cur_vals[0] = selectedString;
			}

			var cv_lenght = cur_vals.length;

			$.each(cur_vals, function(i, val23) {

				var curr_value = val23;

				curr_els.each( function() {

					var curr_chckd = $(this);
					var curr_chckdval = $(this).val();

					if ( curr_chckdval.indexOf( ',' ) > 0 ) {
						curr_chckd.val(curr_chckdval.replace(',' + curr_value, '').replace(curr_value + ',', ''));
					}
					else if ( curr_chckdval.indexOf( '+' ) > 0 ) {
						curr_chckd.val(curr_chckdval.replace('+' + curr_value, '').replace(curr_value + '+', ''));
					}
					else {
						curr_chckd.val(curr_chckdval.replace(curr_value, '').replace(curr_value, ''));
					}

				});

				if ( !--cv_lenght ) {

					curr_els.each( function() {

						var curr_chckd = $(this);

						if ( termParent ) {
							curr_chckd.val(termParent);
							if ( curr_chckd.val() == '' ) {
								curr_chckd.val(termParent);
							}
							
						}

					});

				}

			});

		}
		else {
			if ( $(this).closest('.prdctfltr_sc_products').length>0 ) {
				if ( filter == 'rng_price' ) {
					$(this).closest('.prdctfltr_sc_products').find('.prdctfltr_range.prdctfltr_price input[type="hidden"]').each(function() {
						$(this).remove();
					});
					$('.prdctfltr_wc_widget').find('.prdctfltr_range.prdctfltr_price input[type="hidden"]').remove()
				}
				else {
					$(this).closest('.prdctfltr_sc_products').find('.prdctfltr_range input[type="hidden"][name$="'+filter.substr(4, filter.length)+'"]').each(function() {
						$(this).remove();
					});
					$('.prdctfltr_wc_widget').find('.prdctfltr_range input[type="hidden"][name$="'+filter.substr(4, filter.length)+'"]').remove();
				}

			}
			else {
				if ( filter == 'rng_price' ) {
					$('.prdctfltr_wc').find('.prdctfltr_range.prdctfltr_price input[type="hidden"]').each(function() {
						$(this).remove();
					});
				}
				else {
					$('.prdctfltr_wc').find('.prdctfltr_range input[type="hidden"][name$="'+filter.substr(4, filter.length)+'"]').each(function() {
						$(this).remove();
					});
				}
			}
		}


		prdctfltr_respond_550($(this).closest('.prdctfltr_wc').find('form.prdctfltr_woocommerce_ordering'));

		//pfClearSure = false;

		return false;

	});

	$(document).on('click', '.prdctfltr_checkboxes label > i', function() {

		var curr = $(this).parent().next();

		$(this).parent().toggleClass('prdctfltr_clicked');

		if ( curr.hasClass('prdctfltr_sub') ) {
			curr.slideToggle(100, function() {
				if ( curr.closest('.prdctfltr_woocommerce').hasClass('pf_mod_masonry') ) {
					curr.closest('.prdctfltr_woocommerce').find('.prdctfltr_filter_inner').isotope('layout');
				}
			});

		}

		return false;

	});

	function prdctfltr_get_loader(curr) {
		var curr_loader = curr.closest('.prdctfltr_wc').attr('data-loader');

		if ( curr_loader == 'none' ) {
			return false;
		}

		if ( typeof curr_loader !== 'undefined' ) {
			if ( curr_loader.substr(0, 4) == 'css-' ) {
				if ( curr.closest('.prdctfltr_wc').find('.prdctfltr_woocommerce_filter').length>0 ) {
					curr.closest('.prdctfltr_wc').find('.prdctfltr_woocommerce_filter').addClass('pf_ajax_loading');
				}
				else {
					curr.closest('.prdctfltr_wc').prepend('<span class="prdctfltr_added_loader prdctfltr_woocommerce_filter pf_ajax_'+curr_loader+' pf_ajax_loading"><i class="prdctfltr-bars '+curr_loader+'"></i></span>');
				}
			}
			else {
				if ( curr.closest('.prdctfltr_wc').find('.prdctfltr_woocommerce_filter i').length > 0 && curr.closest('.prdctfltr_wc').find('.prdctfltr_woocommerce_filter img').length == 0 ) {
					curr.closest('.prdctfltr_wc').find('.prdctfltr_woocommerce_filter').addClass('pf_ajax_loading');
					curr.closest('.prdctfltr_wc').find('.prdctfltr_woocommerce_filter i').replaceWith('<img src="'+prdctfltr.url+'lib/images/svg-loaders/'+curr_loader+'.svg" class="prdctfltr_reset_this prdctfltr_loader" />');
				}
				else {
					curr.closest('.prdctfltr_wc').prepend('<div class="prdctfltr_added_loader"><img src="'+prdctfltr.url+'lib/images/svg-loaders/'+curr_loader+'.svg" class="prdctfltr_reset_this prdctfltr_loader" /></div>');
				}
			}
		}

	}

	function prdctfltr_reset_filters_550(obj) {

		checkAddInputs(obj);

		obj.find('.prdctfltr_filter input[type="hidden"]').each( function() {
			if ( prdctfltr.clearall[0] !== null) {
				if ( $.inArray( this.name, prdctfltr.clearall ) > -1 ) {
					if ( !$(this).val() ) {
						if ( $(this).attr('data-parent') ) {
							$(this).val($(this).attr('data-parent'));
						}
						else {
							$(this).remove();
						}
					}
				}
				else {
					if ( $(this).attr('data-parent') ) {
						$(this).val($(this).attr('data-parent'));
					}
					else {
						$(this).remove();
					}
				}
			}
			else {
				if ( $(this).attr('data-parent') ) {
					$(this).val($(this).attr('data-parent'));
				}
				else {
					$(this).remove();
				}
			}
		});

		obj.find('.prdctfltr_filter input.pf_search').val('').prop('disabled',true).attr('disabled','true');

		if ( obj.find('input[name="s"]').length>0 ) {
			obj.find('input[name="s"]').val('');
		}
		if ( obj.find('.prdctfltr_buttons input[name="sale_products"]').length>0 ) {
			obj.find('.prdctfltr_buttons input[name="sale_products"]').remove();
		}
		if ( obj.find('.prdctfltr_buttons input[name="instock_products"]').length>0 ) {
			obj.find('.prdctfltr_buttons input[name="instock_products"]').remove();
		}
		if ( obj.find('.prdctfltr_add_inputs input[name="orderby"]').length>0 ) {
			obj.find('.prdctfltr_add_inputs input[name="orderby"]').remove();
		}

		obj.find('input[name="reset_filter"]').remove();

	}

	function checkAddInputs(obj) {

		obj.find('.prdctfltr_attributes label.prdctfltr_active input[value]').each( function() {

			var eVal = $(this).val();
			var nVal = $(this).closest('.prdctfltr_attributes').attr('data-filter');

			$('.prdctfltr_wc .prdctfltr_add_inputs .pf_added_input[name="'+nVal+'"]').each(function(){
				if ( $(this).val().indexOf(eVal) > -1 ) {
					if ( $(this).val().indexOf(',') > -1 || $(this).val().indexOf('+') > -1 ) {
						$(this).val($(this).val().replace(',' + eVal, '').replace(eVal + ',', ''));
					}
					else {
						$(this).val('');
					}
					
				}
			});

			$.each(prdctfltr.js_filters, function(n18,obj43){
				if ( typeof obj43.adds !== 'undefined' && typeof obj43.adds[nVal] !== 'undefined' ) {
					delete prdctfltr.js_filters[n18].adds[nVal];
				}
			});

		});

	}

	function prdctfltr_remove_empty_inputs_550(obj) {

		obj.find('.prdctfltr_filter input[type="hidden"], .prdctfltr_filter input.pf_search, .prdctfltr_add_inputs input[type="hidden"]').each(function() { //, .prdctfltr_add_inputs input[type="hidden"]:not([name="post_type"])

			var curr_val = $(this).val();

			if ( curr_val == '' ) {
				if ( $(this).is(':visible') ) {
					$(this).prop('disabled',true).attr('disabled','true');
				}
				else {
					$(this).remove();
				}
			}

		});

	}

	function prdctfltr_remove_ranges_550(obj) {
		obj.find('.prdctfltr_filter.prdctfltr_range').each( function() {
			var curr_rng = $(this);
			if ( curr_rng.find('[name^="rng_min_"]').val() == undefined || curr_rng.find('[name^="rng_max_"]').val() == undefined ) {
				curr_rng.find('input').remove();
			}
		});
	}

	function prdctfltr_check_display_550(obj) {

		if ( $('body').hasClass('wc-prdctfltr-active') ) {

			if ( obj.attr('class').indexOf( 'pf_sidebar' ) == -1 ) {
				if ( obj.hasClass( 'pf_fullscreen' ) ) {
					obj.find('form').stop(true,true).fadeOut(200, function() {
						obj.find('.prdctfltr_close_sidebar').remove();
					});
				}
				else {
					if ( !obj.hasClass('prdctfltr_wc_widget') &&  !obj.hasClass('prdctfltr_always_visible') ) {
						obj.find('form').stop(true,true).slideUp(200);
					}
				}
			}
			else {
				obj.find('form').fadeOut(200);

				if ( obj.attr('class').indexOf( 'pf_sidebar_css' ) > 0 ) {
					if ( obj.hasClass('pf_sidebar_css_right') ) {
						$('body').css({'right':'0px','bottom':'auto','top':'auto','left':'auto'});
					}
					else {
						$('body').css({'right':'auto','bottom':'auto','top':'auto','left':'0px'});
					}
					$('.prdctfltr_overlay').remove();
				}
				obj.find('form').removeClass('prdctfltr_active');
				$('body').removeClass('wc-prdctfltr-active');

			}

		}

	}

	function prdctfltr_get_fields_550(obj) {

		var curr_fields = {};

		if ( obj.css('display') == 'none' ) {
			return curr_fields;
		}

		var lookAt = '.prdctfltr_filter input[type="hidden"], .prdctfltr_filter input.pf_search, .prdctfltr_add_inputs input[name="orderby"], .prdctfltr_add_inputs input[name="s"], .prdctfltr_add_inputs input.pf_added_input';

		obj.find(lookAt).each( function() {
			if ( $(this).val() !== '' ) {
				curr_fields[$(this).attr('name')] = $(this).val();
			}
		});

		if ( obj.find('.prdctfltr_buttons input[name="sale_products"]:checked').length > 0 ) {
			curr_fields.sale_products = 'on';
		}
		if ( obj.find('.prdctfltr_buttons input[name="instock_products"]:checked').length > 0 ) {
			curr_fields.instock_products = obj.find('.prdctfltr_buttons:first input[name="instock_products"]:checked').val();
		}

		if ( $.isEmptyObject( curr_fields ) === false ) {
			if ( prdctfltr.analytics == 'yes' ) {

				var analyticsData = {
					action: 'prdctfltr_analytics',
					pf_filters: curr_fields,
					pf_nonce: obj.attr('data-nonce')
				};

				$.ajax({
					type: 'POST',
					url: prdctfltr.ajax,
					data: analyticsData,
					success: function(response) {

					},
					error: function() {

					}
				});

			}
		}

		return curr_fields;

	}



	var infiniteWasReset = false;
	function after_ajax(curr_next) {

		function AAscrollHandler() {

			if( infiniteLoad.find('a.disabled').length == 0 && $(window).scrollTop()>=infiniteLoad.position().top-$(window).height()*0.8){
				infiniteLoad.find('a:not(.disabled)').trigger('click');
			}

		};

		$.each(curr_next, function(b,setView) {
			setView = $(setView);

			infiniteLoad = $('.prdctfltr-pagination-infinite-load');
			if ( infiniteLoad.length>0 ) {
				if ( infiniteLoad.find('.button.disabled').length>0 ) {

					scrollInterval = null;
					infiniteWasReset = true;
				}
				else {
					if ( infiniteWasReset ) {
						scrollInterval = setInterval(function() {
							if ( didScroll ) {
								didScroll = false;
								if ( ajaxActive !== false || historyActive !== false ) {
									return false;
								}
								AAscrollHandler();
							}
						}, 250);
					}
				}
			}


			if ( setView.hasClass('pf_after_ajax') ) {
				return false;
			}
			setView.addClass('pf_after_ajax');

			if ( setView.hasClass('pf_mod_masonry') ) {

				setView.find('.prdctfltr_woocommerce_ordering').show();
				setView.find('.prdctfltr_filter_inner').isotope({
					resizable: false,
					masonry: { }
				});
			}

			if ( setView.find('.prdctfltr_filter.prdctfltr_attributes.prdctfltr_expand_parents').length > 0 ) {
				prdctfltr_all_cats(setView);
			}
			else {
				prdctfltr_show_opened_cats(setView);
			}
			prdctfltr_init_scroll(setView);
			prdctfltr_init_tooltips(setView);
			reorder_selected(setView);
			reorder_adoptive(setView);
			set_select_index(setView);
			init_search(setView);
			init_ranges(setView);
			do_zindexes(setView);
			reorder_limit(setView);
			prdctfltr_tabbed_selection(setView);
			if ( $('body').hasClass('wc-prdctfltr-active') ) {
				$('body').removeClass('wc-prdctfltr-active');
			}

			if ( setView.hasClass('prdctfltr_step_filter') ) {
				if ( setView.find('.prdctfltr_woocommerce_filter_submit').length>0) {
					setView.find('.prdctfltr_woocommerce_filter_submit').remove();
				}
				setView.find('.prdctfltr_buttons').prepend('<a class="button prdctfltr_woocommerce_filter_submit pf_stopajax" href="#">'+(prdctfltr.js_filters[setView.attr('data-id')].button_text==''?prdctfltr.localization.getproducts:prdctfltr.js_filters[setView.attr('data-id')].button_text)+'</a>');
				setView.closest('prdctfltr_sc').addClass('prdctfltr_sc_step_filter');
			}

			prdctfltr_filter_terms_init(setView);

			get_category_mode(setView);
			prdctfltr_added_check(setView);
			prdctfltr_make_clears(setView);

			setView.find('.prdctfltr_filter').each( function() {
				check_selection_boxes($(this),'init');
			});

			setView.find('.prdctfltr_buttons:first label.prdctfltr_active').each(function() {
				check_buttons($(this),'init');
			});

			prdctfltr_show_opened_widgets(setView);

			if ( setView.hasClass('pf_mod_masonry') ) {
				setView.find('.prdctfltr_filter_inner').isotope('layout');
				if ( !setView.hasClass('prdctfltr_always_visible') ) {
					setView.find('.prdctfltr_woocommerce_ordering').hide();
				}
			}

		});
	}

	var pf_paged = 1;
	var pf_offset = 0;
	var pf_restrict = '';

	$(document).on('click', '.prdctfltr_sc_products.prdctfltr_ajax '+prdctfltr.ajax_pagination_class+' a, body.prdctfltr-ajax.prdctfltr-shop '+prdctfltr.ajax_pagination_class+' a, .prdctfltr-pagination-default a, .prdctfltr-pagination-load-more a', function() {

		if (ajaxActive===true) {
			return false;
		}

		ajaxActive = true;

		var loadMore = ( $(this).closest('.prdctfltr-pagination-load-more').length > 0 ? true : false );
		var curr_link = $(this);

		var shortcodeAjax = false;
		var checkShortcode = curr_link.closest('.prdctfltr_sc_products');

		if ( archiveAjax===false && checkShortcode.length > 0 && checkShortcode.hasClass('prdctfltr_ajax') ) {
			shortcodeAjax = true;
			var obj = checkShortcode.find('form:first');
		}
		else {
			var obj = $('div:not(.prdctfltr_sc_products) .prdctfltr_wc:not(.prdctfltr_step_filter):first form');
		}

		var curr_href = curr_link.attr('href');

		if ( loadMore === true ) {
			$(this).closest('.prdctfltr-pagination-load-more').addClass('prdctfltr-ignite');
			if ( shortcodeAjax===false ) {
				pf_offset = parseInt( $(prdctfltr.ajax_class).find(prdctfltr.ajax_product_class).length, 10 );
			}
			else {
				pf_offset = parseInt( checkShortcode.find(prdctfltr.ajax_product_class).length, 10 );
			}
		}
		else {
			if ( curr_href.indexOf('paged=') >= 0 ) {
				pf_paged = parseInt( curr_href.getValueByKey('paged'), 10 );
			}
			else {
				var arrUrl = curr_href.split('/'+prdctfltr.page_rewrite+'/');
				if ( typeof arrUrl[1] !== 'undefined' ) {
					if ( arrUrl[1].indexOf('/')>0 ) {
						arrUrl[1] = arrUrl[1].substr( 0, arrUrl[1].indexOf('/') );
					}
					pf_paged =  parseInt( arrUrl[1], 10 );
				}
			}
		}

		pf_restrict = 'pagination';

		ajaxActive = false;
		prdctfltr_respond_550(obj);

		return false;

	});

	function get_shortcode(id) {
		var wrf = {};
		if ( typeof prdctfltr.pagefilters[id].wcsc !== 'undefined' && prdctfltr.pagefilters[id].wcsc === true ) {
			wrf = prdctfltr.pagefilters[id].atts;
		}
		$.each( prdctfltr.pagefilters, function(i,o) {
			if ( i !== id ) {
				if ( typeof prdctfltr.pagefilters[i].wcsc !== 'undefined' && prdctfltr.pagefilters[i].wcsc === true ) {
					wrf = prdctfltr.pagefilters[i].atts;
				}
			}
		} );
		return wrf;
	}


	function prdctfltr_respond_550(curr) {

		if (ajaxActive===true) {
			return false;
		}

		ajaxActive = true;

		var curr_filter = curr.closest('.prdctfltr_wc');

		var ourObj = prdctfltr_get_obj_580(curr_filter);
		var pf_length = prdctfltr_count_obj_580(ourObj);
		var or_length = pf_length;

		if ( !curr.closest('.prdctfltr_wc').hasClass('prdctfltr_step_filter') && archiveAjax === true ) {
			$(prdctfltr.ajax_class+':first').fadeTo(200,0.5).addClass('prdctfltr_faded');
		}

		if ( prodcutsWrapper !== false ) {
			prodcutsWrapper.fadeTo(200,0.5).addClass('prdctfltr_faded');
		}

		if ( stopAjax === true ) {
			shortcodeAjax = false;
			archiveAjax = false;
			stopAjax = false;
		}

		var curr_fields = {};
		var requested_filters = {};

		$.each( ourObj, function(i, obj) {

			obj=$(obj);

			if ( obj.find('input[name="reset_filter"]:checked').length > 0 ) {
				prdctfltr_reset_filters_550(obj);
			}
			else {
				prdctfltr_remove_empty_inputs_550(obj);
			}

			prdctfltr_get_loader(obj);

			var pf_id = obj.attr('data-id');

			prdctfltr_remove_ranges_550(obj);

			prdctfltr_check_display_550(obj);

			if ( !obj.hasClass('prdctfltr_mobile') ) {
				requested_filters[pf_id] = pf_id;
			}

			if ( !--pf_length ) {

				$.each( ourObj, function(i, obj1) {
					curr_fields[$(obj1).attr('data-id')] = prdctfltr_get_fields_550(obj1);
				});

				if (archiveAjax===true||shortcodeAjax===true) {

					var pf_set = 'archive';
					if ( archiveAjax===true && !$('body').hasClass('prdctfltr-shop') ) {
						pf_set = 'shortcode';
					}
					else {
						pf_set = ( archiveAjax === true ? 'archive' : 'shortcode' );
					}

					var data = {
						action: 'prdctfltr_respond_550',
						pf_url: location.protocol + '//' + location.host + location.pathname,
						pf_request: prdctfltr.js_filters,
						pf_requested: requested_filters,
						pf_shortcode: prdctfltr.js_filters[pf_id].atts,
						pf_filters: curr_fields,
						pf_set: pf_set,
						pf_id: pf_id,
						pf_paged: pf_paged,
						pf_pagefilters: prdctfltr.pagefilters,
						pf_restrict: pf_restrict
					};

					if ( $('.prdctfltr_wc_widget').length > 0 ) {

						var widget = $('.prdctfltr_wc_widget:first');

						var rpl = $('<div></div>').append(widget.find('.prdctfltr_filter:first').children(':not(input):first').clone()).html().toString().replace(/\t/g, '');
						var rpl_off = $('<div></div>').append(widget.find('.prdctfltr_filter:first').children(':not(input):first').find('.prdctfltr_widget_title').clone()).html().toString().replace(/\t/g, '');

						rpl = rpl.replace(rpl_off, '%%%');

						rpl = rpl.replace('<div class="pf-help-title">', '');
						rpl = rpl.substr(0,rpl.length-6);

						data.pf_widget_title = $.trim(rpl);

					}

					if ( typeof obj.attr('data-lang') !== 'undefined' ) {
						data.lang = obj.attr('data-lang');
					}

					if ( pf_offset>0 ) {
						data.pf_offset = pf_offset;
					}

					if ( $(prdctfltr.ajax_orderby_class).length>0 ) {
						data.pf_orderby_template = 'set';
					}

					if ( $(prdctfltr.ajax_count_class).length>0 ) {
						data.pf_count_template = 'set';
					}

					if ( or_length==1 && obj.hasClass('prdctfltr_step_filter') ) {
						data.pf_step = 1;
						data.pf_set = 'shortcode';
					}

					if ( pf_set == 'shortcode' ) {
						if ( prdctfltr.active_sc !== '' ) {
							data.pf_active = prdctfltr.active_sc;
						}
					}

					curr_filter.find('.pf_added_input').each(function() {
						if ( typeof data.pf_adds == 'undefined' ) {
							data.pf_adds = {};
						}
						data.pf_adds[$(this).attr('name')] = $(this).val();
					});

					$.ajax({
						type: 'POST',
						url: prdctfltr.ajax,
						data: data,
						success: function(response) {
							if (response) {
								if ( pf_offset>0 ) {
									response.offset = pf_offset;
								}
								var getElement = shortcodeAjax === true ? prodcutsWrapper : false;
								prdctfltr_handle_response_580(response, archiveAjax, shortcodeAjax, getElement);
							}
						},
						error: function(response) {
							alert('Error!');
						}
					});

				}
				else {

					obj.find('.prdctfltr_filter input[type="hidden"]:not([name="post_type"]), .prdctfltr_filter input[name="s"], .prdctfltr_filter input[name="sale_products"], .prdctfltr_filter input[name="instock_products"]').each(function () {
						obj.find('input[name="'+this.name+'"]:gt(0)').remove();
					});

					var cf_length = $.pfcount(curr_fields);

					if ( cf_length > 1 ) {
						var notEmpty = false;
						$.each( curr_fields, function(e1,w1) {
							$.each( w1, function( k02, s02 ) {
								notEmpty = true;
								if ( k02 != 's' && obj.find('input[name="'+k02+'"]').length == 0 ) {
									obj.find('.prdctfltr_add_inputs').append('<input type="hidden" name="'+k02+'" value="'+s02+'" class="pf_added_input" />');
								}
								else if ( k02 != 's' && obj.find('input[name="'+k02+'"]').length > 0 ) {
									obj.find('input[type="hidden"][name="'+k02+'"]').val(s02);
								}
								if ( k02 == 's' && obj.find('input[name="s"]').length == 0 ) {
									obj.find('.prdctfltr_add_inputs').append('<input type="hidden" name="s" value="'+s02+'" class="pf_added_input" />');
								}
							});
						});
					}

					if ( $('.prdctfltr_wc input[name="orderby"][value="'+prdctfltr.orderby+'"]').length > 0 ) {
						$('.prdctfltr_wc input[name="orderby"][value="'+prdctfltr.orderby+'"]').remove();
					}

					obj.find('.prdctfltr_woocommerce_ordering').submit();

				}

			}

		});

	}

	function u(e) {
		return typeof e == 'undefined' ? false : e;
	}

	function prdctfltr_handle_response_580(response, archiveAjax, shortcodeAjax, getElement) {

		var ajax_length = prdctfltr_count_obj_580(response);
		var ajaxRefresh = {};
		var query = '';

		for (var n in response) {
			if (response.hasOwnProperty(n)) {
				var obj2 = response[n];
				if ( n == 'products' ) {

					if ( !isStep ) {

						var obj3 = ( $(obj2).find(prdctfltr.ajax_class).length > 0 ? $(obj2).find(prdctfltr.ajax_class) : $(obj2) );

						if (archiveAjax===true) {
							var products = $(prdctfltr.ajax_class+':first');
						}
						else if ( shortcodeAjax===true ) {
							var products = getElement === false ? $(prdctfltr.ajax_class+':first') : getElement.find(prdctfltr.ajax_class);
						}
						else {
							var products = $(prdctfltr.ajax_class+':first');
						}

						if ( u(response.loop_start) && $(response.loop_start).find('.pl-loops').length>0 && products.find('.pl-loops').length>0 ) {
							products = products.find('.pl-loops:first');
						}

						if ( u(response.loop_start) && $(response.loop_start).find('.pl-loops').length>0 && products.is('.pl-loops') && products.data('isotope') ) {
							if ( typeof response.offset == 'undefined' ) {
								products.isotope( 'remove', products.data('isotope').element.children );
														}

							products.isotope( 'insert', obj3.find(prdctfltr.ajax_product_class) );
							var container = products;
							container.imagesLoaded( function() {
								products.isotope('layout');
								$('.prdctfltr_faded').fadeTo(200,1).removeClass('prdctfltr_faded');
							} );
						}
						else {
							if ( obj3.length<1 ) {
								products.empty();
								$('.prdctfltr_faded').fadeTo(200,1).removeClass('prdctfltr_faded');
							}
							else {
								if ( typeof response.offset == 'undefined' ) {
									if ( obj3.find(prdctfltr.ajax_product_class).length > 0 || obj3.find(prdctfltr.ajax_category_class).length > 0 ) {
										pf_animate_products( products, obj3, 'replace' );
										$('.prdctfltr_faded').fadeTo(200,1).removeClass('prdctfltr_faded');
										setTimeout( function() {
											pf_get_scroll(products, 0);
										}, 200 );
									}
									else {
										products.empty().append(obj3);
										$('.prdctfltr_faded').fadeTo(200,1).removeClass('prdctfltr_faded');
										setTimeout( function() {
											pf_get_scroll(products, 0);
										}, 200 );
									}
								}
								else {
									if ( obj3.find(prdctfltr.ajax_product_class).length > 0 || obj3.find(prdctfltr.ajax_category_class).length > 0 ) {
										pf_animate_products( products, obj3, historyActive===false?'append':'replace' );
										$('.prdctfltr_faded').fadeTo(200,1).removeClass('prdctfltr_faded');
										setTimeout( function() {
											pf_get_scroll(products, response.offset);
										}, 200 );
									}
									else {
										$('.prdctfltr_faded').fadeTo(200,1).removeClass('prdctfltr_faded');
									}
									response.products = $('<div></div>').append(products.clone().removeAttr('style').removeClass('prdctfltr_faded')).html();
								}
							}
						}
					}
				}
				else if ( n == 'pagination' ) {

					getElement === false ? $(prdctfltr.ajax_class+':first') : getElement.find(prdctfltr.ajax_class);

					if (archiveAjax===true&&$('body').hasClass('prdctfltr-shop')) {
						var pagination = ( prdctfltr.ajax_pagination_type=='default' ? $(prdctfltr.ajax_pagination_class) : $('.'+prdctfltr.ajax_pagination_type) );
					}
					else if ( shortcodeAjax===true ) {
						if ( getElement === false ) {
							getElement = $(prdctfltr.ajax_class+':first');
						}

						var pagination = getElement.find(prdctfltr.ajax_pagination_class);
						if ( pagination.length < 1 ) {
							pagination = getElement.find('.prdctfltr-pagination-default');
						}
						if ( pagination.length < 1 ) {
							pagination = getElement.find('.prdctfltr-pagination-load-more');
						}
						if ( pagination.length < 1 ) {
							pagination = $(prdctfltr.ajax_pagination_class);
						}
					}
					else if ( shortcodeAjax===false ) {
						var pagination = $(prdctfltr.ajax_pagination_class);
						if ( pagination.length < 1 ) {
							pagination = $('.prdctfltr-pagination-default');
						}
						if ( pagination.length < 1 ) {
							pagination = $('.prdctfltr-pagination-load-more');
						}
					}

					if ( !isStep && typeof products !== 'undefined' && products.find(prdctfltr.ajax_product_class).length>0 ) {

						obj2 = $(obj2);

						if ( obj2 !== '' ) {
							if ( pagination.length < 1 ) {
								if ( $('.pf_pagination_dummy').length == 0 )  {
									if ( shortcodeAjax===true ) {
										getElement.find(prdctfltr.ajax_class+':first').after('<div class="pf_pagination_dummy"></div>');
									}
									else {
										$(prdctfltr.ajax_class+':first').after('<div class="pf_pagination_dummy"></div>');
									}
								}

								pagination = $('.pf_pagination_dummy');
							}
						}

						if ( obj2.length<1 ) {
							pagination.empty();
						}
						else {
							pagination.replaceWith(obj2);
						}

					}
					else {
						pagination.empty();
					}
				}
				else if ( n == 'ranges' ) {
					obj2 = $(obj2);
					prdctfltr.rangefilters = obj2[0];
				}
				else if ( n == 'orderby' ) {
					obj2 = $(obj2);
					$(prdctfltr.ajax_orderby_class).replaceWith(obj2);
				}
				else if ( n == 'count' ) {
					obj2 = $(obj2);
					if ( obj2.length<1 ) {
						$(prdctfltr.ajax_count_class).html(prdctfltr.localization.noproducts);
					}
					else {
						$(prdctfltr.ajax_count_class).replaceWith(obj2);
					}
				}
				else if ( n == 'query' ) {
					if ( prdctfltr.permalinks !== 'yes'  ) {
						query = ( obj2 == '' ? location.protocol + '//' + location.host + location.pathname : obj2 );
					}
					else {
						query = location.protocol + '//' + location.host + location.pathname;
					}
				}
				else if ( n.substring(0, 9) == 'prdctfltr' ){
					obj2 = $(obj2);

					if ( obj2.hasClass('prdctfltr_wc') ) {
						if ( pf_offset>0&&$(response.products).find(prdctfltr.ajax_product_class).length>0 || pf_offset==0 ) {
							if ( $('.prdctfltr_wc[data-id="'+n+'"]').length > 0 ) {
								$('.prdctfltr_wc[data-id="'+n+'"]').replaceWith(obj2);
								ajaxRefresh[n] = n;
							}
						}
						else {
							$('.prdctfltr_wc[data-id="'+n+'"]').find('.prdctfltr_woocommerce_filter').replaceWith(obj2.find('.prdctfltr_woocommerce_filter'));
						}
					}
					else if ( obj2.hasClass('prdctfltr-widget') ) {
						if ( $('.prdctfltr_wc[data-id="'+n+'"]').length>0 ) {
							if ( $('.prdctfltr_wc[data-id="'+n+'"] + .prdctfltr_mobile').length>0 ) {
								obj2.addClass('prdctfltr_mobile_widget').attr('data-mobile', $('.prdctfltr_wc[data-id="'+n+'"] + .prdctfltr_mobile').attr('data-id'));
							}
							$('.prdctfltr_wc[data-id="'+n+'"]').closest('.prdctfltr-widget').replaceWith(obj2);
							ajaxRefresh[n] = n;
						}
					}
				}
				else if ( n == 'title' && obj2 !== '' ) {
					if ( $('h1.page-title').length>0 ) {
						$('h1.page-title').replaceWith(obj2);
					}
				}
				else if ( n == 'desc' ) {
					if ( pf_paged<2 && obj2 !== '' ) {
						if ( $('div.term-description').length>0 ) {
							$('div.term-description').replaceWith(obj2);
						}
						else if ( $('div.page-description').length>0 ) {
							$('div.page-description').replaceWith(obj2);
						}
						else if ( $('h1.page-title').length>0 ) {
							$('h1.page-title').after(obj2);
						}
					}
					else {
						if ( $('div.term-description').length>0 ) {
							$('div.term-description').html('');
						}
						if ( $('div.page-description').length>0 ) {
							$('div.page-description').html('');
						}
					}
				}

			}

			if ( !--ajax_length ) {

				$('.prdctfltr_mobile + .prdctfltr_mobile').each( function() {
					$(this).prev().attr('data-id', $(this).attr('data-id'));
					$(this).remove();
				});

				$('.prdctfltr_mobile_widget').each( function() {
					$(this).find('.prdctfltr_mobile').attr('data-id', $(this).attr('data-mobile'));
					$(this).removeClass('prdctfltr_mobile_widget').removeAttr('data-mobile');
				});

				if ( !$.isEmptyObject( ajaxRefresh ) ) {
					$.each(ajaxRefresh, function(m,obj4) {
						after_ajax($('.prdctfltr_wc[data-id="'+m+'"]'));
						if ( $('.prdctfltr_wc[data-id="'+m+'"]').next().is('.prdctfltr_mobile') ) {
							after_ajax($('.prdctfltr_wc[data-id="'+m+'"]').next());
						}
					});
				}

				$(document.body).trigger( 'post-load' );
				if ( prdctfltr.js !== '' ) {
					eval(prdctfltr.js);
				}

				if ( historyActive === false && ( archiveAjax || $('body').hasClass('prdctfltr-sc') ) === true /*&& pf_offset == 0*/ ) {
					if ( query.indexOf('https:') > -1 && location.protocol != 'https:' ) {
						query = query.replace('https:','http:');
					}
					else if ( query.indexOf('http:') > -1 && location.protocol != 'http:' ) {
						query = query.replace('http:','https:');
					}

					if ( pf_offset>0 ) {
						query += query.indexOf('?')>-1 ? '&offset='+pf_offset : '?offset='+pf_offset;
					}

					var historyId = guid();

					makeHistory[historyId] = response;
					history.pushState({filters:historyId, archiveAjax:archiveAjax, shortcodeAjax:shortcodeAjax}, document.title, query);
				}

				ajaxActive = false;
				pf_paged = 1;
				pf_offset = 0;
				pf_restrict = '';

			}

		}

	}

	var historyActive = false;

	if ( archiveAjax === true || $('body').hasClass('prdctfltr-sc') ) {

		window.addEventListener('popstate', function(e) {
			if ( ajaxActive === false && historyActive === false ) {
				historyActive = true;
				ajaxActive = true;
				var state = typeof history.state != 'undefined' ? history.state : null;
				if ( state != null ) {
					if ( typeof state.filters !== 'undefined' ) {
						prdctfltr_handle_response_580(makeHistory[state.filters], state.archiveAjax, state.shortcodeAjax, false);
					}
					else if ( typeof pageFilters !== 'undefined' ) {
						prdctfltr_handle_response_580(pageFilters, ( $('body').hasClass('prdctfltr-ajax') || $('body').hasClass('prdctfltr-sc') ? true : false ), false, false);
					}
				}
				setTimeout( function() {
					historyActive = false;
				}, 500 );
			}
		});
	}


	$(window).load( function() {
		$('.pf_mod_masonry .prdctfltr_filter_inner').each( function() {
			$(this).isotope('layout');
		});
	});

	if ( $('.prdctfltr-widget').length == 0 || $('.prdctfltr-widget .prdctfltr_error').length == 1 ) {

		$(window).on('resize', function() {

			$('.prdctfltr_woocommerce').each( function() {

				var curr = $(this);
		
				if ( curr.hasClass('pf_mod_row') ) {

					if ( window.matchMedia('(max-width: 768px)').matches ) {
						curr.find('.prdctfltr_filter_inner').css('width', 'auto');
					}
					else {
						var curr_columns = curr.find('.prdctfltr_filter_wrapper:first').attr('data-columns');

						var curr_scroll_column = curr.find('.prdctfltr_woocommerce_ordering').width();
						var curr_columns_length = curr.find('.prdctfltr_filter').length;

						curr.find('.prdctfltr_filter_inner').css('width', curr_columns_length*curr_scroll_column/curr_columns);
						curr.find('.prdctfltr_filter').css('width', curr_scroll_column/curr_columns);
					}
				}
			});
		});
	}

	if ((/Trident\/7\./).test(navigator.userAgent)) {
		$(document).on('click', '.prdctfltr_checkboxes label img', function() {
			$(this).parents('label').children('input:first').change().click();
		});
	}

	if ((/Trident\/4\./).test(navigator.userAgent)) {
		$(document).on('click', '.prdctfltr_checkboxes label > span > img, .prdctfltr_checkboxes label > span', function() {
			$(this).parents('label').children('input:first').change().click();
		});
	}

	function prdctfltr_filter_results(currThis,list,searchIn,curr_filter) {
		var filter = currThis.val();

		if(filter) {
			var curr = currThis.closest('.prdctfltr_filter');
			if ( curr.find('div.prdctfltr_sub').length > 0 ) {
				$(list).find(".prdctfltr_sub:not(:visible)").css({'margin-left':0}).show().prev().addClass('prdctfltr_clicked');
				if ( curr.hasClass('prdctfltr_searching') === false ) {
					curr.addClass('prdctfltr_searching');
				}
			}
			$(list).find(searchIn+" > span:not(:Contains(" + filter + "))").closest('label').attr('style', 'display:none !important');
			$(list).find(searchIn+" > span:Contains(" + filter + ")").closest('label').show();
			curr.find('.pf_more').hide();
		}
		else {
			var curr = currThis.closest('.prdctfltr_filter');
			if ( curr.find('div.prdctfltr_sub').length > 0 ) {
				$(list).find(".prdctfltr_sub:visible").css({'margin-left':'22px'}).hide().prev().removeClass('prdctfltr_clicked');
			}
			curr.removeClass('prdctfltr_searching');
			$(list).find(searchIn+" > span").closest('label').show();

			var checkboxes = curr.find('.prdctfltr_checkboxes');

			checkboxes.each(function(){
				var max = parseInt(curr.attr('data-limit'));
				if (max != 0 && currThis.find(searchIn).length > max+1) {
					currThis.find(searchIn+':gt('+max+')').attr('style', 'display:none !important');
					currThis.find(".pf_more").html('<span>'+prdctfltr.localization.show_more+'</span>').removeClass('pf_activated');
				}
			});
			curr.find('.pf_more').show();
		}

		if ( curr_filter.hasClass('pf_mod_masonry') ) {
			curr_filter.find('.prdctfltr_filter_inner').isotope('layout');
		}
		if ( currThis.closest('.prdctfltr_filter').hasClass('prdctfltr_expand_parents') ) {
			prdctfltr_all_cats(currThis.closest('.prdctfltr_filter'));
		}

		return false;
	}

	function prdctfltr_filter_terms_init(curr) {
		curr = ( curr == null ? $('.prdctfltr_woocommerce') : curr );

		curr.each( function() {
			var curr_el = $(this);
			if ( curr_el.hasClass('prdctfltr_search_fields') ) {
				curr_el.find('.prdctfltr_filter.prdctfltr_attributes .prdctfltr_add_scroll, .prdctfltr_filter.prdctfltr_vendor .prdctfltr_add_scroll, .prdctfltr_filter.prdctfltr_meta .prdctfltr_add_scroll').each( function() {
					var curr_list = $(this);
					prdctfltr_filter_terms(curr_list);
				});
			}
		});

	}

	function prdctfltr_filter_terms(list) {

		var curr_filter = list.closest('.prdctfltr_wc');
		var form = $("<div>").attr({"class":"prdctfltr_search_terms","action":"#"}),
		input = $("<input>").attr({"class":"prdctfltr_search_terms_input prdctfltr_reset_this","type":"text","placeholder":prdctfltr.localization.filter_terms});
		

		if ( curr_filter.hasClass('pf_select') || curr_filter.hasClass('pf_default_select') || list.closest('.prdctfltr_filter').hasClass('prdctfltr_terms_customized_select') ) {
			$(form).append("<i class='prdctfltr-search'></i>").append(input).prependTo(list);
		}
		else {
			$(form).append("<i class='prdctfltr-search'></i>").append(input).insertBefore(list);
		}

		if ( curr_filter.hasClass('pf_adptv_default') ) {
			var searchIn = 'label:not(.pf_adoptive_hide)';
		}
		else {
			var searchIn = 'label';
		}

		var timeoutId = 0;

		$(input)
		.change( function () {

			var filter = $(this);

			clearTimeout(timeoutId);
			timeoutId = setTimeout(function() {prdctfltr_filter_results(filter,list,searchIn,curr_filter);}, 500);

		})
		.keyup( function () {
			$(this).change();
		});

	}

	$(document).on('click', '.prdctfltr_sc_products '+prdctfltr.ajax_class+' '+prdctfltr.ajax_category_class+' a, .prdctfltr-shop.prdctfltr-ajax '+prdctfltr.ajax_class+' '+prdctfltr.ajax_category_class+' a', function() {

		var curr = $(this).closest(prdctfltr.ajax_category_class);

		var curr_sc = ( curr.closest('.prdctfltr_sc_products:not(.prdctfltr_sc_step_filter)').length > 0 ? curr.closest('.prdctfltr_sc_products:not(.prdctfltr_sc_step_filter)') : $('.prdctfltr_sc_products:not(.prdctfltr_sc_step_filter):first').length > 0 ? $('.prdctfltr_sc_products:not(.prdctfltr_sc_step_filter):first') : $('.prdctfltr_woocommerce:not(.prdctfltr_step_filter):first').length > 0 ? $('.prdctfltr_woocommerce:not(.prdctfltr_step_filter):first') : 'none' );

		if ( curr_sc == 'none' ) {
			return;
		}

		if ( curr_sc.hasClass('prdctfltr_sc_products') ) {
			var curr_filter = ( curr_sc.find('.prdctfltr_woocommerce:not(.prdctfltr_step_filter)').length > 0 ? curr_sc.find('.prdctfltr_woocommerce:not(.prdctfltr_step_filter):not(.prdctfltr_mobile)') : $('.prdctfltr-widget').find('.prdctfltr_woocommerce:not(.prdctfltr_mobile)') );
		}
		else if ( $('.prdctfltr_sc_products:not(.prdctfltr_sc_step_filter)').length == 0 ) {
			var curr_filter = curr_sc;
		}
		else {
			return;
		}

		var cat = curr.find('.prdctfltr_cat_support').data('slug');

		var hasFilter = curr_filter.find('.prdctfltr_filter[data-filter="product_cat"] input[type="checkbox"][value="'+cat+'"]:first');

		if ( hasFilter.length > 0 ) {
			ajaxActive = true;
			$.each( curr_filter.find('.prdctfltr_filter[data-filter="product_cat"] label.prdctfltr_active'), function() {
				$(this).trigger('click');
			} );
			setTimeout( function() {
				ajaxActive = false;
				hasFilter.closest('label').trigger('click');
				if ( !curr_filter.hasClass('prdctfltr_click_filter') ) {
					curr_filter.find('.prdctfltr_woocommerce_filter_submit').trigger('click');
				}
			}, 25 );
		}
		else {
			var hasField = curr_filter.find('.prdctfltr_filter[data-filter="product_cat"]');

			if ( hasField.length > 0 ) {
				hasField.find('input[name="product_cat"]').val(cat);
			}
			else {
				var append = $('<input name="product_cat" type="hidden" value="'+cat+'" />');
				curr_filter.find('.prdctfltr_add_inputs').append(append);
			}

			if ( !curr_filter.hasClass('prdctfltr_click_filter') ) {
				curr_filter.find('.prdctfltr_woocommerce_filter_submit').trigger('click');
			}
			else {
				prdctfltr_respond_550(curr_filter.find('form'));
			}
		}

		return false;

	});

	if ( $('body').hasClass('prdctfltr-ajax') ) {
		if ( $('body.prdctfltr-ajax '+prdctfltr.ajax_orderby_class).length>0 ) {

			$(document).on('submit', 'body.prdctfltr-ajax '+prdctfltr.ajax_orderby_class, function() {
				return false;
			});

			$(document).on('change', 'body.prdctfltr-ajax '+prdctfltr.ajax_orderby_class+' select', function() {

				var orderVal = $(this).val();
				$('.prdctfltr_wc form').each(function(){
					if ( $(this).closest('.prdctfltr_sc_products').length==0 ) {
						if ( $(this).find('.prdctfltr_orderby input[type="checkbox"][val="'+orderVal+'"]').length>0 ) {
							$(this).find('.prdctfltr_orderby input[type="checkbox"][val="'+orderVal+'"]').trigger('click');
						}
						else {
							$(this).find('.prdctfltr_add_inputs').append('<input name="orderby" value="'+orderVal+'" />');
							prdctfltr_respond_550($(this));
						}
					}

				});

			});

		}

	}

	function pf_get_scroll( products, offset ) {

		var objOffset = -1;

		if ( products.length==0 ) {
			objOffset = $('.prdctfltr_wc:first').offset().top;
		}
		else {
			if ( offset>0 ) {

				var thisWrap = ( products.find(prdctfltr.ajax_product_class+':gt('+(offset-1)+')').length>0 ? products.find(prdctfltr.ajax_product_class+':gt('+(offset-1)+')') : products.find(prdctfltr.ajax_product_class+':last') );

				objOffset = thisWrap.offset().top;

			}
			else {
				if ( prdctfltr.ajax_scroll == 'products' ) {
					objOffset = ( products.find(prdctfltr.ajax_product_class+':first').length>0 ? products.find(prdctfltr.ajax_product_class+':first').offset().top : products.offset().top );
				}
				else if ( prdctfltr.ajax_scroll == 'top' ) {
					objOffset = 0;
				}
				else if ( prdctfltr.ajax_scroll == 'filter' ) {
					if ( products.closest('.prdctfltr_sc_products').find('.prdctfltr_wc').length>0 ) {
						objOffset = products.closest('.prdctfltr_sc_products').find('.prdctfltr_wc').offset().top;
					}
					else {
						objOffset = $('.prdctfltr_wc:first').offset().top;
					}
				}
			}
		}

		if ( objOffset > -1 ) {
			scrollTo(parseInt(objOffset, 10));
		}

	}

	function pf_animate_products( products, obj2, type ) {
		var beforeLength = products.find(prdctfltr.ajax_product_class).length;
		var newProducts = obj2.find(prdctfltr.ajax_product_class);

		if ( type == 'replace' ) {
			products.find(prdctfltr.ajax_product_class).remove();
			products.find('.woocommerce-info').remove()
			products.find('.prdctfltr-added-wrap').remove();
			var hasCats = obj2.find(prdctfltr.ajax_category_class);
			if ( hasCats.length>0 ) {
				products.find(prdctfltr.ajax_category_class).remove();
				products.append(hasCats);
			}
			else if ( products.find(prdctfltr.ajax_category_class).length>0 ) {
				products.find(prdctfltr.ajax_category_class).remove();
			}
		}

		if ( newProducts.length>0 ) {
			products.append(newProducts);

			var addedProducts = ( type == 'replace' || historyActive === true ? products.find(prdctfltr.ajax_product_class) : products.find(prdctfltr.ajax_product_class+':gt('+beforeLength+')') );
			if ( typeof addedProducts !== 'undefined') {

				var dr = parseInt( prdctfltr.animation.duration, 10 );
				var dl = parseInt( prdctfltr.animation.delay, 10 );

				switch ( prdctfltr.ajax_animation ) {
					case 'slide':
						addedProducts.hide();
						addedProducts.each(function(i) {
							$(this).delay((i++) * dl).slideDown({duration: dr,easing: 'linear'});
						});
					break;
					case 'random':
						addedProducts.not('.pf_faded').css('opacity', '0');
						var interval = setInterval(function () {
							var $ds = addedProducts.not('.pf_faded');
							$ds.eq(Math.floor(Math.random() * $ds.length)).fadeTo(dr, 1).addClass('pf_faded');
							if ($ds.length == 1) {
								clearInterval(interval);
							}
						}, dl );
					break;
					case 'none':
					break;
					default:
						addedProducts.css('opacity', '0');
						addedProducts.each(function(i) {
							$(this).delay((i++) * dl).fadeTo(dr, 1);
						});
					break;
				}
			}
		}

	}

	function do_zindexes(curr) {
		curr = ( curr == null ? $('.prdctfltr_wc') : curr );

		curr.each( function() {
			if ( $(this).hasClass('pf_select')) {
				var objCount = $(this).find('.prdctfltr_filter');
			}
			else {
				var objCount = $(this).find('.prdctfltr_terms_customized_select');
			}
			

			var c = objCount.length;
			objCount.css('z-index', function(i) {
				return c - i + 10;
			});

		});
	}
	do_zindexes();

	function prdctfltr_show_opened_widgets() {

		if ( $('.prdctfltr-widget').length > 0 && $('.prdctfltr-widget .prdctfltr_error').length !== 1 ) {
			$('.prdctfltr-widget .prdctfltr_filter').each( function() {

				var curr = $(this);

				if ( curr.find('input[type="checkbox"]:checked').length > 0 ) {

					curr.find('.prdctfltr_widget_title .prdctfltr-down').removeClass('prdctfltr-down').addClass('prdctfltr-up');
					curr.find('.prdctfltr_add_scroll').addClass('prdctfltr_down').css({'display':'block'});

				}
				else if ( curr.find('input[type="hidden"]:first').length == 1 && curr.find('input[type="hidden"]:first').val() !== '' ) {

					curr.find('.prdctfltr_widget_title .prdctfltr-down').removeClass('prdctfltr-down').addClass('prdctfltr-up');
					curr.find('.prdctfltr_add_scroll').addClass('prdctfltr_down').css({'display':'block'});

				}
				else if ( curr.find('input[type="text"]').length > 0 && curr.find('input[type="text"]').val() !== '' ) {

					curr.find('.prdctfltr_widget_title .prdctfltr-down').removeClass('prdctfltr-down').addClass('prdctfltr-up');
					curr.find('.prdctfltr_add_scroll').addClass('prdctfltr_down').css({'display':'block'});

				}

			});
		}

	}
	prdctfltr_show_opened_widgets();


	function prdctfltr_tabbed_selection(curr) {
		curr = ( curr == null ? $('.prdctfltr_wc') : curr );

		curr.each( function() {
			if ( $(this).hasClass('prdctfltr_tabbed_selection') ) {

				$(this).find('label.prdctfltr_ft_,label.prdctfltr_ft_none').each( function() {
					$(this).remove();
				});

				var checkLength = $(this).find('.prdctfltr_filter').length;
				var checkObj = $(this);
				$(this).find('.prdctfltr_filter').each( function() {
					if ( $(this).find('input[type="hidden"]:first').length > 0 && $(this).find('input[type="hidden"]').val() !== '' ) {
						$(this).addClass('prdctfltr_has_selection');
					}
					if ( $(this).find('input[type="text"]:first').length > 0 && $(this).find('input[type="text"]').val() !== '' ) {
						$(this).addClass('prdctfltr_has_selection');
					}
					if ( !--checkLength ) {
						var newLength = checkObj.find('.prdctfltr_has_selection').length;
						var count = 0;
						checkObj.find('.prdctfltr_has_selection').each( function() {
							count++;
							if ( newLength !== count ) {
								//checkObj.find('a[data-key="'+$(this).attr('data-filter')+'"]').remove();
							}
						});
					}
				});

			}
		});
	}
	prdctfltr_tabbed_selection();

	$.pfcount = function (array) {
		if(array.length) {
			return array.length;
		}
		else {
			var length = 0;
			for ( var p in array ){
				if(array.hasOwnProperty(p)) length++;
			}
			return length;
		}
	};

	function check_shortcode_search() {
		var wg = $('.prdctfltr_wc_widget');
		var sc = $('.prdctfltr_sc_products:not(.prdctfltr_sc_step_filter)');
		if ( wg.length>0 && sc.length>0 ) {
			wg.each( function() {
				$(this).find('input[name="s"]').each( function() {
					$(this).attr('name', 'search_products');
				});
				if ( !$(this).hasClass('prdctfltr_mobile') ) {
					var id = $(this).attr('data-id');
					if ( typeof prdctfltr.pagefilters[id] == 'undefined' ) {
						var done = false;
						$.each( prdctfltr.pagefilters, function(i,e) {
							if ( !done ) {
								if ( typeof e.wcsc !== 'undefined' ) {
									prdctfltr.pagefilters[id] = e;
									done = true;
								}
								if ( typeof e.wc !== 'undefined' && typeof e.query_vars.show_products !== 'undefined' && e.query_vars.show_products == 'yes' ) {
									prdctfltr.pagefilters[id] = e;
									done = true;
								}
							}
						} );
					}
				}
			} );
		}
	}
	check_shortcode_search();

	var infiniteLoad = $('.prdctfltr-pagination-infinite-load');

	function fixScroll() {
		didScroll = true;
	}

	function scrollHandler() {

		if( infiniteLoad.find('a.disabled').length == 0 && $(window).scrollTop()>=infiniteLoad.position().top-$(window).height()*0.8){
			infiniteLoad.find('a:not(.disabled)').trigger('click');
		}

	};

	if ( infiniteLoad.length>0 ) {

		var scrollTimeout;

		$(window).on({
			'scroll': fixScroll
		});

		var didScroll = false; 

		var scrollInterval = setInterval(function() {
			if ( didScroll ) {
				didScroll = false;
				if ( ajaxActive !== false || historyActive !== false ) {
					return false;
				}
				scrollHandler();
			}
		}, 250);

	}

	function scrollTo(to) {
		to = to>-1?to-130:0;
		var start = $(window).scrollTop(),
			duration = parseInt((Math.abs(to-start)+1000)/7.5, 10),
			change = to - start,
			currentTime = 0,
			increment = 20; 
		var animateScroll = function() {
			currentTime += increment;
			var val = Math.easeInOutQuad(currentTime, start, change, duration);
			window.scrollTo(0,val);
			
			if(currentTime < duration) {
				setTimeout(animateScroll, increment);
			}
		};
		animateScroll();
	}

	Math.easeInOutQuad = function (t, b, c, d) {
		t /= d/2;
		if (t < 1) return c/2*t*t + b;
		t--;
		return -c/2 * (t*(t-2) - 1) + b;
	};

})(jQuery);