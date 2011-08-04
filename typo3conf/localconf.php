<?php
$TYPO3_CONF_VARS['SYS']['sitename'] = 'New TYPO3 site';

	// Default password is "joh316" :
$TYPO3_CONF_VARS['BE']['installToolPassword'] = 'e70637f1500e785fc3d1e941a239f2c5';

$TYPO3_CONF_VARS['EXT']['extList'] = 'version,tsconfig_help,context_help,extra_page_cm_options,impexp,sys_note,tstemplate,tstemplate_ceditor,tstemplate_info,tstemplate_objbrowser,tstemplate_analyzer,func_wizards,wizard_crpages,wizard_sortpages,lowlevel,install,belog,beuser,aboutmodules,setup,taskcenter,info_pagetsconfig,viewpage,rtehtmlarea,css_styled_content,t3skin,t3editor,reports';

$typo_db_extTableDef_script = 'extTables.php';

## INSTALL SCRIPT EDIT POINT TOKEN - all lines after this points may be changed by the install script!

$typo_db_username = 'root';	//  Modified or inserted by TYPO3 Install Tool.
$typo_db_password = '';	//  Modified or inserted by TYPO3 Install Tool.
$typo_db_host = 'localhost';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['BE']['disable_exec_function'] = '0';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['im'] = '1';
$TYPO3_CONF_VARS['GFX']['im_version_5'] = 'gm';	// Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS["GFX"]["im_v5effects"] = '0';
$TYPO3_CONF_VARS['GFX']['gdlib'] = '1'; 
$TYPO3_CONF_VARS['GFX']['gdlib_2'] = '1';
$TYPO3_CONF_VARS['GFX']['gdlib_png'] = '1';
$TYPO3_CONF_VARS['GFX']['im_path'] = 'c:\\GraphicsMagick/';	// Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['im_path_lzw'] = 'c:\\GraphicsMagick/';	// Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['thumbnails'] = '1';
$TYPO3_CONF_VARS['GFX']['thumbnails_png'] = '3';
$TYPO3_CONF_VARS['GFX']['jpg_quality'] = '80'; 
$TYPO3_CONF_VARS['GFX']['TTFdpi'] = '96'; 
$TYPO3_CONF_VARS['GFX']['png_truecolor'] = '1';
$TYPO3_CONF_VARS['GFX']['im_combine_filename'] = 'composite';	// Modified or inserted by TYPO3 Install Tool. 
$TYPO3_CONF_VARS['GFX']['im_negate_mask'] = '1';	// Modified or inserted by TYPO3 Install Tool. 
$TYPO3_CONF_VARS['GFX']['im_imvMaskState'] = '0';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['image_processing'] = '1';
$TYPO3_CONF_VARS['SYS']['setDBinit'] = 'SET NAMES utf8;';
$TYPO3_CONF_VARS['SYS']['multiplyDBfieldSize'] = '2';
$typo_db = 'r-revue';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['SYS']['sitename'] = 'RR';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['SYS']['encryptionKey'] = '4bfe2ed11d18ab32c3414ce9c0ca2627a94c1847016ffa126041f2a3e974887758c482c0085fdeaba6ac843fa355fc80';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['SYS']['compat_version'] = '4.4';	//  Modified or inserted by TYPO3 Install Tool.
// Updated by TYPO3 Install Tool 06-09-10 22:31:47
$TYPO3_CONF_VARS['EXT']['extList'] = 'css_styled_content,version,tsconfig_help,context_help,extra_page_cm_options,impexp,sys_note,tstemplate,tstemplate_ceditor,tstemplate_info,tstemplate_objbrowser,tstemplate_analyzer,func_wizards,wizard_crpages,wizard_sortpages,lowlevel,install,belog,beuser,aboutmodules,setup,taskcenter,info_pagetsconfig,viewpage,rtehtmlarea,t3skin,t3editor,reports,templavoila,media_center,kickstarter,ew_tsconfig,dam,ew_gallery,dam_index,ew_pibase,static_info_tables,ew_facts,ew_press,recycler,formhandler,ew_calendar,rb_flashobject,realurl,realurlmanagement,aeurltool,ew_content';	// Modified or inserted by TYPO3 Extension Manager. Modified or inserted by TYPO3 Core Update Manager. 
$TYPO3_CONF_VARS['EXT']['extList_FE'] = 'css_styled_content,version,install,rtehtmlarea,t3skin,templavoila,media_center,kickstarter,ew_tsconfig,dam,ew_gallery,dam_index,ew_pibase,static_info_tables,ew_facts,ew_press,formhandler,ew_calendar,rb_flashobject,realurl,realurlmanagement,aeurltool,ew_content';	// Modified or inserted by TYPO3 Extension Manager. 
$TYPO3_CONF_VARS['BE']['forceCharset'] = 'utf-8';
// Updated by TYPO3 Install Tool 19-09-10 14:15:39
$TYPO3_CONF_VARS['EXT']['extConf']['dam_index'] = 'a:2:{s:18:"add_media_indexing";s:1:"0";s:23:"add_media_file_indexing";s:1:"1";}';	//  Modified or inserted by TYPO3 Extension Manager.
$TYPO3_CONF_VARS['EXT']['extConf']['templavoila'] = 'a:1:{s:7:"enable.";a:3:{s:13:"oldPageModule";s:1:"0";s:16:"selectDataSource";s:1:"0";s:15:"renderFCEHeader";s:1:"1";}}';	//  Modified or inserted by TYPO3 Extension Manager.
// Updated by TYPO3 Extension Manager 05-10-10 22:03:00
// Updated by TYPO3 Core Update Manager 09-10-10 20:05:00
$TYPO3_CONF_VARS['EXT']['extConf']['rtehtmlarea'] = 'a:13:{s:21:"noSpellCheckLanguages";s:23:"ja,km,ko,lo,th,zh,b5,gb";s:15:"AspellDirectory";s:15:"/usr/bin/aspell";s:17:"defaultDictionary";s:2:"en";s:14:"dictionaryList";s:2:"en";s:20:"defaultConfiguration";s:105:"Typical (Most commonly used features are enabled. Select this option if you are unsure which one to use.)";s:12:"enableImages";s:1:"1";s:20:"enableInlineElements";s:1:"0";s:19:"allowStyleAttribute";s:1:"1";s:24:"enableAccessibilityIcons";s:1:"0";s:16:"enableDAMBrowser";s:1:"0";s:16:"forceCommandMode";s:1:"0";s:15:"enableDebugMode";s:1:"0";s:23:"enableCompressedScripts";s:1:"1";}';	//  Modified or inserted by TYPO3 Extension Manager.
// Updated by TYPO3 Extension Manager 09-11-10 11:23:33

@include(PATH_typo3conf.'urltoolconf_realurl.php'); // RealUrl-Configuration inserted by extension aeurltool
// Updated by TYPO3 Extension Manager 14-11-10 21:19:09
?>