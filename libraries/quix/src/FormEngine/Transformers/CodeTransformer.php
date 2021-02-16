<?php

namespace ThemeXpert\FormEngine\Transformers;

class CodeTransformer extends TextTransformer
{
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
        return "code";
    }

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

        $c['value'] = [];

        if ($this->getDepends($config)) $c['depends'] = $this->getDepends($config);

        $c['value'] = $this->getValue($config) == "" 
                    ? [ "code" => "", "mode" => "css" ]
                    : $this->getValue($config);

        return $c;
    }
}
