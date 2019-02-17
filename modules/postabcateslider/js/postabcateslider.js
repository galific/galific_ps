$(document).ready(function() {
	var $tabcateSlideConf = $('.tab-category-container-slider');
	var items       = parseInt($tabcateSlideConf.attr('data-items'));
	var speed     	= parseInt($tabcateSlideConf.attr('data-speed'));
	var autoPlay    = parseInt($tabcateSlideConf.attr('data-autoplay'));
	var time    	= parseInt($tabcateSlideConf.attr('data-time'));
	var arrow       = parseInt($tabcateSlideConf.attr('data-arrow'));
	var pagination  = parseInt($tabcateSlideConf.attr('data-pagination'));
	var move        = parseInt($tabcateSlideConf.attr('data-move'));
	var pausehover  = parseInt($tabcateSlideConf.attr('data-pausehover'));
	var md          = parseInt($tabcateSlideConf.attr('data-md'));
	var sm          = parseInt($tabcateSlideConf.attr('data-sm'));
	var xs          = parseInt($tabcateSlideConf.attr('data-xs'));
	var xxs         = parseInt($tabcateSlideConf.attr('data-xxs'));
	
	if(autoPlay==1) {
		if(time){
			autoPlay = time;
		}else{
			autoPlay = '3000';
		}
	}else{
		autoPlay = false;
	}
	if(pausehover){pausehover = true}else{pausehover=false}
	if(move){move = false}else{move=true}
	if(arrow){arrow =true}else{arrow=false}
	if(pagination==1){pagination = true}else{pagination=false}

	var tabcateSlide = $(".tab-category-container-slider .productTabCategorySlider");
	tabcateSlide.owlCarousel({
		autoplay : autoPlay ,
		smartSpeed: speed,
		autoplayHoverPause: pausehover,
		addClassActive: true,
		scrollPerPage: move,
		nav : arrow,
		dots : pagination,
		responsiveClass:true,		
		responsive:{
			0:{
				items:xxs,
			},
			480:{
				items:xs,
			},
			768:{
				items:sm,
				nav:false,
			},
			992:{
				items:md,
			},
			1200:{
				items:items,
			}
		}
	});
	
	$(".tab_category").hide();
	$(".tab_category:first").show(); 

	$("ul.tab_cates li").click(function() {
		$("ul.tab_cates li").removeClass("active");
		$(this).addClass("active");
		$(".tab_category").hide();
		var activeTab = $(this).attr("rel"); 
		$("#"+activeTab).fadeIn().addClass("animatetab");  
	});
});

