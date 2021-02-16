<?php
/** FIXME */

JLoader::register('QuixFrontendHelper', JPATH_SITE . '/components/com_quix/helpers/quix.php');

// Load module by id
if (!function_exists('qxModuleById')) {
    function qxModuleById($id, $style = 'raw')
    {
        $document = \JFactory::getDocument();
        $renderer = $document->loadRenderer('module');

        $db = JFactory::getDBo();
        $query = $db->getQuery(true);
        $query->select('*')
          ->from('#__modules')
          ->where('published = ' . 1)
          ->where('id = ' . $id);
        $db->setQuery($query);
        $module = $db->loadObject();

        // check if module not found
        if (!isset($module->id)) {
            return;
        }
        $params = json_decode($module->params, true);

        $response = '';
        ob_start();

        if ($module->id > 0) {
            echo $renderer->render($module, $params);
        }

        $response = ob_get_clean();

        return $response;
    }
}

if (!function_exists('qxGetCollections')) {
    function qxGetCollections($details = false, $builder = '*', $type = '')
    {
        JModelLegacy::addIncludePath(JPATH_SITE . '/administrator/components/com_quix/models', 'QuixModel');

        // Get an instance of the generic articles model
        $model = JModelLegacy::getInstance('Collections', 'QuixModel', ['ignore_request' => true]);

        // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', 999);

        if (!$details) {
            $model->setState('list.select', 'a.id, a.uid, a.title, a.type, a.builder');
        }

        $model->setState('filter.state', 1);

        // set builder filter
        $model->setState('filter.builder', $builder);

        // set template types
        if ($type != '') {
            $model->setState('filter.collection', $type);
        }

        // Access filter
        $access = !JComponentHelper::getParams('com_quix')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        $model->setState('filter.access', $access);

        // Retrieve Content
        return $model->getItems();
    }
}

if (!function_exists('qxGetElementsInfo')) {
    function qxGetElementsInfo($details = false)
    {
        JModelLegacy::addIncludePath(JPATH_SITE . '/administrator/components/com_quix/models', 'QuixModel');

        // Get an instance of the generic articles model
        $model = JModelLegacy::getInstance('Elements', 'QuixModel', ['ignore_request' => true]);

        // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', 999);
//    $model->setState( 'filter.state', 1 );

        // Retrieve Content
        return $model->getItems();
    }
}

// $field = array,
// $name = string > name of the icon field
if (!function_exists('get_icon')) {
    function get_icon($field = [], $name = 'icon')
    {
        $result = [];
        $icon = [];
        if (!is_array($field[$name])) {
            $icon = json_decode($field[$name], true);
        }

        // if(!is_array($field['icon']) && !count($icon)):
        if (empty($icon) or !count($icon)):
      $result['class'] = $field[$name];
        $result['content'] = ''; else:
      if (!isset($icon['iconsType']) or empty($icon['iconsType'])) {
          return;
      }

        // print_r($icon);die;
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onQuixPrepareFont', [$icon['iconsType']]);
        if ($icon['renderer'] == 'content-based'):
        $result['class'] = $icon['className'];
        $result['content'] = $icon['name']; else:
        // only else as we dont have any other option
        // elseif($field['renderer'] == "class-based"):

        $result['class'] = $icon['name'];
        $result['content'] = '';
        endif;
        endif;

        return $result;
    }
}

function quix_default_template()
{
    $db = JFactory::getDBO();
    $query = 'SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1';
    $db->setQuery($query);
    return $db->loadResult();
}

function qxGetCollectionById($id)
{
    JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_quix/models', 'QuixModel');
    require_once JPATH_SITE . '/components/com_quix/helpers/quix.php';
    
    $app = JFactory::getApplication();
    $model = JModelLegacy::getInstance('Collection', 'QuixModel', ['ignore_request' => true]);
    $model->setState('list.select', 'a.id, a.uid, a.title, a.state, a.type, a.builder, a.data, a.metadata, a.params');
    
    // Retrieve Content
    try {
        $item = $model->getData($id); //!$app->isAdmin() was before. now global. make sure works.!
    } catch (Exception $e) {
        $item = $model->getItem($id); // was for site
    }
    return $item;

}

function qxGetCollectionInfoById($id)
{
    JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_quix/models', 'QuixModel');
    require_once JPATH_SITE . '/components/com_quix/helpers/quix.php';
    $model = JModelLegacy::getInstance('Collection', 'QuixModel', ['ignore_request' => true]);
    // Retrieve Content
    $item = $model->getData($id);

    return $item;
}

/**
 * @return mixed
 */
function qxGetComponentInfo()
{
    $extension = JTable::getInstance('extension');
    $id = $extension->find(['element' => 'com_quix']);
    $extension->load($id);
    $componentInfo = json_decode($extension->manifest_cache, true);

    return $componentInfo;
}

function qxGetBlocks($builder = 'frontend')
{
    if ($builder == 'admin') {
        // $url .= '&max_version=1.9.9';
        return getResponsefromLocal();
    }

    $input = JFactory::getApplication()->input;

    // filters params
    $license = $input->get('license', '');
    $type = $input->get('type', '');
    $min_version = $input->get('min_version', '');

    $config = JComponentHelper::getParams('com_quix');
    $api_https = $config->get('api_https', 1);

    // absolute url of list json
    $url = ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1' ? 'http' : 'https') . '://getquix.net/index.php?option=com_quixblocks&view=category&format=json';

    if ($license) {
        $url .= '&license=' . $license;
    }
    if ($type) {
        $url .= '&type=' . $type;
    }
    if ($min_version) {
        $url .= '&min_version=' . $min_version;
    }

    $cache = JFactory::getCache('lib_quix');
    $cache->setCaching(1);
    $result = $cache->call('getResponsefromAPI', $url);
    return ($result ? $result : '{"success": false}');
}

function getResponsefromAPI($url)
{
    // Get the handler to download the blocks
    try {
        $http = new JHttp();
        $result = $http->get($url);

        if (!$result || ($result->code != 200 && $result->code != 310)) {
            $exception = new Exception(JText::_('Server connection error!'));
            return new JResponseJSON($exception);
        }

        // json decode and test output for json error
        json_decode($result->body);
        if (json_last_error() == JSON_ERROR_NONE) {
            return $result->body;
        }
    } catch (RuntimeException $e) {
        $exception = new Exception($e->getMessage());
        return new JResponseJSON($exception);
    }
}

/**
* Method getResponsefromLocal
* @param none
* @return json
*/
function getResponsefromLocal()
{
    if (file_exists(JPATH_SITE . '/media/quix/json/blocks.json')) {
        jimport('joomla.filesystem.file');

        $json = JFile::read(JPATH_SITE . '/media/quix/json/blocks.json');
        return $json;
    } else {
        JError::raiseWarning('', JText::_('COM_QUIX_BLOCKS_MISSING_FILES'));
        return false;
    }
}
