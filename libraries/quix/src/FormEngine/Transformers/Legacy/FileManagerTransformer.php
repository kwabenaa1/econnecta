<?php

namespace ThemeXpert\FormEngine\Transformers\Legacy;

class FileManagerTransformer extends TextTransformer
{
    /**
     * Get file manager type.
     *
     * @param        $config
     * @param string $type
     *
     * @return string
     */
    public function getType($config, $type = "")
    {
        return "file-manager";
    }
}
