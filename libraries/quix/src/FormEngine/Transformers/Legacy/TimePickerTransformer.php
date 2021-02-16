<?php

namespace ThemeXpert\FormEngine\Transformers\Legacy;

class TimePickerTransformer extends TextTransformer
{
    /**
     * Get time picker type.
     *
     * @param        $config
     * @param string $type
     *
     * @return string
     */
    public function getType($config, $type = "")
    {
        return "time";
    }
}
