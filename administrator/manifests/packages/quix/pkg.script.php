<?php
/**
 * @package		Quix
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package     Joomla.Administrator
 * @subpackage  com_quix
 * @since       3.4
 */
class pkg_QuixInstallerScript
{
    public $migration = false;

    public function preflight($type, $parent)
    {
        // check if has 1.9, then dont install
        // if($type == 'update')
        // {
        // 	$currentQuix = $this->getParam('version', 'com_quix');
        // 	if( version_compare( $currentQuix, '1.9.99', 'lt' ) ) {
        // 		JFactory::getApplication()->enqueueMessage( JText::_('PKG_QUIX_WARNING_UPDATE_TO2'), 'error');
        // 		return false;
        // 	}
        // }

        // Installing component manifest file version
        // if($type == 'install')
        // {
        // 	$version = $this->getParam('version', 'com_quicx');
        // 	if($version){
        // 		// we found old quix, so check if its less then
        // 		if( version_compare( $version, '1.0.0', 'lt' ) ) {
        // 			$this->migration = true;
        // 			// we need to migrate the db, uninstall the old extensions
        // 			//first migrate the db
        // 			$this->renameDB();

        // 			// echo "<p class=\"alert alert-warning\"><strong>Heads Up!</strong><br/>
        // 			// We re-branded 'Quicx > Quix' and all your data has been migrated to new tables. Don't panic, your data is completly safe and un-touched. In case of any problem, contact us immediately.</p>";
        // 		}
        // 	}
        // }
    }

    /**
     * method to rename old tables to new name
     *
     * @return void
     */
    public function renameDB()
    {
        $app = JFactory::getApplication();
        $prefix = $app->get('dbprefix');

        $db = JFactory::getDbo();
        $tables = JFactory::getDbo()->getTableList();

        if (in_array($prefix . 'quicx', $tables)) {
            $db->setQuery('RENAME TABLE #__quicx TO #__quix');
            $db->execute();
        }

        if (in_array($prefix . 'quicx_collections', $tables)) {
            $db->setQuery('RENAME TABLE #__quicx_collections TO #__quix_collections');
            $db->execute();
        }

        if (in_array($prefix . 'quicx_collection_map', $tables)) {
            $db->setQuery('RENAME TABLE #__quicx_collection_map TO #__quix_collection_map');
            $db->execute();
        }

        return true;
    }

    /*
     * get a variable from the manifest file (actually, from the manifest cache).
     */
    public function getParam($name, $options = 'com_quix')
    {
        $db = JFactory::getDbo();
        $db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "' . $options . '"');
        $result = $db->loadResult();
        if (isset($result) && !empty($result)) {
            $manifest = json_decode($result, true);

            return $manifest[$name];
        }

        return false;
    }

    /*
     * get a variable from the manifest file (actually, from the manifest cache).
     */
    public function uninstallOldExtensions()
    {
        JModelLegacy::addIncludePath(JPATH_SITE . '/adminstrator/components/com_installer/models', 'InstallerModel');
        $model = JModelLegacy::getInstance('Manage', 'InstallerModel');
        $db = JFactory::getDbo();
        $db->setQuery("SELECT * FROM `#__extensions` WHERE `name` LIKE '%quicx%'");
        $results = $db->loadObjectList();
        if (isset($results) && !empty($results)) {
            // print_r($results);die;
            $ids = [];
            foreach ($results as $key => $value) {
                $ids[] = $value->extension_id;
            }

            JArrayHelper::toInteger($ids, []);
            $model->remove($ids);
        }

        return true;
    }

