<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );


JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/rednao-isolated-jq.js' );
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/rnCommons.js' );
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/bootstrap/bootstrap.min.js' );
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/bootstrap/bootstrapUtils.js' );
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/bootstrap/spin.min.js' );
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/bootstrap/ladda.min.js' );

JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/css/bootstrap/bootstrap-theme.css' );
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/css/bootstrap/bootstrap-scopped.css' );
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/css/bootstrap/ladda-themeless.min.css' );
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/css/bootstrap/font-awesome.min.css' );


