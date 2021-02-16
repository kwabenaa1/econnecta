<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
class physical_file_uploader {
    function __construct()
    {

    }

    public function CreateFolderIfNeeded($path){
        $db=JFactory::getDBO();
        $query="CREATE TABLE IF NOT EXISTS #__smart_forms_uploaded_files (
                        uploaded_file_id INT AUTO_INCREMENT,
                            file_key  VARCHAR(32),
                            file_name VARCHAR(500),
                            file_mime VARCHAR(500),
                            original_name VARCHAR(500),
                            PRIMARY KEY (uploaded_file_id)
                ) COLLATE utf8_general_ci;";

        $db->setQuery($query);
        $db->execute();


        if ( !JFolder::exists( $path ) ) {
            if(!@JFolder::create( $path ))
                return false;
            @file_put_contents( $path . '/.htaccess', 'deny from all' );
            @touch( $path . '/index.php' );
            return true;
        }
        return true;
    }

    public function UploadFiles($entryData)
    {

        $path='media/smartforms/uploads';
        $this->CreateFolderIfNeeded($path);
        if(!JFile::exists($path.'/.htaccess'))
        {
            @file_put_contents( $path . '.htaccess', 'deny from all' );
            @touch( $path . 'index.php' );
        }

        $files=array();

        foreach($_FILES as $key=>$value)
        {
            $splittedFiles=explode("@",$key);
            $fieldName=$splittedFiles[1];
            $imageNumber=$splittedFiles[2];
            $fieldElement=&$entryData[$fieldName][$imageNumber-1];
            $allowedExtensions=explode(',',"zip,rar,pdf,doc,xls,ppt,jpg,jpeg,gif,png,txt");
            $fileName=pathinfo($value["name"], PATHINFO_FILENAME).'_'.uniqid("",true);
            $destination='media/smartforms/uploads';
            $fieldName=JFile::makeSafe($fileName);
            $p=1;
            $finalName=$fileName;
            while(file_exists( $destination.'/'.$finalName))
            {
                $fileName=$fileName.'_'.$p;
                $p++;
            }

            $extension=strtolower(pathinfo($value["name"], PATHINFO_EXTENSION));
            $finalName.=".".$extension;

            if(!in_array($extension,$allowedExtensions))
            {
                return array("success"=>false,
                    "entryData"=>"","cause"=>"Invalid Extension");
            }


            if(!JFile::upload($value['tmp_name'], $destination.'/'.$finalName))
            {
                return array("success"=>false,
                    "entryData"=>"","cause"=>"");
            }

            /*$path= wp_handle_upload($value,array(
                'test_form' => false
            ));*/

            $id=md5(uniqid($fileName,true));
            $fieldElement["path"]=JUri::root(false).'?option=com_ajax&plugin=publicajax&format=raw&action=openfile&id='.$id;
            $fieldElement["type"]=$extension;
            $fieldElement["id"]=$id;
            $fieldElement["ppath"]=$destination.'/'.$finalName;

            $files[]=array(
                'file_key'=>$id,
                'file_name'=>$destination.'/'.$finalName,
                'original_name'=>$value['name'],
                'file_mime'=>$value['type']
            );
        }

        foreach($files as $file)
        {
            $db=JFactory::getDBO();
            $db->setQuery("insert into #__smart_forms_uploaded_files (file_key,file_name,original_name,file_mime) values(
              ".$db->quote($file['file_key']).",
              ".$db->quote($file['file_name']).",
              ".$db->quote($file['original_name']).",
              ".$db->quote($file['file_mime'])."
            )");

            $db->execute();
        }

        return array("success"=>true,
            "entryData"=>$entryData);

    }

} 