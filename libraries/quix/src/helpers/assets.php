<?php

global $assetLoaded;
$assetLoaded = false;

class_alias('ThemeXpert\Assets\Assets', 'Assets');

// booting assets loader
function bootAssetsLoader()
{
    // # Loading assets.
    // global $assetsLoaded;

    // if (!$assetsLoaded) {
    //     Assets::load();
    //     $assetsLoaded = true;
    // }
}

/**
 * Remove Joomla specific css/js files on classic builder page
 */
function removeJoomlaAssetsForClassicBuilder()
{
    $app = JFactory::getApplication();
    $document = JFactory::getDocument();
    $tmpl = $app->getTemplate();

    // removing bootstrap
    $bootstrap_css = JUri::root(true) . '/media/jui/css/bootstrap.css';
    $bootstrap_js = JUri::root(true) . '/media/jui/js/bootstrap.min.js';

    $template = JUri::root(true) . '/administrator/templates/' . $tmpl . '/css/template.css?' . $document->getMediaVersion();
    $templatej37 = JUri::root(true) . '/administrator/templates/' . $tmpl . '/css/template.css';
    // $template_js = JUri::root( true ) . '/administrator/templates/' . $tmpl . '/js/template.js?' . $document->getMediaVersion();
    $template_js = JUri::root(true) . '/administrator/templates/' . $tmpl . '/js/template.js';
    // var_dump($bootstrap_js);
    unset($document->_styleSheets[$bootstrap_css] , $document->_styleSheets[$template] , $document->_styleSheets[$templatej37] , $document->_scripts[$template_js]);

    // unset( $document->_scripts[$bootstrap_js] );
}

/**
 * Common Builder scripts used on v1 and v2
 */
function loadCommonBuilderScripts()
{
    $version = 'ver=' . QUIX_VERSION;

    $document = JFactory::getDocument();
    // Date Time
    $document->addScript(QUIX_URL . "/assets/js/moment.js?$version");
    // String search
    $document->addScript(QUIX_URL . "/assets/js/fuzzy.js?$version");
    // HTTP client
    $document->addScript(QUIX_URL . "/assets/js/axios.js?$version");
    // Date Time picker
    $document->addScript(QUIX_URL . "/assets/js/react-date-picker.js?$version");
    // Magnific popup
    $document->addScript(QUIX_URL . "/assets/js/jquery.magnific-popup.js?$version");
    $document->addStyleSheet(QUIX_URL . "/assets/css/magnific-popup.css?$version");
}

/**
 * CSS & JS used on Joomla > Quix backend
 */
function loadAssetsForJoomlaBackend()
{
    $version = 'ver=' . QUIX_VERSION;
    Assets::Css('admin', QUIX_URL . '/assets/css/admin.css');
}

/********************************************
 * Load All Classic Builder scripts (v1)
 ********************************************/
/**
 * Load specific assets for classic builder
 */
