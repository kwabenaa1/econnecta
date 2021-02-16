<?php

namespace ThemeXpert\View\Engines;

use JComponentHelper;
use Twig\Environment;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use Twig_Extension_Debug;
use Twig\Loader\ArrayLoader;
use ThemeXpert\Image\ResponsiveImage;
use Joomla\CMS\Crypt\Crypt;

class TwigEngine implements EngineInterface
{
    /**
     * Get the evaluated contents of the view.
     *
     * @param  string $path
     * @param  array  $data
     *
     * @return string
     */
    public function get($path, array $data = [])
    {
        $output = '';
        $path = $this->getPath($path);

        if (is_array($path)) {
            foreach ($path as $p) {
                $output .= $this->getContent($p, $data) . ';';
            }

            return $output;
        } else {
            return $this->getContent($path, $data);
        }
    }

    /**
     * get content
     */
    protected function getContent($path, $data)
    {
        // first check ACL
        $user = \JFactory::getUser();
        // If no access filter is set, the layout takes some responsibility for display of limited information.
        $groups = $user->getAuthorisedViewLevels();
        // $fieldid = isset($data['identifier'][1]['id']) ? $data['identifier'][1]['id'] : '';
        // $id = isset($data['identifier'][1]['value']) ? $data['identifier'][1]['value'] : $fieldid;
        $formIdentifier = isset($data['node']['form']['advanced']['identifier']) ? $data['node']['form']['advanced']['identifier'] : [];
        if ($formIdentifier) {
            flatten_array_ref($formIdentifier);
        }

        $acl = isset($formIdentifier['acl']) ? $formIdentifier['acl'] : '';

        if (!empty($acl) and !in_array($acl, $user->groups) and !in_array($acl, $groups)) {
            return '';
        }

        if (!file_exists($path)) {
            return '';
        }

        // form data...
        $data = array_merge($data, $data['field']);
        unset($data['field']);

        $visibility = '';

        foreach ($data['visibility'] as $key => $vs) {
            if ($vs) {
                $visibility .= $key . ' ';
            }
        }

        $data['visibility'] = $visibility;

        $data['grid'] = ''; //$this->getGrid($data);

        $config = \JComponentHelper::getParams('com_media');
        $imagePath = $config->get('image_path', 'images');

        $data['IMAGE_PATH'] = $imagePath;
        $data['FILE_MANAGER_ROOT_URL'] = \JURI::root() . $imagePath;

        // twig loading....
        $loader = new ArrayLoader([
            'view_content' => '{% autoescape false %} ' . $this->getQuixFileContent($path) . ' {% endautoescape %} ',
            'global.twig' => "{% autoescape false %} \n" . $this->getQuixFileContent(QUIX_PATH . '/app/frontend/global.twig') . " \n {% endautoescape %} ",
            'animation.twig' => "{% autoescape false %} \n" . $this->getQuixFileContent(QUIX_PATH . '/app/frontend/animation.twig') . " \n {% endautoescape %} ",
            'elements/form/partials/macro.html' => "{% autoescape false %} \n" . $this->getQuixFileContent(QUIX_PATH . '/app/frontend/elements/form/partials/macro.twig') . " \n {% endautoescape %} ",
        ]);

        if (QUIX_DEBUG) {
            $twig = new Environment($loader, ['debug' => true, 'cache' => false]);
            $twig->addExtension(new Twig_Extension_Debug());
            $twig->addGlobal('session', $_SESSION);
        } else {
            $twig = new Environment($loader, ['debug' => false]);
            $twig->addGlobal('session', $_SESSION);
        }

        // register field function
        $twig->addFunction($this->getFieldFunction($data));
        $twig->addFunction($this->getWrapperFunction($data));
        $twig->addFunction($this->getformFooterFunction($data));
        $twig->addFunction($this->getImageFunction($data));
        $twig->addFunction($this->getPrepareSvgSizeValueFunction($data));
        $twig->addFunction($this->getPrepareResponsiveValueFunction($data));
        $twig->addFunction($this->getPrepareWidthValueFunction($data));
        $twig->addFunction($this->getClassNamesFunction($data));
        $twig->addFunction($this->getVisibilityClassFunction($data));
        $twig->addFunction($this->getVisibilityClassNodeFunction($data));
        $twig->addFunction($this->getRawFunction($data));
        $twig->addFunction($this->getFileContentFunction($data));
        $twig->addFunction($this->getStartsWithFunction($data));
        $twig->addFunction($this->getJoomlaModuleFunction($data));
        $twig->addFunction($this->getElementApiCallFunction($data));
        $twig->addFunction($this->getGetOpacityFunction($data));
        $twig->addFunction($this->getFieldsGroupFunction($data));
        $twig->addFunction($this->getStartTagFunction($data));
        $twig->addFunction($this->getLoadSvgFunction($data));
        $twig->addFunction($this->getImageUrlFunction($data));
        $twig->addFunction($this->getGetQuixElementPathFunction($data));
        $twig->addFunction($this->getAllFieldFunction($data));
        $twig->addFunction($this->getPrepareContentFunction($data));
        $twig->addFunction($this->getValidateJoomlaCaptchaFunction($data));
        $twig->addFunction($this->getLessThanFunction($data));
        $twig->addFunction($this->getGreaterThanFunction($data));
        $twig->addFunction($this->getGreaterThanSignFunction($data));
        $twig->addFunction($this->getVideoFunction($data));
        $twig->addFunction($this->getCaptchaPublicKeyFunction($data));
        $twig->addFunction($this->getLoadElementAssetFunction($data));

        // register with filter
        $twig->addFilter($this->getWrapFilter($data));
        $twig->addFilter($this->getLinkFilter($data));
        $twig->addFilter($this->getJsonDecodeFilter($data));
        $twig->addFilter($this->getRemoveLinesFilter($data));

        // return $rendered = $twig->render(
        //     'view_content',
        //     $data
        // );

        /**
         * Fourth issue: Quix - HTML tags rendered as plain text
         * First reported: April 9th, 2019
         * Link to post: https://www.themexpert.com/forum/html-shows-on-front-end-in-quixpro2-4-4
         * Customer reported that after updating to Quix 2.4.4 all HTML tags on Titles are being rendered as plain text, I assume it has to do with the fix that was implemented to avoid contractions (It's) to break the slider Pro. Might be best to add Styling options for individual Accordion titles, at least padding. Video of the issue: https://www.loom.com/share/b81e1459a6b64076ac539f266be17ab0
         */
        return $rendered = str_replace(['&lt;', '&gt;'], ['<', '>'], $twig->render(
            'view_content',
            $data
        ));
    }

