/**
 * @name		Page Builder CK
 * @package		com_pagebuilderck
 * @copyright	Copyright (C) 2015. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */

jQuery(document).ready(function($){
	var hashtag = window.location.hash;
	$('.accordionsck').each(function() {
		var activetab = typeof($(this).attr('activetab')) != 'undefined' ? parseInt($(this).attr('activetab')) : false;
		$(this).accordionck({
			collapsible: true,
			active: activetab,
			heightStyle: "content",
			scrollToActive: false
		});
	});
	$('.tabsck').each(function() {
		var activetab = parseInt($(this).attr('activetab'));
		if (window.location.hash) {
			if (hashtag.substr(0, 5) == '#tab-') {
				var tabindex = 0;
				$('> ol > li a', $(this)).each(function() {
					if ('#tab-' + $(this).text().toLowerCase().replace(/ /g, '-') == decodeURI(hashtag)) {
						activetab = tabindex;
					}
					tabindex++;
				});
			}
		}
		$(this).tabsck({
			active: activetab
		});
		$('.ui-tabs-nav > li', $(this)).click(function() {
			$(window).trigger('resize');
		});
	});
	$('.blockck, .rowck').each(function() {
		var $this = $(this);
		$window = $(window);

		// function to be called whenever the window is scrolled or resized
		function update($this){
				var top = $this.offset().top;
				var pos = $window.scrollTop();
				var height = $this.height();
				var windowheight = $(window).height();

			// check if totally above or totally below viewport
			if (top + height < pos || top > pos + windowheight) {
				if (! $this.hasClass('noreplayck')) $this.removeClass('animateck');
				return;
			}
			// animate the content if found
			if (top < pos || top > pos + windowheight-30) {
				// is out of the screen
			} else {
				$this.addClass('animateck');
			}
		}
		update($this);
		$window.scroll(function() { update($this); });
	});
	$('.rowckfullwidth .rowckfullwidth').removeClass('rowckfullwidth'); // remove for imbricated rows
	$('.rowckfullwidth').each(function() {
		var $this = $(this);
		$window = $(window);
		$this.css('position', 'relative');
		$this.css('z-index', '1');
		function update($this){
			var w = $window.width();
			if ($this.offset().left != 0) $this.css('margin-left', '-=' + $this.offset().left);
			$this.css('width', w);
		}
		update($this);
		$window.on('resize load', function() { update($this); });
	});
});
