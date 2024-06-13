<?php 

$path_to_root ="../../.."; 
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");

include_once("../kvcodes.inc");

if(isset($_GET['Line_chart'])){
	 $top_selling_items =  class_balances($_GET['Line_chart']); 
	 $area_chart =  array(); 
	 foreach($top_selling_items as $top) { 
	 	$area_chart[] =   array("class" => $top['class_name'] , "value" => abs($top['total']));  
	 } 
	 echo json_encode($area_chart); exit; 
}

if(isset($_GET['Area_chart'])){
	 $top_selling_items =  Top_selling_items($_GET['Area_chart']); 
	 $area_chart =  array(); 
	 foreach($top_selling_items as $top) { $area_chart[] =   array("y" => $top['description'] , "a" => round($top['total'], 2), "b" => round($top['costs'], 2));  } echo json_encode($area_chart); exit; 
}

if(isset($_GET['Customer_chart'])){
	$cutomers = get_top_customers($_GET['Customer_chart']);
	$donut_chart =  array(); 
	 foreach($cutomers as $top) { 
	 	$donut_chart[] =   array("label" => $top['name'] , "value" => round($top['total'], 2));  
	 } 
	 echo json_encode($donut_chart); exit;
}

if(isset($_GET['Supplier_chart'])){
	$suppliers = get_top_suppliers($_GET['Supplier_chart']);
	$donut_chart =  array(); 
	 foreach($suppliers as $top) { 
	 	$donut_chart[] =   array("label" => $top['supp_name'] , "value" => round($top['total'], 2));  
	 } 
	 echo json_encode($donut_chart); exit;
}

if(isset($_GET['Expense_chart'])){
	$cutomers = Expenses($_GET['Expense_chart']);
	$bar_chart =  array(); 
	 foreach($cutomers as $top) { 
	 	$bar_chart[] =   array("y" => htmlspecialchars_decode($top['name']) , "a" => round($top['balance'], 2));  
	 } 
	 if(empty($bar_chart)){
		$bar_chart[] = array("y" => "nothing" , "a" => 0);
	}
	 echo json_encode($bar_chart); exit;
}
if(isset($_GET['Tax_chart'])){
	$suppliers = get_tax_reports($_GET['Tax_chart']);
	$donut_chart =  array(); 
	 foreach($suppliers as $top) { 
		if(isset($top['total']) && is_numeric($top['total']))
	 		$donut_chart[] =   array("label" => $top['name'] , "value" => abs(round($top['total'], 2)));  
	 } 
	// $donut_chart['grandtotal'] = abs(round($suppliers['grandtotal'],2));
	 echo json_encode($donut_chart); exit;
}

