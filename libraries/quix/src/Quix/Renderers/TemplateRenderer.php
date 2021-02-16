<?php

namespace ThemeXpert\Quix\Renderers;

use Mobile_Detect;
use ThemeXpert\View\View;
use ThemeXpert\View\Engines\PhpEngine;
use ThemeXpert\Quix\Renderers\Contracts\NodeRendererInterface;

class TemplateRenderer implements NodeRendererInterface
{
    /**
     * Quix Builder Type.
     * 
     * @var string 
     */
    protected $builder;

    /**
     * Instance of view.
     *
     * @var \ThemeXpert\View\View
     */
    protected $view;

    /**
     * Create a new instance of node reanderer.
     *
     * @param View          $view
     * @param Mobile_Detect $detect
     * @param               $nodes
     */
    public function __construct(View $view, Mobile_Detect $detect, $nodes)
    {
        $this->view = new View(new PhpEngine);

        $this->nodes = $nodes;

        $Mobile_Detect = new Mobile_Detect();
        if ($Mobile_Detect->isTablet()) {
            $device = 'tablet';
        } elseif ($Mobile_Detect->isMobile()) {
            $device = 'mobile';
        } else {
            $device = 'all';
        }

        $this->device = $device;
    }

    /**
     * Render node.
     *
     * @param $node
     *
     * @return string
     */
    public function renderNodeNode($node)
    {
        $schema = array_find_by($this->nodes, 'slug', $node['slug']);

        if (!$schema) {
            $name = is_string($node) ? $node : '';
            return "<!---- {node} - template node not found ---->";
        }

        /**
         * FIXME: throw exception
         */
        if (!file_exists($node['template_file'])) {
            return "<!--node {$node['slug']} view file {$node['template_file']} does not exist-->";
        }
        $data = [];

        if ($this->builder !== 'frontend') {
            $override_file = QUIX_TEMPLATE_PATH . '/nodes/' . $node['slug'] . "/{$node['slug']}.php";

            if (file_exists($override_file)) {
                return $this->view->make($override_file, $data);
            }
        } else {
            $override_file = QUIX_TEMPLATE_PATH . '/frontend/elements/' . $node['slug'] . '/partials/html.twig';

            if (file_exists($override_file)) {
                return $this->view->make($override_file, $data, $this->builder);
            }
        }

        return $this->view->make($node['template_file'], $data);
    }

    /**
     * Render node.
     *
     * @param $node
     *
     * @return string
     */
    public function renderElementNode($node)
    {
        $schema = array_find_by($this->nodes, 'slug', $node['slug']);

        if (!$schema) {
            return '<!----element not found---->';
        }

        $global = $this->getGlobalFilePath($schema);

        if (file_exists($global)) {
            include $global;
        }

        /**
         * FIXME: throw exception
         */
        if (!file_exists($schema['template_file'])) {
            return "<!--element {$node['slug']} file {$schema['template_file']} does not exist-->";
        }
        $data = [];

        if ($this->builder !== 'frontend') {
            $override_file = QUIX_TEMPLATE_PATH . '/elements/' . $node['slug'] . '/element.php';

            if (file_exists($override_file)) {
                return $this->view->make($override_file, $data);
            }

            $override_file = QUIX_TEMPLATE_PATH . '/overrides/' . $node['slug'] . '/element.php';

            if (file_exists($override_file)) {
                return $this->view->make($override_file, $data);
            }
        }

        return $this->view->make($schema['template_file'], $data);
    }

    /**
     * Get global file path.
     */
    protected function getGlobalFilePath($schema)
    {
        //DIRECTORY_SEPARATOR
        $global = explode('/', $schema['view_file']);
        array_pop($global);
        array_push($global, 'global.php');
        return implode('/', $global);
    }

    /**
     * Render single node.
     *
     * @param $nodes
     *
     * @return string
     */
    public function render($nodes, $item = null, $builder = 'classic')
    {
        $this->builder = $builder;

        return implode('', $this->renderNodes($nodes));
    }
}
