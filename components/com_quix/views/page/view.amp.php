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
use Joomla\Registry\Registry;

/**
 * View to edit
 *
 * @since  1.6
 */
class QuixViewPage extends JViewLegacy
{
    protected $state;

    protected $item;

    protected $params;

    protected $config;

    /**
     * Display the view
     *
     * @param   string  $tpl  Template name
     *
     * @return void
     *
     * @throws Exception
     */
    public function display($tpl = null)
    {
        JLoader::register('QuixHelperAMP', JPATH_COMPONENT_SITE . '/helpers/amp.php');

        $app = JFactory::getApplication();
        $user = JFactory::getUser();

        $this->state = $this->get('State');
        $this->item = $this->get('Data');
        $this->params = $this->state->get('params');
        $this->config = JComponentHelper::getComponent('com_quix')->params;

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));

            return false;
        }

        if (isset($this->item->id) && $this->item->id) {
            // hardcode type for builder use, so we know its page
            $this->item->type = 'page';

            // Check the view access to the article (the model has already computed the values).
            if ($this->item->params->get('access-view') == false && ($this->item->params->get('show_noauth', '0') == '0')) {
                $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                $app->setHeader('status', 403, true);

                return;
            }

            // count hits
            $this->get('Hit');

            // render quix content and trigger content plugin
            $this->item->text = quixRenderItem($this->item);
        } else {
            JError::raiseError(404, JText::_('JERROR_PAGE_NOT_FOUND'));
            return false;
        }

        $this->amp_html = QuixHelperAMP::prepareOutputAmp($this->item->text);

        //add custom code to jdoc
        $registry = new Registry;
        $params = $registry->loadString($this->item->params);
        $code = $params->get('code', '');
        if ($code) {
            $this->document->addCustomTag($code);
        }

        // add js & css from v2 only
        $codecss = $params->get('codecss', '');
        if ($codecss) {
            $this->document->addStyleDeclaration($codecss);
        }
        $codejs = $params->get('codejs', '');
        if ($codejs) {
            $this->document->addScriptDeclaration($codejs);
        }

        // now prepare document for metainfo
        $this->_prepareDocument();

        $this->setLayout('amp');

        parent::display($tpl);
    }

    /**
     * Prepares the document
     *
     * @return void
     *
     * @throws Exception
     */
    protected function _prepareDocument()
    {
        $app = JFactory::getApplication();
        $menus = $app->getMenu();
        $title = null;

        $registry = new Registry;
        if (!method_exists($registry, 'loadString')) {
            return;
        }

        // prepare metainfo
        $this->metadata = $registry->loadString($this->item->metadata);

        //get title from quix
        $this->meta_title = $this->metadata->get('title', '');

        // Because the application sets a default page title,
        // We need to get it from the menu item itself
        // give Menu priority
        $menu = $menus->getActive();
        if (isset($menu->id) and $menu->id) {
            $title = $menu->params->get('page_title', $this->meta_title);
        } else {
            $title = $this->meta_title;
        }

        if (empty($title)) {
            $title = $this->params->get('page_title', JText::_('COM_QUIX_DEFAULT_PAGE_TITLE'));
        }

        if ($app->get('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } elseif ($app->get('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }
        $this->title = $title;
        $this->document->setTitle($title);

        // set description
        $this->meta_desc = $this->metadata->get('desc', '');
        if (isset($menu->id) and $menu->id) {
            $description = $menu->params->get('menu-meta_description', $this->meta_desc);
        } else {
            $description = $this->meta_desc;
        }

        if ($description) {
            $this->document->setDescription($description);
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }

        // Quix Meta
        $addog = $this->metadata->get('addog');
        $addtw = $this->metadata->get('addtw');

        $this->addOpenGraph();

        if ($this->config->get('generator_meta', 1) && QuixHelper::isFreeQuix()) {
            $this->document->setMetadata('application-name', 'Quix Page Builder');
        }
    }

    public function addOpenGraph()
    {
        $app = JFactory::getApplication();
        $this->document->setMetadata('og:type', 'website', 'property');
        $this->document->setMetadata('og:site_name', $app->get('sitename'), 'property');
        $this->document->setMetadata('og:title', $this->meta_title, 'property');
        $this->document->setMetadata('og:description', $this->meta_desc, 'property');

        $this->document->setMetadata('title', $this->meta_title);
        $this->document->setMetadata('description', $this->meta_desc);

        if (!empty($this->metadata->get('image_intro'))) {
            $this->image_intro = $this->metadata->get('image_intro');
            if (
                !preg_match('/^(https?:\/\/)|(http?:\/\/)|(\/\/)|([a-z0-9-].)+(:[0-9]+)(\/.*)?$/', $this->image_intro)
            ) {
                $this->image_intro = \JURI::root() . $this->deslash('images/' . $this->image_intro);
            }

            $this->document->setMetadata('og:image', $this->image_intro, 'property');
        }

        $this->document->setMetadata('og:url', JURI::current(), 'property');
        $this->document->setMetadata('fb:app_id', $this->metadata->get('fb_appid', ''));

        return true;
    }

    public function addTwitterCard()
    {
        $this->document->setMetadata('twitter:card', 'summary');
        $this->document->setMetadata('twitter:site', $this->metadata->get('twitter_username', ''));
        $this->document->setMetadata('twitter:title', $this->meta_title);
        $this->document->setMetadata('twitter:description', $this->meta_desc);

        return true;
    }

    public function deslash($url)
    {
        $url = str_replace('//', '/', $url);

        return $url;
    }
}