    /**
     * Get wrap filter.
     *
     * @param $data data of field
     */
    protected function getWrapFilter($data)
    {
        return new Twig_SimpleFilter('wrap', function ($value, $tag) use ($data) {
            return new \Twig_Markup("<$tag> $value </$tag>", 'UTF-8');
        });
    }

    /**
     * load File content after check
     *
     * @param $data data of field
     */
    protected function getQuixFileContent($path)
    {
        if (!file_exists($path)) {
            return '';
        }

        return file_get_contents($path);
    }

    /**
     * Remove lines filter.
     *
     * @param $data data of field
     */
    protected function getRemoveLinesFilter($data)
    {
        return new Twig_SimpleFilter('removeLines', function ($value) use ($data) {
            $value = trim(preg_replace('/\s+/', ' ', $value));

            return new \Twig_Markup("$value", 'UTF-8');
        });
    }

    /**
     * Get image source link
     */
    protected function getSrcLink($src)
    {
        $config = JComponentHelper::getParams('com_media');
        $imagePath = $config->get('image_path', 'images');

        if (
            preg_match('/^(https?:\/\/)|(http?:\/\/)|(\/\/)|([a-z0-9-].)+(:[0-9]+)(\/.*)?$/', $src)
        ) {
            return $src;
        }

        return \JURI::root() . $imagePath . '/' . $src;
    }

    /**
     * Get json decode.
     */
    protected function getJsonDecodeFilter($data)
    {
        return new Twig_SimpleFilter('json_decode', function ($value) use ($data) {
            return json_decode($value);
        });
    }

    /**
     * Get prepareSvgSizeValue
     *
     * @param $data data of field
     */
    protected function getPrepareSvgSizeValueFunction($data)
    {
        return new Twig_SimpleFunction('prepareSvgSizeValue', function ($size) use ($data) {
            if (is_array($size)) {
                return $size;
            }

            return[
                'value' => $size,
                'unit' => 'px'
            ];
        });
    }

    /**
     * Get prepareResponsiveValue
     *
     * @param $data data of field
     */
    protected function getPrepareResponsiveValueFunction($data)
    {
        return new Twig_SimpleFunction('prepareResponsiveValue', function ($responsive) use ($data) {
            if (isset($responsive['desktop'])) {
                $responsive['unit'] = empty($responsive['unit']) ? 'px' : $responsive['unit'];
                return $responsive;
            } else {
                if (!isset($responsive['value'])) {
                    $newResponsive = [];
                    $newResponsive['desktop'] = '';
                    $newResponsive['tablet'] = '';
                    $newResponsive['phone'] = '';
                    $newResponsive['unit'] = 'px';
                    return $newResponsive;
                } else {
                    $newResponsive = $responsive['value'];
                    $newResponsive['unit'] = empty($responsive['unit']) ? 'px' : $responsive['unit'];
                    return $newResponsive;
                }
            }
        });
    }

