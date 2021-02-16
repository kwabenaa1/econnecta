/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
(function ($, window, document) {
	"use strict";

	jQuery(function () {
		// Turn off link click
		jQuery('body').on('click', 'a', function(e){
	       	if(!jQuery(e.target).closest(".seo-snippet.qxui-drawer").length){
				e.stopPropagation();
				e.preventDefault();
				return false;
	       	}
		});
		// turn off form submit
		jQuery("form").on("submit", function (e) {
			e.stopPropagation();
			e.preventDefault();
			return false;
		});

		jQuery('#pageoption-tab-wrapper a').click(function (e) {
			e.preventDefault()
			jQuery(this).tab('show')
		})

		// updateAjax make ajax request on the background if we need to;
		if (!window.quix) {
			window.parent.jQuery(".preloader").remove();
			// window.parent.jQuery('#quixBuilderLoadingFailed').modal();
			qxUIkit.modal('#quixBuilderLoadingFailed').show();
		}

		axios({
			method: 'get',
			url: `${quix.url}/index.php?option=com_quix&task=updateAjax`
		});


		jQuery('html').addClass('quix-builder');
		jQuery('body').addClass('com_quix view-form layout-iframe').removeClass('modal');

	    // disable button click
	    jQuery('body').on('mouseover', '.qx-element-wrap', function(e){
	        jQuery(this).find('button, a').attr("onclick", "false"); 
	    });

	  	// trigger qx-element-gallery__wrapper 
	    jQuery('body').on('mouseover', '.qx-element-gallery__wrapper', function(e){
	    	jQuery(".qx-element-gallery__wrapper a").each(function(){
		        jQuery(this).attr('href', jQuery(this).attr('data-href'));
		    });
	    });
	  	
	  	// trigger column resizer position
	    jQuery('body').on('mouseover', '.qx-fb-columns', function(e){
	    	jQuery(".qx-col-wrap").each(function(){
	    		var currrentMargin = parseInt(jQuery(this).css('margin-right').length);
	    		if(currrentMargin){
	    			var newMargin = parseInt(jQuery(this).css('margin-right')) - 1;
		        	jQuery(this).find('.react-resizable-handle')
		        				.css('margin-right', '-' + newMargin + 'px');
	    		}
		    });
	    });

	    // fix filemanager modal tab content position
	    jQuery('body').on('mouseover', '.fm-modal.qxui-modal--with-tab', function(e){
	    	jQuery(this).find('.qxui-tabs-content').addClass('qxui-tabs-bottom-content');
	    });
	  	
	});

})(window.jQuery, window, document);