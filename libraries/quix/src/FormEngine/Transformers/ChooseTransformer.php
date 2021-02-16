<?php

namespace ThemeXpert\FormEngine\Transformers;

class ChooseTransformer extends TextTransformer
{
    /**
     * @var string
     */
    protected $defaultDesktopValue = [
        'label' => '',
        'icon' => '',
        'value' => ''
    ];

    /**
     * @var string
     */
    protected $defaultTabletValue = [
        'label' => '',
        'icon' => '',
        'value' => ''
    ];

    /**
     * @var string
     */
    protected $defaultPhoneValue = [
        'label' => '',
        'icon' => '',
        'value' => ''
    ];

    /**
     * Transform the choose.
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
        $value = $this->getValue($config);

        if ($this->getDepends($config)) $c['depends'] = $this->getDepends($config);

        $c['responsive'] = isset($config['responsive'])? $config['responsive'] : true;

        $c['options'] = isset($config['options'])? $config['options'] : [];

        if(! $c['responsive']) {
            $c['value'] = $this->getMergedValue($c, $value);
        } else {
            $c['value']['desktop'] = isset($value['desktop'])? $this->getMergedValue($c, $value['desktop']) : $this->defaultDesktopValue;
            $c['value']["tablet"] = isset($value['tablet'])? $this->getMergedValue($c, $value['tablet']) : $this->defaultTabletValue;
            $c['value']["phone"] = isset($value['phone'])? $this->getMergedValue($c, $value['phone']) : $this->defaultPhoneValue;
            $c['value']["responsive_preview"] = true;
        }

        $c['default'] = $c['value'];

        return $c;
    }

    /**
     * Get merged value
     */
    public function getMergedValue($config, $key)
    {
        if(empty($key) or is_array($key)) return $this->defaultDesktopValue;

        return array_merge($config['options'][ $key ], [ "value" => $key ]);
    }

    /**
     * Get choose value.
     *
     * @param $config
     *
     * @return mixed|null
     */
    public function getValue($config)
    {
        return $this->get($config, 'value', []);
    }
}
