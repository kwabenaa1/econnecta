<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class plgContentloadsmartforms extends JPlugin
{
    public function onContentPrepare($context, &$row, &$params, $page=0 )
    {
        if ( JString::strpos( $row->text, 'smartform' ) === false ) {

            return true;

        }
        $regex = '/{smartform\s([0-9]+)}/';
        if ( !$this->params->get( 'enabled', 1 ) ) {

            $row->text = preg_replace( $regex, '', $row->text );

            return true;

        }
        preg_match_all( $regex, $row->text, $matches,PREG_SET_ORDER);
        $count = count( $matches[0] );
        foreach($matches as $match)
        {
            if(count($match)<2)
                continue;

            $this->process($row,$match);
        }

    }


    public function onAjaxSaveForm(){
        return 'asdfasdf';
    }


    private function process($row,$match)
    {
        require_once JPATH_SITE.'/administrator/components/com_smartforms/plugin/SmartFormsLoader.php';
        $loader=new SmartFormsLoader();
        $formText=$loader->LoadForm($match[1]);
        $row->text = str_replace( $match[0], $formText, $row->text );
    }





}
