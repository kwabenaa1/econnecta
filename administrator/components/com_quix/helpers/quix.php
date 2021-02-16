<?php

/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Quix helper
 *
 * @since  1.6
 */
class QuixHelper
{
    /**
     * Configure the Linkbar.
     *
     * @param   string  $vName  string
     *
     * @return void
     */
    public static function addSubmenu($vName = '')
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_QUIX_SIDEBAR_DASHBOARD'),
            'index.php?option=com_quix&view=dashboard',
            $vName == 'dashboard'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_QUIX_SIDEBAR_PAGES'),
            'index.php?option=com_quix&view=pages',
            $vName == 'pages'
        );

        // JHtmlSidebar::addEntry(
        // 	JText::_('JCATEGORIES'),
        // 	'index.php?option=com_categories&view=categories&extension=com_quix',
        // 	$vName == 'categories'
        // );

        JHtmlSidebar::addEntry(
            JText::_('COM_QUIX_SIDEBAR_COLLECTIONS'),
            'index.php?option=com_quix&view=collections',
            $vName == 'collections'
        );

        // JHtmlSidebar::addEntry(
        //     JText::_('COM_QUIX_SIDEBAR_THEMES'),
        //     'index.php?option=com_quix&view=themes',
        //     $vName == 'themes'
        // );

        JHtmlSidebar::addEntry(
            JText::_('COM_QUIX_SIDEBAR_INTEGRATIONS'),
            'index.php?option=com_quix&view=integrations',
            $vName == 'integrations'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_QUIX_SIDEBAR_ELEMENTS_MANAGER'),
            'index.php?option=com_quix&view=elements',
            $vName == 'elements'
        );

        // JHtmlSidebar::addEntry(
        // 	JText::_('COM_QUIX_SIDEBAR_FILEMANAGER_MANAGER'),
        // 	'index.php?option=com_quix&view=filemanager',
        // 	$vName == 'filemanager'
        // );
        if (self::isFreeQuix()) {
            JHtmlSidebar::addEntry(
                JText::_('COM_QUIX_SIDEBAR_QUIXRANK'),
                'index.php?option=com_quix&view=rank',
                $vName == 'rank'
            );
        }

        if (self::isFreeQuix()) {
            JHtmlSidebar::addEntry(
                JText::_('COM_QUIX_SIDEBAR_OPTIMIZE'),
                'index.php?option=com_quix&view=optimize',
                $vName == 'optimize'
            );
        }
        if (self::isFreeQuix()) {
            JHtmlSidebar::addEntry(
                JText::_('COM_QUIX_SIDEBAR_AMP'),
                'index.php?option=com_quix&view=amp',
                $vName == 'amp'
            );
        }

        JHtmlSidebar::addEntry(
            JText::_('COM_QUIX_SIDEBAR_HELP'),
            'index.php?option=com_quix&view=help',
            $vName == 'help'
        );
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return    JObject
     *
     * @since    1.6
     */
    public static function getActions()
    {
        $user = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_quix';

        $actions = [
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        ];

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

    /**
    * Get group name using group ID
    * @param integer $group_id Usergroup ID
    * @return mixed group name if the group was found, null otherwise
    */
    public static function getGroupNameByGroupId($group_id)
    {
        static $groupNameByGroupId;
        // Function has already run
        if ($groupNameByGroupId !== null) {
            return $groupNameByGroupId;
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('title')
            ->from('#__usergroups')
            ->where('id = ' . intval($group_id));

        $db->setQuery($query);
        $groupNameByGroupId = $db->loadResult();

        return $groupNameByGroupId;
    }

    /*
    * to get update info
    * use layout to get alert structure
    */

    public static function getUpdateStatus()
    {
        $update = self::checkUpdate();
        // print_r($update);die;
        if (isset($update->update_id) && $update->update_id) {
            $credentials = self::hasCredentials();
            // Instantiate a new JLayoutFile instance and render the layout
            $layout = new JLayoutFile('toolbar.update');
            return $layout->render(['info' => $update, 'credentials' => $credentials]);
        }

        return;
    }

    /*
    * show warning
    * for free versions only
    */

    public static function getFreeWarning()
    {
        jimport('joomla.form.form');
        //##QUIX_CREATIONDATE##
        $form = simplexml_load_file(JPATH_COMPONENT_ADMINISTRATOR . '/quix.xml');
        if ($form->tag != 'pro') {
            JFactory::getDocument()->addScriptDeclaration('(function($){window.QuixVersion = "free";})(jQuery);');
            $layout = new JLayoutFile('toolbar.freenotice');
            return $layout->render([]);
        }

        return;
    }

    /*
    * show warning
    * for free versions only
    */

    public static function getBuyPro($medium = 'button', $source = 'joomla-admin')
    {
        jimport('joomla.form.form');
        //##QUIX_CREATIONDATE##
        $form = simplexml_load_file(JPATH_COMPONENT_ADMINISTRATOR . '/quix.xml');
        if ($form->tag != 'pro') {
            $layout = new JLayoutFile('toolbar.getpro');
            return $layout->render(['source' => $source, 'medium' => $medium]);
        }

        return;
    }

    public static function proActivationMessage()
    {
        $isFree = self::isFreeQuix();
        if ($isFree) {
            return;
        }

        $credentials = self::hasCredentials();
        if (empty($credentials) or empty($credentials->username) or empty($credentials->key)) {
            $layout = new JLayoutFile('toolbar.authorise');
            return $layout->render([]);
        }
    }

    public static function askreview()
    {
        $layout = new JLayoutFile('toolbar.askreview');
        return $layout->render([]);
    }

    public static function getAuthInfo()
    {
        $update = self::checkUpdate();
        // print_r($update);die;
        if (isset($update->update_id) && $update->update_id) {
            $credentials = self::hasCredentials();
            // Instantiate a new JLayoutFile instance and render the layout
            $layout = new JLayoutFile('toolbar.update');
            return $layout->render(['info' => $update, 'credentials' => $credentials]);
        }

        return;
    }

    /*
    * show pro elements
    * for free versions only
    */

    public static function getProElementBanner()
    {
        jimport('joomla.form.form');
        //##QUIX_CREATIONDATE##
        $form = simplexml_load_file(JPATH_COMPONENT_ADMINISTRATOR . '/quix.xml');
        if ($form->tag != 'pro') {
            $layout = new JLayoutFile('blocks.elements');
            return $layout->render([]);
        }

        return;
    }

    /*
    * Method isFreeQuix
    * @return boolian
    */

    public static function isFreeQuix()
    {
        jimport('joomla.form.form');
        $form = simplexml_load_file(JPATH_COMPONENT_ADMINISTRATOR . '/quix.xml');

        if ($form->tag != 'pro') { //free
            return true;
        }

        return false; // pro
    }

    /*
    * to get php warning
    * we require at least php 5.4
    */

    public static function getPHPWarning()
    {
        if (version_compare(phpversion(), '5.5', '<')) {
            // Instantiate a new JLayoutFile instance and render the layout
            $layout = new JLayoutFile('toolbar.phpwarning');
            return $layout->render([]);
        }

        return;
    }

    /*
    * check filemanager status
    * some special permission required for filemanager
    */

    public static function checkFileManager()
    {
        try {
            // Create an instance of a default JHttp object.
            $http = new JHttp();
            // Invoke the HEAD request.
            $response = $http->head(JUri::root() . 'media/quix/filemanager/index.php');

            // The response code is included in the "code" property.
            if ($response->code == 403) {
                // show warning or fix guide:
                // Instantiate a new JLayoutFile instance and render the layout
                $layout = new JLayoutFile('toolbar.filemanagerguide');
                return $layout->render([]);
            }
        } catch (Exception $e) {
            // nothing to show now, lets ignore
            return;
        }

        return;
    }

    /*
    * to get update info
    * use layout to get alert structure
    */

    public static function checkUpdate()
    {
        // Get a database object.
        $db = JFactory::getDbo();

        // get extensionid
        $query = $db->getQuery(true)
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where($db->quoteName('type') . ' = ' . $db->quote('package'))
                    ->where($db->quoteName('element') . ' = ' . $db->quote('pkg_quix'));

        $db->setQuery($query);

        $extensionid = $db->loadResult();

        // get update_site_id
        $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__updates')
                    ->where($db->quoteName('extension_id') . ' = ' . $db->quote($extensionid))
                    ->where($db->quoteName('element') . ' = ' . $db->quote('pkg_quix'))
                    ->where($db->quoteName('type') . ' = ' . $db->quote('package'));
        $db->setQuery($query);

        return $db->loadObject();
    }

    public static function hasCredentials()
    {
        $config = JModelLegacy::getInstance('Config', 'QuixModel', ['ignore_request' => false]);
        $config->generateState();
        return $config->getItem();
    }

    public static function randerSysMessage()
    {
        $layout = new JLayoutFile('toolbar.message');
        return $layout->render([]);
    }

    public static function webpCheck()
    {
        if (!function_exists('imagewebp')) {
            $layout = new JLayoutFile('toolbar.webp');
            return $layout->render([]);
        }
    }

    public static function getSystemInfo()
    {
        $info = [];
        $info['php_version'] = phpversion();
        $info['gd_info'] = function_exists('gd_info');
        $info['curl_support'] = extension_loaded('curl');
        $info['ctype_support'] = extension_loaded('ctype');
        $info['fileinfo'] = extension_loaded('fileinfo');
        $info['magicQuotes'] = get_magic_quotes_gpc();
        $info['memory_limit'] = ini_get('memory_limit');
        $info['postSize'] = ini_get('post_max_size');
        $info['max_execution'] = ini_get('max_execution_time');
        $info['allow_url_fopen'] = ini_get('allow_url_fopen');
        $info['cache_writable'] = is_writable(JPATH_CACHE);

        return $info;
    }

    public static function cleanCache()
    {
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.path');

        if (JFolder::exists(JPATH_ROOT . '/media/quix/css')) {
            $cssfiles = (array) JFolder::files(JPATH_ROOT . '/media/quix/css');
            array_map(
                function ($file) {
                    if ($file == 'index.html') {
                        return;
                    }
                    JFile::delete(JPATH_ROOT . '/media/quix/css/' . $file);
                },
                $cssfiles
            );
        }

        if (JFolder::exists(JPATH_ROOT . '/media/quix/js')) {
            $jsfiles = (array) JFolder::files(JPATH_ROOT . '/media/quix/js');
            array_map(
                function ($file) {
                    if ($file == 'index.html') {
                        return;
                    }
                    JFile::delete(JPATH_ROOT . '/media/quix/js/' . $file);
                },
                $jsfiles
            );
        }

        // Clear relavent cache
        QuixHelper::cachecleaner('com_quix');
        QuixHelper::cachecleaner('mod_quix');
    }

    public static function cachecleaner($group = 'com_quix', $client_id = 0)
    {
        $conf = \JFactory::getConfig();

        $options = [
            'defaultgroup' => $group,
            'cachebase' => $client_id ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'),
            'result' => true,
        ];

        try {
            /** @var \JCacheControllerCallback $cache */
            $cache = \JCache::getInstance('callback', $options);
            $cache->clean();
        } catch (\JCacheException $exception) {
            $options['result'] = false;
        }

        // Trigger the onContentCleanCache event.
        \JEventDispatcher::getInstance()->trigger('onContentCleanCache', $options);
    }

    /**
     * check and update icons list from server
     *
     * @return    void
     *
     * @since    2.0
     */
    public static function getUpdateIconsList()
    {
        // test profler
        // $profiler = new JProfiler();

        // checked_flaticon_quix, quix_flatIcon_latest
        $session = JFactory::getSession();

        // test enable
        // $latest = $session->set('quix_flatIcon_latest', false);
        $latest = $session->get('quix_flatIcon_latest', false);

        if (!$latest) {
            // do the operation
            // 1. get local hash from cache
            $cache = new JCache(['defaultgroup' => 'lib_quix', 'cachebase' => JPATH_SITE . DIRECTORY_SEPARATOR . 'cache']);
            $cacheid = 'quix_flaticons_hash';
            $cache->setCaching(true);
            $cache->setLifeTime(2592000);  //24 hours 86400// 30days 2592000//
            // $cache = self::getCache();

            // get localhash
            $localhash = $cache->get($cacheid);
            // print_r($localhash);die;

            // now match the hash and get latest file
            if (!$localhash or empty($localhash)) {
                // update fonts file
                self::updateFlatIcons();

                //get serverHash and update locals
                $localhash = self::getServerHashForIcon();
                $cache->store($localhash, $cacheid);

                // we have latest version
                $session->set('quix_flatIcon_latest', true);
            } else {
                // we have local hash already
                //get serverHash
                $serverHash = self::getServerHashForIcon();

                // get serverHash and verify
                if ($serverHash == $localhash) {
                    //setSession  update about quix_flatIcon_latest
                    // we have latest version
                    $session->set('quix_flatIcon_latest', true);

                    return true;
                } else {
                    // update fonts file
                    self::updateFlatIcons();

                    // updateHash local with server hash
                    $cache->store($serverHash, $cacheid);

                    // we have latest version
                    $session->set('quix_flatIcon_latest', true);
                }
            }
        }

        return true;
    }

    public static function updateFlatIcons()
    {
        // need to update
        // so, get the icons list from server
        $icons = QuixFrontendHelper::getFlatIconsJSONfromServer();

        // store them
        QuixFrontendHelper::saveOutputIconsJSON($icons);

        return true;
    }

    public static function getServerHashForIcon()
    {
        $config = JComponentHelper::getParams('com_quix');
        $api_https = $config->get('api_https', 1);

        // absolute url of list json
        $url = ($api_https ? 'https' : 'http') . '://getquix.net/index.php?option=com_quixblocks&view=flaticons&format=json&hash=true';

        $process = true;
        // Get the handler to download the blocks
        try {
            $http = new JHttp();
            $result = $http->get($url);

            if ($result->code != 200 && $result->code != 310) {
                $exception = new Exception(JText::_('COM_QUIX_SERVER_RESPONSE_ERROR'));
                echo new JResponseJSON($exception);
            }

            $json = json_decode($result->body);
            return $json->data;
        } catch (RuntimeException $e) {
            $exception = new Exception($e->getMessage());
            return new JResponseJSON($exception);
        }
    }

    public static function getUpdateGoogleFontsList()
    {
        $session = JFactory::getSession();
        $latest = $session->get('quix_googlefonts_latest', false);

        if (!$latest) {
            // do the operation
            // 1. get local hash from cache
            $cache = new JCache(['defaultgroup' => 'lib_quix', 'cachebase' => JPATH_SITE . DIRECTORY_SEPARATOR . 'cache']);
            $cacheid = 'quix_googlefonts';
            $cache->setCaching(true);
            $cache->setLifeTime(2592000);

            // get localhash
            $localdata = $cache->get($cacheid);
            $result = false;
            // now match the hash and get latest file
            if (!$localdata or empty($localdata)) {
                // update fonts file
                $result = QuixFrontendHelper::getGoogleFontsJSONfromServer();
            }

            // we have latest version
            $session->set('quix_googlefonts_latest', $result);
        }
        return true;
    }

    public static function verifyLicense()
    {
        $input = JFactory::getApplication()->input;
        $session = JFactory::getSession();
        $username = $input->get('username', '', 'string');
        $key = $input->get('key', '', 'string');
        $data = $input->get('data', '', 'string');
        $response = json_decode($data);
        if ($response === false or !$response->success) {
            return new Exception(JText::_('Unable to verify your license or your hosting provider has blocked outgoing connections. Details: ' . $response->message));
        }

        $validLicense = self::getValidLicense($response);
        // json_encode(['hasPro' => true, 'hasFree' => false, 'hasLicense' => true, 'name' => $proProduct, 'id' => $proID]);

        if (!$validLicense['hasLicense']) {
            return new Exception(JText::_('Your license is missing. Chances are, you\'ve entered wrong credentials or your order has expired.'));
        }

        if ($validLicense['hasLicense'] && $validLicense['hasPro']) {
            return JText::_('Your <strong>' . $validLicense['name'] . '</strong> license has activated for this site. Now you are eligible for automatic update and support for this website.');
        }

        if ($validLicense['hasLicense'] && $validLicense['hasFree']) {
            return new Exception(JText::_('Quix free license has activated for your site. Checkout <strong>Quix PRO</strong> and unlock the true magic of page building.'));
        }
    }

    public static function verifyLicense_backup()
    {
        $input = JFactory::getApplication()->input;
        $session = JFactory::getSession();
        $username = $input->get('username', '', 'string');
        $key = $input->get('key', '', 'string');

        // Verify the key
        $result = new stdClass();
        $response = self::verifyApiKey($username, $key);

        if ($response === false or !$response->success) {
            return new Exception(JText::_('Unable to verify your license or your hosting provider has blocked outgoing connections. Details: ' . $response->message));
        }

        $validLicense = self::getValidLicense($response);
        // print_r($validLicense);die;
        // json_encode(['hasPro' => true, 'hasFree' => false, 'hasLicense' => true, 'name' => $proProduct, 'id' => $proID]);

        if (!$validLicense['hasLicense']) {
            return new Exception(JText::_('Your license is missing. Chances are, you\'ve entered wrong credentials or your order has expired.'));
        }

        if ($validLicense['hasLicense'] && $validLicense['hasPro']) {
            return JText::_('Your <strong>' . $validLicense['name'] . '</strong> license has activated for this site. Now you are eligible for automatic update and support for this website.');
        }

        if ($validLicense['hasLicense'] && $validLicense['hasFree']) {
            return new Exception(JText::_('Quix free license has activated for your site. Checkout <strong>Quix PRO</strong> and unlock the true magic of page building.'));
        }
    }

    /**
     * Verifies the api key
     *
     * @since	2.1.0
     * @access	public
     */
    public static function verifyApiKey($username, $key)
    {
        $url = 'https://www.themexpert.com/index.php?option=com_digicom&task=responses&source=authapi&catid=38&username=' . $username . '&key=' . $key;

        $http = new JHttp();
        $str = $http->get($url);
        if ($str->code != 200 && $str->code != 310) {
            return false;
        }

        $result = json_decode($str->body);

        return $result;
    }

    public static function getValidLicense($data)
    {
        $products = $data->data;
        $quixPro = [116, 118, 127, 202, 220]; // agency, pro, extended license id[117 is free]
        $hasPro = false;
        $hasFree = false;
        $proProduct = '';
        $proID = 0;

        foreach ($products as $key => $product) {
            if (in_array($product->id, $quixPro) && ($product->has_access == 1)) {
                $hasPro = true;
                $proProduct = $product->name;
                $proID = $product->id;

                break;
            }

            if ($product->id == 117 && $product->has_access === true) {
                $hasFree = true;
            }
        }

        // now return result
        if ($hasPro) {
            return ['hasPro' => true, 'hasFree' => $hasFree, 'hasLicense' => true, 'name' => $proProduct, 'id' => $proID];
        } elseif ($hasFree) {
            return ['hasPro' => false, 'hasFree' => true, 'hasLicense' => true];
        } else {
            return ['hasPro' => false, 'hasFree' => false, 'hasLicense' => false];
        }
    }

    public static function isProActivated()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('`params`')
              ->from('#__quix_configs')
              ->where("`name` = 'activated'");
        $db->setQuery($query);
        $activated = $db->loadResult();

        return $activated;
    }

    public static function checkSystemPlugin()
    {
        $plugin = JPluginHelper::getPlugin('system', 'quix');
        if (isset($plugin->id) and $plugin->id) {
            return;
        }
        // set warning

        JFactory::getApplication()->enqueueMessage(JText::_('QUIX_SYSTEM_PLUGIN_MISSING_DESC'), JText::_('QUIX_SYSTEM_PLUGIN_MISSING_TITLE'));
    }

    public static function autoVerifyLicense()
    {
        $free = self::isFreeQuix();
        $pro = self::isProActivated();
        if (!$free and !empty($pro) and $pro != null and $pro) {
            // its activated aready, now recheck
            $credentials = self::hasCredentials();
        }

        return false;
    }
}
