<?php

namespace ThemeXpert\Quix\Element\Config;

use Symfony\Component\Yaml\Yaml;
use ThemeXpert\FormEngine\FormEngine;
use ThemeXpert\Config\Contracts\TransformerInterface;

class Transformer implements TransformerInterface
{
    /**
     * Instance of form engine.
     *
     * @var \ThemeXpert\FormEngine\FormEngine
     */
    protected $formEngine;

    /**
     * Append form.
     *
     * @var array
     */
    protected $appendForm = [];

    protected $builder = null;

    /**
     * Create a new instance of Transformer.
     *
     * @param FormEngine $formEngine
     */
    public function __construct(FormEngine $formEngine, $builder = null)
    {
        $this->formEngine = $formEngine;

        $this->builder = $builder;
    }

    /**
     * Transform element configuration.
     *
     * @param $config
     *
     * @return mixed
     */
    public function transform($config, $path = null)
    {
        $config['element_path'] = $path;
        $config['view_file'] = $this->getView($config);
        $config['thumb_file'] = $this->getThumbnail($config);
        $config['css_file'] = $this->getCss($config);
        $config['dynamic_style_file'] = $this->getStyle($config);
        $config['groups'] = $this->getGroups($config);
        $config['form'] = $this->getForm($config);
        $config['visibility'] = $this->getVisibility($config);
        $config['template_file'] = $this->getTemplate($config);

        return $config;
    }

    /**
     * Getting form.
     *
     * @param $config
     *
     * @return mixed
     */
    public function getForm($config)
    {
        $form = array_get($config, 'form', []);
        $path = array_get($config, 'element_path', []);

        // $fragments = explode("/", $path);

        // if(in_array("templates", $fragments)) {
        //     $startPushing = false;
        //     $path = [];

        //     foreach($fragments as $fragment) {
        //         if($fragment == "templates" or $startPushing) {
        //             array_push($path, $fragment);
        //             $startPushing = true;
        //         }
        //     }

        //     $path = "/" . implode("/", $path);
        // } else {
        //     $path = '/libraries/quix/app/elements';
        // }
        
        // if(checkQuixIsVersion2() && ($this->builder == "frontend") ) {
        if($this->builder == "frontend") {
            // override element yml form data
            $overridableYmlPath = QUIX_PATH . '/app/config/element.yml';
        } else {
            $overridableYmlPath = QUIX_PATH . '/app/config/append.yml';
        }

        if (file_exists($overridableYmlPath)) {
            $parsedElementYml = Yaml::parse($overridableYmlPath);

            if (!is_null($parsedElementYml)) {
                $this->appendForm = array_merge($this->appendForm, $parsedElementYml);
            }
        }

        // Local copy
        $appendForm = $this->appendForm;

        // If there is something in the config file then add it to the append file
        foreach ($appendForm as $tab => $controls) {
            if (array_key_exists($tab, $form)) {
                if (is_array($form[$tab])) {
                    $appendForm[$tab] = array_merge($controls, $form[$tab]);
                } else {
                    $appendForm[$tab] = array_merge($controls, []);
                }
            }
        }

        // Merge all
        $form = array_merge($form, $appendForm);

        return $this->formEngine->transform($form, $path);
    }

    /**
     * Get view.
     *
     * @param $config
     *
     * @return string
     */
    protected function getView($config)
    {
        if (array_get($config, 'view')) {
            return $config['path'] . '/' . $config['view'];
        } else {
            return $config['path'] . '/view.php';
        }
    }

    /**
     * Get Template.
     *
     * @param $config
     *
     * @return string
     */
    protected function getTemplate($config)
    {
        if (array_get($config, 'template_file')) {
            return $config['path'] . '/' . $config['template_file'];
        } else {
            return $config['path'] . '/element.php';
        }
    }

    /**
     * Get thumbnail.
     *
     * @param $config
     *
     * @return string
     */
    protected function getThumbnail($config)
    {
        if (array_get($config, 'thumb')) {
            return $config['url'] . '/' . $config['thumb'];
        } else {
            if (file_exists($config['path'] . '/element.svg')) {
                return $config['url'] . '/element.svg';
            } else {
                if (file_exists($config['path'] . '/element.png')) {
                    return $config['url'] . '/element.png';
                } else {
                    return QUIX_DEFAULT_ELEMENT_IMAGE;
                }
            }
        }
    }

    /**
     * Get style.
     *
     * @param $config
     *
     * @return string
     */
    protected function getStyle($config)
    {
        if (array_get($config, 'style')) {
            return $config['path'] . '/' . $config['style'];
        } else {
            return $config['path'] . '/style.php';
        }
    }

    /**
     * Get css.
     *
     * @param $config
     *
     * @return string
     */
    protected function getCss($config)
    {
        if (array_get($config, 'css')) {
            return $config['url'] . '/' . $config['css'];
        } else {
            return $config['url'] . '/element.css';
        }
    }

    /**
     * Get groups.
     *
     * @param $config
     *
     * @return array
     */
    protected function getGroups($config)
    {
        return (array)array_get($config, 'groups', []);
    }

    /**
     * Get visibility.
     *
     * @param $config
     *
     * @return array
     */
    protected function getVisibility($config)
    {
        return ['lg' => true, 'md' => true, 'sm' => true, 'xs' => true];
    }

    /**
     * Set append form.
     *
     * @param mixed $appendForm
     */
    public function setAppendForm($appendForm)
    {
        $this->appendForm = $appendForm;
    }
}
