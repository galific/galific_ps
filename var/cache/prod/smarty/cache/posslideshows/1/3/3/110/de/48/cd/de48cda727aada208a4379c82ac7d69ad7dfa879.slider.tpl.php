<?php
/* Smarty version 3.1.33, created on 2019-02-13 11:10:16
  from '/var/www/html/modules/posslideshows/views/templates/hook/slider.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c63adc0cbc572_09130685',
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
function content_5c63adc0cbc572_09130685 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="slideshow_container">
	<div class="pos-slideshow">
		<div class="flexslider ma-nivoslider">
			<div class="pos-loading"></div>
			<div id="pos-slideshow-home" class="slides">
				
													<a href="https://galific.com/" title="Personalized Gifts " ><img style="display:none" src="https://galific.com/modules/posslideshows/images/87d6ad8da5287f263ede71dca1b8a4ed29a4a830_banner-galific4.jpg"  data-thumb="https://galific.com/modules/posslideshows/images/87d6ad8da5287f263ede71dca1b8a4ed29a4a830_banner-galific4.jpg"  alt="" title="#htmlcaption1"  /> </a>
			   					<a href="https://galific.com" title="Valentine Sale Offer" ><img style="display:none" src="https://galific.com/modules/posslideshows/images/8aa8f4b814d881a4aab40b2292196d8d817c0c44_Valentine Sale galific.jpg"  data-thumb="https://galific.com/modules/posslideshows/images/8aa8f4b814d881a4aab40b2292196d8d817c0c44_Valentine Sale galific.jpg"  alt="" title="#htmlcaption3"  /> </a>
			   			</div>
												<div id="htmlcaption1" class="pos-slideshow-caption nivo-html-caption nivo-caption">					
							<div class="timethai" style=" 
								position:absolute;
								top:0;
								left:0;
								z-index:8;
								background-color: rgba(49, 56, 72, 0.298);
								height:5px;
								-webkit-animation: myfirst 5000ms ease-in-out;
								-moz-animation: myfirst 5000ms ease-in-out;
								-ms-animation: myfirst 5000ms ease-in-out;
								animation: myfirst 5000ms ease-in-out;
							
							">
							</div>
														<div class="banner7-des"><div class="container"><!--<div class="info desc1">
<p class="title1">Gift for Her</p>
<p class="title2">Gift for Him</p>
<p class="title3">Wooden Craft, T-Shirts, Soft Toys, Crafts</p>
<p class="readmore"><a href="#"><span>Shop now</span></a></p>
</div>--></div> </div>
												</div>
				 					<div id="htmlcaption3" class="pos-slideshow-caption nivo-html-caption nivo-caption">					
							<div class="timethai" style=" 
								position:absolute;
								top:0;
								left:0;
								z-index:8;
								background-color: rgba(49, 56, 72, 0.298);
								height:5px;
								-webkit-animation: myfirst 5000ms ease-in-out;
								-moz-animation: myfirst 5000ms ease-in-out;
								-ms-animation: myfirst 5000ms ease-in-out;
								animation: myfirst 5000ms ease-in-out;
							
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
