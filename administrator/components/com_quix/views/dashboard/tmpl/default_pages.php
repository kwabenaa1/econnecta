<?php
/**
* @package     Joomla.Administrator
* @subpackage  com_messages
*
* @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;
use Joomla\Registry\Registry;
?>
<div id="dashboard_pages">
  <div class="card">
    <div class="card-header">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item active">
          <a class="nav-link" id="recent-tab" data-toggle="tab" href="#recent" role="tab" aria-controls="recent" aria-selected="true">Recent Pages</a>
        </li>
      </ul>    
    </div>
    <div class="card-body">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show in active" id="recent" role="tabpanel" aria-labelledby="recent-tab">
          
          <!-- list pages start -->
          <div class="pupular-pages">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th scope="col" width="60%">Pages</th>
                  <th scope="col">SEO</th>
                  <th scope="col">Image</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($this->recentItems as $key => $item) { ?>
                  <?php 
                  $registry = new Registry;
                  $metadata = $registry->loadString($item->metadata);
                  $seoScore = $metadata->get('seo_score', 0);
                  $registry = new Registry;
                  $params = $registry->loadString($item->params);
                  $image_optimized = $params->get('image_optimized', false);
                   ?>
                  <tr>
                    <td style="line-height: 2; word-break: break-all;">
                      <a href="<?php echo JUri::root() . 'index.php?option=com_quix&view=page&id='.$item->id; ?>" target="_blank"><?php echo $item->title; ?></a>
                      <?php if ($item->mobile_size) { ?>
                        <code title="This is the size you have saved!" class="has"><small><?php echo number_format((($item->original_size)/1024), 2); ?>MB</small></code>
                      <?php } ?>
                    </td>
                    <td>
                      <?php 
                      if($seoScore == 0){ $warningScrore = true; $messageScore = '0%'; }
                        elseif ($seoScore < 80){ $warningScrore = true; $messageScore = $seoScore . '%'; }
                          else {$warningScrore = false; $messageScore = $seoScore . '%'; }
                      ?>
                      <label class="label label-<?php echo ($warningScrore ? 'warning' : 'success');  ?> hasTooltip" title="SEO Score">
                        <?php echo $messageScore; ?>
                      </label>
                    </td>
                    <td style="text-align: center;">
                      <i class="icon-<?php echo $image_optimized == 'true' ? 'ok qx-status-green' : 'cancel qx-status-red' ?>"></i>
                    </td>
                    <td>
                      <?php 
                        if(QuixHelper::isFreeQuix()){
                          $link = 'https://www.themexpert.com/quix-pagebuilder?utm_medium=button&utm_campaign=quix-pro&utm_source=joomla-admin&utm_content=seo-button';
                        }else{
                          $link = JUri::root() . 'index.php?option=com_quix&task=page.edit&id='.(int) $item->id . '&quixlogin=true';
                        } ?>
                        <a 
                          class="btn btn-<?php echo ($warningScrore ? 'warning' : 'success');  ?>"
                          <?php echo ($item->builder == 'frontend' ? 'target="_blank"' : ''); ?>
                          href="<?php echo JRoute::_($link); ?>">
                          <?php echo ($seoScore < 80 ? 'Improve' : 'Update'); ?> 
                        </a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
            <div class="well well-small">
              <h4>Tips: Improving low SEO score page</h4>
              <ol>
                <li>Click <code>Improve</code> button to open the page in visual builder.</li>
                <li>Click <code>Optimize Image</code> icon from toolbar and optimize your images.</li>
                <li>Click <code>QuixRank</code> button and you'll get the suggestion window for improving your SEO scrore.</li>
              </ol>
            </div>
          </div>
          <!-- // end -->
        </div><!-- //end tab pane -->
      </div><!-- //end tab content -->
    </div>
  </div>
</div>