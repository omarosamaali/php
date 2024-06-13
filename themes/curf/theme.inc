<?php 
/**********************************************************************
   Theme:Curf 
   Developer:Dinesh

***********************************************************************/

include_once($path_to_root . "/themes/curf/common_db.inc");

// ------------------------------------------------------------------------------------

// theme functions 

// ------------------------------------------------------------------------------------

function theme_get_all_theme_options(){
	$query =GetAll('curf_options',array('type'=>'theme','type_name'=>'curf'));
	$result =array();
	if($query){
		foreach ($query as $key => $myrow) {
			$result[$myrow['name']]=$myrow['value'];
		}
	}
	return $result;
}


// ------------------------------------------------------------------------------------

function theme_applications_list($name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	$modules =array_keys($_SESSION['App']->applications);
	$items =array();
	foreach ($modules as $app) {
		$items[$app] =access_string($_SESSION['App']->applications[$app]->name,true);
	}
	

	return array_selector($name, $selected_id, $items, 
		array( 
			'spec_option'=>$all_option===true?_("Select Module"):$all_option, 	// option text or false
			'spec_id' => ALL_TEXT,
			'select_submit'=> $submit_on_change ) ); // FIX?
}

function theme_applications_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo theme_applications_list($name, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n";
}

function theme_applications_list_row($label, $name, $selected_id=null, $all_option=false,$submit_on_change =false)
{
	echo "<tr><td class='label'>$label</td>";
	theme_applications_list_cells(null, $name, $selected_id, $all_option,
		$submit_on_change);
	echo "</tr>\n";
}

// ------------------------------------------------------------------------------------

function theme_pages_list($name,$app, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	$modules =$_SESSION['App']->applications[$app]->modules;
	$items =array();
	foreach ($modules as $module) {
		foreach ($module->lappfunctions as $appfunction){
			if ($_SESSION["wa_current_user"]->can_access_page($appfunction->access)){
				$items[] = access_string($appfunction->label,true);
			}
		}
		foreach ($module->rappfunctions as $appfunction){;
			if ($_SESSION["wa_current_user"]->can_access_page($appfunction->access)){
				$items[] = access_string($appfunction->label,true);
			}
		}
	}
	
	return array_selector($name, $selected_id, $items, 
		array( 
			'spec_option'=>$all_option===true?_("Select Module"):$all_option, 	// option text or false
			'spec_id' => ALL_TEXT,
			'select_submit'=> $submit_on_change ) ); // FIX?
}

function theme_pages_list_cells($label, $name,$app, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo theme_pages_list($name,$app, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n";
}

function theme_pages_list_row($label, $name,$app, $selected_id=null, $all_option=false,$submit_on_change =false)
{
	echo "<tr><td class='label'>$label</td>";
	theme_pages_list_cells(null, $name,$app, $selected_id, $all_option,
		$submit_on_change);
	echo "</tr>\n";
}


function is_module_active($module)
{
	global $installed_extensions;

	foreach ($installed_extensions as $key => $value) {
		if($value['package']==$module && $value['active'] ==true){
			return true;
		}
	}
	return false;
}
// ------------------------------------------------------------------------------------

?>