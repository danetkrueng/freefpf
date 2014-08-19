<?php

class tx_freefpf_hook
{ // BEGIN class freefpf_hook
	// variables
	//var $;

	// constructor
	function tx_freefpf_hook()
	{ // BEGIN constructor
		
	} // END constructor


    function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, $object)
    { // BEGIN function processDatamap_afterDatabaseOperations
    	
    
         //$n= var_export($fieldArray, true);

		
		if(isset($fieldArray['pi_flexform']))
		{
			
			$res_town = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'*',         			// SELECT ...
					'tt_content',     		// FROM ...
					"uid = '$id'",    // WHERE...
					'',            			// GROUP BY...
					'',    					// ORDER BY...
					''            			// LIMIT ...
					);
			
			if($ce_data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res_town))
			{
				$flexform=$ce_data['pi_flexform'];
				///echo $flexform;
				$petras=t3lib_div::xml2array($flexform);
				
				$fl_array=array();
				
				if (is_array($petras)) 
				{
					foreach ( $petras as $sheet => $data ) 
					{
						foreach ( $data as $lang => $value ) 
						{
							foreach ( $value as $key => $val ) 
							{
								foreach ( $val as $ke => $va ) 
								{
									$fl_array[$ke]=$va['vDEF'];
								}
							}
						}
					}
				}
				
				//print_r($fl_array);
				
				$params=array();
				
				
				$image_path=$this->lConf['path'];
				$all=$this->lConf['allowed_ext'];
				$debug=$this->lConf['debug'];
				
				
				$source_dir="typo3conf/ext/freefpf/free";
				
				
				$params['file_chooser']=$fl_array['file_chooser'];
				$params['use_path']=$fl_array['use_path'];
				$params['image_path']=$fl_array['path'];
				$params['debug']=$fl_array['debug_b'];
				//$params['debug']=true;
				$params['allowed_ext']=$fl_array['allowed_ext'];
				
				$params['source_dir']=$source_dir;
				$params['upload_dir']="uploads/tx_freefpf";
				$params['flex_upload_dir']="uploads";
				$params['new_dir_name']="instance_".$id;
				$params['return_no_content']=true;
				$params['generate_anyway']=true;
				
				$params['flexform_array']=$fl_array;
				
				$params['iframe_dir']="typo3conf/ext/freefpf/free";
				
				$dir1 = $_SERVER['DOCUMENT_ROOT'];
				$typo3_dir1 = t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT');
				
				@include_once('typo3conf/ext/freefpf/res/functions.php');
				
				if (!function_exists('freefpf_generate_new_flipper')) 
				{
					@include_once($dir1.'/typo3conf/ext/freefpf/res/functions.php');
				}
				if (!function_exists('freefpf_generate_new_flipper')) 
				{
					@include_once($typo3_dir1.'/typo3conf/ext/freefpf/res/functions.php');
				}
				if(function_exists('freefpf_generate_new_flipper'))
				{
					$content.=freefpf_generate_new_flipper($params);
					if($fl_array['debug_b'])
					{
						echo $content;
					}
				}
				else
				{
					if($fl_array['debug_b'])
					{
						echo "Could not include file 'typo3conf/ext/freefpf/res/functions.php' or '$dir1/typo3conf/ext/freefpf/res/functions.php' ";
					}
				}
			}
		

	
       }
    	
    	
    } // END function processDatamap_afterDatabaseOperations

} // END class freefpf_hook

?>
