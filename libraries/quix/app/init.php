<?php
use ThemeXpert\Quix\Quix;

$config = JComponentHelper::getParams('com_media');
$imagePath = $config->get('image_path', 'images');
/*****************************
 *  FILE MANAGER LIB CONFIG
 *****************************/
defined('FILE_MANAGER_ROOT') or define('FILE_MANAGER_ROOT', JPATH_ROOT . '/' . $imagePath);
defined('FILE_MANAGER_ROOT_BASE_PATH') or define('FILE_MANAGER_ROOT_BASE_PATH', JPATH_ROOT . '/' . $imagePath);

/**
 * Define quix function.
 *
 * @return Application
 */
if (!function_exists('quix')) {
    function quix()
    {
        // set builder type
        global $QuixBuilderType;

        if (isset($QuixBuilderType)) {
            $builder = $QuixBuilderType;
        } else {
            $builder = 'frontend';
        }

        // $cache = JFactory::getCache();
        // return $cache->call( array( new Quix, 'getInstance' ), $builder );
        return (new Quix)->getInstance($builder);
    }
}

/**
 * Determine frontend / classic builder
 */
if (!function_exists('checkQuixIsVersion2')) {
    function checkQuixIsVersion2()
    {
        static $checkedQuixIsVersionIDs;

        $app = \JFactory::getApplication();
        if ($app->isAdmin()) {
            return false;
        }

        $input = $app->input;
        $option = $input->get('option');
        $id = $input->get('id');
        $view = $input->get('view', 'page');
        $type = $input->get('type', '');

        if (!is_array($checkedQuixIsVersionIDs)) {
            $checkedQuixIsVersionIDs = [];
        }

        if (!empty($checkedQuixIsVersionIDs[$id])) {
            return $checkedQuixIsVersionIDs[$id];
        }

        $checkedQuixIsVersionIDs[$id] = true;
        if ($option == 'com_quix' && $id) {
            if (($view == 'form' && $type == 'collection') or $view == 'collection') {
                $source = 'collections';
            } else {
                $source = 'page';
            }

            $db = \JFactory::getDbo();
            $sql = 'SELECT builder FROM ' . ($source == 'page' ? '`#__quix`' : '`#__quix_collections`') . ' WHERE `id` = ' . $id;
            $db->setQuery($sql);
            $result = $db->loadResult();

            if ($result == 'classic') {
                $checkedQuixIsVersionIDs[$id] = false;
            }
        }

        return $checkedQuixIsVersionIDs[$id];
    }
}

/**
 * Determine quix version number.
 */
if (!function_exists('checkQuixCollectionIsVersion2')) {
    function checkQuixCollectionIsVersion2($id)
    {
        if ($id) {
            $db = \JFactory::getDbo();
            $sql = 'SELECT builder FROM `#__quix_collections` WHERE `id` = ' . $id;
            $db->setQuery($sql);
            $result = $db->loadResult();

            if ($result == 'classic') {
                return false;
            }
        }

        return true;
    }
}

/**
 * Get compiled css file path.
 */
if (!function_exists('get_compiled_css_path')) {
    function get_compiled_css_path()
    {
        return array_reduce(['frontend', 'css'], function ($path, $dir) {
            $path = $path . $dir . '/';

            if (!file_exists($path)) {
                \JFolder::create($path);
            }

            return $path;
        }, JPATH_BASE . '/media/quix/');
    }
}

/**
 * Determine compiled css file existence.
 */
if (!function_exists('is_compiled_css_exists')) {
    function is_compiled_css_exists($file)
    {
        return file_exists(get_compiled_css_path() . "/{$file}");
    }
}

/**
 * Get comppiled css file path.
 */
if (!function_exists('get_compiled_css')) {
    function get_compiled_css($file)
    {
        return file_get_contents(get_compiled_css_path() . "/{$file}");
    }
}

/**
 * Get compiled js file path.
 */
if (!function_exists('get_compiled_js_path')) {
    function get_compiled_js_path()
    {
        return array_reduce(['frontend', 'js'], function ($path, $dir) {
            $path = $path . $dir . '/';

            if (!file_exists($path)) {
                \JFolder::create($path);
            }

            return $path;
        }, JPATH_BASE . '/media/quix/');
    }
}

/**
 * Determine compiled js file existence.
 */
if (!function_exists('is_compiled_js_exists')) {
    function is_compiled_js_exists($file)
    {
        return file_exists(get_compiled_js_path() . "/{$file}");
    }
}

/**
 * Get compiled js file.
 */
if (!function_exists('get_compiled_js')) {
    function get_compiled_js($file)
    {
        return file_get_contents(get_compiled_js_path() . "/{$file}");
    }
}

/**
 * Get compiled assets file path.
 */
if (!function_exists('get_compiled_assets_path')) {
    function get_compiled_assets_path()
    {
        return array_reduce(['frontend'], function ($path, $dir) {
            $path = $path . $dir . '/';

            if (!file_exists($path)) {
                \JFolder::create($path);
            }

            return $path;
        }, JPATH_BASE . '/media/quix/');
    }
}

/**
 * Determine Builder mode or preview mode
 */
if (!function_exists('checkQuixIsBuilderMode')) {
    function checkQuixIsBuilderMode()
    {
        $app = \JFactory::getApplication();
        if ($app->isAdmin()) {
            return false;
        }

        $input = $app->input;
        $option = $input->get('option');
        $view = $input->get('view', '');
        $layout = $input->get('layout', '');
        $builder = $input->get('builder', '');

        if ($option == 'com_quix' && $view == 'form' && $layout == 'iframe' && $builder == 'frontend') {
            return true;
        }

        return false;
    }
}
