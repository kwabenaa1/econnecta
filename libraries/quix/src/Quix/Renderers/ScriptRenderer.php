<?php

namespace ThemeXpert\Quix\Renderers;

class ScriptRenderer extends NodeRenderer
{
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
            return "/* {$name} - script node not found*/";
        }

        $data = $this->getData($node, $schema);

        $scriptTemplate = str_replace('/style.php', '/partials/script.twig', $schema['dynamic_style_file']);

        $override_file = QUIX_TEMPLATE_PATH . '/frontend/elements/' . $node['slug'] . '/partials/script.twig';

        if (file_exists($override_file)) {
            return $this->view->make($override_file, $data, $this->builder);
        }

        return $this->view->make($scriptTemplate, $data, $this->builder);
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