if(isset($_GET['ChangeCompany'])) {
	if(isset($db_connections[$_GET['ChangeCompany']])){
		$_SESSION['wa_current_user']->company = $_GET['ChangeCompany'];
		$db_table_name = $db_connections[$_SESSION['wa_current_user']->company]['dbname'].'.'.$db_connections[$_SESSION['wa_current_user']->company]['tbpref'];
		$sql = "SELECT id FROM ".$db_table_name."users WHERE user_id =".db_escape($_SESSION['wa_current_user']->loginname)." LIMIT 1";
		$res = db_query($sql, "Can't get user Account");
		if(db_num_rows($res) == 1){
			if($row = db_fetch($res)){
				$current_user_id = $row['id'];
			} else {
				$current_user_id = -1;
			}
		} else 
			$current_user_id = -1;

		if($current_user_id == -1){
			$sql = "SELECT id FROM ".$db_table_name."users ORDER BY id LIMIT 1";
				$res = db_query($sql, "Can't get user Account");
				$row = db_fetch($res);
				$current_user_id = $row['id'];
		}

		echo $_SESSION['wa_current_user']->user = $current_user_id;
	}

	echo $_SESSION['wa_current_user']->company;
} elseif(isset($_GET['GetCompany']) && $_GET['GetCompany'] == 'yes') {
	$filtered =[];
	include_once($path_to_root."/themes/LTE/includes/users.php");
	$row = get_master_login($_SESSION['wa_current_user']->loginname);
	$companies = unserialize(base64_decode($row['companies']));

	if(isset($_GET['term']) && $_GET['term'] != ''){		
		foreach($db_connections as $cid => $data){
			if (strpos($_GET['term'], $data['name']) !== FALSE && (isset($companies) && in_array($cid, $companies))) { // Yoshi version
			        $filtered[$cid] = $data['name'];
   			}
		}
	}
	if(!empty($filtered))
		echo json_encode($filtered);
	else
		echo false;
}
if(isset($_GET['search_dropdown']) && $_GET['search_dropdown'] == 'stock_id'){
	$stock_types = ['sales', 'manufactured', 'purchasable', 'costable', 'component', 'kits', 'all'];

	$search_stock_id = kv_get_items_list((isset($_GET['q']) ? $_GET['q'] : ''), (isset($_GET['type']) && in_array(trim($_GET['type']), $stock_types) ? $_GET['type'] : 'sales'));
	$search_stock = [];
	while ($r = db_fetch($search_stock_id)) {
            $search_stock[] = ['id' => $r['item_code'], 'text' => $r['description']];
        }
	// $donut_chart['grandtotal'] = abs(round($suppliers['grandtotal'],2));
	 echo json_encode($search_stock); exit;
}
if(isset($_GET['search_dropdown']) && ($_GET['search_dropdown'] == 'gl_code' || $_GET['search_dropdown'] == 'code_id')){

	$search_stock_id = kv_get_account_list(isset($_GET['q'])?$_GET['q']:'');   
    $all_list = $categories = $final = [];
   
    $final[] = ['id' => 0, 'text' => _("All GL Accounts")];       
    
    while ($r = db_fetch($search_stock_id)) {
        $category = htmlspecialchars_decode($r['name']);
        if(!in_array($category, $categories)){
            $categories[] = $category;
        }
        $all_list[$category][] =  ['id' => $r['account_code'], 'text' => $r['account_code'] ." - ".htmlspecialchars_decode(nl2br($r['account_name']))];
    }
   
    if(!empty($categories)){
        foreach($categories as $keyy => $categoryy){
            $final[] = ['id' => $keyy, 'text' => $categoryy, 'children' => $all_list[$categoryy]];
        }
    }
    echo json_encode($final); exit;
}
if(isset($_GET['search_dropdown']) && $_GET['search_dropdown'] == 'customer_id'){

	$search_stock_id = kv_get_customers_list(isset($_GET['q']) ? $_GET['q'] : '');
	$search_stock = [];
	while ($r = db_fetch($search_stock_id)) {
            $search_stock[] = ['id' => $r['debtor_no'], 'text' => $r['name']];
        }
	// $donut_chart['grandtotal'] = abs(round($suppliers['grandtotal'],2));
	 echo json_encode($search_stock); exit;
}
if(isset($_GET['search_dropdown']) && $_GET['search_dropdown'] == 'supplier_id'){

	$search_stock_id = kv_get_suppliers_list(isset($_GET['q']) ? $_GET['q'] : '');
	$search_stock = [];
	while ($r = db_fetch($search_stock_id)) {
            $search_stock[] = ['id' => $r['supplier_id'], 'text' => $r['supp_name']];
        }
	// $donut_chart['grandtotal'] = abs(round($suppliers['grandtotal'],2));
	 echo json_encode($search_stock); exit;
}

