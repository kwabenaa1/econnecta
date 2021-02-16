<?php
/**
 * @version    2.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

require JPATH_LIBRARIES . '/quix/vendor/autoload.php';

/**
 * Handle App API request through one controller
 *
 * @since  2.0
 */
class QuixControllerApi extends JControllerLegacy
{
    /**
     * Instance of image optimizer.
     *
     * @var \ThemeXpert\Image\Optimizer
     */
    protected $imageOptimizer = null;

    /**
     * Responsive sizes.
     *
     * @var array
     */
    protected $responsiveSizes = [];

    /**
     * Responsive breakpoints.
     *
     * @var array
     */
    protected $responsiveBreakPoints = [];

    /**
     * Image quality of responsive image.
     *
     * @var integer it should be 0 to 100
     */
    protected $responsiveImageQuality = 100;

    /**
     * Create a new instance of TwigEngine.
     */
    public function __construct()
    {
        parent::__construct();

        $this->responsiveBreakPoints = [
            'large_desktop' => 1900,
            'desktop' => 1400,
            'tablet' => 1024,
            'mobile' => 768,
            'mini' => 400
        ];

        $config = JComponentHelper::getParams('com_quix');
        $responsive_image = (array) $config->get('responsive_image', ['quality' => 80, 'responsive_image' => ['large_desktop' => 1900, 'desktop' => 1400, 'tablet' => 1024, 'mobile' => 786, 'mini' => 400]]);

        $this->responsiveImageQuality = (int)$responsive_image['quality'];
        unset($responsive_image['quality']);

        foreach ($responsive_image as $breakPoint => $size) {
            $this->responsiveSizes[$this->responsiveBreakPoints[$breakPoint]] = (int)$size;
        }
    }

    /*
    * Method to check the image
    * previous: hasImage
    */
    public function checkImage()
    {
        // Reference global application object
        $app = JFactory::getApplication();

        // JInput object
        $input = $app->input;

        // Requested format passed via URL
        $format = strtolower($input->getWord('format', 'json'));

        // Requested element name
        $path = strtolower($input->get('path', '', 'string'));

        // check if path passed
        if (!$path) {
            $results = new InvalidArgumentException(JText::_('COM_QUIX_NO_ARGUMENT'), 403);
        }

        // first check if its from default template
        if (is_file(JPATH_ROOT . $path)) {
            $results = true;
        } else {
            $results = new InvalidArgumentException(JText::_('COM_QUIX_FILE_NOT_EXISTS'), 404);
        }

        // return result
        echo new JResponseJson($results, null, false, $input->get('ignoreMessages', true, 'bool'));

        $app->close();
    }

    /*
    * Method to encode image or data
    * previous name: base64EncodedJson
    */
    public function encodeBase64Json()
    {
        // Reference global application object
        $app = JFactory::getApplication();
        $input = $app->input;
        $input->post->getArray();
        // ssl header
        $arrContextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];

        $post = $input->post->getArray();
        if (!count($post)) {
            $post = @file_get_contents('php://input');
        }

        // taking posted data
        $quix = json_decode($post, true)['quix'];

        // preg matching
        preg_match_all('/([-a-z0-9_\/:.]+\.(jpg|jpeg|png))/i', $quix, $matches);

        $base64EncodedImage = [];

