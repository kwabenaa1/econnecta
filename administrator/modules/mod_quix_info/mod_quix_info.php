<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_sampledata
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
// Include dependencies.
JLoader::register('ModQuixInfoHelper', __DIR__ . '/helper.php');

$isProAuthinticated = ModQuixInfoHelper::isProAuthinticated();
$isPro = ModQuixInfoHelper::isPro();
$authorise = ModQuixInfoHelper::isProAuthinticated();
$jchOptimized = true; //ModQuixInfoHelper::fixJCH();

if($isPro && $authorise){ // && !$jchOptimized
	return;
}

require JModuleHelper::getLayoutPath('mod_quix_info', $params->get('layout', 'default'));
