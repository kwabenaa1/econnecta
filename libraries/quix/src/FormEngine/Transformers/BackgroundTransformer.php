<?php

namespace ThemeXpert\FormEngine\Transformers;

class BackgroundTransformer extends TextTransformer
{
    /**
     * Background field supported types.
     */
    protected $types = [
        'classic',
        'gradient',
        'video'
    ];

    /**
     * Default background field type.
     */
    protected $type = 'classic';

    /**
     * array of config.
     */
    protected $config;

    /**
     * Transform the given configuration for the group repeater.
     *
     * @param $config
     *
     * @return array
     */
    public function transform($config, $path)
    {
        $c = parent::transform($config, $path);
        $this->path = $path;
        $this->config = $config;

        $c['name'] = $this->getName($config);
        $c['type'] = $this->getType($config);
        $c['label'] = $this->getLabel($config);
        $c['class'] = $this->getClass($config);
        $c['help'] = $this->getHelp($config);
        $c['schema'] = $this->getSchema($config);
        $c['value'] = $this->getValue($config);
        $c['default'] = $this->getValue($config);
        $c['placeholder'] = $this->getPlaceholder($config);
        $c['supportedTypes'] = [];
        $c['tab'] = $this->get($config, "tab", false);

        foreach($this->types as $type) {
            if($this->isRequireType($config, $type)) $c['supportedTypes'][] = $type;
        }
        
        $c['types'] = [
            'classic' => $this->defaultProperties(),
            'gradient' => [
                'type' => 'gradient',
                'properties' => [
                    'color_1' => '',
                    'color_2' => '#f36',
                    'type' => 'linear',
                    'direction' => 180,
                    'start_position' => 0,
                    'end_position' => 100,
                    'overlay' => false
                ]
            ],
            'video' => [
                'type' => 'video',
                'properties' => [
                    'url' => '',
                    'width' => '320',
                    'height' => '320',
                    'pause' => true
                ]
            ]
        ];
        $c['popover'] = $this->get($config, 'popover', false);
        
        return $c;
    }

    /**
     * Get code type.
     *
     * @param        $config
     * @param string $type
     *
     * @return string
     */
    public function getType($config, $type = "")
    {
        return "background";
    }

     /**
     * Get the background value.
     *
     * @param $config
     *
     * @return array|mixed|null
     */
    public function getValue($config)
    {
        $value = $this->get($config, "value", null);

        if(is_null($value)) {
            $requiredOpacity = isset($this->config['opacity']) ? $this->config['opacity'] : false;
            $requiredTransition = isset($this->config['transition']) ? $this->config['transition'] : true;

            $value['state'] = [
                'normal' => array_merge([
                    'required_opacity' => $requiredOpacity,
                    'opacity' => 0,
                    'required_transition' => $requiredTransition,
                    'transition' => 0.3
                ],
                $this->defaultProperties()),

                'hover' => array_merge([
                    'required_opacity' => $requiredOpacity,
                    'opacity' => 0,
                    'required_transition' => $requiredTransition,
                    'transition' => 0.3
                ],
                $this->defaultProperties())
            ];
        }

        return $value;
    }

    /**
     * Get default properties.
     */
    protected function defaultProperties()
    {
        $requiredParallax = isset($this->config['parallax']) ? $this->config['parallax'] : true;
        $jsParallax = isset($this->config['jsparallax']) ? $this->config['jsparallax'] : true;

        return [
            "type" => "classic",
            "properties" => [
                    'color' => '',
                    'src' => '',
                    'size' => 'cover',
                    'position' => 'center',
                    'repeat' => 'no-repeat',
                    'blend' => 'normal',
                    'required_parallax' => $requiredParallax,
                    'parallax' => false,
                    'parallax_method' => 'css',
                    'jsparallax' => $jsParallax
                ]
            ];
    }

    /**
     * Determine background required type.
     */
    protected function isRequireType($config, $type)
    {
        if(isset($config[$type]) and ($config[$type] == true)) {
            return true;
        } else if (isset($config[$type]) and ($config[$type] == false)) {
            return false;
        } else {
            return "video" == $type ? false : true;
        }
    }
}
