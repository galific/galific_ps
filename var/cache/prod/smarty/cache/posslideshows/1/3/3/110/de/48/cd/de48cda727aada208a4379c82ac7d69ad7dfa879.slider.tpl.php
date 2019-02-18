<?php
/* Smarty version 3.1.33, created on 2019-02-18 00:19:13
  from '/var/www/html/modules/posslideshows/views/templates/hook/slider.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c69aca9d0ee45_68524376',
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
function content_5c69aca9d0ee45_68524376 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="slideshow_container">
	<div class="pos-slideshow">
		<div class="flexslider ma-nivoslider">
			<div class="pos-loading"></div>
			<div id="pos-slideshow-home" class="slides">
				
													<a href="https://galific.com/21-gifts-toys" title="Grab 10% Off" ><img style="display:none" src="https://galific.com/modules/posslideshows/images/b2e914df284d82b6b6e508ca967370399a2c60bb_wide-banner.jpg"  data-thumb="https://galific.com/modules/posslideshows/images/b2e914df284d82b6b6e508ca967370399a2c60bb_wide-banner.jpg"  alt="" title="#htmlcaption6"  /> </a>
			   					<a href="https://galific.com" title="Great Deal" ><img style="display:none" src="https://galific.com/modules/posslideshows/images/a336c4ebddc4837d2684a96094f1acfd7dc3b4a5_wide-banner2.jpg"  data-thumb="https://galific.com/modules/posslideshows/images/a336c4ebddc4837d2684a96094f1acfd7dc3b4a5_wide-banner2.jpg"  alt="" title="#htmlcaption7"  /> </a>
			   			</div>
												<div id="htmlcaption6" class="pos-slideshow-caption nivo-html-caption nivo-caption">					
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
				 					<div id="htmlcaption7" class="pos-slideshow-caption nivo-html-caption nivo-caption">					
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
