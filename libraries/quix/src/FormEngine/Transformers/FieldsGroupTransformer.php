<?php

namespace ThemeXpert\FormEngine\Transformers;

use ThemeXpert\FormEngine\ControlsTransformer;

class FieldsGroupTransformer extends TextTransformer
{
    /**
     * Instance of ControlsTransformer.
     *
     * @var \ThemeXpert\FormEngine\ControlsTransforme
     */
    protected $controlsTransformer;

    /**
     * Create a new instance of group repeater transformer.
     *
     * @param $controlsTransformer
     */
    public function __construct(ControlsTransformer $controlsTransformer)
    {
        $this->controlsTransformer = $controlsTransformer;
    }

    /**
     * Get fields group type.
     *
     * @param        $config
     * @param string $type
     *
     * @return string
     */
    public function getType($config, $type = "text")
    {
        return "fields-group";
    }

    /**
     * Get fields group schema.
     *
     * @param $config
     *
     * @return mixed|null
     */
    public function getSchema($config)
    {
        return $this->get($config, 'schema', []);
    }

    /**
     * Transform the group repeater.
     *
     * @param $config
     *
     * @return array
     */
    public function transform($config, $path)
    {
        $c = parent::transform($config, $path);

       // $c['value'] = $this->getSchema($config);
        $c['name'] = $this->getName($config);
        $c['type'] = $this->getType($config);
        $c['label'] = $this->getLabel($config);
        $c['class'] = $this->getClass($config);
        $c['help'] = $this->getHelp($config);
        $c['schema'] = $this->getSchema($config);
        $c['placeholder'] = $this->getPlaceholder($config);

        $newSchema = [];

        foreach($c['schema'] as $key => $schema) {
                $c['schema'][$key] = $this->controlsTransformer->transformControl($schema, $path);
        }

        $c['value'] = $c['schema'];
        $c['status'] = isset($config['status'])? $config['status'] : 'close';

        return $c;
    }
}
