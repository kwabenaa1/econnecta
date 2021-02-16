<?php
/**
 * @copyright   Copyright (C) 2015 GiMeSpace All rights reserved.
 * @license     http://http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL 
 */
// No direct access
defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.
?>

<div class="subnav<?php echo $class_sfx; ?>">

    <ul class="nav nav-pills menu<?php echo $class_sfx; ?>"<?php
$tag = '';
if ($params->get('tag_id') != null)
{
	$tag = $params->get('tag_id') . '';
	echo ' id="' . $tag . '"';
}
?>>
			<?php
      $multiColumn = $params->get('multiColumn');

      if ($multiColumn)
      {
      $columninx=0;
      $columns[$columninx]=0;
      $multiColumn=false;
      $lastlevel=1;
      
			foreach ($list as $i => &$item)
			{
				// The next item is deeper.
				if ($item->level==2)
				{
          if ($lastlevel==1) 
          {
            $columninx++;
            $columns[$columninx]=1;
          }
				}
				if ($item->type == 'separator')
				{
          if ($item->level==2)
          { 
          $columns[$columninx]=$columns[$columninx]+1;
          $multiColumn=true;
          }
				}
        $lastlevel=$item->level;
			}        
      }
      
      $columninx=0;  
      $lastlevel=1;
      $extrastyle = '';
			foreach ($list as $i => &$item)
			{
				// The next item is deeper.
				if (($item->level==2) and ($lastlevel==1)) 
          {
            $columninx++;
          }

				$class = 'item-' . $item->id;
				if ($item->id == $active_id)
				{
					$class .= ' current';
				}

				if (in_array($item->id, $path))
				{
					$class .= ' active';
				}
				elseif ($item->type == 'alias')
				{
					$aliasToId = $item->params->get('aliasoptions');
					if (count($path) > 0 && $aliasToId == $path[count($path) - 1])
					{
						$class .= ' active';
					}
					elseif (in_array($aliasToId, $path))
					{
						$class .= ' alias-parent-active';
					}
				}

				if ($item->deeper)
				{
          if ($item->level>1)
         {
					$class .= ' deeper dropdown dropdown-submenu';
          }
          else
          {
					$class .= ' deeper dropdown';
          }
				}

				if ($item->parent)
				{
					$class .= ' parent';
				}

				if (!empty($class))
				{
					$class = ' class="' . trim($class) . '"';
				}

        if ($multiColumn and ($item->level==2))
        {
  				echo '<li' . $class . ' >';
        }
        else
        {
	  			echo '<li' . $class . '>';
        }

				// Render the menu item.
        if ($item->type == 'separator')
        {
          if ($multiColumn and ($item->level==2))
          { 
            echo '</ul><ul class="unstyled span4" style="min-width: 130px;">';
            $paddingleft=1;
            $extrastyle = 'onmouseover="this.style.textDecoration=\'none\';" style="display: block; padding: 3px 20px;" ';
          }
          else
          {    
            echo '<li class="divider"></li>';
          }
        }
        elseif ($item->deeper)
        {
          if ($item->level>1)
          {
            echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown">';
            require JModuleHelper::getLayoutPath('mod_gmsmenu', 'default_url');
            echo '</a>';
          }
          else
          {
            echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown">';
            require JModuleHelper::getLayoutPath('mod_gmsmenu', 'default_url');
            echo ' <b class="caret"></b></a>';
          }
        }
        else
        {
				switch ($item->type)
				{
					case 'separator':
					case 'url':
					case 'component':
						require JModuleHelper::getLayoutPath('mod_gmsmenu', 'default_' . $item->type);
						break;

					default:
						require JModuleHelper::getLayoutPath('mod_gmsmenu', 'default_url');
						break;
				}
        }
				// The next item is deeper.
				if ($item->deeper)
				{
					echo '<ul class="dropdown-menu">';
          if (($item->level==1) and ($multiColumn))
          {
            echo '<li><div class="row-fluid" style="width: ';
            echo (140*$columns[$columninx+1]);
            echo 'px;"><ul class="unstyled span4" style="min-width: 130px;">';
            $paddingleft=20;
            $extrastyle = 'onmouseover="this.style.textDecoration=\'none\';" style="display: block; padding: 3px 20px;" ';
          }
				}
				// The next item is shallower.
				elseif ($item->shallower)
				{
					echo '</li>';
          $nlevel=$item->level;
          for ($x=0; $x<$item->level_diff; $x++)
          {
            $nlevel--;
            if ($multiColumn and ($nlevel==1))
            {
              echo '</ul></div></li>';
            }
   					echo '</ul></li>';
            $extrastyle = '';
          } 
				}
				// The next item is on the same level.
				else
				{
					echo '</li>';
				}
        $lastlevel=$item->level;
			}
			?></ul>

</div>