<?php

namespace ThemeXpert\Quix\Renderers;

class StyleRenderer extends NodeRenderer
{
    /**
     * Quix Builder Type.
     * 
     * @var string 
     */
    protected $builder;
    
    /**
     * Render note.
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
            return "/* {$name} style node not found*/";
        }

        if ($this->isMobile && !$node['visibility']['xs']) {
            return "/* {$node['form']['advanced']['label']} hidden from mobile device */";
        } elseif ($this->isTablet && !$node['visibility']['sm']) {
            return "/* {$node['form']['advanced']['label']} hidden from tablet device */";
        }

        /**
         * FIXME: throw exception
         */
        if ($this->builder != 'frontend') {
            if (!file_exists($schema['dynamic_style_file'])) {
                return "/*style file {$schema['dynamic_style_file']} does not exist*/";
            }
        }
        
        $data = $this->getData($node, $schema);

        if ($this->builder !== 'frontend') {
            $override_file = QUIX_TEMPLATE_PATH . '/elements/' . $node['slug'] . '/style.php';

            if (file_exists($override_file)) {
                return $this->view->make($override_file, $data, $this->builder);
            }

            $data = $this->getData($node, $schema);

            $override_file = QUIX_TEMPLATE_PATH . '/overrides/' . $node['slug'] . '/style.php';
            if (file_exists($override_file)) {
                return $this->view->make($override_file, $data, $this->builder);
            }
        } else {
            $override_file = QUIX_TEMPLATE_PATH . '/frontend/elements/' . $node['slug'] . '/partials/style.twig';

            if (file_exists($override_file)) {
                return $this->view->make($override_file, $data, $this->builder);
            }
        }
        
        return $this->view->make($schema['dynamic_style_file'], $data, $this->builder);
    }

    /**
     * Render node.
     *
     * @param $nodes
     *
     * @return string
     */
    public function render($nodes, $item = null, $builder = 'classic')
    {
        $this->builder = $builder;

        return implode("\n", $this->renderNodes($nodes));
    }
}
