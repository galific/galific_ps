<?php
/* Smarty version 3.1.33, created on 2019-02-22 14:22:33
  from '/var/www/html/modules/posslideshows/views/templates/hook/slider.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c6fb851a8f983_73131811',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd219e5662980272fb219288b22a9d1a88d844715' => 
    array (
      0 => '/var/www/html/modules/posslideshows/views/templates/hook/slider.tpl',
      1 => 1548364870,
      2 => 'file',
    ),
  ),
  'cache_lifetime' => 31536000,
),true)) {
function content_5c6fb851a8f983_73131811 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="slideshow_container">
	<div class="pos-slideshow">
		<div class="flexslider ma-nivoslider">
			<div class="pos-loading"></div>
			<div id="pos-slideshow-home" class="slides">
				
													<a href="https://galific.com/31-90-personalized-printed-t-shirts-for-him-her-she-s-mine-i-m-his-t-shirt.html#/35-men_s_t_shirt_size-s/39-women_s_t_shirt_size-s" title="Couple Tshirts" ><img style="display:none" src="https://galific.com/modules/posslideshows/images/26b57b499250b9503616b93bf18329352d3361bd_tshirt-banner-couple.jpg"  data-thumb="https://galific.com/modules/posslideshows/images/26b57b499250b9503616b93bf18329352d3361bd_tshirt-banner-couple.jpg"  alt="" title="#htmlcaption6"  /> </a>
			   					<a href="https://galific.com/21-gifts-toys" title="Great Deal" ><img style="display:none" src="https://galific.com/modules/posslideshows/images/1d068d272ac8f9306be8692c14554b41c824a409_wide-banner2.jpg"  data-thumb="https://galific.com/modules/posslideshows/images/1d068d272ac8f9306be8692c14554b41c824a409_wide-banner2.jpg"  alt="" title="#htmlcaption7"  /> </a>
			   					<a href="https://galific.com/20-gadgets" title="Gadgets" ><img style="display:none" src="https://galific.com/modules/posslideshows/images/267c99875c44362f7ed32b05690051d42f91a14a_wide-banner3.jpg"  data-thumb="https://galific.com/modules/posslideshows/images/267c99875c44362f7ed32b05690051d42f91a14a_wide-banner3.jpg"  alt="" title="#htmlcaption9"  /> </a>
			   			</div>
												<div id="htmlcaption6" class="pos-slideshow-caption nivo-html-caption nivo-caption">					
							<div class="timethai" style=" 
								position:absolute;
								top:0;
								left:0;
								z-index:8;
								background-color: rgba(49, 56, 72, 0.298);
								height:5px;
								-webkit-animation: myfirst 3000ms ease-in-out;
								-moz-animation: myfirst 3000ms ease-in-out;
								-ms-animation: myfirst 3000ms ease-in-out;
								animation: myfirst 3000ms ease-in-out;
							
							">
							</div>
												</div>
				 					<div id="htmlcaption7" class="pos-slideshow-caption nivo-html-caption nivo-caption">					
							<div class="timethai" style=" 
								position:absolute;
								top:0;
								left:0;
								z-index:8;
								background-color: rgba(49, 56, 72, 0.298);
								height:5px;
								-webkit-animation: myfirst 3000ms ease-in-out;
								-moz-animation: myfirst 3000ms ease-in-out;
								-ms-animation: myfirst 3000ms ease-in-out;
								animation: myfirst 3000ms ease-in-out;
							
							">
							</div>
												</div>
				 					<div id="htmlcaption9" class="pos-slideshow-caption nivo-html-caption nivo-caption">					
							<div class="timethai" style=" 
								position:absolute;
								top:0;
								left:0;
								z-index:8;
								background-color: rgba(49, 56, 72, 0.298);
								height:5px;
								-webkit-animation: myfirst 3000ms ease-in-out;
								-moz-animation: myfirst 3000ms ease-in-out;
								-ms-animation: myfirst 3000ms ease-in-out;
								animation: myfirst 3000ms ease-in-out;
							
							">
							</div>
												</div>
				 			 		</div>
	</div>
</div>

 <script type="text/javascript">
 $(document).ready(function() {
	//Function to animate slider captions 
	function doAnimations( elems ) {
		//Cache the animationend event in a variable
		var animEndEv = 'webkitAnimationEnd animationend';
		
		elems.each(function () {
			var $this = $(this),
				$animationType = $this.data('animation');
			$this.addClass($animationType).one(animEndEv, function () {
				$this.removeClass($animationType);
			});
		});
	}
	//Variables on page load 
	var $myCarousel = $('.ma-nivoslider'),
		$firstAnimatingElems = $myCarousel.find('.nivo-caption').find("[data-animation ^= 'animated']");
	//Animate captions in first slide on page load 
	doAnimations($firstAnimatingElems);

});
</script><?php }
}