    /*
    * update db structure
    */
    public function updateDBfromOLD()
    {
        $app = JFactory::getApplication();
        $prefix = $app->get('dbprefix');

        $db = JFactory::getDbo();
        $tables = JFactory::getDbo()->getTableList();

        if (!in_array($prefix . 'quix', $tables)) {
            return;
        }

        $query = "SHOW COLUMNS FROM `#__quix` LIKE 'catid'";
        $db->setQuery($query);
        $column = (object) $db->loadObject();
        if (empty($column) or empty($column->Field)) {
            $query = "
				ALTER TABLE  `#__quix` 
				ADD `catid` int(11) NOT NULL AFTER  `title`,
				ADD `version` int(10) unsigned NOT NULL DEFAULT '1' AFTER `params`,
				ADD `hits` int(11) NOT NULL AFTER `version`,
				ADD `xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.' AFTER `hits`,
				ADD INDEX `idx_access` (`access`),
				ADD INDEX `idx_catid` (`catid`),
				ADD INDEX `idx_state` (`state`),
				ADD INDEX `idx_createdby` (`created_by`),
				ADD INDEX `idx_xreference` (`xreference`);
				";
            $db->setQuery($query);
            $db->execute();
        }
    }

    public function cleanQuixCache()
    {
        require_once JPATH_ADMINISTRATOR . '/components/com_quix/helpers/quix.php';
        QuixHelper::cleanCache();
        QuixHelper::cachecleaner('lib_quix');
        QuixHelper::cachecleaner('lib_quix', 1);
    }

    /**
     * Function to perform changes during install
     *
     * @param   JInstallerAdapterComponent  $parent  The class calling this method
     *
     * @return  void
     *
     * @since   3.4
     */
    public function postflight($parent)
    {
        self::enablePlugins();
        self::insertMissingUcmRecords();

        // clean quix cache
        // self::cleanQuixCache();

        if ($this->migration) {
            // now uninstall all the extensions
            $this->uninstallOldExtensions();
        }

        $this->updateDBfromOLD();

        ob_start(); ?>
<div class="quix_success_message">
	<style>
		.quix-wrap {
			background: #5d3ed2;
			color: #fff;
			padding: 20px;
			border-radius: 4px;
			box-shadow: 0 0 4px #ddd;
		}

		.quix-wrap img {
			margin-right: 40px;
		}

		.quix-wrap .btn-link {
			background: #e91e63;
			color: #fff;
			display: inline-block;
			padding: 0 2rem;
			margin-right: 10px;
			margin-top: 15px;
			height: 36px;
			line-height: 36px;
			text-align: center;
			letter-spacing: .5px;
			text-transform: uppercase;
			text-decoration: none;
			transition: .2s ease-out;
			border-radius: 2px;
			box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
		}

		.quix-wrap .btn-link:hover {
			background: #ec407a;
		}
	</style>
	<div class="media quix-wrap">
		<div class="pull-left">
			<img width="170" height="195" title="" alt=""
				src="<?php echo JUri::root() ?>media/quix/images/logo-big.png" />
		</div>
		<div class="media-body">
			<h3>Quix Installed Successfully!</h3>
			<p>You are one step closer to experiencing the true magical visual builder for Joomla!</p>
			<p>
				<a class="btn-link" href="index.php?option=com_quix">Get Started</a>
			</p>
		</div>
	</div>
</div>

<?php
    }

    /**
    * enable necessary plugins to avoid bad experience
    */
    public function enablePlugins()
    {
        $db = JFactory::getDBO();
        $sql = "SELECT `element`,`folder` from `#__extensions` WHERE `type` = 'plugin' AND `folder` in ('finder', 'system', 'content', 'editors-xtd', 'quickicon') AND `name` like '%quix%' AND `enabled` = '0'";
        $db->setQuery($sql);
        $plugins = $db->loadObjectList();
        if (count($plugins)) {
            foreach ($plugins as $key => $value) {
                if ($value->folder == 'finder' or $value->folder == 'system' or $value->folder == 'editors-xtd') {
                    $query = $db->getQuery(true);
                    $query->update($db->quoteName('#__extensions'));
                    $query->set($db->quoteName('enabled') . ' = ' . $db->quote('1'));
                    $query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
                    $query->where($db->quoteName('element') . ' = ' . $db->quote($value->element));
                    $query->where($db->quoteName('folder') . ' = ' . $db->quote($value->folder));
                    $db->setQuery($query);
                    $db->execute();
                }
            }
        }

        $sql = "SELECT `element`,`folder`, `enabled` from `#__extensions` WHERE `type` = 'plugin' AND `folder` ='system' AND `element` = 'seositeattributes' AND `enabled` = '0'";
        $db->setQuery($sql);
        $plugins = $db->loadObjectList();
        if (!count($plugins)) {
            return false;
        }
        foreach ($plugins as $key => $value) {
            $query = $db->getQuery(true);
            $query->update($db->quoteName('#__extensions'));
            $query->set($db->quoteName('enabled') . ' = ' . $db->quote('1'));
            $query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
            $query->where($db->quoteName('element') . ' = ' . $db->quote($value->element));
            $query->where($db->quoteName('folder') . ' = ' . $db->quote($value->folder));
            $db->setQuery($query);
            $db->execute();
        }

        return true;
    }

    /**
     * Method to insert missing records for the UCM tables
     *
     * @return  void
     *
     * @since   3.4.1
     */
    public function insertMissingUcmRecords()
    {
        // Insert the rows in the #__content_types table if they don't exist already
        $db = JFactory::getDbo();

        // Get the type ID for a xDoc
        $query = $db->getQuery(true);
        $query->select($db->quoteName('type_id'))
            ->from($db->quoteName('#__content_types'))
            ->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_quix.page'));
        $db->setQuery($query);

        $docTypeId = $db->loadResult();

        // Set the table columns to insert table to
        $columnsArray = [
            $db->quoteName('type_title'),
            $db->quoteName('type_alias'),
            $db->quoteName('table'),
            $db->quoteName('rules'),
            $db->quoteName('field_mappings'),
            $db->quoteName('router'),
            $db->quoteName('content_history_options'),
        ];

        // If we have no type id for com_xdocs.doc insert it
        if (!$docTypeId) {
            // Insert the data.
            $query->clear();
            $query->insert($db->quoteName('#__content_types'));
            $query->columns($columnsArray);
            $query->values(
                $db->quote('Quix Page') . ', '
                . $db->quote('com_quix.page') . ', '
                . $db->quote('{"special":{"dbtable":"#__quix","key":"id","type":"Page","prefix":"QuixTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}') . ', '
                . $db->quote('') . ', '
                . $db->quote('{"common":{"core_content_item_id":"id","core_title":"title","core_state":"state","core_body":"description", "core_hits":"hits","core_access":"access", "core_params":"params", "core_metadata":"metadata", "core_language":"language", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_xreference":"xreference", "asset_id":"null"}, "special":{}}') . ', '
                . $db->quote('QuixFrontendHelperRoute::getPageRoute') . ', '
                . $db->quote('{"formFile":"administrator\\/components\\/com_quix\\/models\\/forms\\/page.xml", "hideFields":["asset_id","checked_out","checked_out_time"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time", "version", "hits"], "convertToInt":["publish_up", "publish_down", "featured", "ordering"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"} ]}')
            );

            $db->setQuery($query);
            $db->execute();
        }

        return true;
    }
}
