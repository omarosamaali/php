<?php
/**********************************************************************
   Theme:Curf 
   Developer:Dinesh

***********************************************************************/

$page_security = 'SA_OPEN';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

page(_($help_context = "Prime - Quick Menus"));

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/themes/prime/prime_theme.inc");
simple_page_mode(true);
//----------------------------------------------------------------------------------------------------

function can_process()
{
	if ($_POST['module'] == ALL_TEXT)
	{
		display_error(_("Module should be selected"));
		set_focus('module');
		return false;
	}
	if ($_POST['page'] == ALL_TEXT)
	{
		display_error(_("Page should be selected"));
		set_focus('page');
		return false;
	}
	if(GetSingleValue('pirme_theme_quick_menus','count(id)',array('user_id'=>$_SESSION['wa_current_user']->user)) >=8){
		display_error(_("Only eight quick menus are allowed"));
		set_focus('page');
		return false;
	}
	return true;
}

//----------------------------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' && can_process())
{

	$modules =$_SESSION['App']->applications[$_POST['module']]->modules;
	$items =array();
	foreach ($modules as $module) {
		foreach ($module->lappfunctions as $appfunction){
			if ($_SESSION["wa_current_user"]->can_access_page($appfunction->access)){
				$items[] = array($appfunction->label,$appfunction->link);
			}
		}
		foreach ($module->rappfunctions as $appfunction){;
			if ($_SESSION["wa_current_user"]->can_access_page($appfunction->access)){
				$items[] = array($appfunction->label,$appfunction->link);
			}
		}
	}

	Insert('pirme_theme_quick_menus',array('user_id'=>$_SESSION['wa_current_user']->user,'module'=>$_POST['module'],'link'=>$items[$_POST['page']][1],'label'=>access_string($items[$_POST['page']][0],true)));
	display_notification(_('New Quick menu has been added'));
	$Mode = 'RESET';
}

//----------------------------------------------------------------------------------------------------


//----------------------------------------------------------------------------------------------------

if ($Mode == 'Delete')
{
	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtor_trans'
	
	Delete('pirme_theme_quick_menus',array('id'=>$selected_id));
	display_notification(_('Selected Quick menu has been added'));
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}
//---------------------------------------------------------------------------------------

if(list_updated('module')){
	$Ajax->activate('edit_tbl');
}
//---------------------------------------------------------------------------------------


start_form();

start_table(TABLESTYLE, "width='30%'");

$th = array (_('Module'), _('Page'),'');
table_header($th);
$k = 0;

$quick_menus =GetAll('pirme_theme_quick_menus',array('user_id'=>$_SESSION['wa_current_user']->user));
if($quick_menus){
	foreach ($quick_menus as $myrow) 
	{
		alt_table_row_color($k);
		label_cell(access_string($_SESSION['App']->applications[$myrow['module']]->name,true),'align="center"');
		label_cell("<a href='".$path_to_root."/".$myrow['link']."'>".access_string($myrow['label'],true)."</a>",'align="center"');
		delete_button_cell("Delete".$myrow['id'], _("Delete"));
		end_row();
	}

}else{
	label_cell(_("No Data"),'colspan="3" align="center"');
}
end_table(1);

div_start('edit_tbl');
start_table(TABLESTYLE2);
start_row();
prime_applications_list_row(_('Module'), 'module', null, _("Select Module"), true);
if(get_post('module')){
	prime_pages_list_row(_('Pages'), 'page',get_post('module'),null, _("Select Page"));
}
end_row();
end_table(1);
div_end();
submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();