        // looping throw all original images
        // and setuping base64 encoded image
        foreach ($matches[0] as $key => $image) {
            $type = $matches[2][$key];

            if (!isset($base64EncodedImage[$image])) {
                $base64EncodedImage[$image] = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($this->getSrcLink($image), false, stream_context_create($arrContextOptions)));
            }
        }

        $originalImages = array_keys($base64EncodedImage);

        // replacing all original images with base64 encoded images
        $replacedImage = str_replace($originalImages, $base64EncodedImage, $quix);

        // return result
        echo new JResponseJson(['config' => $replacedImage], null, false, true);

        $app->close();
    }

    /*
    * Method to encode image or data
    * previous name: base64EncodedJson
    */
    public function exportCollection()
    {
        $app = JFactory::getApplication();
        // Send json mime type.
        $app->mimeType = 'application/json';
        $app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);
        $app->sendHeaders();

        // ssl header
        $arrContextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];

        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        $input = $app->input;
        $id = $input->get('id', '', 'int');
        if (!$id) {
            echo new JResponseJSON('No id found!');
            $app->close();
        }

        $result = qxGetCollectionById($id);

        // taking posted data
        $quix = $result->data;
        $config = JComponentHelper::getComponent('com_quix')->params;
        if (!$config->get('export_with_image', false)) {
            echo new JResponseJson(['config' => $quix], null, false, true);
            $app->close();
        }

        // preg matching
        preg_match_all('/([-a-z0-9_\/:.]+\.(jpg|jpeg|png))/i', $quix, $matches);

        $base64EncodedImage = [];

        // looping throw all original images
        // and setuping base64 encoded image
        foreach ($matches[0] as $key => $image) {
            $type = $matches[2][$key];

            if (!isset($base64EncodedImage[$image])) {
                $base64EncodedImage[$image] = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($this->getSrcLink($image), false, stream_context_create($arrContextOptions)));
            }
        }

        $originalImages = array_keys($base64EncodedImage);

        // replacing all original images with base64 encoded images
        $replacedImage = str_replace($originalImages, $base64EncodedImage, $quix);

        // return result
        echo new JResponseJson(['config' => $replacedImage], null, false, true);

        $app->close();
    }

    /**
     * Get image source link
     */
    protected function getSrcLink($src)
    {
        if (
            preg_match('/^(https?:\/\/)|(http?:\/\/)|(\/\/)|(libraries)|([a-z0-9-].)+(:[0-9]+)(\/.*)?$/', $src)
        ) {
            return $src;
        }

        return \JURI::root() . 'images' . $src;
    }

    /**
     * Gets the parent items of the menu location currently.
     *
     * @return  json encoded output and close app
     *
     * @since   2.0
     */
    public function getParentItem()
    {
        JModelLegacy::addIncludePath(JPATH_SITE . '/administrator/components/com_menus/models');
        $app = JFactory::getApplication();

        $results = [];
        $menutype = $this->input->get->get('menutype');

        if ($menutype) {
            $model = $this->getModel('Items', 'MenusModel', []);
            $model->getState();
            $model->setState('filter.menutype', $menutype);
            $model->setState('list.select', 'a.id, a.title, a.level');
            $model->setState('list.start', '0');
            $model->setState('list.limit', '0');

            /** @var  MenusModelItems  $model */
            $results = $model->getItems();

            // Pad the option text with spaces using depth level as a multiplier.
            for ($i = 0, $n = count($results); $i < $n; $i++) {
                $results[$i]->title = str_repeat(' - ', $results[$i]->level) . $results[$i]->title;
            }
        }

        // Output a JSON object
        echo json_encode($results);

        $app->close();
    }

    /**
     * Method to create menu.
     *
     * @return  json result
     *
     * @since   2.0
     */
    public function createMenu()
    {
        // Check for request forgeries.
        // echo JSession::getFormToken();die;
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        JModelLegacy::addIncludePath(JPATH_SITE . '/administrator/components/com_menus/models');
        JTable::addIncludePath(JPATH_SITE . '/administrator/components/com_menus/tables');
        $app = JFactory::getApplication();
        $title = $app->input->post->get('title', '', 'string');
        if (empty($title)) {
            echo new JResponseJson(new Exception('Title required'));
            $app->close();
            return;
        }

        $alias = $app->input->post->get('alias');

        $menu = $app->input->post->get('menu');
        if (empty($menu)) {
            echo new JResponseJson(new Exception('Menu selection is required!'));
            $app->close();
            return;
        }

        $parentid = $app->input->post->get('parentid');
        if (empty($parentid)) {
            echo new JResponseJson(new Exception('Select menu parant!'));
            $app->close();
            return;
        }

        $link = $app->input->post->get('link', '', 'string', 'raw');
        $component_id = JComponentHelper::getComponent('com_quix')->id; // update it
        $language = '*';
        $published = 1;
        $type = 'component';

        $data = ['id' => '', 'link' => $link, 'parent_id' => $parentid, 'menutype' => $menu, 'title' => $title, 'alias' => $alias,
            'type' => $type, 'published' => $published, 'language' => $language, 'component_id' => $component_id
        ];
        $model = $this->getModel('Item', 'MenusModel', []);

        try {
            if ($model->save($data)) {
                $Itemid = $model->getState('item.id');
                $link = JRoute::_($link . (parse_url($link, PHP_URL_QUERY) ? '&' : '?') . 'Itemid=' . $Itemid);
                echo new JResponseJson(['Itemid' => $Itemid, 'link' => $link]);
            } else {
                echo new JResponseJson(new Exception($model->getError()));
            }
        } catch (Exception $e) {
            echo new JResponseJson($e);
        }
        $app->close();
    }

    /**
     * Method to handle file manager operation
     *
     * @return  object
     *
     * @since   2.0
     */
    public function uploadMedia()
    {
        // Check for request forgeries.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        (new \FileManager\FileManager(__DIR__ . '/../filemanager/config.php'));
        exit;
    }

    /**
     * Prepare Joomla content
     *
     * @return  object
     *
     * @since   2.0
     */
    public function prepareContent()
    {
        $app = JFactory::getApplication();
        $app->input->set('tmpl', 'component');
        $text = $app->input->get('content', '', 'raw');

        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        echo JHtml::_('content.prepare', $text);

        jexit();
    }

    /**
     * get Icons pack, store it and return the content
     *
     * @return  object
     *
     * @since   2.0
     */
    public function getIcons()
    {
        // $profiler = new JProfiler();

        $app = JFactory::getApplication();
        // Send json mime type.
        $app->mimeType = 'application/json';
        $app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);
        $app->setHeader('Cache-Control', 'max-age=86400');
        $app->sendHeaders();

        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        // now call the cache
        $cache = new JCache(['defaultgroup' => 'lib_quix', 'cachebase' => JPATH_SITE . DIRECTORY_SEPARATOR . 'cache']);
        $cacheid = 'QuixFlatIcons30';
        $cache->setCaching(true);
        $cache->setLifeTime(2592000);  //24 hours 86400// 30days 2592000

        // return from cache
        $output = $cache->get($cacheid);

        // if no cache, read from file
        if (empty($output)) {
            // this will check local files, if not found will call from server
            $output = QuixFrontendHelper::getFlatIconsfromLocal();
            // store to cache
            $cache->store($output, $cacheid);
        }

        // response json
        echo $output;

        // close the output
        $app->close();
    }

    public function getTemplates()
    {
        $app = JFactory::getApplication();
        // Send json mime type.
        $app->mimeType = 'application/json';
        $app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);

        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        $source = $app->input->get('source', 'local');
        $type = $app->input->get('type', '');
        $details = $app->input->get('details', false);

        // return from cache

        if ($source == 'local') {
            $result = qxGetCollections($details, 'frontend', $type);
            $output = json_encode($result);
        } else {
            $app->setHeader('Cache-Control', 'max-age=3600');
            // online from getquix
            $output = qxGetBlocks();
        }
        $app->sendHeaders();

        // response json
        echo $output;

        // close the output
        $app->close();
    }

    public function getFileContent()
    {
        $app = JFactory::getApplication();
        $app->setHeader('Cache-Control', 'max-age=3600');
        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        if ($app->input->get('file', '') == 'animation') {
            // now call the cache
            $cache = new JCache(['defaultgroup' => 'lib_quix', 'cachebase' => JPATH_SITE . DIRECTORY_SEPARATOR . 'cache']);
            $cacheid = 'quix.animation';
            $cache->setCaching(true);
            $cache->setLifeTime(2592000);  //24 hours 86400// 30days 2592000

            // return from cache
            $output = $cache->get($cacheid);

            // if no cache, read from file
            if (empty($output)) {
                $path = QUIX_PATH . '/app/frontend/animation.twig';
                try {
                    $output = file_get_contents($path);
                    $cache->store($output, $cacheid);
                } catch (Exception $e) {
                    $output = 'Does not exist: ' . $path;
                }
                // store to cache
            }

            echo $output;
            jexit();
        } elseif ($app->input->get('file', '') == 'global') {
            // now call the cache
            $cache = new JCache(['defaultgroup' => 'lib_quix', 'cachebase' => JPATH_SITE . DIRECTORY_SEPARATOR . 'cache']);
            $cacheid = 'quix.global';
            $cache->setCaching(true);
            $cache->setLifeTime(2592000);  //24 hours 86400// 30days 2592000

            // return from cache
            $output = $cache->get($cacheid);

            // if no cache, read from file
            if (empty($output)) {
                $path = QUIX_PATH . '/app/frontend/global.twig';
                try {
                    $output = file_get_contents($path);
                    $cache->store($output, $cacheid);
                } catch (Exception $e) {
                    $output = 'Does not exist: ' . $path;
                }
                // store to cache
            }

            echo $output;
            jexit();
        } else {
            $path = $app->input->get('path', '', 'base64');
            $ext = $app->input->get('ext');
            $path = base64_decode($path);
            if ($ext == 'php') {
                $exception = new Exception(JText::_('Invalid File Extension'));
                echo new JResponseJSON($exception);
            } else {
                // $path = '/app/frontend/elements/alert/element.svg';
                try {
                    $content = file_get_contents(QUIX_PATH . $path . '.' . $ext);
                } catch (Exception $e) {
                    $content = 'Does not exist: ' . QUIX_PATH . $path . '.' . $ext;
                }
                echo new JResponseJSON($content);
            }
        }

        // close the output
        $app->close();
    }

    public function getTemplate()
    {
        $app = JFactory::getApplication();
        // Send json mime type.
        $app->mimeType = 'application/json';
        $app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);
        $app->sendHeaders();

        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        // set cache
        $app->setHeader('Cache-Control', 'max-age=3600');

        $id = $app->input->get('id');
        $result = qxGetCollectionById($id);
        $output = json_encode($result);

        // response json
        echo $output;

        // close the output
        $app->close();
    }

    public function getJoomlaModules()
    {
        $app = JFactory::getApplication();
        $app->input->set('tmpl', 'component');

        // Send json mime type.
        $app->mimeType = 'application/json';
        $app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);
        $app->sendHeaders();

        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        JModelLegacy::addIncludePath(JPATH_SITE . '/administrator/components/com_modules/models', 'ModulesModel');

        // Get an instance of the generic articles model
        $model = JModelLegacy::getInstance('Modules', 'ModulesModel', ['ignore_request' => true]);

        // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', 9999);

        // Access filter
        // $access = ! JComponentHelper::getParams( 'com_modules' )->get( 'show_noauth' );
        // $model->setState( 'filter.access', $access );
        $model->setState('filter.state', 1);

        // Set ordering
        $model->setState('list.ordering', 'a.ordering');

        $model->setState('list.direction', 'ASC');

        // Retrieve Content
        $items = $model->getItems();

        echo new JResponseJSON($items);
        $app->close();
    }

    public function getJoomlaCategories()
    {
        $options = JHtml::_('category.options', 'com_content');
        array_unshift($options, JHtml::_('select.option', 'root', JText::_('JGLOBAL_ROOT')));

        echo new JResponseJSON($options);
        jexit();
    }

    public function getJoomlaModule()
    {
        $app = JFactory::getApplication();
        $app->input->set('tmpl', 'component');

        // Send json mime type.
        // $app->mimeType = 'application/json';
        // $app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);
        // $app->sendHeaders();

        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            // $exception = new Exception(JText::_('JINVALID_TOKEN'));
            // echo new JResponseJSON($exception);
            echo '<p class="qx-alert qx-alert-warning qx-m-0">' . JText::_('JINVALID_TOKEN') . '</p>';
            $app->close();
        }

        $id = $app->input->get('id');
        $style = $app->input->get('style');

        if (empty($id)) {
            echo '<p class="qx-alert qx-alert-warning qx-m-0">' . JText::_('Please select a module first!') . '</p>';
            $app->close();
        }

        $db = \JFactory::getDBo();
        $query = $db->getQuery(true);
        $query->select('*')
                ->from('#__modules')
                ->where('published = ' . 1)
                ->where('id = ' . $id);
        $db->setQuery($query);
        $module = $db->loadObject();

        if (!isset($module->module) && empty($module->params)) {
            echo 'Sorry! Module not found or not published! please check your module.';
            $app->close();
        }

        $mparams = json_decode($module->params, true);
        $enabled = ModuleHelper::isEnabled($module->module);

        $result = '';
        if ($enabled) {
            // Load Jquery in case any module does not have it as we are loading backdoor way
            JHtml::_('jquery.framework');

            $moduleinfo = ModuleHelper::getModule($module->module, $module->title);
            $info = (object) array_merge((array) $moduleinfo, (array) $module);

            $result = ModuleHelper::renderModule($info, $mparams);
        }
        // $output = json_encode($result);
        // response json
        // echo $output;
        echo $result;

        // close the output
        // $app->close();
    }

    public function getWebFonts()
    {
        $app = JFactory::getApplication();
        // Send json mime type.
        $app->mimeType = 'application/json';
        $app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);
        $app->setHeader('Cache-Control', 'max-age=3600');
        $app->sendHeaders();

        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        // now call the cache
        $cache = new JCache(['defaultgroup' => 'lib_quix', 'cachebase' => JPATH_SITE . DIRECTORY_SEPARATOR . 'cache']);
        $cacheid = 'QuixWebFonts30';
        $cache->setCaching(true);
        $cache->setLifeTime(2592000);  //24 hours 86400// 30days 2592000

        // return from cache
        $output = $cache->get($cacheid);

        // if no cache, read from file
        if (empty($output)) {
            // this will check local files, if not found will call from server
            $output = QuixFrontendHelper::getGoogleFontsJSONfromLocal();
            // store to cache
            $cache->store($output, $cacheid);
        }

        // response json
        echo $output;

        // close the output
        $app->close();
    }

    public function storeCompiledCSs()
    {
        $app = JFactory::getApplication();
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        $content = $app->input->get('compiled_css', '', 'RAW');
        $fontContent = json_encode($app->input->get('font_families', '', 'RAW'));

        $compiledFilePath = JPATH_BASE . '/media/quix/frontend/css/' . $app->input->get('compiled_css_file_name');

        if (strlen($content)) {
            file_put_contents($compiledFilePath, $content);
        }
        if (strlen($fontContent)) {
            file_put_contents($compiledFilePath . '.font-families', $fontContent);
        }

        echo new JResponseJSON('Compiled Data Saved');

        $app->close();
    }

    /**
     * Method to check users license
     *
     * @return  object
     *
     * @since   2.0
     */
    public function validation()
    {
        require_once JPATH_COMPONENT . '/helpers/quix.php';

        // Reference global application object
        $app = JFactory::getApplication();

        // check pro version + activation
        $free = QuixHelper::isFreeQuix();
        $pro = QuixHelper::isProActivated();
        if ($free or empty($pro) or $pro == null or !$pro) {
            // echo new JResponseJson('Thank you. Valid Pro license has been found.'); // TODO:remove after testing
            $err = new Exception('No valid pro license has been found or license period has expired!.');
            echo new JResponseJson($err);
        } else {
            echo new JResponseJson('Thank you. Valid Pro license has been found.');
        }

        $app->close();
    }

    /**
     * Method to check users license
     *
     * @return  object
     *
     * @since   2.0
     */
    public function licenseStatus()
    {
        require_once JPATH_COMPONENT . '/helpers/quix.php';

        // Reference global application object
        $app = JFactory::getApplication();

        // check pro version + activation
        $free = QuixHelper::isFreeQuix();
        $pro = QuixHelper::isProActivated();
        if ($free) {
            echo new JResponseJson('free');
        } elseif ($pro) {
            echo new JResponseJson('pro');
        } else {
            echo new JResponseJson('inactive');
        }

        $app->close();
    }

    /**
     * Image optimization
     *
     * @param [type] $src
     * @return void
     */
    public function imageOptimization()
    {
        $app = JFactory::getApplication();
        ini_set('memory_limit', '-1');

        // JInput object
        $input = $app->input;

        $itemId = $input->get('id');

        $type = $input->get('type');
        $totalImages = $input->get('total_images');
        $reoptimized = filter_var($input->get('reoptimized'), FILTER_VALIDATE_BOOLEAN);

        // Requested element name
        $src = '/' . $input->get('image', '', 'string');

        $splitSrc = explode('.', $src);
        $originalExtension = array_pop($splitSrc);
        $srcWithoutExtension = implode('', $splitSrc);

        $hasWebpImage = true;
        $enabledCache = true;

        if (function_exists('imagewebp')) {
            $hasWebpImage = empty(glob(JPATH_ROOT . '/media/quix/cache/images' . $srcWithoutExtension . '-*.webp'));
        }

        $hasOriginalImageFormat = empty(glob(JPATH_ROOT . '/media/quix/cache/images' . $srcWithoutExtension . '-*.jpeg'));

        if (!$hasWebpImage or $hasOriginalImageFormat or $reoptimized) {
            $enabledCache = false;
        }

        $isProUser = $this->isProUser();

        $originalImagePath = FILE_MANAGER_ROOT . '/' . $srcWithoutExtension . '.' . $originalExtension;
        $overrideImagePath = FILE_MANAGER_ROOT . '/' . $srcWithoutExtension . '.' . strtolower($originalExtension);

        $originalImageBinary = file_get_contents($originalImagePath);
        unlink($originalImagePath);

        file_put_contents($overrideImagePath, $originalImageBinary);

        // lets do the optimization
        $this->imageOptimizer = image($srcWithoutExtension . '.' . strtolower($originalExtension), [
            'source' => FILE_MANAGER_ROOT,
            'cache' => JPATH_ROOT . '/media/quix/cache/images',
            'base_url' => \JUri::base() . 'media/quix/cache/images',
            'enableCache' => $enabledCache,
            'sizes' => $this->responsiveSizes,
            'scaler' => 'sizes',
            'quality' => $isProUser ? $this->responsiveImageQuality : 100,
            'want_webp' => $isProUser,
            'optimize' => $isProUser
        ]);

        // now start work for log and record purpose
        // comes form db....
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/images.php';

        $model = \QuixHelperImages::get($itemId, $type);

        $path = JPATH_ROOT . '/media/quix/cache/images';

        $images = $this->sort(glob($path . $srcWithoutExtension . '-*.jpeg'));
        if (!$images && file_exists($path . $srcWithoutExtension . '.jpeg')) {
            $images = [$path . $srcWithoutExtension . '.jpeg'];
        }

        $imagesBaseName = array_map(function ($image) use ($path) {
            // dd(str_replace($path, '', $image));
            return str_replace($path, '', $image);
        }, $images);

        $imagesWebp = $this->sort(glob($path . $srcWithoutExtension . '-*.webp'));
        if (!$imagesWebp && file_exists($path . $srcWithoutExtension . '.webp')) {
            $imagesWebp = [$path . $srcWithoutExtension . '.webp'];
        }

        $webpImagesBaseName = array_map(function ($image) use ($path) {
            // dd(str_replace($path, '', $image));
            return str_replace($path, '', $image);
        }, $imagesWebp);

        $largeSize = JPATH_ROOT . '/media/quix/cache/images' . $srcWithoutExtension . '-large_desktop.jpeg';
        if (!file_exists($largeSize)) {
            $largeSize = JPATH_ROOT . '/media/quix/cache/images' . $srcWithoutExtension . '-desktop.jpeg';
            if (!file_exists($largeSize)) {
                $largeSize = JPATH_ROOT . '/media/quix/cache/images' . $srcWithoutExtension . '.jpeg';
            }
        }

        $miniSize = JPATH_ROOT . '/media/quix/cache/images' . $srcWithoutExtension . '-mini.jpeg';
        if (!file_exists($miniSize)) {
            $miniSize = JPATH_ROOT . '/media/quix/cache/images' . $srcWithoutExtension . '.jpeg';
        }

        $currentImageDetails = [
            'original_size' => (filesize(FILE_MANAGER_ROOT . $src) / 1024),
            'optimise_size' => (filesize($largeSize) / 1024),
            'mobile_size' => (filesize($miniSize) / 1024)
        ];

        if (is_null($model)) {
            $imageOptimization = [
                'images_count' => $totalImages,
                'original_size' => $currentImageDetails['original_size'],
                'optimise_size' => $currentImageDetails['optimise_size'],
                'mobile_size' => $currentImageDetails['mobile_size'],
                'extra_information' => json_encode([
                    $src => [
                        'jpeg' => $imagesBaseName,
                        'webp' => $webpImagesBaseName
                    ]
                ])
            ];

            \QuixHelperImages::log($itemId, $type, $imageOptimization);
        } else {
            $extraInformation = json_decode($model->params, true);

            if (is_null($extraInformation)) {
                $extraInformation = [];
            }

            $f = array_merge($extraInformation, [
                $src => [
                    'jpeg' => $imagesBaseName,
                    'webp' => $webpImagesBaseName
                ]
            ]);

            $imageOptimization = [
                'images_count' => $totalImages,
                'original_size' => $model->original_size + $currentImageDetails['original_size'],
                'optimise_size' => $model->optimise_size + $currentImageDetails['optimise_size'],
                'mobile_size' => $model->mobile_size + $currentImageDetails['mobile_size'],
                'extra_information' => json_encode($f)
            ];

            \QuixHelperImages::log($itemId, $type, $imageOptimization);
        }

        echo new JResponseJSON($imageOptimization, 'Image Optimization Done.');

        $app->close();
    }

    protected function sort($images)
    {
        $i = [];
        foreach ($images as $image) {
            if (strpos($image, 'large_desktop')) {
                $i[4] = $image;
            } elseif (strpos($image, 'desktop')) {
                $i[3] = $image;
            } elseif (strpos($image, 'tablet')) {
                $i[2] = $image;
            } elseif (strpos($image, 'mobile')) {
                $i[1] = $image;
            } elseif (strpos($image, 'mini')) {
                $i[0] = $image;
            }
        }

        return $i;
    }

    /**
     * Determine current user's license type.
     *
     * @return boolean
     */
    protected function isProUser()
    {
        require_once JPATH_COMPONENT . '/helpers/quix.php';

        $free = QuixHelper::isFreeQuix();
        $pro = QuixHelper::isProActivated();

        return ($free or empty($pro) or $pro == null or !$pro) ? false : true;
    }

    public function getElementPath()
    {
        jimport('quix.app.bootstrap');
        jimport('quix.app.init');

        $app = JFactory::getApplication();

        $input = $app->input;
        $slug = $input->get('slug');

        $element = array_find_by(quix()->getElements(), 'slug', $slug);

        if (empty($element)) {
            echo new JResponseJSON("Element {$slug} doesn't exists");
            $app->close();
            return;
        }

        echo new JResponseJSON([
            'element_path' => $element['element_path'],
            'element_url' => $element['url'],
        ]);

        $app->close();
    }

    public function getEditor()
    {
        require_once JPATH_SITE . '/components/com_quix/helpers/editor.php';

        $app = JFactory::getApplication();
        $input = $app->input;
        $context = $input->get('source', '', 'string');
        $context_id = $input->get('sid', '', 'int');

        $getId = QuixHelperEditor::getId($context, $context_id);
        if ($getId) {
            $app->redirect('index.php?option=com_quix&task=collection.edit&id=' . $getId . '&quixlogin=true');
        } else {
            // first create then go to edit
            JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_quix/tables');
            $row = JTable::getInstance('collection', 'QuixTable', []);
            $title = explode('.', $context);
            $row->title = ucfirst($title[1]) . ':' . $context_id;
            $row->type = 'editor';
            $row->state = '1';
            $row->builder = 'frontend';
            $row->builder_version = QUIX_VERSION;
            $row->data = '[]';

            try {
                $row->store();
                $getId = $row->id;

                QuixHelperEditor::log($context, $context_id, $getId);

                $app->redirect('index.php?option=com_quix&task=collection.edit&id=' . $getId . '&quixlogin=true');
            } catch (Exception $e) {
                echo $e->getMessage();
                jexit();
            }
        }
    }

    public function captchePublicKey()
    {
        $joomla_captcha = JFactory::getConfig()->get('captcha');

        if ($joomla_captcha != '0') {
            $app = JFactory::getApplication();

            $params = new \JRegistry(\JPluginHelper::getPlugin('captcha')[0]->params);
            echo new JResponseJSON($params->get('public_key'));

            $app->close();
        }
    }
}
