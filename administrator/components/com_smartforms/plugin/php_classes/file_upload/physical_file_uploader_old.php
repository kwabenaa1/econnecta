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

	public function UploadFiles($entryData)
	{
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
            $fieldElement["path"]=JUri::root(false).'media/smartforms/uploads/'.$finalName;
            $fieldElement["type"]=$extension;
            $fieldElement["ppath"]=$destination.'/'.$finalName;


		}
		return array("success"=>true,
					"entryData"=>$entryData);

	}

} 