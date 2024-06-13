<?php
/**********************************************************************
   Theme:Curf 
   Developer:Dinesh

***********************************************************************/

$page_security = 'SA_OPEN';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

page(_($help_context = "Theme Options"));

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/themes/curf/theme.inc");

//----------------------------------------------------------------------------------------------------
$theme_option_names=array(
	'color_scheme',
	'theme_mode',
	'font_api_link',
	'font_api_css',
	'site_name',
	'site_url',
	'footer_hide',
	'footer_hide_version',
	'footer_date_time',
	'footer_hide_servername',
	'footer_hide_companyname',
	'footer_hide_themename',
	'footer_hide_username',
	'pusher_app_id',
	'pusher_app_key',
	'pusher_secret_key',
	'pusher_toastr_position'
);

$theme_color_schemes =array(
	'#31708F'=>'Calypso',
	'#2b7a77'=>'Paradiso',
	'#2b307a'=>'Astronaut',
	'#C30891'=>'Red Violet',
	'#B5AF0E'=>'La Rioja',
	'#B7112B'=>'Shiraz',
	'#A609B8'=>'Purple',
);

$pusher_toastr_positions =array(
	'top-left'=>_('Top Left'),
	'top-center'=>_('Top Center'),
	'top-right'=>_('Top Right'),
	'top-full-width'=>_('Top Full Width'),
	'bottom-left'=>_('Bottom Left'),
	'bottom-center'=>_('Bottom center'),
	'bottom-right'=>_('Bottom right'),
	'bottom-full-width'=>_('ottom Full Width'),
);
//----------------------------------------------------------------------------------------------------

if(isset($_GET['Updated'])){
	display_notification(_('Theme options updated successfully'));
}
if(isset($_POST['update']) && $_POST['update']){

	//----------------------------------------fav icon-----------------------------------------

	$favicon =true;

	if(!isset($_FILES['favicon']) || !$_FILES['favicon']['name']){
		$favicon =false;
	}
	if (isset($_FILES['favicon']) && $favicon && $_FILES['favicon']['error'] > 0) {
    	if ($_FILES['favicon']['error'] == UPLOAD_ERR_INI_SIZE){ 
		  	display_error(_("The favicon file size is over the maximum allowed."));
		  	$favicon =false;
		}
  	}


  	if(isset($_FILES['favicon']) && $favicon){
  		$filetype = $_FILES['favicon']['type'];
  		if(pathinfo($_FILES['favicon']['name'],PATHINFO_EXTENSION)=="ico" && $filetype !='image/x-icon'){
  			display_error(_("Favicon should be a <b>.ico</b> file"));
  		}
  	}
  	if(isset($_FILES['favicon']) && $favicon){
  		$tmpname = $_FILES['favicon']['tmp_name'];
	  	$favicon_path =$path_to_root.'/themes/curf/images/favicon.ico';
	  	
	  

		if (file_exists($favicon_path))
			unlink($favicon_path);

		if(!move_uploaded_file($tmpname, $favicon_path)){
			$favicon =false;
		}
  	}

	//---------------------------------------------------------------------------------------
	if($_POST['color_scheme_name'] !=ALL_NUMERIC)
		$_POST['color_scheme'] =$_POST['color_scheme_name'];
  	foreach ($theme_option_names as $name) {
  		if(!isset($_POST[$name]))
  			continue;
  		if($name =='footer_hide' || $name =='footer_hide_version'|| $name =='footer_date_time'|| $name =='footer_hide_servername'|| $name =='footer_hide_companyname'|| $name =='footer_hide_themename'|| $name =='footer_hide_username'){
  			Update('curf_options',array('name'=>$name,'type'=>'theme','type_name'=>'curf'),array('value'=>check_value($name)));
  		}
  		else{
  		    
  		    Update('curf_options',array('name'=>$name,'type'=>'theme','type_name'=>'curf'),array('value'=>$_POST[$name]));
  			
  		}
  	}
	meta_forward($_SERVER['PHP_SELF'],"Updated=1");
}

//----------------------------------------------------------------------------------------------------

$result = theme_get_all_theme_options();
foreach ($theme_option_names as $name) {
	if(isset($result[$name]) && !isset($_POST[$name])){
		if($name =='color_scheme' && isset($theme_color_schemes[$result['color_scheme']]) && !isset($_POST['color_scheme_name'])){
			$_POST['color_scheme_name'] =htmlspecialchars_decode($result[$name]);
			continue;
		}
		$_POST[$name] =htmlspecialchars_decode($result[$name]);
	}
}
//----------------------------------------------------------------------------------------------------
if(list_updated('footer_hide') || list_updated('color_scheme_name')){
	$Ajax->Activate('_page_body');
}
//----------------------------------------------------------------------------------------------------

start_form(true);

start_outer_table(TABLESTYLE2);
table_section(1);
table_section_title(_("Colors Schemes"));
array_selector_row(_("Theme Mode"),'theme_mode',null,array('light'=>_("Light"),'dark'=>_("Dark")));
array_selector_row(_("Select Color"),'color_scheme_name',null,$theme_color_schemes,array('spec_option'=>_("Color Picker"),'spec_id'=>ALL_NUMERIC,'select_submit'=>true));
if($_POST['color_scheme_name'] ==ALL_NUMERIC){
	start_row();
	label_cell(_("Select Color"),'class="label"');
	echo '<td>';
	echo '<input type="color"  name="color_scheme" value='.$_POST['color_scheme'].'>';
	echo '</td>';
	end_row();
}

table_section_title(_("Google fonts"));
textarea_row(_("Font API link"), 'font_api_link', null, 30, 8);
textarea_row(_("Font CSS rule"), "font_api_css", null, 30, 8);

if(is_module_active('CurfPusher')){
	table_section_title(_("Pusher Notification Settings"));
	array_selector_row(_("Toastr position"),'pusher_toastr_position',null,$pusher_toastr_positions);
	text_row(_("App Key"), "pusher_app_key",null, 20, 30);
	text_row(_("App Secret Key"), "pusher_secret_key",null, 20, 30);
	text_row(_("App Id"), "pusher_app_id",null, 20, 30);
}


table_section(2);
table_section_title(_("Site Settings"));
file_row(_("Favicon"), 'favicon', 'favicon');

$favicon_img_link = "<img id='favicon' alt = '[favicon.ico]' src='".$path_to_root."/themes/curf/images/favicon.ico?nocache=".rand()."' height='".$SysPrefs->pic_height."' border='0'>";

label_row(_("Favicon"), $favicon_img_link);
text_row(_("Site Name"), "site_name",null, 20, 30);
text_row(_("Site Url"), "site_url", null, 20, 30);
table_section_title(_("Footer Settings"));

check_row(_("Hide footer"), 'footer_hide',null,true);
if(!check_value('footer_hide')){
	check_row(_("Hide version"), 'footer_hide_version',null);
	check_row(_("Hide Date Time"), 'footer_date_time',null);
	check_row(_("Hide Server name"), 'footer_hide_servername',null);
	check_row(_("Hide Company name"), 'footer_hide_companyname',null);
	check_row(_("Hide User name"), 'footer_hide_username',null);
	check_row(_("Hide Theme name"), 'footer_hide_themename',null);
}

end_outer_table(2);

submit_center('update', _("Update"), true, '',  'default');

end_form();

end_page();

?>