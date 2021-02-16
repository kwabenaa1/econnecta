<?php

namespace ThemeXpert\FormEngine\Transformers;

class MediaTransformer extends TextTransformer
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
        return "media";
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

        $c['filters'] = $this->getFilters($config);
        $c['value'] = [];
        $c['value']['url'] = $this->getValue($config);
        $c['value']['media'] = [];

        if( 
            isset($c['value']['media']['type'])
            and ( $c['value']['media']['type'] == "svg" )
        ) {
            $c['value']['media']['svg'] = [ "color" => "red", "size" => "30px" ];
        }
        
        // set hideStyle
        $c['showstyle'] = $this->get($config, "showstyle", false);
        return $c;
    }

    /**
     * Get filters
     */
    public function getFilters($config)
    {
        return $this->get($config, "filters", "image,icon,video,unsplash");
    }
}
