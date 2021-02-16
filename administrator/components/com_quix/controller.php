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
 * Class QuixController
 *
 * @since  1.6
 */

require_once JPATH_COMPONENT . '/helpers/quix.php';

class QuixController extends JControllerLegacy
{
    /**
     * The default view.
     *
     * @var    string
     * @since  1.6
     */
    protected $default_view = 'dashboard';

    protected $Quixlistpages = ['dashboard', 'pages', 'collections', 'themes', 'integrations', 'elements', 'filemanager', 'rank', 'optimize', 'amp', 'help', 'config'];

    /**
     * Method to display a view.
     *
     * @param   boolean $cachable If true, the view output will be cached
     * @param   mixed $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return   JController This object to support chaining.
     *
     * @since    1.5
     */
    public function display($cachable = false, $urlparams = false)
    {
        QuixHelper::checkSystemPlugin();

        $view = $this->input->get('view', 'dashboard');
        $layout = $this->input->get('layout', 'default');
        $id = $this->input->getInt('id');
        $document = JFactory::getDocument();
        JFactory::getApplication()->input->set('view', $view);

        // Check for edit form.
        if ($view == 'page' && $layout == 'edit' && !$this->checkEditId('com_quix.edit.page', $id)) {
            // Somehow the person just went to the form - we don't allow that.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_quix&view=pages', false));

            return false;
        }
        // Check for edit form.
        if ($view == 'collection' && $layout == 'edit' && !$this->checkEditId('com_quix.edit.collection', $id)) {
            // Somehow the person just went to the form - we don't allow that.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_quix&view=collections', false));

            return false;
        }

        // if( 'dashboard' == $view or 'help' == $view or 'pages' == $view OR 'collections' == $view OR 'elements' == $view OR 'integrations' == $view OR 'filemanager' == $view )
        if (in_array($view, $this->Quixlistpages)) {
            JHtml::_('jquery.framework');
            // $document->addCustomTag('<link rel="preload" href="'.QUIX_URL . '/assets/js/materialize.js" as="script">');
            $document->addCustomTag('<link rel="preload" href="' . JUri::root(true) . '/administrator/components/com_quix/assets/style.css" as="style">');
            $document->addCustomTag('<link rel="preload" href="' . QUIX_URL . '/assets/css/admin.css" as="style">');

            $document->addStyleSheet(JUri::root(true) . '/administrator/components/com_quix/assets/style.css');
            $document->addStyleSheet(QUIX_URL . '/assets/css/admin.css');

            $document->addScript(JUri::root(true) . '/administrator/components/com_quix/assets/script.js');
            $document->addScript(QUIX_URL . '/assets/js/materialize.js');
            // $document->addCustomTag('<link rel="preload" href="'.JUri::root(true) . '/administrator/components/com_quix/assets/script.js" as="script">');
        }

        // Load the submenu.
        parent::display();
    }

    public function modal()
    {
        echo \ThemeXpert\View\View::getInstance()->make(QUIX_PATH . '/app/modal.php', []);
        die;
    }

