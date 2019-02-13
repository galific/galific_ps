$(document).ready(function() {
	var $recommendedproductconf = $('.recommended-product');
	var items       = parseInt($recommendedproductconf.attr('data-items'));
	var lazyload    = parseInt($recommendedproductconf.attr('data-lazyload'));
	var speed     	= parseInt($recommendedproductconf.attr('data-speed'));
	var autoPlay    = parseInt($recommendedproductconf.attr('data-autoplay'));
	var time    	= parseInt($recommendedproductconf.attr('data-time'));
	var arrow       = parseInt($recommendedproductconf.attr('data-arrow'));
	var pagination  = parseInt($recommendedproductconf.attr('data-pagination'));
	var move        = parseInt($recommendedproductconf.attr('data-move'));
	var pausehover  = parseInt($recommendedproductconf.attr('data-pausehover'));
	var md          = parseInt($recommendedproductconf.attr('data-md'));
	var sm          = parseInt($recommendedproductconf.attr('data-sm'));
	var xs          = parseInt($recommendedproductconf.attr('data-xs'));
	var xxs         = parseInt($recommendedproductconf.attr('data-xxs'));
	
	if(autoPlay==1) {
		if(time){
			autoPlay = time;
		}else{
			autoPlay = '3000';
		}
	}else{
		autoPlay = false;
	}
	if(lazyload){lazyload = true}else{lazyload=false}
	if(pausehover){pausehover = true}else{pausehover=false}
	if(move){move = false}else{move=true}
	if(arrow){arrow =true}else{arrow=false}
	if(pagination==1){pagination = true}else{pagination=false}

	var recommendedproductslide = $(".recommended-product .recommendedproductslide");
	recommendedproductslide.owlCarousel({
		lazyLoad: lazyload,
		autoplay : autoPlay ,
		smartSpeed: speed,
		autoplayHoverPause: pausehover,
		addClassActive: true,
		scrollPerPage: move,
		nav : arrow,
		dots : pagination,
		responsive : {
		    0 : {
		        items : xxs,
		    },
		    480 : {
		        items : xs,
		    },
		    768 : {
		        items : sm,
		    },
		    992 : {
		        items : md,
		    },
		    1200 : {
		        items : items,
		    }
		}
	});
	var recommendedproductslide_position = $(".recommended-product-position .recommendedproductslide_position");
	recommendedproductslide_position.owlCarousel({
		lazyLoad: lazyload,
		autoplay : autoPlay ,
		smartSpeed: speed,
		autoplayHoverPause: pausehover,
		addClassActive: true,
		scrollPerPage: move,
		nav : arrow,
		dots : pagination,
		responsive : {
		    0 : {
		        items : 1,
		    },
		    480 : {
		        items : 1,
		    },
		    768 : {
		        items : 2,
		    },
		    992 : {
		        items : 3,
		    },
		    1200 : {
		        items : 3,
		    }
		}
	});

});

