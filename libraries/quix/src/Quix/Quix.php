<?php

namespace ThemeXpert\Quix;

use Pimple\Container;

class Quix
{
    /**
     * Quix Builder Type.
     *
     * @var string
     */
    protected $builder;

    /**
     * Instance of different builder type.
     *
     * @var array
     */
    protected static $instance = [];

    /**
     * Get Quix Instance depends on builder type.
     */
    public function getInstance($builder)
    {
        $this->builder = $builder;

        if (!isset(self::$instance[$this->builder])) {
            $cache_time = 60 * 60 * 24;
            $should_cache = QUIX_CACHE;
            $container = new Container();
            $jcache = \JFactory::getCache('lib_quix', 'output');
            $jcache->setCaching(1);
            $cache = new Cache($jcache, $cache_time, $should_cache);

            self::$instance[$this->builder] = new Application($container, $cache, $this->builder);

            $method = 'init' . ucfirst(strtolower($this->builder)) . 'Builder';

            \call_user_func([$this, $method]);
        }

        return self::$instance[$this->builder];
    }

    /**
     * Init class builder elements, nodes, and presets.
     */
    protected function initClassicBuilder()
    {
        self::$instance[$this->builder]->getElementsBag()->fill(QUIX_PATH . '/app/elements', QUIX_URL . '/app/elements');
        self::$instance[$this->builder]->getNodesBag()->fill(QUIX_PATH . '/app/nodes', QUIX_URL . '/app/nodes');

        if (file_exists(QUIX_TEMPLATE_PATH . '/elements')) {
            self::$instance[$this->builder]->getElementsBag()->fill(QUIX_TEMPLATE_PATH . '/elements', QUIX_TEMPLATE_URL . '/elements');
        }

        if (file_exists(QUIX_TEMPLATE_PATH . '/nodes')) {
            self::$instance[$this->builder]->getNodesBag()->fill(QUIX_TEMPLATE_PATH . '/nodes', QUIX_TEMPLATE_URL . '/nodes');
        }

        if (file_exists(QUIX_TEMPLATE_PATH . '/quix.php')) {
            require QUIX_TEMPLATE_PATH . '/quix.php';
        }

        if (QUIX_EDITOR) {
            self::$instance[$this->builder]->getPresetsBag()->fill(QUIX_PATH . '/app/presets', QUIX_URL . '/app/presets');

            if (file_exists(QUIX_TEMPLATE_PATH . '/presets')) {
                self::$instance[$this->builder]->getPresetsBag()->fill(QUIX_TEMPLATE_PATH . '/presets', QUIX_TEMPLATE_URL . '/presets');
            }
        }
    }

    /**
     * Init frontend builder elements and nodes.
     */
    protected function initFrontendBuilder()
    {
        self::$instance[$this->builder]->getElementsBag()->fill(QUIX_PATH . '/app/frontend/elements', QUIX_URL . '/app/frontend/elements', [], $this->builder);
        self::$instance[$this->builder]->getNodesBag()->fill(QUIX_PATH . '/app/frontend/nodes', QUIX_URL . '/app/frontend/nodes', [], $this->builder);

        if (file_exists(QUIX_TEMPLATE_PATH . '/frontend/elements')) {
            self::$instance[$this->builder]->getElementsBag()->fill(QUIX_TEMPLATE_PATH . '/frontend/elements', QUIX_TEMPLATE_URL . '/frontend/elements', [], $this->builder);
        }

        if (file_exists(QUIX_TEMPLATE_PATH . '/frontend/nodes')) {
            self::$instance[$this->builder]->getNodesBag()->fill(QUIX_TEMPLATE_PATH . '/frontend/nodes', QUIX_TEMPLATE_URL . '/frontend/nodes', [], $this->builder);
        }

        if (file_exists(QUIX_TEMPLATE_PATH . '/frontend/quix.php')) {
            require QUIX_TEMPLATE_PATH . '/frontend/quix.php';
        }
    }

    public function content()
    {
    }
}
