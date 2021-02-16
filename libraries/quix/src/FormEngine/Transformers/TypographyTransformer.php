<?php

namespace ThemeXpert\FormEngine\Transformers;

class TypographyTransformer extends SliderTransformer
{
    /**
     * Get type for the typography.
     *
     * @param        $config
     * @param string $type
     *
     * @return string
     */
    public function getType($config, $type = "")
    {
        return "typography";
    }

    /**
     * Transform the slider.
     *
     * @param $config
     *
     * @return array
     */
    public function transform($config, $path)
    {
        $c = parent::transform($config, $path);

        $c['value'] = $this->getValue($config);
        $c['default'] = $this->getDefaultValue($config);
        $c['units'] = $this->getUnits($config);
        $c['popover'] = $this->get($config, 'popover', false);

        return $c;
    }

    /**
     * Get units.
     */
    public function getUnits($config)
    {
        $units = $this->get($config, "units", "px, %");
        
        if(is_array($units)) return $units;
        
        return array_map(function($value) {
            return trim($value);
        }, explode(",", $units));
    }

    /**
     * Get default value.
     */
    public function getDefaultValue()
    {
        return [
            "family" => "",
            "weight" => "",
            "size" => [
                'desktop' => 0,
                'tablet' => 0,
                'phone' => 0,
                'unit' => 'px'
            ],
            "transform" => '',
            "style" =>'',
            "decoration" =>'',
            "spacing" => [
                'desktop' => 0,
                'tablet' => 0,
                'phone' => 0,
                'unit' => 'px'
            ],
            "height" => [
                'desktop' => 0,
                'tablet' => 0,
                'phone' => 0,
                'unit' => 'em'
            ],
            "text_shadow" => [
                'color' => '',
                'blur' => 10,
                'horizontal' => 0,
                'vertical' => 0
            ],
        ];
    }

    /**
     * Get typography value.
     *
     * @param $config
     *
     * @return array|mixed|null
     */
    public function getValue($config)
    {
        $value = $this->get($config, "value", null);

        if ($value === null) {
            return $this->getDefaultValue();
        } else {
            $value = (array)$value;
        }

        $value = array_pick($value, [
            "family",
            "weight",
            "size",
            "transform",
            "style",
            "decoration",
            "spacing",
            "height",
            "text_shadow",
        ], true); //exclusive

        return $value;
    }

    /**
     * Get desktop value.
     *
     * @param $config
     * @return mixed
     */
    public function getDesktopValue($config)
    {
        return $this->getValue($config)['size'];
    }

    /**
     * Determine element is by default responsive mode.
     *
     * @return bool
     */
    public function isResponsive()
    {
        return true;
    }
}
