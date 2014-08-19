<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009  <>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'free FPF' for the 'freefpf' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_freefpf
 */
class tx_freefpf_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_freefpf_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_freefpf_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'freefpf';	// The extension key.
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
	
			$ceid = $this->cObj->data['uid'];
		
		
		
		$this->pi_initPIflexForm(); // Init and get the flexform data of the plugin
		$this->lConf = array(); // Setup our storage array...
		// Assign the flexform data to a local variable for easier access
		$piFlexForm = $this->cObj->data['pi_flexform'];
		
		//print_r($this->cObj->data['pi_flexform']);
		
		
		// Traverse the entire array based on the language...
		// and assign each configuration option to $this->lConf array...
		if (is_array($piFlexForm['data'])) 
		{
			foreach ( $piFlexForm['data'] as $sheet => $data ) 
			{
				foreach ( $data as $lang => $value ) 
				{
					foreach ( $value as $key => $val ) 
					{
						$this->lConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
					}
				}
			}
		}
		
		$params=array();
		
		//$new_directory="uploads/tx_freefpf/instance_$ceid";
		
		$image_path=$this->lConf['path'];
		$all=$this->lConf['allowed_ext'];
		$debug=$this->lConf['debug'];
		
		$dir1 = $_SERVER['DOCUMENT_ROOT'];
		$root_dir=$dir1.'/';
		$source_dir=$root_dir."typo3conf/ext/freefpf/free";
		
		$root_dir='';
		$params['file_chooser']=$this->lConf['file_chooser'];
		$params['use_path']=$this->lConf['use_path'];
		$params['image_path']=$this->lConf['path'];
		$params['debug']=$this->lConf['debug'];
		$params['allowed_ext']=$this->lConf['allowed_ext'];
		$params['source_dir']=$source_dir;
		$params['upload_dir']="uploads/tx_freefpf";
		$params['flex_upload_dir']="uploads";
		$params['new_dir_name']="instance_".$ceid;
		$params['return_no_content']=false;
		
		$params['iframe_dir']="typo3conf/ext/freefpf/free";
		
		$params['generate_anyway']=true;
		$params['do_not_generate']=true;
		
		$params['iframe_dir']="typo3conf/ext/freefpf/free";
		
		include_once('typo3conf/ext/freefpf/res/functions.php');
		
		$content.=freefpf_generate_new_flipper($params);
		
		
		return $this->pi_wrapInBaseClass($content);

		
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/freefpf/pi1/class.tx_freefpf_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/freefpf/pi1/class.tx_freefpf_pi1.php']);
}

?>