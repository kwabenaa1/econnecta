<?php
if( !defined('QX_ELEMENT_V2_VIDEO') ) {
  define('QX_ELEMENT_V2_VIDEO', true);
  // JFactory::getDocument()->addStylesheet(QUIX_URL."/assets/css/plyr.css");
  // JFactory::getDocument()->addScript(QUIX_URL."/assets/js/plyr.js");

  Assets::Css('video-element', QUIX_URL."/assets/css/plyr.css");
  Assets::Js('video-element', QUIX_URL."/assets/js/plyr.js");
}
 