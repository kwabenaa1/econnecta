<?php

namespace ThemeXpert\FormEngine\Transformers;

class BorderTransformer extends TextTransformer
{
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
        $c['label'] = $this->getLabel($config);
        $c['class'] = $this->getClass($config);
        $c['help'] = $this->getHelp($config);
        $c['schema'] = $this->getSchema($config);
        $c['value'] = $this->getValue($config);
        $c['default'] = $this->getValue($config);
        $c['placeholder'] = $this->getPlaceholder($config);
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
        return "border";
    }

     /**
     * Get the border value.
     *
     * @param $config
     *
     * @return array|mixed|null
     */
    public function getValue($config)
    {
        $value = $this->get($config, "value", null);

        if(is_null($value)) {
            $value['state'] = [
                'normal' => $this->defaultProperties(),

                'hover' => $this->defaultProperties()
            ];
        }

        return $value;
    }

    /**
     * Get default properties.
     */
    protected function defaultProperties()
    {
        return [
            "properties" => [
                "border_type" => 'none',
                "border_radius" => [
                    "top" => "",
                    "left" => "",
                    "bottom" => "",
                    "right" => "",
                    "unit" => "px"
                ],
                "border_width" => [
                    "top" => "",
                    "left" => "",
                    "bottom" => "",
                    "right" => "",
                    "unit" => "px"
                ],
                "box_shadow" => [
                    'color' => '',
                    'spread' => 0,
                    'blur' => 10,
                    'horizontal' => 0,
                    'vertical' => 0,
                    'position' => 'outline'
                ],
                "border_color" => "",
                'transition' => 0.3
            ]
        ];
    }
}
