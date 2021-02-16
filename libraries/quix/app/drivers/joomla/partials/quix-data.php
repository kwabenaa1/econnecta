<?php

/**
 * Bind QUIX data with JS global variable
 */
function quix_js_data($builder = 'admin') 
{
  $input = JFactory::getApplication()->input;
  $cache = JFactory::getCache('lib_quix');
  $cache->setCaching( 1 );
  // $profiler = new JProfiler();

  if($builder == 'admin'){
    $cache->call('quix_js_data_from_cache_admin', $builder );
  }
  else
  {
    $cache->call('quix_js_data_from_cache_site', $builder );
  }

  // extending
  $id = $input->get('id');
  $type = $input->get('type');
  $model = $input->get('view');
  $_token = JSession::getFormToken();
  
  if($builder == 'admin'){
    $api = 'index.php?option=com_quix&task=' . $model . '.apply';
  }else{
    $api = 'index.php?option=com_quix&task='. $type .'.apply';
  }
  $config = \JComponentHelper::getParams('com_media');
  $imagePath = $config->get('image_path', 'images');
  ?>
  <script type="text/javascript">
    var quix = jQuery.extend({}, quix , {
      id: '<?php echo $id ?>',
      type: '<?php echo $type ?>',
      model: '<?php echo $model ?>',
      _token: '<?php echo $_token ?>',
      api: '<?php echo $api ?>',
      image_path: '<?php echo $imagePath ?>',
    });
  </script>
  <?php
  // echo $profiler->mark( ' with caching' );
  // die;
}

function quix_js_data_from_cache_admin($builder)
{
  $url = QUIX_SITE_URL;
  $quix = quix();
  $input = JFactory::getApplication()->input;

  // check for safemode for low memory server
  $params = JComponentHelper::getParams('com_quix');
  $safemode = $params->get('safemode', 0);
  if($safemode)
  {
    $collections = [];
    $presets = [];
  }
  else
  {
    $collections = qxGetCollections( true );
    $presets = $quix->getPresets(); 
  }
  
  $blocks = qxGetBlocks($builder);
  if(property_exists($blocks, "success") and !$blocks->success) $blocks = json_encode([]);

  // fetching required data
  
  $nodes = $quix->getNodes();
  $elements = $quix->getElementsJson();  

  // encoding data
  $quixData = json_encode( compact(
    'url',
    'blocks',
    'collections',
    'presets'
  ) );

  // binding quix data to the JS variables
  ?>
    
  <script>
    var qx_site_url = '<?php echo $url ?>';
    var qx_elements = <?php qxStringEchobig($elements); ?>;
    var qx_nodes = <?php echo json_encode( $nodes ) ?>;
    var quix = <?php echo $quixData; ?>;
    var filemanager = {
      api_keys: {
        unsplash: 'adadd32119ba45bcc6cdfc7c8fe2c2c54cd4fcd89e6fca21b503308dd4bd3f2d'
      }
    };
  </script>
  <?php
}

function quix_js_data_from_cache_site($builder)
{
  $url = QUIX_SITE_URL;
  $quix = quix();
  $input = JFactory::getApplication()->input;
  
  $presets = []; 
  $collections = [];
  $blocks = json_encode([]);

  // fetching required data
  $nodes = $quix->getNodes();  
  $elements = $quix->getElementsJson();

  // encoding data
  $quixData = json_encode( compact(
    'url',
    'blocks',
    'collections',
    'presets'
  ) );
  // binding quix data to the JS variables
  ?>

  <script>
    var qx_site_url = '<?php echo $url ?>';
    var qx_elements = <?php qxStringEchobig($elements); ?>;
    var qx_nodes = <?php echo json_encode( $nodes ) ?>;
    var quix = <?php echo $quixData; ?>;
    var filemanager = {
      api_keys: {
        unsplash: 'adadd32119ba45bcc6cdfc7c8fe2c2c54cd4fcd89e6fca21b503308dd4bd3f2d'
      }
    };
  </script>
  <?php
}