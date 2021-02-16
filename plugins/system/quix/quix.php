<?php
/**
 * @package    Quix
 * @author    ThemeXpert http://www.themexpert.com
 * @copyright  Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license  GNU General Public License version 3 or later; see LICENSE.txt
 * @since    1.0.0
 */

defined('_JEXEC') or die;

JLoader::discover('QuixSiteHelper', JPATH_SITE . '/components/com_quix/helpers/');

// Include dependencies
jimport('quix.app.bootstrap');

global  $QuixBuilderType ;
if (empty($QuixBuilderType)) {
    $QuixBuilderType = 'frontend';
}
jimport('quix.app.init');

class plgSystemQuix extends JPlugin
{
    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    public $configs = null;
    protected $app;

    public function __construct($subject, $config)
    {
        // Process the content plugins.
        JPluginHelper::importPlugin('quix');
        parent::__construct($subject, $config);
    }

    public function onAfterInitialise()
    {
        if (!$this->app) {
            $this->app = JFactory::getApplication();
        }
        // work only on front-end
        if ($this->app->isAdmin()) {
            return;
        } else {
            // check login task
            $this->doLoginCheck();
        }
    }

    public function onAfterRoute()
    {
        include_once JPATH_SITE . '/components/com_quix/helpers/theme.php';

        if ($this->app->isSite()) {
            $option = $this->app->input->get('option', '');
            $view = $this->app->input->get('view', '');
            $id = $this->app->input->get('id');
            $isQuixItem = false; // TODO: check for quix page
            $getMatch = QuixHelperTheme::getAllTypesMatch('article', 'com_content', 'article');

            $canMove = false;
            if ($getMatch and isset($getMatch->id) and $getMatch->condition_id == 0) {
                $collection = qxGetCollectionInfoById($getMatch->item_id);
                if (!empty($collection) and $collection->state) {
                    $canMove = true;
                }
            }
            if ($option == 'com_content' && $view == 'article' && $canMove) {
                $this->app->input->set('option', 'com_quix');
                $this->app->input->set('view', 'collection');
                $this->app->input->set('id', $getMatch->item_id);
                $this->app->input->set('content_id', $id);
            }
        }
    }

    /**
    * Load Quix Assets
    * previous event name: onAfterInitialise
    * error: due to mutilingual issue, change to onBeforeCompileHead.
    */
    public function onBeforeCompileHead()
    {
        if (JFactory::getApplication()->isAdmin()) {
            // Load all assets
            Assets::load();

            return;
        }

        $docType = JFactory::getDocument()->getType();
        if ($docType !== 'html' && $docType !== 'quix') {
            return true;
        }

        // for view=form page
        $this->prepareBuilderView();

        // load scripts from settings
        $this->loadCustomAssets();

        // Load IE fix
        $this->loadIECustomFix();

        // Load Quix Assets from all if we have any
        $this->onBeforeCompileHeadLoadQuixAssets();
    }

