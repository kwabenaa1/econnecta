<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<?php echo QuixHelper::randerSysMessage(); ?>
<?php echo QuixHelper::getPHPWarning(); ?>
<?php echo QuixHelper::getFreeWarning(); ?>
<?php echo QuixHelper::getUpdateStatus(); ?>
<?php echo QuixHelper::proActivationMessage(); ?>
<?php echo QuixHelper::askreview(); ?>
<?php echo QuixHelper::webpCheck(); ?>
