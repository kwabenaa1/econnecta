<?php

namespace ThemeXpert\FormEngine\Transformers;

class SliderTransformer extends TextTransformer
{
    /**
     * @var int
     */
    protected $defaultDesktopValue= 0;

    /**
     * @var int
     */
    protected $defaultTabletValue= 0;

    /**
     * @var int
     */
    protected $defaultPhoneValue= 0;

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

        $c['name'] = $this->getName($config);
        $c['type'] = $this->getType($config);
        $c['label'] = $this->getLabel($config);
        $c['class'] = $this->getClass($config);
        $c['help'] = $this->getHelp($config);
        $c['suffix'] = $this->getSuffix($config);
        $c['units'] = $this->getUnits($config);
        $c['min'] = $this->getMin($config);
        $c['max'] = $this->getMax($config);
        $c['step'] = $this->getStep($config);
        $c['value'] = [];

        if ($this->getDepends($config)) $c['depends'] = $this->getDepends($config);

        $value = $this->getValue($config);

        $c['responsive'] = isset($config['responsive'])? $config['responsive'] : true;

         if(! $c['responsive']) {
            if(! is_array($value) ) {
                $c['value'] = [
                    'value' => $value,
                    'unit' => $this->get($config, "defaultUnit", "px")
                ];
            } else {
                $c['value'] = $value;
            }
            
            $c['unit'] = $this->get($config, "defaultUnit", "px");
        } else {
            $c['value']['desktop'] = isset($value['desktop'])? $value['desktop'] : $this->getValue($config);
            $c['value']["tablet"] = isset($value['tablet'])? $value['tablet'] : $this->getValue($config);
            $c['value']["phone"] = isset($value['phone'])? $value['phone'] : $this->getValue($config);
            $c['value']["responsive_preview"] = true;
            $c['value']['unit'] = isset($value['unit'])? $value['unit'] : $this->get($config, "defaultUnit", "px");
        }

        $c['default'] = $c['value'];

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
     * Get max value.
     *
     * @param $config
     *
     * @return mixed|null
     */
    public function getMax($config)
    {
        return $this->get($config, 'max', 100);
    }

    /**
     * Get suffix.
     *
     * @param $config
     *
     * @return mixed|null
     */
    public function getSuffix($config)
    {
        return $this->get($config, 'suffix', "px");
    }

    /**
     * Get min value.
     *
     * @param $config
     *
     * @return mixed|null
     */
    public function getMin($config)
    {
        return $this->get($config, 'min', 0);
    }

    /**
     * Get step.
     *
     * @param $config
     *
     * @return mixed|null
     */
    public function getStep($config)
    {
        return $this->get($config, 'step', 1);
    }

    /**
     * Get slider value.
     *
     * @param $config
     *
     * @return mixed|null
     */
    public function getValue($config)
    {
        return $this->get($config, 'value', $this->getMin($config));
    }

    /**
     * Determine responsive mode.
     *
     * @param $c
     * @return mixed
     */
    protected function isResponsived($c)
    {
        return $c['slider']['responsive'] == true;
    }

    /**
     * Determine responsive preview mode.
     *
     * @param $c
     * @return bool
     */
    protected function isResponsivePreviewModed($c)
    {
        return isset($c['slider']["responsive_preview"]);
    }
}