    public function onBeforeCompileHeadLoadQuixAssets()
    {
        if (JDEBUG) {
            \JProfiler::getInstance('Application')->mark('Quix Loading Assets onBeforeCompileHeadLoadQuixAssets');
        }
        Assets::load();
        return;

        // Load all assets
        // $document = JFactory::getDocument();
        // if ($this->params->get('load_compiled_library', 0) == 'testing') { // return 0;
        //     $document = JFactory::getDocument();
        //     $version = 'ver=' . QUIX_VERSION;

        //     // prepare links
        //     $quixBS = JUri::root(true) . '/libraries/quix/assets/css/qxbs.css?' . $version;
        //     $quixKit = JUri::root(true) . '/libraries/quix/assets/css/qxkit.css?' . $version;
        //     $quixCSS = JUri::root(true) . '/libraries/quix/assets/css/quix.css?' . $version;
        //     $quixIcon = JUri::root(true) . '/libraries/quix/assets/css/qxi.css?' . $version;

        //     // unset originals
        //     unset($document->_styleSheets[$quixBS], $document->_styleSheets[$quixKit], $document->_styleSheets[$quixCSS], $document->_styleSheets[$quixIcon]);

        //     $cssCotent1 = file_get_contents(JPATH_SITE . '/libraries/quix/assets/css/qxbs.css');
        //     $document->addStyleDeclaration($cssCotent1);

        //     $cssCotent2 = file_get_contents(JPATH_SITE . '/libraries/quix/assets/css/qxkit.css');
        //     $document->addStyleDeclaration($cssCotent2);

        //     $cssCotent3 = file_get_contents(JPATH_SITE . '/libraries/quix/assets/css/quix.css');
        //     $document->addStyleDeclaration($cssCotent3);

        //     // $cssCotent4 = ''; //file_get_contents(JPATH_SITE . '/libraries/quix/assets/css/qxi.css');
        //     // $cssCotent = $cssCotent1 . $cssCotent2 . $cssCotent3 . $cssCotent4;
        //     // Assets::bulkCssMinifier(
        //     //     'quix-core-css',
        //     //     $cssCotent,
        //     //     'core',
        //     //     'system'
        //     // );

        //     // // add preload
        //     // $document->addCustomTag('<link rel="preload" href="'.$quixBS.'" as="style"/>');
        //     // $document->addCustomTag('<link rel="preload" href="'.$quixKit.'" as="style"/>');
        //     // $document->addCustomTag('<link rel="preload" href="'.$quixCSS.'" as="style"/>');
        //     $document->addCustomTag('<link rel="preload" href="' . $quixIcon . '" as="style"/>');

        //     // // add new
        //     // $document->addCustomTag('<link href="'.$quixBS.'" rel="stylesheet" media="all" async />');
        //     // $document->addCustomTag('<link href="'.$quixKit.'" rel="stylesheet" media="all" async />');
        //     // $document->addCustomTag('<link href="'.$quixCSS.'" rel="stylesheet" media="all" async />');
        //     $document->addCustomTag('<link href="' . $quixIcon . '" rel="stylesheet" media="all" async />');
        // }

        // Assets::load();
    }