    public function clear_cache()
    {
        header('Content-type: application/json');

        try {
            quix()->getCache()->clearCache();

            QuixHelper::cleanCache();
            QuixHelper::cachecleaner('lib_quix');
            QuixHelper::cachecleaner('lib_quix', 1);

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false]);
        }

        die;
    }

    public function Collections()
    {
        header('Content-type: application/json');
        // Get an instance of the generic articles model
        $input = JFactory::getApplication()->input;

        // Get an instance of the generic articles model
        $model = JModelLegacy::getInstance('Collections', 'QuixModel', ['ignore_request' => true]);

        // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', 999);

        if (!$input->get('details', false)) {
            $model->setState('list.select', 'a.id, a.uid, a.title, a.type');
        }

        $model->setState('filter.state', 1);

        // Access filter
        $access = !JComponentHelper::getParams('com_quix')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        $model->setState('filter.access', $access);

        // Retrieve Content
        $items = $model->getItems();

        echo new JResponseJson($items);

        JFactory::getApplication()->close();
    }

    public function Collection()
    {
        header('Content-type: application/json');

        // Get an instance of the generic articles model
        $input = JFactory::getApplication()->input;
        $model = JModelLegacy::getInstance('Collection', 'QuixModel', ['ignore_request' => true]);
        $id = $input->get('id');
        // Retrieve Content
        $item = $model->getItem($id);

        echo new JResponseJson($item);

        JFactory::getApplication()->close();
    }

    /*
    * url: index.php?option=com_quix&task=updateElement
    * data: : alias: joomla-article, status: 0,1,2, token: 0000=1
    * method: post data
    */
    public function updateElement()
    {
        header('Content-type: application/json');

        $input = JFactory::getApplication()->input;

        if (empty($input->get('alias', ''))) {
            $err = new Exception('Empty Alias not allowed');
            echo new JResponseJson($err);
            JFactory::getApplication()->close();
        } else {
            $alias = $input->get('alias', '');
            $model = $this->getModel('Element');
            $table = $model->getTable();
            $table->load(['alias' => $alias]);
            $status = $input->get('status', 0);

            QuixHelper::cachecleaner('lib_quix', true);
            QuixHelper::cachecleaner('lib_quix', false);

            if ($table->id) {
                $table->status = $status;
                if (!$table->store()) {
                    $err = new Exception($this->getError());
                    echo new JResponseJson($err);
                    JFactory::getApplication()->close();
                } else {
                    echo new JResponseJson('Element updated successfully!');
                    JFactory::getApplication()->close();
                }
            } else {
                // as not exist, create and set status
                $table->alias = $alias;
                $table->status = $status;

                if (!$table->store()) {
                    $err = new Exception($this->getError());
                    echo new JResponseJson($err);
                    JFactory::getApplication()->close();
                } else {
                    echo new JResponseJson('Element updated successfully!');
                    JFactory::getApplication()->close();
                }
            }
        }
    }

    /*
    * url: index.php?option=com_quix&task=addElement
    * data: : alias: joomla-article, status: 0,1,2, token: 0000=1
    * method: post data
    */
    public function addElement()
    {
        header('Content-type: application/json');

        $input = JFactory::getApplication()->input;

        if (empty($input->get('alias', ''))) {
            $err = new Exception('Empty Alias not allowed');
            echo new JResponseJson($err);
            JFactory::getApplication()->close();
        } else {
            $alias = $input->get('alias', '');
            $model = $this->getModel('Element');
            $table = $model->getTable();
            $table->load(['alias' => $alias]);
            if ($table->id) {
                $err = new Exception('Sorry! Element exist!');
                echo new JResponseJson($err);
                JFactory::getApplication()->close();
            } else {
                $status = $input->get('status', 0);
                $table->alias = $alias;
                $table->status = $status;

                if (!$table->store()) {
                    $err = new Exception($this->getError());
                    echo new JResponseJson($err);
                    JFactory::getApplication()->close();
                } else {
                    echo new JResponseJson('Element updated successfully!');
                    JFactory::getApplication()->close();
                }
            }
        }
    }

    /*
    * url: index.php?option=com_quix&task=removeElement
    * data: : alias: joomla-article, status: 0,1,2, token: 0000=1
    * method: post data
    */
    public function removeElement()
    {
        header('Content-type: application/json');

        $input = JFactory::getApplication()->input;

        if (empty($input->get('alias', ''))) {
            $err = new Exception('Empty Alias not allowed');
            echo new JResponseJson($err);
            JFactory::getApplication()->close();
        } else {
            $alias = $input->get('alias', '');
            $model = $this->getModel('Element');
            $table = $model->getTable();
            $table->load(['alias' => $alias]);

            QuixHelper::cachecleaner('lib_quix', true);
            QuixHelper::cachecleaner('lib_quix', false);

            if ($table->id) {
                if (!$table->delete()) {
                    $err = new Exception($this->getError());
                    echo new JResponseJson($err);
                    JFactory::getApplication()->close();
                } else {
                    echo new JResponseJson('Element removed successfully!');
                    JFactory::getApplication()->close();
                }
            } else {
                $err = new Exception('Sorry! Element not found.');
                echo new JResponseJson($err);
                JFactory::getApplication()->close();
            }
        }
    }

    /*
    * url: index.php?option=com_quix&task=Elements
    * method: get
    * return: json
    */
    public function Elements()
    {
        header('Content-type: application/json');
        // Get an instance of the generic articles model
        $input = JFactory::getApplication()->input;

        // Get an instance of the generic articles model
        $model = JModelLegacy::getInstance('Elements', 'QuixModel', ['ignore_request' => true]);

        // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', 999);

        if (!$input->get('details', false)) {
            $model->setState('list.select', 'a.*');
        }

        $model->setState('filter.state', '*');

        // Access filter
        $access = !JComponentHelper::getParams('com_quix')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        $model->setState('filter.access', $access);

        // Retrieve Content
        $items = $model->getItems();

        echo new JResponseJson($items);

        JFactory::getApplication()->close();
    }

    public function updateAjax()
    {
        // update svg icons
        // QuixHelper::getUpdateIconsList();

        // update fonts google
        QuixHelper::getUpdateGoogleFontsList();

        echo new JResponseJson('Quix');
        JFactory::getApplication()->close();
    }

    public function live()
    {
        echo new JResponseJson('Quix');
        JFactory::getApplication()->close();
    }

    public function verify()
    {
        $result = QuixHelper::verifyLicense();

        if (!is_object($result)) {
            echo new JResponseJson('SUCCESS', $result);
        } else {
            echo new JResponseJson($result);
        }

        JFactory::getApplication()->close();
    }

    public function updateSchema()
    {
        require_once JPATH_COMPONENT . '/helpers/schema.php';

        QuixHelperSchema::updateDB();

        $this->setMessage('Schema Updated successfully!', 'success');
        $this->setRedirect(JRoute::_('index.php?option=com_quix&view=dashboard', true));

        return true;
    }

    public function cleanPageAssets()
    {
        $cssfiles = JFolder::files(JPATH_ROOT . '/media/quix/frontend/css');
        array_map(
            function ($file) {
                if ($file == 'index.html') {
                    return;
                }
                JFile::delete(JPATH_ROOT . '/media/quix/frontend/css/' . $file);
            },
            $cssfiles
        );

        $jsfiles = JFolder::files(JPATH_ROOT . '/media/quix/frontend/js');
        array_map(
            function ($file) {
                if ($file == 'index.html') {
                    return;
                }
                JFile::delete(JPATH_ROOT . '/media/quix/frontend/js/' . $file);
            },
            $jsfiles
        );

        $this->setMessage('Page assets cleaned successfully!', 'success');
        $this->setRedirect('index.php?option=com_quix&view=help', true);

        return true;
    }
}
