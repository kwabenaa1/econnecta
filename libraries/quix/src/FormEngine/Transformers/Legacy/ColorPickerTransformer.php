<?php

namespace ThemeXpert\FormEngine\Transformers\Legacy;

class ColorPickerTransformer extends TextTransformer
{
    /**
     * Get color picker value.
     *
     * @param $config
     *
     * @return mixed|null
     */
    public function getValue($config)
    {
        return $this->get($config, 'default', "");
    }
}
