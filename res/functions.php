<?php


function freefpf_generate_new_flipper($params=array())
{ // BEGIN function freefpf_generate_new_flipper
	$content;
	$debug_string='';
	
	$file_chooser=$params['file_chooser'];
	$use_path=$params['use_path'];
	$image_path=$params['image_path'];
	$debug = $params['debug'];
	$all = $params['allowed_ext'];
	$source_dir = $params['source_dir'];
	$upload_dir=$params['upload_dir'];
	$flex_upload_dir=$params['flex_upload_dir'];
	$new_dir_name=$params['new_dir_name'];
	$return_no_content=$params['return_no_content'];
	$generate_anyway=$params['generate_anyway'];
	$do_not_generate=$params['do_not_generate'];
	
	$flexform_array=$params['flexform_array'];
	//print_r($flexform_array);
	
	$use_path=false;
	if(trim($image_path)!='')
	{
		$use_path=true;
	}
	///var/www/virtual/okt-modelimport.dk/htdocs/fileadmin/flashcatalogs
	if(!is_array($flexform_array))
	{
		$flexform_array=array();
	}
	
	$iframe_dir=$params['iframe_dir'];
	
	$dir1 = $_SERVER['DOCUMENT_ROOT'];
	$root_dir=$dir1.'/';
	
	
	//Removing trailing slash: 
	if (substr($image_path ,-1)=='/' or substr($image_path ,"",-1)=='\\') 
	{
		$image_path = substr_replace($image_path ,"",-1);
		//$debug_string.="Removing trailing slash $image_path <br />";
	}
	
	$new_directory=$upload_dir.'/'.$new_dir_name;
	//$new_directory=$upload_dir.'/'.md5($image_path);
	if($do_not_generate)
	{
		$generate_only_iframe_src=$new_directory."/Default.html";
		$generate_only_iframe_src_clean=str_replace($root_dir,'',$generate_only_iframe_src);
		
		if (!file_exists($generate_only_iframe_src) and !file_exists($generate_only_iframe_src_clean)) 
		{
			$debug_string="Does not exists: $generate_only_iframe_src_clean <br />";
			$generate_only_iframe_src="$source_dir/Default.html";
			$generate_only_iframe_src_clean=str_replace($root_dir,'',$generate_only_iframe_src);
		}
		
		$content="<iframe width='100%' height='800' src='$generate_only_iframe_src_clean'></iframe>";
		$debug_string="Generating iframe only: $generate_only_iframe_src_clean <br />";
		if($return_no_content)
		{
			$content='';
		}
		if($debug) $content=$debug_string.$content;
		return $content;
	} 
	

	$image_path=str_replace($root_dir,'',$image_path);
	$image_path_root=$root_dir.$image_path;
	
	//If specified path is wrong - display default flash page flipper:	   
	if((!file_exists($image_path) or !is_dir($image_path))and (!file_exists($image_path_root) or !is_dir($image_path_root)))
	{
		$content="<iframe width='100%' height='800' src='$source_dir/Default.html'></iframe>";
		$debug_string.="There is no directory $image_path or $image_path_root <br />";
		if($return_no_content)
		{
			$content='';
		}
		if($debug) $content=$debug_string.$content;
		//return $content;
		$use_path=false;
	} 
	//$debug_string="Image  path is: $image_path <br />";
	
	$all_array=array();
	$will_check_ext=false;
	if (trim($all)!='') 
	{
		$all_array=explode(',',$all);
		
		foreach ($all_array as $key=>$value) 
		{
			$all_array[$key]=strtolower($value);
		}
		$will_check_ext=true;
	}
	
	
	$str_dir=array();
	$str_dir[]=array('type'=>'abs_dir','path'=>$upload_dir);
	$str_dir[]=array('type'=>'abs_dir','path'=>$new_directory);
	$str_dir[]=array('type'=>'rel_file','path'=>"Default.html",'source'=>'Default2.html');
	$str_dir[]=array('type'=>'rel_dir','path'=>"swf");
	$str_dir[]=array('type'=>'rel_file','path'=>"swf/Magazine.swf");
	$str_dir[]=array('type'=>'rel_file','path'=>"swf/Pages.swf");
	$str_dir[]=array('type'=>'rel_dir','path'=>"txt");
	$str_dir[]=array('type'=>'rel_file','path'=>"txt/Lang.txt");
	$str_dir[]=array('type'=>'rel_dir','path'=>"xml");
	$str_dir[]=array('type'=>'rel_dir','path'=>"pages");
	
	$failure=false;
	$failure_reason='';
	
	
	
	
	foreach ($str_dir as $key=>$value) 
	{
		$file_dir_created=false;
		$file_or_dir=$new_directory.'/'.$value['path'];
		if ($value['type']=='abs_dir') 
		{
			$file_or_dir=$value['path'];
		}
		
		if (!file_exists($file_or_dir) and !file_exists($root_dir.$file_or_dir)) 
		{
		
			if($value['type']=='abs_dir' or $value['type']=='rel_dir')
			{ 
				$file_dir_created=@mkdir("$dir1/$file_or_dir",0777);
			}
			if($value['type']=='rel_file')
			{ 
				$source_file="$dir1/$source_dir/{$value['path']}";
				if (trim($value['source'])!='') 
				{
					$source_file="$dir1/$source_dir/".trim($value['source']);
				}
				//echo "@copy('$source_file', '$dir1/$file_or_dir');";
				$file_dir_created=@copy("$source_file", "$dir1/$file_or_dir");
			}
		
		}
		
		if (!file_exists($file_or_dir) and !file_exists($root_dir.$file_or_dir))
		{
			$failure=true;
			$failure_reason="$file_or_dir ($root_dir.$file_or_dir) could not be created!";
			break;
		}
	}
	
	
	
	if($failure==false)
	{
	
		$latest_file=0;
		$files=array();
		if($use_path)
		{
			if ($dh = @opendir($image_path_root)) 
			{
				while (($file = @readdir($dh)) !== false) 
				{
				
					if (!is_dir($image_path_root.'/'.$file)) 
					{
						$path_parts = @pathinfo($image_path_root.'/'.$file);
						$new_latest_file=@filemtime($image_path_root.'/'.$file);
						if ($new_latest_file>$latest_file) 
						{
							$latest_file = $new_latest_file;
						}
						
						if (!$will_check_ext or($will_check_ext and in_array(strtolower($path_parts['extension']),$all_array)))
						{
						
							$files[]=trim($path_parts['basename']);
							$new_latest_file=@filemtime($image_path_root.'/'.$file);
							if ($new_latest_file>$latest_file) 
							{
								$latest_file = $new_latest_file;
							}
						
						}
					
					}
				}
				@closedir($dh);
			}
			sort($files);      
			//$debug_string.="Newest file time: ".date('Y-m-d H:i:s' ,$latest_file).'<br />';      
			
			
			$regenerate_images=false;     
			if (!file_exists("$new_directory/xml/Pages.xml") and !file_exists($root_dir."$new_directory/xml/Pages.xml")) 
			{
			//$debug_string.="File $new_directory/xml/Pages.xml does not exists.".'<br />'; 
				$regenerate_images=true;
			
			}
			
			if (file_exists("$new_directory/xml/Pages.xml")) 
			{
			
				if (@filemtime("$new_directory/xml/Pages.xml")<$latest_file) 
				{
					//$debug_string.="Pages.xml time is: ".date('Y-m-d H:i:s' ,@filemtime("$new_directory/xml/Pages.xml")).'<br />'; 
					$regenerate_images=true;
				} 
			}
			$debug_string.= "Will regenerate from path: $image_path".'<br />';    
		}
		else
		{
			$files=explode(',',$file_chooser);
			$image_path = $flex_upload_dir;
			$regenerate_images = true;
			$debug_string.= "Will regenerate from file chooser".'<br />'; 
		}
		
		
		$xml_params=array();
		$xml_params['width']=368;
		$xml_params['height']=450;
		$xml_params['bgcolor']='cccccc';
		$xml_params['loadercolor']='ffffff';
		$xml_params['panelcolor']='5d5d61';
		$xml_params['buttoncolor']='5d5d61';
		$xml_params['textcolor']='ffffff';
		
		
		if($flexform_array['page_width']>0) $xml_params['width']=$flexform_array['page_width'];
		if($flexform_array['page_height']>0) $xml_params['height']=$flexform_array['page_height'];
		if(trim($flexform_array['bgcolor'])!='') $xml_params['bgcolor']=$flexform_array['bgcolor'];
		if(trim($flexform_array['loadercolor'])!='') $xml_params['loadercolor']=$flexform_array['loadercolor'];
		if(trim($flexform_array['panelcolor'])!='') $xml_params['panelcolor']=$flexform_array['panelcolor'];
		if(trim($flexform_array['buttoncolor'])!='') $xml_params['buttoncolor']=$flexform_array['buttoncolor'];
		if(trim($flexform_array['textcolor'])!='') $xml_params['textcolor']=$flexform_array['textcolor'];
		
		foreach ($xml_params as $key=>$value) 
		{
			if($key=='width' or $key=='height') continue;
			
			$xml_params[$key]=str_replace('#','',$value);
		}
		
		if($regenerate_images or $generate_anyway)
		{		    
			$xml_string='<content width="'.$xml_params['width'].'" height="'.$xml_params['height'].'" bgcolor="'.$xml_params['bgcolor'].'" loadercolor="'.$xml_params['loadercolor'].'" panelcolor="'.$xml_params['panelcolor'].'" buttoncolor="'.$xml_params['buttoncolor'].'" textcolor="'.$xml_params['textcolor'].'">';
			foreach ($files as $key=>$value) 
			{
				@copy($dir1.'/'.$image_path.'/'.$value,"$dir1/$new_directory/pages/$value");
				$xml_string.='<page src="pages/'.$value.'"/>';
			}   
			$xml_string.='</content>';
			@file_put_contents("$dir1/$new_directory/xml/Pages.xml",$xml_string);
			
			$debug_string.= "Regenerating image structure to $new_directory".'<br />'; 
		
		}	else $debug_string.= " Using old image structure".'<br />'; 	  
		
		if (file_exists("$new_directory/xml/Pages.xml") or file_exists($root_dir."$new_directory/xml/Pages.xml")) 
		{
			$content="<iframe width='100%' height='800' src='$new_directory/Default.html'></iframe>";
		} 
		else
		{
			$debug_string.= "File $new_directory/xml/Pages.xml could not be created, so using default flipper.".'<br />'; 
			$content="<iframe width='100%' height='800' src='$source_dir/Default.html'></iframe>";
		}
	
	}else $debug_string=$failure_reason;
	

	if($return_no_content)
	{
		$content='';
	}
	if($debug) $content=$debug_string.$content;
	
	return $content;
	

} // END function freefpf_generate_new_flipper


?>