function kv_get_items_list($description, $type='sales'){
    global $SysPrefs;

    $sql = "SELECT COUNT(i.item_code) AS kit, i.item_code, i.description, c.description category
		FROM " . TB_PREF . "stock_master s, " . TB_PREF . "item_codes i
			LEFT JOIN " . TB_PREF . "stock_category c ON i.category_id=c.category_id
		WHERE i.stock_id=s.stock_id
			AND !i.inactive AND !s.inactive " ;

	if($description != '' )	
		$sql .= " AND ( i.item_code LIKE " . db_escape("%" . $description . "%") . " OR 
				i.description LIKE " . db_escape("%" . $description . "%") . " OR 
				c.description LIKE " . db_escape("%" . $description . "%") . ") ";


    switch ($type) {
        case "sales":
            $sql .= " AND !s.no_sale AND mb_flag <> 'F'";
            break;
        case "manufactured":
            $sql .= " AND mb_flag = 'M'";
            break;
        case "purchasable":
            $sql .= " AND NOT no_purchase AND mb_flag <> 'F' AND i.item_code=i.stock_id";
            break;
        case "costable":
            $sql .= " AND mb_flag <> 'D' AND mb_flag <> 'F' AND  i.item_code=i.stock_id";
            break;
        case "component":
            $parent = $_GET['parent'];
            $sql .= " AND  i.item_code=i.stock_id AND i.stock_id <> '$parent' AND mb_flag <> 'F' ";
            break;
        case "kits":
            $sql .= " AND !i.is_foreign AND i.item_code!=i.stock_id AND mb_flag <> 'F'";
            break;
        case "all":
            $sql .= " AND mb_flag <> 'F' AND i.item_code=i.stock_id";
            break;
    }

    if (isset($SysPrefs->max_rows_in_search))
        $limit = $SysPrefs->max_rows_in_search;
    else
        $limit = 10;

    $sql .= " GROUP BY i.category_id,i.item_code ORDER BY i.category_id,i.description LIMIT 0," . (int)($limit);
    
    return db_query($sql, "Failed in retreiving item list.");
}

function kv_get_customers_list($customer){
    global $SysPrefs;

    if (isset($SysPrefs->max_rows_in_search))
        $limit = $SysPrefs->max_rows_in_search;
    else
        $limit = 10;

    $sql = "SELECT debtor_no, name FROM ".TB_PREF."debtors_master WHERE  1 ";
	  
	if($customer != '')
		$sql .= "  AND (  name LIKE " . db_escape("%" . $customer. "%") . " OR 
    		 debtor_ref LIKE " . db_escape("%" . $customer. "%") . " OR 
	        address LIKE " . db_escape("%" . $customer. "%") . " OR 
    	     tax_id LIKE " . db_escape("%" . $customer. "%").")";

	  $sql .=" ORDER BY name LIMIT 0,".(int)($limit);

    return db_query($sql, "Failed in retreiving customer list.");
}

function kv_get_suppliers_list($supplier){
    global $SysPrefs;

    if (isset($SysPrefs->max_rows_in_search))
        $limit = $SysPrefs->max_rows_in_search;
    else
        $limit = 10;

    $sql = "SELECT supplier_id , supp_name	FROM ".TB_PREF."suppliers	WHERE  1 ";
	if($supplier != '')
		$sql .= " AND (supp_name LIKE " . db_escape("%" . $supplier. "%") . " OR 
			supp_ref LIKE " . db_escape("%" . $supplier. "%") . " OR 
			address LIKE " . db_escape("%" . $supplier. "%") . " OR 
			gst_no LIKE " . db_escape("%" . $supplier. "%") . ")";

		$sql .=" ORDER BY supp_name LIMIT 0,".(int)($limit);

    return db_query($sql, "Failed in retreiving supplier list.");
}

function kv_get_account_list($account, $skip=false){
    global $SysPrefs;

    // $user_query_size =user_query_size();
    // if ($user_query_size > 0 )
    //     $limit = $user_query_size*3;
    // else
        $limit = 800;

    $sql = "SELECT chart.account_code, chart.account_name, type.name, chart.inactive, type.id
            FROM ".TB_PREF."chart_master chart,".TB_PREF."chart_types type
            WHERE chart.account_type=type.id ";

    if(strlen(trim($account)) ==0){
        $sql .=" ORDER BY account_code  LIMIT 0,".(int)($limit);
    }else{
        $sql .=" AND (chart.account_code LIKE " . db_escape("%" . $account. "%") . " OR 
            chart.account_name LIKE " . db_escape("%" . $account. "%") . ")
        ORDER BY account_code LIMIT 0,".(int)($limit);
    }       

    return db_query($sql, _("Failed in retreiving account list."), false);
}

?>