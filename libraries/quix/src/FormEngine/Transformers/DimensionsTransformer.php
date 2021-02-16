<?php

namespace ThemeXpert\FormEngine\Transformers;

class DimensionsTransformer extends TextTransformer
{
    /**
     * Transform text.
     *
     * @param $config
     *
     * @return array
     */
    public function transform($config, $path)
    {
        $c = parent::transform($config, $path);

        $c['value'] = $this->getValue($config);
        $c['units'] = $this->getUnits($config);
        $c['name'] = $this->getName($config);
        $c['type'] = $this->getType($config);
        $c['label'] = $this->getLabel($config);
        $c['class'] = $this->getClass($config);
        $c['help'] = $this->getHelp($config);
        $c['placeholder'] = $this->getPlaceholder($config);

        return $c;
    }

    /**
     * Get units.
     */
    public function getUnits($config)
    {
        $units = $this->get($config, "units", "px, %");

        return array_map(function($value) {
            return trim($value);
        }, explode(",", $units));
    }

    /**
     * Get the dimensions type.
     *
     * @param        $config
     * @param string $type
     *
     * @return string
     */
    public function getType($config, $type = "")
    {
        return "dimensions";
    }

    /**
     * Get the dimensions value.
     *
     * @param $config
     *
     * @return array|mixed|null
     */
    public function getValue($config)
    {
        $value = $this->get($config, "value", null);

        if(!isset($config['responsive'])) {
            $config['responsive'] = false;
        }

        $responsive = true;

        if ($value === null) {
            return [
                "top" => "",
                "left" => "",
                "bottom" => "",
                "right" => "",
                "responsive" => $responsive,
                "responsive_preview" => false,
                "tablet" => [
                    "top" => "",
                    "left" => "",
                    "bottom" => "",
                    "right" => ""
                ],
                "phone" => [
                    "top" => "",
                    "left" => "",
                    "bottom" => "",
                    "right" => ""
                ],
                "unit" => "px"
            ];
        } else {
            $defaultValue = (array)$value;

            if(!isset($value["responsive_preview"])) {
                $value["responsive_preview"] = false;
                $value["responsive"] = $responsive;

                $value["tablet"] = [
                    "top" => "",
                    "left" => "",
                    "bottom" => "",
                    "right" => ""
                ];

                $value["phone"] = [
                    "top" => "",
                    "left" => "",
                    "bottom" => "",
                    "right" => ""
                ];

                $value["unit"] = "px";
            }

            $value = array_merge($value, $defaultValue);
        }

        $value = array_pick($value,
            ["top", "left", "bottom", "right", "desktop", "phone", "tablet", "responsive_preview", "responsive", "unit"],
            true); //exclusive

        return $value;
    }
}