    public function loadIECustomFix()
    {
        if ($this->params->get('fix_internetExplorer', 0)) {
            JFactory::getDocument()->addStyleDeclaration(
                '@media screen and (min-width: 0\0), screen\0 {
          .qx-column,.qx-col-wrap{flex: 1;}
          img {max-width: 100%;width:100%;width: auto\9;height: auto;}
          figure{display:block;}

          .qx-inner.classic .qx-row {overflow: hidden;}
          .qx-inner.classic .qx-element {animation-name: unset !important;}
          .qx-inner.classic .qx-element:hover {animation-name: unset !important;}
        }'
      );
        }
    }

    public function loadCustomAssets()
    {
        if ($this->params->get('load_global', 0)) {
            // move quix at top of css :D
            self::addQuixTrapCSS();
        }

        if ($this->params->get('init_wow', 1)) {
            JHtml::_('jquery.framework');
            $version = 'ver=' . QUIX_VERSION;
            JFactory::getDocument()->addScript(JUri::root(true) . '/libraries/quix/assets/js/wow.js?' . $version);
            // JFactory::getDocument()->addScriptDeclaration('new WOW().init();');
        }

        // apply gantry fix for offcanvas toggler
        if ($this->params->get('gantry_fix_offcanvas', 0) && class_exists('Gantry5\Loader')) {
            JFactory::getDocument()->addScriptDeclaration('function stopGantryQuixEvent(e){e.stopPropagation()}function preventGantryQuixDef(e){e.preventDefault()}document.addEventListener("DOMContentLoaded",function(e){var t=document.getElementsByClassName("g-offcanvas-toggle");/Mobi/.test(navigator.userAgent)?t[0].addEventListener("click",stopGantryQuixEvent,!1):t[0].addEventListener("click",preventGantryQuixDef,!1)});');
        }

        // apply apply fix for dropdown
        if ($this->params->get('fix_bootstrap_dropdown', 0)) {
            JFactory::getDocument()->addScriptDeclaration(
                "jQuery(document).ready(function(){jQuery('.dropdown-toggle').dropdown();});"
      );
        }
    }

    /**
     * Listener for the `onAfterRender` event
     *
     * @return  void
     *
     * @since   1.0
     */
    public function onAfterRender()
    {
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');
        $layout = $app->input->get('layout');

        if ($app->isAdmin()) {
            if ($option == 'com_quix' && ($view == 'page' or $view == 'collection') && $layout == 'edit') {
                if ($this->params->get('fix_rocketLoader', 0)) {
                    $this->updateAllScriptAsyncFalse();
                }
            }

            return;
        }

        // add quix vars to head
        $this->addQuixCoreVars();

        // update quix frontend builder
        if ($option == 'com_quix' && $view == 'form') {
            $this->updateAllScriptAsyncFalse();
        } elseif ($option == 'com_quix') {
            if ($this->params->get('fix_rocketLoader', 0)) {
                $this->updateAllScriptAsyncFalse();
            }
        }
    }

    public function addQuixCoreVars()
    {
        // check doctype and then add scripts
        $docType = JFactory::getDocument()->getType();

        if ($docType !== 'html' && $docType !== 'quix') {
            return true;
        }

        $format = $this->app->input->get('format', 'html', 'string');
        if($format !== 'html') return;

        $config = JComponentHelper::getParams('com_media');
        $imagePath = $config->get('image_path', 'images');

        $body = JResponse::getBody();
        $script = '
    <script data-cfasync="false">var QUIX_ROOT_URL = "' . JUri::root() . '";window.FILE_MANAGER_ROOT_URL = "' . JUri::root() . $imagePath . '/";</script>';
        $body = str_replace('</title>', '</title>' . $script, $body); // worked

        JResponse::setBody($body);
    }

    public function updateAllScriptAsyncFalse()
    {
        $body = JResponse::getBody();
        $body = str_replace('<script', '<script data-cfasync="false"', $body); // worked

        JResponse::setBody($body);
    }

    /**
     * determine is version 2
     */
    protected static function isV2()
    {
        // return \JFactory::getApplication()->input->get('v') == 2;
        $input = JFactory::getApplication()->input;
        $option = $input->get('option');
        $id = $input->get('id');
        $view = $input->get('view', 'page');

        if ($option == 'com_quix' && $id) {
            $db = JFactory::getDbo();
            $sql = 'SELECT builder FROM ' . ($view == 'page' ? '`#__quix`' : '`#__quix_collections`') . ' WHERE `id` = ' . $id;
            $db->setQuery($sql);
            $result = $db->loadResult();

            if ($result == 'classic') {
                return false;
            }
        }

        return true;
    }

    /*
    * Method addQuixTrapCSS
    */
    public static function addQuixTrapCSS()
    {
        if (checkQuixIsVersion2()) {
            self::addQuixTrapCSSfrontend();
        } else {
            self::addQuixTrapCSSclassic();
        }
    }

    /*
    * Method addQuixTrapCSS for Frontend
    */
    public static function addQuixTrapCSSfrontend()
    {
        $document = JFactory::getDocument();
        $_styleSheets = $document->_styleSheets;
        $version = 'ver=' . QUIX_VERSION;

        $quixBS = JUri::root(true) . '/libraries/quix/assets/css/qxbs.css?' . $version;
        $quixKit = JUri::root(true) . '/libraries/quix/assets/css/qxkit.css?' . $version;
        $quixCSS = JUri::root(true) . '/libraries/quix/assets/css/quix.css?' . $version;
        $quixIcon = JUri::root(true) . '/libraries/quix/assets/css/qxi.css?' . $version;

        $stylesheetQuix = [
            $quixBS => ['async' => 'true'],
            $quixKit => ['async' => 'true'],
            $quixCSS => ['async' => 'true'],
            $quixIcon => ['async' => 'true'],
        ];

        $styleSheets = $stylesheetQuix + $_styleSheets;

        $document->_styleSheets = $styleSheets;
    }

    /*
    * Method addQuixTrapCSS for Classic
    */
    public static function addQuixTrapCSSclassic()
    {
        $document = JFactory::getDocument();
        $_styleSheets = $document->_styleSheets;
        $version = 'ver=' . QUIX_VERSION;

        $quixTrap = JUri::root(true) . '/libraries/quix/assets/css/quixtrap.css?' . $version;
        $quixClassic = JUri::root(true) . '/libraries/quix/assets/css/quix-classic.css?' . $version;
        $quixMP = JUri::root(true) . '/libraries/quix/assets/css/magnific-popup.css?' . $version;

        $stylesheetQuix = [
            $quixTrap => ['async' => 'true'],
            $quixClassic => ['async' => 'true'],
            $quixMP => ['async' => 'true']
        ];

        $styleSheets = $stylesheetQuix + $_styleSheets;

        $document->_styleSheets = $styleSheets;
    }

    public function getConfigs()
    {
        if (!$this->configs) {
            $config = JComponentHelper::getComponent('com_quix');
            $this->configs = $config->params;
        }

        return $this->configs;
    }

    public function onGetIcons($context)
    {
        if ($context == 'mod_quickicon') {
            return [
                [
                    'link' => JRoute::_('index.php?option=com_quix&view=dashboard'),
                    'image' => 'home',
                    'icon' => 'header/icon-48-home.png',
                    'text' => JText::_('Dashboard'),
                    'access' => ['core.manage', 'com_quix'],
                    'group' => 'QUIX',
                ],
                [
                    'link' => JRoute::_('index.php?option=com_quix&view=pages'),
                    'image' => 'list-2',
                    'icon' => 'header/icon-48-article.png',
                    'text' => JText::_('All Pages'),
                    'access' => ['core.manage', 'com_quix'],
                    'group' => 'QUIX',
                ],
                // array(
                //   'link'   => JRoute::_('index.php?option=com_quix&task=page.add'),
                //   'image'  => 'pencil-2',
                //   'icon'   => 'header/icon-48-article-add.png',
                //   'text'   => JText::_('Add New Page'),
                //   'access' => array('core.manage', 'com_quix', 'core.create', 'com_quix'),
                //   'group'  => 'QUIX',
                // ),
                [
                    'link' => JRoute::_('index.php?option=com_quix&view=collections'),
                    'image' => 'puzzle',
                    'icon' => 'header/icon-48-puzzle.png',
                    'text' => JText::_('Templates'),
                    'access' => ['core.manage', 'com_quix'],
                    'group' => 'QUIX',
                ]
            ];
        }
    }

    public function doLoginCheck()
    {
        if (!$this->app->input->get('quixlogin', false)) {
            return;
        }

        // Check for a cookie if user is not logged in (quest cookie)
        if (JFactory::getUser()->get('guest')) {
            $config = JFactory::getConfig();
            $cookie_domain = $config->get('cookie_domain', '');
            $cookie_path = $config->get('cookie_path', '/');
            // prepare cookie name
            $cookie_name = md5(JApplicationHelper::getHash('administrator'));
            if ($_COOKIE[$cookie_name] !== '') {
                $sessionId = $_COOKIE[$cookie_name];
                // find back-end session
                $db = $this->db = JFactory::getDbo();
                $query = $db->getQuery(true)
            ->select($db->quoteName(['session_id', 'client_id', 'guest', 'time', 'data', 'userid', 'username']))
            ->from($db->quoteName('#__session'))
            ->where($db->quoteName('session_id') . ' = ' . $db->quote($sessionId))
            ->order('client_id ASC');
                $db->setQuery($query);
                $adminSession = $db->loadObjectList();

                // second check if the session exists but it was changed to guest session (login -> logout)
                preg_match('/"guest";i:(\d)/mis', $adminSession[0]->data, $guest);
                if (count($adminSession) > 0 && !(isset($guest[1]) ? $guest[1] : false)) {
                    $adminSession = $adminSession[0];
                    // user is already logged to back-end
                    $session = JFactory::getSession();
                    // Update the user related fields for the Joomla sessions table.
                    $query = $db->getQuery(true)
            ->update($db->quoteName('#__session'))
            ->set($db->quoteName('client_id') . ' = ' . '0')
            ->set($db->quoteName('guest') . ' = ' . '0')
            ->set($db->quoteName('data') . ' = ' . $db->quote($adminSession->data))
            ->set($db->quoteName('username') . ' = ' . $db->quote($adminSession->username))
            ->set($db->quoteName('userid') . ' = ' . (int) $adminSession->userid)
            ->where($db->quoteName('session_id') . ' = ' . $db->quote($session->getId()));
                    $res = $db->setQuery($query)->execute();

                    if ($res) {
                        $this->app = JFactory::getApplication();
                        // find user ID in back-end session 'data' string
                        // preg_match('/("id";s:\d*:)"(\w*)"/mis', $adminSession->data, $matches);
                        $userId = $adminSession->userid;
                        $user = JUser::getInstance($userId);

                        // new
                        $instance = JUser::getInstance($userId);
                        // If _getUser returned an error, then pass it back.
                        if ($instance instanceof Exception) {
                            return false;
                        }

                        // If the user is blocked, redirect with an error
                        if ($instance->block == 1) {
                            $this->app->enqueueMessage(JText::_('JERROR_NOLOGIN_BLOCKED'), 'warning');

                            return false;
                        }

                        // Check the user can login.
                        $result = $instance->authorise('core.manage');

                        if (!$result) {
                            $this->app->enqueueMessage(JText::_('JERROR_LOGIN_DENIED'), 'warning');

                            return false;
                        }

                        // Mark the user as logged in
                        $instance->guest = 0;

                        $session = JFactory::getSession();

                        // Grab the current session ID
                        $oldSessionId = $session->getId();

                        // Fork the session
                        $session->fork();

                        $session->set('user', $instance);

                        // Ensure the new session's metadata is written to the database
                        $this->app->checkSession();

                        // Purge the old session
                        $query = $this->db->getQuery(true)
              ->delete('#__session')
              ->where($this->db->quoteName('session_id') . ' = ' . $this->db->quote($oldSessionId));

                        try {
                            $this->db->setQuery($query)->execute();
                        } catch (RuntimeException $e) {
                            // The old session is already invalidated, don't let this block logging in
                        }

                        // Hit the user last visit field
                        $instance->setLastVisit();

                        // Add "user state" cookie used for reverse caching proxies like Varnish, Nginx etc.
                        if ($this->app->isClient('site')) {
                            $this->app->input->cookie->set(
                                'joomla_user_state',
                                'logged_in',
                                0,
                                $this->app->get('cookie_path', '/'),
                                $this->app->get('cookie_domain', ''),
                                $this->app->isHttpsForced(),
                                true
              );
                        }

                        // now comes the Login One! part
                        //
                        $cookie_domain = $this->app->get('cookie_domain', '');
                        $cookie_path = $this->app->get('cookie_path', '/');

                        // get the configured session lifetime in minutes
                        $session_lifetime = $this->app->get('lifetime');

                        // // always set this cookie when user is logged in
                        // setcookie(
                        //JApplication::getHash('COM_QUIX'.$instance->username, $session->getId(), time()+$session_lifetime*65, $cookie_path, $cookie_domain)
                        //);   // cookie lives just a little bit longer than the session

                        // return true;

                        $_SESSION['__default']['user'] = $instance;
                    // hide the message
            // $this->app->enqueueMessage(JText::_('You\'ve been automatically logged based on your administrator area session.'), 'message');
                    } else {
                        // hide the message
                        $this->app->enqueueMessage(JText::_('Sorry! can\t auhorize user.'), 'notice');
                    }
                }
            }
        }
    }

    public function prepareBuilderView()
    {
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');
        $preview = $app->input->get('preview', false);

        if ($option == 'com_quix' && $view == 'form') {
            JFactory::getApplication()->input->set('jchbackend', 1);
            JFactory::getDocument()->setType('quix');
            $this->fixAdminTools();
        }

        if ($option == 'com_quix' && $preview == true) {
            JFactory::getDocument()->setType('quix');
            JFactory::getApplication()->input->set('jchbackend', 1);
        }
    }

    public function fixAdminTools()
    {
        $fix_adminToolsFirewall = $this->params->get('fix_admintoolsfirewall', 1);
        if (!$fix_adminToolsFirewall) {
            return;
        }

        if (!JFile::exists(JPATH_ADMINISTRATOR . '/components/com_admintools/version.php')) {
            return;
        }

        include_once JPATH_ADMINISTRATOR . '/components/com_admintools/version.php';

        $isPro = (ADMINTOOLS_PRO == 1);
        if ($isPro) {
            $db = JFactory::getDbo();
            $sql = "SELECT `option` FROM `#__admintools_wafexceptions` WHERE `option` = 'com_quix'";
            $db->setQuery($sql);
            $result = $db->loadResult();

            if ($result == 'com_quix') {
                return false;
            }

            // create one
            $obj = new stdClass();
            $obj->option = 'com_quix';
            $obj->view = '';
            $obj->query = '';
            JFactory::getDbo()->insertObject('#__admintools_wafexceptions', $obj);
        }
    }
}
