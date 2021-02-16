<?php

namespace ThemeXpert\View;

use ThemeXpert\View\Engines\PhpEngine;
use ThemeXpert\View\Engines\TwigEngine;
use ThemeXpert\View\Engines\EngineInterface;

class View
{
    /**
     * Instance of engine interface.
     *
     * @var \ThemeXpert\View\Engines\EngineInterface
     */
    protected $compilerEngine;

    /**
     * Instance of view.
     *
     * @var object
     */
    protected static $instance;

    protected static $phpEngine;

    protected static $twigEngine;

    protected static $builder;

    /**
     * Create a new instance of viw.
     *
     * @param EngineInterface $compilerEngine
     */
    public function __construct(EngineInterface $compilerEngine, $builder = null)
    {
        $this->compilerEngine = $compilerEngine;

        self::$builder = $builder;
    }

    /**
     * Get view instance.
     *
     * @return object|View
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            if (checkQuixIsVersion2() && (self::$builder == 'frontend')) {
                self::$instance = new self(new TwigEngine);
            } else {
                self::$instance = new self(new PhpEngine);
            }
        }

        return self::$instance;
    }

    /**
     * Generating view from the given template file.
     *
     * @param $file
     * @param $data
     *
     * @return string
     */
    public function make($file, $data, $builder = null)
    {
        if ($builder == 'frontend') {
            if (!self::$twigEngine) {
                self::$twigEngine = new TwigEngine;
            }

            return self::$twigEngine->get($file, $data, $builder);
        } else {
            if (!self::$phpEngine) {
                self::$phpEngine = new phpEngine;
            }

            return self::$phpEngine->get($file, $data);
        }
    }
}
