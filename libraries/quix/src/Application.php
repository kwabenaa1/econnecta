<?php 

namespace ThemeXpert;

class Application
{
    /**
     * Assets driver name.
     * 
     * @var string
     */
    protected $assetsDriver;

    /**
     * Run Quix application
     */
    public function run()
    {
        # Bootstrapping application
        $this->bootstrap();
    }

    /**
     * Bootstrapping application required file.
     */
    protected function bootstrap()
    {
        # Bootstrapping application required assets
        $this->bootstrapAssets();
    }

    /**
     * Bootstrapping assets.
     */
    protected function bootstrapAssets()
    {
        $this->loadJoomlaAssets();
    }

    /**
     * To load all joomla assets that required for the Quix application
     */
    protected function loadJoomlaAssets()
    {
        jimport('quix.app.drivers.joomla.functions');
        jimport('quix.app.drivers.joomla.joomla');
        jimport('quix.app.drivers.joomla.template');
        jimport('quix.app.drivers.joomla.css');
    }

    /**
     * Set asset platform name.
     *
     * @param $platform
     */
    public function setAssetPlatform($platform)
    {
        $this->assetsDriver = $platform;
    }
}