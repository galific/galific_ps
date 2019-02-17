<?php
/* Smarty version 3.1.33, created on 2019-02-17 14:26:38
  from 'C:\wamp64\www\galific\modules\posslideshows\views\templates\hook\slider.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c6921c662e704_38204150',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fc1b96f2459df72abfbd852304cd029623ddee45' => 
    array (
      0 => 'C:\\wamp64\\www\\galific\\modules\\posslideshows\\views\\templates\\hook\\slider.tpl',
      1 => 1550393263,
      2 => 'file',
    ),
  ),
  'cache_lifetime' => 31536000,
),true)) {
function content_5c6921c662e704_38204150 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="slideshow_container">
	<div class="pos-slideshow">
		<div class="flexslider ma-nivoslider">
			<div class="pos-loading"></div>
			<div id="pos-slideshow-home" class="slides">
				
													<a href="http://www.posthemes.com" title="Sample 1" ><img style="display:none" src="http://localhost/galific/modules/posslideshows/images/sample-1.jpg"  data-thumb="http://localhost/galific/modules/posslideshows/images/sample-1.jpg"  alt="" title="#htmlcaption1"  /> </a>
			   					<a href="http://www.posthemes.com" title="Sample 2" ><img style="display:none" src="http://localhost/galific/modules/posslideshows/images/sample-2.jpg"  data-thumb="http://localhost/galific/modules/posslideshows/images/sample-2.jpg"  alt="" title="#htmlcaption2"  /> </a>
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
														<div class="banner7-des"><div class="container"><div class="info desc1">
					<p class="title1">Kitchen</p>
					<p class="title2">Convenience</p>
					<p class="title3">Utensils, Crockery, Storage and Wooden Accessories</p>
					<p class="readmore"><a href="#"><span>Shop now</span></a></p>
					</div></div> </div>
												</div>
				 					<div id="htmlcaption2" class="pos-slideshow-caption nivo-html-caption nivo-caption">					
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
														<div class="banner7-des"><div class="container"><div class="info desc2">
					<p class="title1">Best Blenders</p>
					<p class="title2">in Australia!</p>
					<p class="title3">As rated by consumers on Product Review</p>
					<p class="readmore"><a href="#"><span>Shop now</span></a></p>
					</div></div> </div>
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