    /**
     * Get prepareWidthValue
     *
     * @param $data data of field
     */
    protected function getPrepareWidthValueFunction($data)
    {
        return new Twig_SimpleFunction('prepareWidthValue', function ($width) use ($data) {
            if (isset($width['unit'])) {
                return $width;
            } else {
                return [
                    'unit' => '%',
                    'value' => $width
                ];
            }

            return $width;
        });
    }

    /**
     * Get image function.
     *
     * @param $data data of field
     */
    protected function getImageFunction($data)
    {
        return new Twig_SimpleFunction('image', function ($src, $alt = '', $cls = '', $attr = '', $force = false) use ($data) {
            $input = \JFactory::getApplication()->input;
            $imagePath = $data['IMAGE_PATH'];
            /**
             * SInce the Title text is being used to fill the alt="" tag also, when you use contractions (We're instead of We are, or haven't instead of have not), the code gets messed up causing the image not to load.
             *
             * @see https://www.themexpert.com/forum/issue-with-slider-pro
             * @see https://www.useloom.com/share/637cf48c69584e39a29b45e3a9eac49e
             */
            $alt = str_replace("'", '&apos;', $alt);

            $ifApplied = false;
            $wantImageOptimization = isset($_GET['image_optimization']);

            if (is_array($src)) {
                $src = $src['url'];
            }
            if ($input->get('format', 'html') == 'amp') {
                return new \Twig_Markup("<img src='{$src}' />", 'UTF-8');
            }
            
            if (strpos($src, 'data:', 0) !== false || strpos($src, '//', 0) !== false) {
                $ifApplied = true;
                $src = $src;
            } elseif (strpos($src, 'libraries', 0) !== false) {
                $ifApplied = true;
                $src = \JURI::root() . '/' . $src;
            } elseif (strpos($src, $imagePath, 0) !== false) {
                $ifApplied = true;
                if(strpos($src, $imagePath, 0) == 0){
                    $src = \JURI::root() . $src;
                }else{
                    $src = \JURI::root() . $imagePath . $src;
                }
            } else {
                $src = ltrim($src, '/');
            }

            if (!$ifApplied) {
                // value of image optimized will come from page params.
                global $isImageOptimized;
                global $userWantOptimization;

                if ($isImageOptimized and filter_var($userWantOptimization, FILTER_VALIDATE_BOOLEAN)) {
                    $hasWebpImage = true;
                    global $responsiveImagesMapper;

                    // $readyToGo = isset($responsiveImagesMapper['/' . $src]) ? true : false;
                    $readyToGo = $this->checkIfReadyToGoResponsive($responsiveImagesMapper, $src);

                    if ($this->optimizedImagesExists($src) and $readyToGo) {
                        list($src, $srcset, $sizes, $mini) = $this->responsiveImage($src);
                        $srcurl = isset($mini) && !empty($mini) ? $mini : $src; //sizes='{$sizes}'
                        return new \Twig_Markup("<img data-src='{$srcurl}' data-srcset='{$srcset}' alt='{$alt}' class='{$cls}' {$attr} data-qx-img/>", 'UTF-8');
                    }
                }

                $src = $this->getSrcLink($src);
            }

            return new \Twig_Markup("<img src='{$src}' alt='{$alt}' class='{$cls}' {$attr} data-qx-img/>", 'UTF-8');
        });
    }

    protected function checkIfReadyToGoResponsive($imgs, $src)
    {
        if (!isset($imgs['/' . $src])) {
            return false;
        }

        $hasValue = false;
        $imgset = $imgs['/' . $src];

        foreach ($imgset as $key => $set) {
            $hasValue = count($set);

            if ($hasValue) {
                break;
            }
        }

        return $hasValue;
    }

    protected function optimizedImagesExists($src)
    {
        $srcSplit = explode('.', $src);
        $extension = array_pop($srcSplit);

        $srcWithoutExtension = implode('.', $srcSplit);

        $hasWebpImage = true;

        if (function_exists('imagewebp')) {
            $hasWebpImage = count(glob(JPATH_ROOT . '/media/quix/cache/images/' . ($srcWithoutExtension) . '-*.webp'));
            if (!$hasWebpImage) {
                $hasWebpImage = file_exists(JPATH_ROOT . '/media/quix/cache/images/' . ($srcWithoutExtension) . '.webp');
            }
            // $hasWebpImage = count(glob(JPATH_ROOT . '/media/quix/cache/images/' . $srcWithoutExtension . '-*.webp')) == 5;
        }

        // $hasOriginalImageFormat = count(glob(JPATH_ROOT . '/media/quix/cache/images/' . $srcWithoutExtension . '-*.jpeg')) == 5; // 5 means, we only support 5 types of device.
        $hasOriginalImageFormat = count(glob(JPATH_ROOT . '/media/quix/cache/images/' . ($srcWithoutExtension) . '-*.jpeg')); // 5 means, we only support 5 types of device.
        if (!$hasOriginalImageFormat) {
            $hasOriginalImageFormat = file_exists(JPATH_ROOT . '/media/quix/cache/images/' . ($srcWithoutExtension) . '.jpeg');
        }

        if ($hasWebpImage and $hasOriginalImageFormat) {
            return true;
        }

        return false;
    }

