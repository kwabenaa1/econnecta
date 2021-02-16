<?php

namespace ThemeXpert\Quix\Renderers;

use Mobile_Detect;
use ThemeXpert\View\View;
use ThemeXpert\Quix\Renderers\Contracts\NodeRendererInterface;

class NodeRenderer implements NodeRendererInterface
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
     * Store form.
     *
     * @var mixed
     */
    protected $form;

    /**
     * Create a new instance of node reanderer.
     *
     * @param View          $view
     * @param Mobile_Detect $detect
     * @param               $nodes
     */
    public function __construct(View $view, Mobile_Detect $detect, $nodes)
    {
        $this->view = $view;

        $this->detect = $detect;

        $this->nodes = $nodes;

        $this->isTablet = $this->detect->isTablet();

        $this->isMobile = $this->detect->isMobile();

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
    public function renderNode($node)
    {
        $slug = (isset($node['slug']) ? $node['slug'] : '');
        $schema = array_find_by($this->nodes, 'slug', $slug);

        if (!$schema) {
            $name = is_string($node) ? $node : '';
            return "<!---- {$name} nodeRenderer not found ---->";
        }

        // print_r($this->device);die;
        switch ($this->device) {
            case 'tablet':

                // switch ($node['slug']) {
                //     case 'section':
                //     case 'row':
                //     case 'column':
                //         break;
                //     default:
                //         // print_r($node);die;
                //         break;
                // }

                if (!$node['visibility']['sm'] and !$node['visibility']['md']) {
                    if (isset($node['form']['advanced']['label'])) {
                        return "<!--- {$node['form']['advanced']['label']} hidden from tablet device ---!>";
                    } else {
                        return '<!--- {Item hidden from tablet device ---!>';
                    }
                }
                break;

            case 'mobile':
                if (!$node['visibility']['xs']) {
                    if (isset($node['form']['advanced']['label'])) {
                        return "<!--- {$node['form']['advanced']['label']} hidden from mobile device ---!>";
                    } else {
                        return '<!--- {Item hidden from mobile device ---!>';
                    }
                }
                break;
            case 'all':
            default:
                // continue
                break;
        }

        // if ($this->isMobile && !$node['visibility']['xs']) {
        //     return "<!--- {$node['form']['advanced']['label']} hidden from mobile device ---!>";
        // } elseif ($this->isTablet && !$node['visibility']['sm']) {
        //     return "<!--- {$node['form']['advanced']['label']} hidden from tablet device ---!>";
        // }

        /**
         * FIXME: throw exception
         */
        if ($this->builder != 'frontend') {
            if (!file_exists($schema['view_file'])) {
                return "<!--view file {$schema['view_file']} does not exist-->";
            }
        }

        $data = $this->getData($node, $schema);

        $global = $this->getGlobalFilePath($schema);

        if (file_exists($global)) {
            require_once $global;
        }

        if ($this->builder !== 'frontend') {
            $override_file = QUIX_TEMPLATE_PATH . '/elements/' . $node['slug'] . '/view.php';

            if (file_exists($override_file)) {
                return $this->view->make($override_file, $data, $this->builder);
            }

            $override_file = QUIX_TEMPLATE_PATH . '/overrides/' . $node['slug'] . '/view.php';

            if (file_exists($override_file)) {
                return $this->view->make($override_file, $data, $this->builder);
            }
        } else {
            $override_file = QUIX_TEMPLATE_PATH . '/frontend/elements/' . $node['slug'] . '/partials/html.twig';

            if (file_exists($override_file)) {
                return $this->view->make($override_file, $data, $this->builder);
            }
        }

        return $this->view->make($schema['view_file'], $data, $this->builder);
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
     * Get data
     *
     * FIXME: CACHE THIS
     *
     * @param $node
     * @param $schema
     *
     * @return array
     */
    public function getData($node, $schema)
    {
        if ($this->builder == 'classic') {
            $field = flatten_array(array_get($node, 'form', []));

            $field = $this->merge_data($field, flatten_array(array_get($schema, 'form', [])));
        } else {
            // loaded only required field data in the frontend builder
            // NOTE: avoiding schema merging for better performance in preview page,
            //       schema merging only happen when a user update his/her page from builder
            $field['title'] = isset($node['form']['advanced']['identifier'][0]['label'])
                              ? $node['form']['advanced']['identifier'][0]['label']
                              : '';
            $fieldid = (isset($node['form']['advanced']['identifier'][1]['id']) ? $node['form']['advanced']['identifier'][1]['id'] : '');
            $field['id'] = $field['identifier'][1]['value'] = $fieldid;
        }

        $visibility = array_get($node, 'visibility', []);

        return [
            'renderer' => $this,
            'title' => array_get($field, 'title', null),
            'id' => array_get($field, 'id', null),
            'type' => array_get($node, 'slug', null),
            'size' => array_get($node, 'size', []),
            'visibility' => $visibility,
            'visibilityClasses' => visibilityClasses($visibility),
            'field' => $field,
            'node' => $node,
        ];
    }

    /**
     * Merge data.
     *
     * @param $data
     * @param $form
     *
     * @return array
     */
    protected function merge_data($data, $form)
    {
        $form = array_reduce($form, function ($carry, $control) {
            $carry[$control['name']] = $control['value'];

            return $carry;
        }, []);

        $data = array_merge_recursive_distinct($form, $data);

        return $data;
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

    /**
     * Render nodes.
     *
     * @param $nodes
     *
     * @return array
     */
    public function renderNodes($nodes)
    {
        if (
            isset($nodes['type'])
            and in_array(strtolower($nodes['type']), ['editor', 'layout', 'article', 'header', 'footer', 'mainbody'])
            ) {
            return array_map([$this, 'renderNode'], $nodes['data']);
        }

        return array_map([$this, 'renderNode'], $nodes);
    }

    /**
     * Set form.
     *
     * @param $form
     */
    protected function setForm($form)
    {
        $this->form = $form;
    }
}
