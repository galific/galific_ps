/**
 * @license
 */
(function() {
	$.fn.tinyTips = function (supCont, supClass) {
		var tipName = 'lightTip';
		var tipFrame = '<div class="' + tipName + '"><div class="content"></div><div class="bottom"></div></div>';

		var animSpeed = 200;

		var tinyTip;
		var tText;

		$(this).hover(function(){

		        $('body').append(tipFrame);
		        var divTip = 'div.' + tipName;
				if (supClass != ''){
					$(divTip).addClass(supClass);
				}
                tinyTip = $(divTip);
                tinyTip.hide();

                if (supCont === 'data-title') {
                    var tipCont = $(this).data('title');
                } else if (supCont !== 'data-title') {
                    var tipCont = supCont;
                }
                $('.'+tipName + ' .content').html(tipCont);
                tText = $(this).data('title');
                $(this).data('title', '');

                var yOffset = tinyTip.height() + 2;
                var xOffset = (tinyTip.width() / 2) - ($(this).width() / 2);

                var pos = $(this).offset();
                var nPos = pos;

                nPos.top = pos.top - yOffset;
                nPos.left = pos.left - xOffset;

                tinyTip.css('position', 'absolute').css('z-index', '1000');
                tinyTip.css(nPos).fadeIn(animSpeed);


		}, function() {
            var divTip = 'div.' + tipName;
            if (supClass != ''){
                $(divTip).addClass(supClass);
            }
            tinyTip = $(divTip);
			$(this).data('title', tText);
			tinyTip.fadeOut(animSpeed, function() {
				$(this).remove();
			});

		});
	}

	
})();