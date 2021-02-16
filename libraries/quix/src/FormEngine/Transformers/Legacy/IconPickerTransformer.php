<?php

namespace ThemeXpert\FormEngine\Transformers\Legacy;

class IconPickerTransformer extends TextTransformer
{
    /**
     * Get icon picker type.
     *
     * @param        $config
     * @param string $type
     *
     * @return string
     */
    public function getType($config, $type = "")
    {
        return "icon";
    }
}
