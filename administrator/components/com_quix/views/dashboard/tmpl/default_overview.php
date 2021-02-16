<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$imageStats = $this->getImageStats();
$optimised = $imageStats->mobile ? (100*$imageStats->mobile)/$imageStats->original : 0;
?>
<div id="dashboard_overview" class="row-fluid">
  <div class="span12">
    <div class="imagify-number-you-optimized">
      <div class="well" style="margin-bottom: 40px;">
        <p>
          <strong id="imagify-total-optimized-attachments" class="number">
            <?php echo ($imageStats->total ? $imageStats->total : 0) ?>
          </strong>
          <span class="text">
            that's the number of original images <br>
            you optimized with QuixOptimize
          </span>
        </p>            
      </div>

      <div style="margin-bottom: 50px;">
        <canvas id="optimized-imagify" style="width: 100%; height: 300px;"></canvas>
        <script type="text/javascript">
          new Chart(document.getElementById("optimized-imagify"),{
            "type":"doughnut",
            "data":{
              "labels":["All","Optimized","Mobile"],
              "datasets":[{
                "label":"My First Dataset",
                "data":[<?php echo $imageStats->original ?: 1 ?>,<?php echo $imageStats->optimise ?: 1 ?>,<?php echo $imageStats->mobile ?: 1 ?>],
                "backgroundColor":["rgb(255, 99, 132)","rgb(54, 162, 235)","rgb(255, 205, 86)"]
              }]
            },
            options: {
              responsive: true,
              legend: {
                position: 'bottom',
              }
            }
          });
          </script>
      </div>

      <div class="row-fluid">
        <div class="span6">
          <p style="margin-bottom: 0px;">Original Size : <strong><?php echo number_format($imageStats->original/1024, 2) ?> MB</strong></p>
            <div class="progress progress-info">
              <div class="bar" style="width: 100%;"></div>
            </div>

            <p style="margin-bottom: 0px;">Mobile Size : <strong><?php echo number_format($imageStats->mobile/1024, 2) ?> MB</strong></p>
            <div class="progress progress-info">
              <div class="bar" style="width: <?php echo $optimised; ?>%;"></div>
            </div>
        </div>
        <div class="span6">
          <div class="imagify-number-you-optimized percent" style="text-align: right;">
            
              <p class="number"><?php echo $optimised ? round(100-$optimised) : 0; ?>%</p>
              <p class="text">
                that's the size you saved by using QuixOptimize
              </p>
                    
          </div>
        </div>
      </div>

    </div>

  </div>
</div>