    protected function responsiveImage($src)
    {
        // check start with /
        $baseURL = \JUri::base() . 'media/quix/cache/images/';
        $srcSplit = explode('.', $src);
        $extension = array_pop($srcSplit);
        $srcWithoutExtension = implode('.', $srcSplit);

        $breakPoints = ['mini', 'mobile', 'tablet', 'desktop', 'large_desktop'];
        global $responsiveImagesMapper;

        $sets = $responsiveImagesMapper['/' . $src];

        // $sources = array_map(function ($breakPoint) use ($sets, $src, $srcWithoutExtension, $baseURL) {
        //     $source = $baseURL . "{$srcWithoutExtension}-{$breakPoint}";
        //     return $source;
        // }, $breakPoints);

        $config = JComponentHelper::getParams('com_quix');
        $responsive_image = (array)$config->get('responsive_image');

        // $webpSrcSet = "
        //     {$sources[4]}.webp {$responsive_image['mini']}w,
        //     {$sources[3]}.webp {$responsive_image['mobile']}w,
        //     {$sources[2]}.webp {$responsive_image['tablet']}w,
        //     {$sources[1]}.webp {$responsive_image['desktop']}w,
        //     {$sources[0]}.webp {$responsive_image['large_desktop']}w";

        // $jpegSrcSet = "
        //     {$sources[4]}.jpeg {$responsive_image['mini']}w,
        //     {$sources[3]}.jpeg {$responsive_image['mobile']}w,
        //     {$sources[2]}.jpeg {$responsive_image['tablet']}w,
        //     {$sources[1]}.jpeg {$responsive_image['desktop']}w,
        //     {$sources[0]}.jpeg {$responsive_image['large_desktop']}w";

        $jpegSrcSet = '';

        $breakPointsInNumber = [];

        $miniImage = $baseURL . ltrim($sets['jpeg'][0], '/');
        foreach ($sets['jpeg'] as $key => $set) {
            $b = $responsive_image[$breakPoints[$key]];
            $jpegSrcSet .= $baseURL . ltrim($set, '/') . " {$b}w, ";

            $breakPointsInNumber[] = $b;
        }

        $sizes = '';
        sort($breakPointsInNumber);
        foreach ($breakPointsInNumber as $point) {
            // max pixel value is used for loading proper image for the proper device
            // you have to use css rules [ width: 100% ] to make your image full width
            // you uploaded image won't full width automatically.
            $maxPixel = round($point / 2);
            $sizes .= "(max-width: {$point}px) {$point}px, ";
        }

        if ($sets['webp']) {
            $miniImage = $baseURL . ltrim($sets['webp'][0], '/');
        }
        $webpSrcSet = '';
        foreach ($sets['webp'] as $key => $set) {
            $b = $responsive_image[$breakPoints[$key]];

            $webpSrcSet .= $baseURL . ltrim($set, '/') . " {$b}w, ";
        }

        $largeDesktop = $responsive_image['large_desktop'];
        $desktop = $responsive_image['desktop'];
        $tablet = $responsive_image['tablet'];
        $mobile = $responsive_image['mobile'];
        $mini = $responsive_image['mini'];

        // $sizes = "
        //     (max-width: {$responsive_image['mini']}px) {$mini}px,
        //     (max-width: {$responsive_image['mobile']}px) {$mobile}px,
        //     (max-width: {$responsive_image['tablet']}x) {$tablet}px,
        //     (max-width: {$responsive_image['desktop']}px) {$desktop}px,
        //     (max-width: {$responsive_image['large_desktop']}px) {$largeDesktop}px";

        $srcset = $jpegSrcSet;

        global $userWantWebp;

        if ((strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') != false) and function_exists('imagewebp') and filter_var($userWantWebp, FILTER_VALIDATE_BOOLEAN)) {
            $srcset = $webpSrcSet;
        }

        $src = str_replace(['JPG', 'JPEG'], ['jpeg', 'jpeg'], $src);

        return [$baseURL . $src, $srcset, $sizes, $miniImage];
    }

    /**
     * Get video function.
     *
     * @param $data data of field
     */
    protected function getVideoFunction($data)
    {
        return new Twig_SimpleFunction('video', function ($id, $src, $poster, $attr = ' controls crossorigin playsinline settings ') use ($data) {
            $source = $this->getSrcLink($src);
            $poster = $this->getSrcLink($poster);
            $extension = explode('.', $src);
            $type = 'video/' . $extension[sizeof($extension) - 1];

            return new \Twig_Markup("<video id='$id' poster='{$poster}'{$attr}>
                <source type='{$type}' src='$source' />
                Your browser does not support the video tag.
            </video>", 'UTF-8');
        });
    }

    /**
     * Get video function.
     *
     * @param $data data of field
     */
    protected function getCaptchaPublicKeyFunction($data)
    {
        return new Twig_SimpleFunction('captchaPublicKey', function () use ($data) {
            $captcha = \JFactory::getConfig()->get('captcha');
            if ($captcha != '0' && $captcha != '' && $captcha != null) {
                $plugin = \JPluginHelper::getPlugin('captcha', $captcha);
                if ($plugin) {
                    $params = new \JRegistry($plugin->params);
                    return $params->get('public_key');
                } else {
                    return '';
                }
            }

            return '';
        });
    }
    
    
    /**
     * Load assets
     *
     * @param $data data of field
     */
    protected function getLoadElementAssetFunction($data)
    {
        return new Twig_SimpleFunction('loadelementasset', function ($type, $name, $url, $root = 'quix_url') use ($data) {
            if ($root == 'quix_url') {
                $urlPrefix = QUIX_URL;
            }else{
                $urlPrefix = QUIX_TEMPLATE_URL;
            }

            switch($type){
                case "css": 
                    \ThemeXpert\Assets\Assets::css($name, $urlPrefix. $url);
                    break;
                case "js": 
                    \ThemeXpert\Assets\Assets::js($name, $urlPrefix. $url);
                    break;
            }
        });
    }

    /**
     * Get startTag function.
     *
     * @param $data data of field
     */
    protected function getStartTagFunction($data)
    {
        return new Twig_SimpleFunction('startTag', function ($tag, $attr) use ($data) {
            return new \Twig_Markup("<$tag $attr>", 'UTF-8');
        });
    }

    /**
     * Get LoadSvg function.
     *
     * @param $data data of field
     */
    protected function getLoadSvgFunction($data)
    {
        return new Twig_SimpleFunction('loadSvg', function ($svg) use ($data) {
            if (strpos($svg, '<svg') === false and strpos($svg, '<?xml') === false) {
                return '<i class="' . $svg . '"></i>';
            } else {
                return $svg;
            }
        });
    }

    /**
     * Get imageUrl
     *
     * @param $data data of field
     */
    protected function getImageUrlFunction($data)
    {
        return new Twig_SimpleFunction('imageUrl', function ($src) use ($data) {
            $imagePath = $data['IMAGE_PATH'];
            if (is_array($src)) {
                $src = $src['url'];
            }

            if (
                preg_match('/^(https?:\/\/)|(http?:\/\/)|(\/\/)|([a-z0-9-].)+(:[0-9]+)(\/.*)?$/', $src)
            ) {
                return $src;
            } elseif (strpos($src, 'libraries') === false) {
                $src = \JURI::root() . '/' . $imagePath . '/' . $src;
            } else {
                $src = \JUri::root() . $src;
            }

            return $src;
        });
    }

    /**
     * Get classNames function.
     *
     * @param $data data of field
     */
    protected function getClassNamesFunction($data)
    {
        return new Twig_SimpleFunction('classNames', function () use ($data) {
            return call_user_func_array('classNames', func_get_args());
        });
    }

    /**
     * Get classNames function.
     *
     * @param $data data of field
     */
    protected function getVisibilityClassFunction($data)
    {
        return new Twig_SimpleFunction('visibilityClass', function ($visibility) use ($data) {
            $class = [];

            $visibility['xs'] = $visibility['sm'];
            foreach ($visibility as $key => $value) {
                if (!$value) {
                    $class[] = 'qx-d-' . $key . '-none';

                    foreach ($visibility as $key2 => $subvalue) {
                        if ($subvalue) {
                            $class[] = 'qx-d-' . $key2 . '-block';
                        }
                    }
                }
            }

            // handle the xs value
            if ($visibility['xs'] && count($class)) {
                $class[] = 'qx-d-block';
            } elseif (!$visibility['xs']) {
                $class[] = 'qx-d-none';
            }

            $class = array_unique($class);

            return implode(' ', $class);
        });
    }

    /**
     * Get classNames function.
     *
     * @param $data data of field
     */
    protected function getVisibilityClassNodeFunction($data)
    {
        return new Twig_SimpleFunction('visibilityClassNode', function ($visibility) use ($data) {
            $class = [];

            $visibility['xs'] = $visibility['sm'];
            foreach ($visibility as $key => $value) {
                if (!$value) {
                    $class[] = 'qx-d-' . $key . '-none';

                    foreach ($visibility as $key2 => $subvalue) {
                        if ($subvalue) {
                            $class[] = 'qx-d-' . $key2 . '-flex';
                        }
                    }
                }
            }

            // handle the xs value
            if ($visibility['xs'] && count($class)) {
                $class[] = 'qx-d-flex';
            } elseif (!$visibility['xs']) {
                $class[] = 'qx-d-none';
            }

            $class = array_unique($class);

            return implode(' ', $class);
        });
    }

    /**
     * Get raw function.
     *
     * @param $data data of field
     */
    protected function getRawFunction($data)
    {
        return new Twig_SimpleFunction('raw', function ($source) use ($data) {
            return file_get_contents(QUIX_PATH . $source);
        });
    }

    /**
     * Get getFileContent
     *
     * @param $data data of field
     */
    protected function getFileContentFunction($data)
    {
        return new Twig_SimpleFunction('getFileContent', function ($element, $path, $ext) use ($data) {
            return file_get_contents(QUIX_PATH . $path . '.' . $ext);
        });
    }

    /**
     * Get lessThan function.
     *
     * @param $data data of field
     */
    protected function getLessThanFunction($data)
    {
        return new Twig_SimpleFunction('lessThan', function ($number1, $number2) use ($data) {
            return $number1 < $number2;
        });
    }

    /**
     * Get greater than function.
     *
     * @param $data data of field
     */
    protected function getGreaterThanFunction($data)
    {
        return new Twig_SimpleFunction('greaterThan', function ($number1, $number2) use ($data) {
            return $number1 > $number2;
        });
    }

    /**
     * Get greater than function.
     *
     * @param $data data of field
     */
    protected function getGreaterThanSignFunction($data)
    {
        return new Twig_SimpleFunction('greaterThanSign', function () use ($data) {
            return '>';
        });
    }

    /**
     * Get quix element path function.
     *
     * @param $data data of field
     */
    protected function getGetQuixElementPathFunction($data)
    {
        return new Twig_SimpleFunction('getQuixElementPath', function ($source) use ($data) {
            return QUIX_ELEMENTS_PATH;
        });
    }

    /**
     * Get starts with function.
     *
     * @param $data data of field
     */
    protected function getStartsWithFunction($data)
    {
        return new Twig_SimpleFunction('qxStringStartsWith', function ($str, $subStr) use ($data) {
            return substr($str, 0, strlen($subStr)) === $subStr;
        });
    }

    /**
     * Get link filter.
     *
     * @param $data data of field
     */
    protected function getLinkFilter($data)
    {
        return new Twig_SimpleFilter('link', function () use ($data) {
            $args = func_get_args();

            $value = isset($args[0]) ? $args[0] : null;
            $options = isset($args[1]) ? $args[1] : [];
            $classes = isset($args[2]) ? $args[2] : null;
            $attrs = isset($args[3]) ? $args[3] : null;

            $url = empty($options['url']) ? null : $options['url'];
            // -- start
            if (
                !preg_match('/^(https?:\/\/)|(http?:\/\/)|(\/\/)|([a-z0-9-].)+(:[0-9]+)(\/.*)?$/', $url)
            ) {
                $url = \JRoute::_($url);
            }
            
            // -- end

            $class = null;
            $target = '';
            $rel = '';
            $attr = null;

            if (isset($classes)) {
                $class = "class='{$classes}'";
            }

            if (isset($attrs)) {
                $attr = " {$attrs}";
            }

            if (isset($options['target']) && $options['target']) {
                $target = "target='_blank'";
            }

            if (isset($options['nofollow']) && $options['nofollow']) {
                $rel = "rel='nofollow'";
            }

            if (!is_null($url)) {
                $value = "<a class='$classes' href=$url $target $rel $attr>$value</a>";
            } else {
                $value = "$value";
            }

            return new \Twig_Markup($value, 'UTF-8');
        });
    }

    /**
     * Get field function.
     *
     * @param $data data of field
     */
    protected function getFieldFunction($data)
    {
        return new Twig_SimpleFunction('field', function ($field) use ($data) {
            return $this->getFieldData($field, $data);
        });
    }

    /**
     * Get field function.
     *
     * @param $data data of field
     */
    protected function getAllFieldFunction($data)
    {
        return new Twig_SimpleFunction('allfield', function () use ($data) {
            return $data['node']['form'];
        });
    }

    /**
     * Get content parse joomla event
     *
     * @param $data data of field
     */
    protected function getPrepareContentFunction($data)
    {
        return new Twig_SimpleFunction('prepareContent', function ($text, $prepare = false) use ($data) {
            return ($prepare ? \JHtml::_('content.prepare', $text) : $text);
        });
    }

    /**
     * Validate Joomla Captcha
     *
     * @param $data data of field
     */
    protected function getValidateJoomlaCaptchaFunction($data)
    {
        return new Twig_SimpleFunction('validateJoomlaCaptcha', function ($value, $rechaptchaId) use ($data) {
            $joomla_captcha = \JFactory::getConfig()->get('captcha');
            if ($joomla_captcha != '0' && $joomla_captcha != '' && $joomla_captcha != null) {
                if ($joomla_captcha == $value) {
                    $plugin = \JPluginHelper::getPlugin('captcha', $value);
                    if (!$plugin) {
                        return false;
                    }

                    // \JPluginHelper::importPlugin('captcha', $joomla_captcha);
                    // $dispatcher = \JEventDispatcher::getInstance();
                    // $dispatcher->trigger('onInit', $rechaptchaId);
                    
                    // lead the script directly
                    \JFactory::getDocument()->addScript('https://www.google.com/recaptcha/api.js', [], ['defer' => 'defer']);
                    return true;
                }
            }
            return false;
        });
    }

    protected function getWrapperFunction($data)
    {
        return new Twig_SimpleFunction(
            'wrapper',
            function ($wrapper, $tag, $multipart = false, $end = false) use ($data) {
                if ($end) {
                    return new \Twig_Markup("</$tag>", 'UTF-8');
                } else {
                    if ($tag == 'form') {
                        $url = \JRoute::_('index.php?option=com_quix');
                        return new \Twig_Markup("<$tag method='post' name='quixform' action='" . $url . "' " . ($multipart ? "enctype='multipart/form-data'" : '') . '>', 'UTF-8');
                    } else {
                        return new \Twig_Markup("<$wrapper>", 'UTF-8');
                    }
                }
            }
        );
    }

    protected function getformFooterFunction($data)
    {
        $configSys = \JFactory::getConfig();
        $session = \JFactory::getSession();
        if ($session->get('quix_form_secret')) {
            $key = $session->get('quix_form_secret');
        } else {
            $secret = $configSys->get('secret');
            $encCrypt = new Crypt(null, null);
            $key = $encCrypt->generateKey();
            $session->set('quix_form_secret', $key);
        }
        $enc = new Crypt(null, $key);

        return new Twig_SimpleFunction('formFooter', function ($element, $config = []) use ($data, $enc) {
            return new \Twig_Markup('
            <input type="hidden" name="option" value="com_quix" />
            <input type="hidden" name="task" value="ajax" />
            <input type="hidden" name="element" value="' . $element . '" />
            <input type="hidden" name="format" value="json" />
            <input type="hidden" name="builder" value="frontend" />
            <input type="hidden" name="jform[info]" value="' . $enc->encrypt(json_encode($config)) . '" />' .
            \JHtml::_('form.token'), 'UTF-8');
            ;
        });
    }

    /**
     * Get fields group function.
     *
     * @param $data data of field
     */
    protected function getFieldsGroupFunction($data)
    {
        return new Twig_SimpleFunction('fieldsGroup', function ($fieldsGroup, $index) use ($data) {
            $data = $fieldsGroup[$index];
            $results = [];

            foreach ($data as $key => $i) {
                // for supporting Quix version < 2.1.0-beta1
                if (isset($i['name'])) {
                    $results[$i['name']] = $i;
                }
                // for supporting Quix latest version
                else {
                    $key = array_keys($i)[0];
                    $value = array_values($i)[0];
                    $results[$key] = compact('value');
                }
            }

            return $results;
        });
    }

    /**
     * Get opacity from background overlay.
     *
     * @param $data data of field
     */
    protected function getGetOpacityFunction($data)
    {
        return new Twig_SimpleFunction('getOpacity', function ($background, $type) use ($data) {
            return isset($background['state'][$type]['opacity']['value']) ? $background['state'][$type]['opacity']['value'] : $background['state'][$type]['opacity'];
        });
    }

    /**
     * Get ajaxQuix
     *
     * @param $data data of field
     */
    protected function getElementApiCallFunction($data)
    {
        return new Twig_SimpleFunction('ElementApiCall', function ($element, $info) use ($data) {
            $className = str_replace('-', ' ', $element);

            $className = ucwords($className);

            $className = str_replace(' ', '', $className);

            $elementClassName = "Quix{$className}Element";

            // Get the method name
            $method = 'getAjax';
            return call_user_func($elementClassName . '::' . $method, $info);
        });
    }

    /**
     * Get Joomla Module
     *
     * @param $data data of field
     */
    protected function getJoomlaModuleFunction($data)
    {
        return new Twig_SimpleFunction('getJoomlaModule', function ($id, $style = 'raw') use ($data) {
            if (empty($id)) {
                return;
            }

            $document = \JFactory::getDocument();
            $renderer = $document->loadRenderer('module');

            $db = \JFactory::getDBo();
            $query = $db->getQuery(true);
            $query->select('*')
                ->from('#__modules')
                ->where('published = ' . 1)
                ->where('id = ' . $id);
            $db->setQuery($query);
            $module = $db->loadObject();

            // check if module not found
            if (!isset($module->id)) {
                return;
            }
            $params = json_decode($module->params, true);
            $response = '';
            ob_start();

            if ($module->id > 0) {
                echo $renderer->render($module, $params);
            }

            $response = ob_get_clean();

            return $response;
        });
    }

    /**
     * Get bootstrap grid
     */
    protected function getGrid($node)
    {
        return implode(' ', array_map(function ($device, $size) {
            switch ($device) {
                case 'xs':
                    $class = 'qx-col-';
                    break;
                case 'sm':
                    $class = 'qx-col-sm-';
                    break;
                case 'md':
                    $class = 'qx-col-md-';
                    break;
                case 'lg':
                    $class = 'qx-col-lg-';
            }

            return $class . ceil($size * 12);
        }, array_keys($node['size']), $node['size']));
    }

    /**
     * get path
     */
    protected function getPath($path)
    {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        $splitPath = explode(DIRECTORY_SEPARATOR, $path);

        if (in_array('script.twig', $splitPath)) {
            $fileName = $splitPath[sizeof($splitPath) - 1];
        } else {
            $fileName = $splitPath[sizeof($splitPath) - 1];
        }

        if ($fileName == 'view.php') {
            $fileName = 'html.twig';
        }

        array_pop($splitPath);

        $subDir = $splitPath[sizeof($splitPath) - 1];

        array_pop($splitPath);

        $dir = $splitPath[sizeof($splitPath) - 1];

        array_pop($splitPath);

        if (
            $splitPath[sizeof($splitPath) - 3] == 'libraries'
            or
            $splitPath[sizeof($splitPath) - 4] == 'libraries'
            or
            $splitPath[sizeof($splitPath) - 5] == 'libraries'
        ) {
            $path = str_replace('nodes', '', implode(DIRECTORY_SEPARATOR, $splitPath));
            $path = str_replace('frontend', '', $path);
            $path = str_replace('elements', '', $path);

            if ($fileName == 'style.php') {
                $path = $path . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $subDir . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'style.twig' ;
            } elseif ($fileName == 'script.twig') {
                $path = $path . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . $splitPath[sizeof($splitPath) - 1] . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'script.twig';
            } else {
                $path = $path . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $subDir . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . $fileName;
            }
        } else {
            $path = str_replace('view.php', '', $path);
            $path = str_replace('style.php', '', $path);

            if ($fileName == 'style.php') {
                $path = $path . 'partials' . DIRECTORY_SEPARATOR . 'style.twig' ;
            } elseif ($fileName == 'script.twig') {
                $path = $path . 'partials' . DIRECTORY_SEPARATOR . 'script.twig' ;
            } else {
                if (strpos($path, DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR) === false) {
                    $path = $path . 'partials' . DIRECTORY_SEPARATOR . $fileName;
                }
            }
        }

        return realpath($path);
    }

    /**
     * Get field data by the given field name.
     */
    public function getFieldData($field, $data)
    {
        $fieldid = isset($data['identifier'][1]['id']) ? $data['identifier'][1]['id'] : '';
        $id = isset($data['identifier'][1]['value']) ? $data['identifier'][1]['value'] : $fieldid;

        if (isset(quix()->container[$id])) {
            $fieldData = quix()->container[$id];

            return isset($fieldData[$field]) ? $fieldData[$field] : '';
        } else {
            $formData = $data['node']['form'];

            // clear data from memory
            unset($data);

            flatten_array_ref($formData);

            // building group-repeater field data
            $groupRepeaterFormData = [];

            foreach ($formData as $key => $formField) {
                if (
                    isset($formField[0][0])
                    and isset($formField[0][1])
                ) {
                    $groupRepeaterFormData[$key] = array_merge($formField);
                    unset($formData[$key]);
                }
            }

            // form data re-flatten and merging group-repeater form data with fields data
            flatten_array_ref($formData, 2);

            quix()->container[$id] = array_merge($formData, $groupRepeaterFormData);
            ;

            // clear formData from memory
            unset($formData, $groupRepeaterFormData);

            return quix()->container[$id][$field];
        }
    }
}