function loadClassicBuilderAssets()
{
    $version = 'ver=' . QUIX_VERSION;
    JEventDispatcher::getInstance()->register('onBeforeRender', 'removeJoomlaAssetsForClassicBuilder');

    JHtml::_('jquery.framework');
    JHtml::_('bootstrap.framework');

    $document = \JFactory::getDocument();

    // load common assets
    loadCommonBuilderScripts();
    // React Color Picker
    $document->addScript(QUIX_URL . "/assets/js/react-color.js?$version");

    // adding fontawesome icons json file
    $fontAwesomeJSON = file_get_contents(__DIR__ . '/json/fa4.json');
    $document->addScriptDeclaration('window.fontAwesomeJSON = ' . $fontAwesomeJSON);

    // init quix builder required js variables
    $document->addScriptDeclaration("window.quixElementsURL = '/libraries/quix/app/elements';");
    $document->addScriptDeclaration("window.quixTemplateURL = '" . QUIX_TEMPLATE_URL . "'");
    $document->addScriptDeclaration("window.jRoot = '" . JUri::root() . "'");

    // var quix ( REQUIRED )
    quix_js_data('admin');

    // tinymace
    Assets::Js('tinymce', JUri::root(true) . '/media/editors/tinymce/tinymce.min.js');
    // materials
    Assets::Js('materialize-js', QUIX_URL . '/assets/js/materialize.js');
    Assets::Css('materialize-css', QUIX_URL . '/assets/css/materialize.css');
    // spiner
    Assets::Css('spinner', QUIX_URL . '/assets/css/spinner.css');
    // image picker
    Assets::Js('image-picker', QUIX_URL . '/assets/js/image-picker.js');
    // scrollbar
    Assets::Js('mousewheel', QUIX_URL . '/assets/js/jquery.mousewheel.js');
    Assets::Css('mCustomScrollbar-css', QUIX_URL . '/assets/css/jquery.mCustomScrollbar.css');
    Assets::Js('mCustomScrollbar-js', QUIX_URL . '/assets/js/jquery.mCustomScrollbar.js');

    // font awesome
    Assets::Css('font-awesome', QUIX_URL . '/assets/css/font-awesome.css');

    //hide navbar if from an iframe modal
    $document->addScriptDeclaration("
    if(parent !== window){
      document.styleSheets[0].insertRule(\".navbar.navbar-inverse.navbar-fixed-top{display:none}\", 0);
    }
    (function($){ $(window).on('load',function(){
      $('.blocks-container .blocks').mCustomScrollbar({
        theme:\"dark\"
      });
    });})
    (jQuery);
  ");

    // joomla admin
    loadAssetsForJoomlaBackend();

    // Boot the asset loader
    bootAssetsLoader();
}
/**
 * Load builder js file (React) for classic builder
 */
function loadClassicBuilderReactScripts()
{
    $MediaVersion = JFactory::getDocument()->getMediaVersion();
    $version = 'ver=' . QUIX_VERSION . '&' . $MediaVersion;

    $config = \JComponentHelper::getParams('com_quix');
    $async = $config->get('async_builderjs', false);
    $dataAsync = ($async ? ' defer data-cfasync="false"' : '');

    // return '<script' . $dataAsync . ' src="' . QUIX_URL . '/assets/builder/bundle.js?' . $version . '"></script>';
    return '<script defer data-cfasync="false" src="' . QUIX_URL . '/assets/builder/bundle.js?' . $version . '"></script>';
}
/**
 * Load scripts for preview pages
 */
function loadClassicBuilderPreviewAssets()
{
    $document = \JFactory::getDocument();
    $version = QUIX_VERSION;
    // Load Jquery
    JHtml::_('jquery.framework');
    // Load Bootstrap 3
    JHtml::_('bootstrap.framework');
    // Get config
    $config = JComponentHelper::getComponent('com_quix')->params;

    // jQuery easing
    Assets::Js('jQuery-easing', QUIX_URL . '/assets/js/jquery.easing.js');

    // FontAwesome
    if ($config->get('load_fontawosome', 1)) {
        Assets::Css('font-awesome', QUIX_URL . '/assets/css/font-awesome.css');
    }

    // Quix
    Assets::Js('quix-classic-js', QUIX_URL . '/assets/js/quix.js', [], [], 1001);
    Assets::Css('quix-classic-css', QUIX_URL . '/assets/css/quix-classic.css');

    // WOW + Animation
    Assets::Css('animate', QUIX_URL . '/assets/css/animate.css');
    Assets::Js('wow', QUIX_URL . '/assets/js/wow.js');

    // Magnific popup
    // TODO : Compress + minify with own enque script
    Assets::Css('magnific-popup', QUIX_URL . '/assets/css/magnific-popup.css');
    Assets::Js('magnific-popup', QUIX_URL . '/assets/js/jquery.magnific-popup.js');

    // Boot the asset loader
    bootAssetsLoader();
}
/**
 * Footer credit and version number on classic builder
 */
function loadClassicBuilderFooterCredit($free = true, $pro = true)
{
    if ($free) {
        $pro = false;
    }
    return '<footer id="footer">
    <p>
    <a href="https://www.themexpert.com/quix-pagebuilder" target="_blank">The Quix Builder</a> version <strong>' . QUIX_VERSION . ' <label class="label label-' . ($pro ? 'success' : 'warning') . '">' . ($pro ? 'PRO' : 'FREE') . '</label></strong> brought to you by <a href="https://www.themexpert.com">ThemeXpert</a> team.
    </p>
    <p class="text-center">
      <a href="https://www.themexpert.com/docs/quix/" target="_blank">Docs</a> | <a href="https://www.themexpert.com/support" target="_blank">Support</a> | <a href="https://www.fb.com/groups/QuixUserGroup/" target="_blank">Community</a> | <a href="http://extensions.joomla.org/write-review/review/add?extension_id=11775" target="_blank">Rate on JED</a>
    </p>
  </footer>';
}

/********************************************
 * Load All Live Builder scripts (v2)
 ********************************************/
/**
 * Load assets for Live builder ( Builder mode )
 */
function loadLiveBuilderAssets()
{
    $version = 'ver=' . QUIX_VERSION;

    $config = JComponentHelper::getComponent('com_quix')->params;
    $dev_mode = $config->get('dev_mode', 0);

    // Get joomla document instance
    $document = \JFactory::getDocument();

    // load common assets
    loadCommonBuilderScripts();

    // if($dev_mode)
    // {
    //   // Sentry Debugger
    //   $document->addScript( "https://cdn.ravenjs.com/3.22.3/raven.min.js" );
    // }

    // load live builder required assets css
    $document->addStyleSheet(QUIX_URL . "/assets/css/qxbs.css?$version");
    $document->addStyleSheet(QUIX_URL . "/assets/css/qx-fb.css?$version");
    $document->addStyleSheet(QUIX_URL . "/assets/css/qxicon.css?$version");
    $document->addStyleSheet(QUIX_URL . "/assets/css/quix.css?$version");
    $document->addStyleSheet(QUIX_URL . "/assets/css/qxi.css?$version");
    $document->addStyleSheet(QUIX_URL . "/assets/css/qxkit.css?$version");

    // Load preview assets
    loadLiveBuilderPreviewAssets(false);
}
/**
 * Main builder script (react)
 */
function loadLiveBuilderReactScripts()
{
    $MediaVersion = JFactory::getDocument()->getMediaVersion();
    // $version = 'ver=' . QUIX_VERSION . '&' . $MediaVersion;
    $version = 'ver=' . QUIX_VERSION;;

    $config = \JComponentHelper::getParams('com_quix');
    $async = $config->get('async_builderjs', false);
    $dataAsync = ($async ? ' defer data-cfasync="false"' : '');
    $quixData = quix_js_data('site');

    // Load Tinymce
    // $document = JFactory::getDocument();
    // $document->addScript( JUri::root(true) . "/media/editors/tinymce/tinymce.min.js?$version" );

    // this required for minify version
    // otherwise typography won't work ( throws webfont not found error )
    $devtools = '<script>window.__REACT_DEVTOOLS_GLOBAL_HOOK__ = window.parent.window.__REACT_DEVTOOLS_GLOBAL_HOOK__</script>';
    $webfont = '<script' . $dataAsync . ' src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js"></script>';

    $qxfb = '<script' . $dataAsync . ' src="' . QUIX_URL . '/assets/builder/vendor.js?' . $version . '"></script>';
    $qxfb .= '<script' . $dataAsync . ' src="' . QUIX_URL . '/assets/builder/qxfb.js?' . $version . '"></script>';

    return $devtools . $quixData . $webfont . $qxfb ;
}
/**
 * Load preview page scripts
 */

function loadLiveBuilderPreviewAssets($loadTemplateHelper = true)
{
    $version = 'ver=' . QUIX_VERSION;

    // Load Jquery
    JHtml::_('jquery.framework');
    // Load Bootstrap 3
    JHtml::_('bootstrap.framework');
    // Get config
    $config = JComponentHelper::getComponent('com_quix')->params;

    $document = \JFactory::getDocument();
    // $document->addScriptDeclaration('var QUIX_ROOT_URL = "'. JUri::root() .'";');
    // Asset Helper
    if ($loadTemplateHelper) {
        // we are loading builder
        JFactory::getApplication()->input->set('jchbackend', 1);
        JFactory::getDocument()->setType('quix');
        
        // Assets::Js('twig', QUIX_URL . "/assets/js/twig.js", [], [], 1007);

        // $document->addScript("//ajax.googleapis.com/ajax/libs/webfont/1.5.18/webfont.js" );
        // Assets::Js('webfont-for-assets-helper', '//ajax.googleapis.com/ajax/libs/webfont/1.6.26//webfont.js', [], [], 1008);

        $document->addScript(QUIX_URL . "/assets/js/lodash.min.js?$version");
        // Assets::Js('qx-lodash', QUIX_URL . '/assets/js/lodash.min.js', [], [], 1009);

        $document->addScript(QUIX_URL . "/assets/js/quix-helper.js?$version");
        // Assets::Js('quix-assets-helper', QUIX_URL . '/assets/js/quix-helper.js', [], [], 1010);

        $document->addScript(QUIX_URL . "/assets/js/jquery.magnific-popup.js?$version");
        // Assets::Js('jquery-magnific-popup', QUIX_URL . '/assets/js/jquery.magnific-popup.js', [], [], 1011);
    }

    // Quix
    // $document->addStyleSheet( QUIX_URL . "/assets/css/quix-classic.css?$version" );
    // Assets::Css('quix-classic-css', QUIX_URL . '/assets/css/quix-classic.css');

    // Bootstrap 4
    // $document->addStyleSheet(QUIX_URL . "/assets/css/qxbs.css?$version", [], ['async' => 'async']);
    // Assets::Css('qxbs', QUIX_URL . '/assets/css/qxbs.css');

    // $document->addStyleSheet( QUIX_URL . "/assets/css/quix.css?$version" );
    // Assets::Css('quix-css', QUIX_URL . '/assets/css/quix.css');

    // Quix icons
    // Assets::Css('qxi', QUIX_URL . '/assets/css/qxi.css');

    // Quixkit - Powered by uikit
    // Assets::Css('quix-kit', QUIX_URL . '/assets/css/qxkit.css');
    Assets::Js('quix-kit', QUIX_URL . '/assets/js/qxkit.js');

    // quix js
    // $document->addScript( QUIX_URL . "/assets/js/quix.js?$version" );
    Assets::Js('quix-js', QUIX_URL . '/assets/js/quix.js');

    // Boot the asset loader
    // bootAssetsLoader();
}
