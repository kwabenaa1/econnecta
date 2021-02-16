/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
(function($, window, document) {
  "use strict";
  sessionStorage.clear();
  
  window.updateQuixPageData = function() {
      var f = window.frames.quixframe;
      var value = f.getQuixPageData();

      jQuery('#jform_data').val(value);
      return true;
  };

  window.updateResponsiveDeviceCookie = function(device) {
      Cookies.set('qx-device', device);
      return true;
  }

  window.adjustModalSize = function() {
    var qxModalSize = sessionStorage.getItem('qx-modal-size');
    if(qxModalSize == null || qxModalSize == 'false' || qxModalSize == 'collapsed')
    {
      sessionStorage.setItem('qx-modal-size', 'expanded');
      jQuery('.qxui-modal-wrap.qxui-modal--with-tab .qxui-modal').css('width', '700px');
      jQuery('.qxui-modal-wrap.qxui-modal--with-tab .qxui-modal-body').css('height', '70vh');
    }else{
      sessionStorage.setItem('qx-modal-size', 'collapsed');
      jQuery('.qxui-modal-wrap.qxui-modal--with-tab .qxui-modal').css('width', '500px');
      jQuery('.qxui-modal-wrap.qxui-modal--with-tab .qxui-modal-body').css('height', '350px');
    }
    
    return true;
  }

  window.getResponsiveDeviceCookie = function(device) {
      var qxDevice = Cookies.get('qx-device', 'desktop');
      return (typeof qxDevice == 'undefined' ? 'desktop' : qxDevice);
  }

  // Update body class
  window.updateBodyDeviceClass = function(currentDevice) {
      var device = Cookies.get('qx-device'),
        iframeWrapper = jQuery('.qx-fb-frame-preview'),
        iframe = jQuery('#quix-iframe-wrapper'),
        deviceSize = {
          desktop : jQuery(window).width(),
          tablet : '769px',
          mobile : '480px'
        }
      iframeWrapper.attr('data-preview', device);
      // Resize iframe
      iframeWrapper.css({
        'width': deviceSize[device]
      });

      return;
  }

  window.ajaxPageSaveQuix = function() {
    $.when( function(){
      var url = Joomla.getOptions('system.paths').root + '/index.php?option=com_quix&task=page.save';
      var data = $('#adminForm').serialize();
      console.log(data);
      $.ajax({
        data, url, type: 'POST',
        success: function (res) {
          res = JSON.parse(res);

          if (!res.success) {
            return reject(res);
          }

          return resolve(res);
        }
      });
      //TODO:: previous comment
    }() ).done(function(response) {
      console.log(response);
    });
  }


  $(function() {
    
      var currentDevice = 'desktop';

      // Update browser class based on cookie device name
      window.updateBodyDeviceClass(currentDevice);

      // add class
      jQuery('html').addClass('com_quix layout-edit view-form');
      jQuery('body').removeClass('modal');

      // trigger qx-sortable
      // jQuery('.qx-sortable').live('mouseover', function(e){
      //   qxUIkit.sortable(jQuery('.qx-sortable'));
      // });

  });

  // Summernote editor
  window.loadEditor = function (selector, callback, content) {
    jQuery(selector).summernote({
      dialogsInBody: true,
      callbacks: {
        onChange: callback
      },
      onCreateLink: function (sLinkUrl) {
        if (sLinkUrl.indexOf('@') !== -1 && sLinkUrl.indexOf(':') === -1) {
          sLinkUrl =  'mailto:' + sLinkUrl;
        }
        
        return sLinkUrl;
      },
      height: 120
    });

    setTimeout(function () { 
        jQuery(selector).summernote('code', content);
    }, 100);
  }

  window.destroyEditor = function (selector) {
    jQuery(selector).summernote('destroy');
  }

  window.getEditorValue = function (selector) {
    return jQuery(selector).summernote('code');
  }

  jQuery(window).load(function(){
    setTimeout(function(){
      window.parent.jQuery('.preloader').hide();
    }, 3000);
    setTimeout(function(){
      if(typeof jQuery().tooltip != 'function'){
        var inlineScript = document.createElement('script');
        inlineScript.src = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js';
        document.head.appendChild(inlineScript);
        
        var inlineStyle=document.createElement('link');
        inlineStyle.rel='stylesheet';
        inlineStyle.href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css';
        document.head.appendChild(inlineStyle);
      }
    }, 2000)

    jQuery(".preloader").delay(1000000000).fadeOut('slow');

  });
})(window.jQuery, window, document);
// The global jQuery object is passed as a parameter

// loadHeartBitApi
var quixHeartBeatApi = {
  init: function(url) {
    var quixHeartBeat = setInterval(function() {
      jQuery.ajax({
        url: url,
        success: function(result) { // success
          try {
            data = JSON.parse(result);
            if (data.success === true) {
              return true;
            } else {
              $content = '<h3 class="qx-alert qx-alert-warning">Your session has expired!</h3>';
              $content = $content + '<p>Due to Joomla! session timeout, you can not save your page at this point. To avoid this, increase your session timeout from <strong>Global Configuration</strong> page. Now, You have to reload the page and login again.</p>';
              qxUIkit.modal.alert($content);

              clearTimeout(quixHeartBeat);
            }
          } catch (err) {
            console.warn("An error occured: " + err.message);
          }
        },
        error: function(xhr) { // error
          console.warn("An error occured: " + xhr.status + " " + xhr.statusText);
        }
      });
    }, 1000 * 30 * 1); // every minute  
  }
};

// helpscout
! function(e, t, n) {
  function a() {
    var e = t.getElementsByTagName("script")[0],
      n = t.createElement("script");
    n.type = "text/javascript", n.async = !0, n.src = "https://beacon-v2.helpscout.net", e.parentNode.insertBefore(n,
      e)
  }
  if (e.Beacon = n = function(t, n, a) {
      e.Beacon.readyQueue.push({
        method: t,
        options: n,
        data: a
      })
    }, n.readyQueue = [], "complete" === t.readyState) return a();
  e.attachEvent ? e.attachEvent("onload", a) : e.addEventListener("load", a, !1)
}(window, document, window.Beacon || function() {});