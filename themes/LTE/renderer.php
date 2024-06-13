<?php
/*-------------------------------------------------------+
|-----------------------------+*/
include_once("kvcodes.inc");
global $lte_options;
create_tbl_option();
$lte_options = LTEGetAll();

function addhttp($url) {
		    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
		        $url = "http://" . $url;
		    }
		    return $url;
		}
	class renderer{
		function get_icon($category){
			global  $path_to_root, $SysPrefs;

			if ($SysPrefs->show_menu_category_icons)
				$img = $category == '' ? 'right.gif' : $category.'.png';
			else	
				$img = 'right.gif';
			return "<img src='$path_to_root/themes/".user_theme()."/images/$img' style='vertical-align:middle;' border='0'>&nbsp;&nbsp;";
		}

		function wa_header(){
			if(isset($_GET['application']) && ($_GET['application'] == 'orders' || $_GET['application'] == 'orders#header'))
				page(_($help_context = "Sales"), false, true);
			elseif(isset($_GET['application']) && ($_GET['application'] == 'AP'|| $_GET['application'] == 'AP#header'))
				page(_($help_context = "Purchases"), false, true);
			elseif(isset($_GET['application']) && ($_GET['application'] == 'stock'|| $_GET['application'] == 'stock#header'))
				page(_($help_context = "Items & Inventory"), false, true);
			elseif(isset($_GET['application']) && ($_GET['application'] == 'manuf'|| $_GET['application'] == 'manuf#header'))
				page(_($help_context = "Manufacturing"), false, true);
			elseif(isset($_GET['application']) && ($_GET['application'] == 'proj'|| $_GET['application'] == 'proj#header'))
				page(_($help_context = "Dimensions"), false, true);
			elseif(isset($_GET['application']) && ($_GET['application'] == 'assets'|| $_GET['application'] == 'assets#header'))
				page(_($help_context = "Fixed Assets"), false, true);
			elseif(isset($_GET['application']) && ($_GET['application'] == 'GL'|| $_GET['application'] == 'GL#header'))
				page(_($help_context = "GL & Banking"), false, true);
			elseif(isset($_GET['application']) && ($_GET['application'] == 'extendedhrm'|| $_GET['application'] == 'extendedhrm#header'))
				page(_($help_context = "HRM and Payroll"), false, true);
			elseif(isset($_GET['application']) && ($_GET['application'] == 'system'|| $_GET['application'] == 'system#header'))
				page(_($help_context = "Setup Menu"), false, true);
			elseif(!isset($_GET['application']) || ($_GET['application'] == 'dashboard'|| $_GET['application'] == 'dashboard#header'))
				page(_("Dashboard"), false, true);
			else
				page(_($help_context = "Main Menu"), false, true);
		}

		function wa_footer(){
			end_page(false, true);
		}

		function menu_header($title, $no_menu, $is_index){
			global $path_to_root, $SysPrefs, $db_connections, $icon_root, $version, $lte_options,$installed_extensions, $fonts_list ;			
			
			require_once("ExtraSettings.php"); ?>
			<script> 
			(function() {
			    var link = document.querySelector("link[rel*='icon']") || document.createElement('link');
			    link.type = 'image/x-icon';
			    link.rel = 'shortcut icon';
			    <?php if(isset($lte_options['favicon']) && file_exists(dirname(__FILE__).'/images/'.$lte_options['favicon']) && !is_dir(dirname(__FILE__).'/images/'.$lte_options['favicon'])){
			    	echo " link.href = '$path_to_root/themes/".user_theme()."/images/".$lte_options['favicon']."?".rand(2,5)."'; ";
			    }else {
			    	echo "link.href = '$path_to_root/themes/".user_theme()."/images/favicon.ico?".rand(2,5)."';";
			    } ?>
			    
			    document.getElementsByTagName('head')[0].appendChild(link);
			}());
			</script> 		
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">

		  <link rel="stylesheet" href="<?php echo $path_to_root."/themes/".user_theme(); ?>/css/bootstrap.min.css">
		  <!-- Font Awesome -->
		  <link rel="stylesheet" href="<?php echo $path_to_root."/themes/".user_theme(); ?>/css/font-awesome.min.css">
		  <!-- Ionicons -->
		  <link rel="stylesheet" href="<?php echo $path_to_root."/themes/".user_theme(); ?>/css/ionicons.min.css">
		  <!--Bootstrap Selectpicker-->
		  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
		  <!-- jvectormap -->
		  <link rel="stylesheet" href="<?php echo $path_to_root."/themes/".user_theme(); ?>/css/jquery-jvectormap.css">
		  <!-- Theme style -->
		  <link rel="stylesheet" href="<?php echo $path_to_root."/themes/".user_theme(); ?>/css/AdminLTE.css">
		  <!-- AdminLTE Skins. Choose a skin from the css/skins
		       folder instead of downloading all of them to reduce the load. -->
		  <link rel="stylesheet" href="<?php echo $path_to_root."/themes/".user_theme(); ?>/css/_all-skins.min.css">

		  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		  <!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		  <![endif]-->

		  <!-- Google Font -->
    	  <!-- <link href="https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i,700|Roboto:300,300i,400,400i,500,500i,700&display=swap" rel="stylesheet"> -->
<?php
if(isset($lte_options['font']) && isset($fonts_list[$lte_options['font']]))
    	echo   '<link href="https://fonts.googleapis.com/css2?family='.$fonts_list[$lte_options['font']]['link'].'&display=swap" rel="stylesheet">
		<style> 
 html, body { font-family : '.$fonts_list[$lte_options['font']]['font-family'].';}
 </style>';

 echo '<script src="'.$path_to_root.'/themes/'.user_theme().'/js/jquery.min.js"></script>';
 
			$color_scheme = (isset($lte_options['color_scheme']) ? $lte_options['color_scheme'] : 'default'); 
				
			if($color_scheme == 'custom'){ 
				require_once('custom_color.php');
			}else 	
				echo '<link rel="stylesheet" href="'.$path_to_root.'/themes/LTE/css/AdminLTE.css">';
			
			$color_scheme = (isset($lte_options['color_scheme']) ? $lte_options['color_scheme'] : 'black'); 
			
			echo '<link rel="stylesheet" href="'.$path_to_root.'/themes/LTE/css/AdminLTE.css">';
			require_once("ExtraSettings.php"); 
      if ($no_menu)
        $background='noMenu';
      else
        $background = '';
			echo '<div class="wrapper '.$background.'">'; // tabs

			$indicator = "$path_to_root/themes/".user_theme(). "/images/ajax-loader.gif";
			if (!$no_menu)			{
				add_access_extensions();
				$applications = $_SESSION['App']->applications;
				$local_path_to_root = $path_to_root;
				$sel_app = $_SESSION['sel_app'];
				if(isset($lte_options['use_software_logo']) && $lte_options['use_software_logo'] == 1){
					if(isset($lte_options['logo']) && file_exists(dirname(__FILE__) .'/images/'.$lte_options['logo']) && !is_dir(dirname(__FILE__) .'/images/'.$lte_options['logo'])){
						$kv_logo = $path_to_root.'/themes/LTE/images/'.$lte_options['logo'];
					} else 
						$kv_logo = $path_to_root.'/themes/LTE/images/LTE.png';

					if(file_exists($path_to_root.'/themes/LTE/images/'.$kv_logo) && !is_dir($path_to_root.'/themes/LTE/images/'.$kv_logo)){
						$logo_img = $path_to_root.'/themes/LTE/images/'.$kv_logo.'?'.rand(2,5);
					}else
						$logo_img =$path_to_root.'/themes/LTE/images/LTE.png?'.rand(2,5);
				} else {
					$coy_logo = get_company_pref("coy_logo");

					if(file_exists(company_path() .'/images/'.$coy_logo) && !is_dir(company_path() .'/images/'.$coy_logo)){
						$logo_img = company_path().'/images/'.$coy_logo.'?'.rand(2,5);
					}else
						$logo_img = $path_to_root.'/themes/LTE/images/LTE.png?'.rand(2,5);
				}
				$role_sql = "SELECT role FROM ".TB_PREF."security_roles WHERE id=".$_SESSION['wa_current_user']->access." LIMIT 1";
				$role_res = db_query($role_sql, "Can't get current_user Role");
				if(db_num_rows($role_res) > 0 ) {
					if($row = db_fetch($role_res))
						$role_name = $row[0];
				} else
					$role_name  ='';
				echo ' <header style="" class="main-header"> <!-- Logo -->
    
    <a style="margin-top: 9px;
    height: 55px;" class="logo" href="'.$path_to_root.'"> <img src="'. $logo_img.'" style="max-width: 180px; max-height:50px;" ><!--'.$db_connections[user_company()]["name"].' --> </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">'._("Toggle navigation").'</span>
      </a>';
	if(lte_check_table(TB_PREF, 'quick_menu') == 0) {

	  $res = db_query("SELECT * FROM ".TB_PREF."quick_menu WHERE user_id = ".$_SESSION['wa_current_user']->user." OR appl_all = 0 GROUP BY menu_label ORDER BY sort_id", '', false) ; 

		if(db_num_rows($res) > 0 ) {
				echo '<div class="btn-group">
						<button type="button" class="btn btn-default"><i class="fa fa-plus"> </i></button>
						<button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
						<span class="sr-only"></span><i class="fa fa-angle-down"> </i>
						</button>
						<div class="dropdown-menu" role="menu" style="">';

				while($row_ = db_fetch($res)) 						
					echo '<a class="dropdown-item"  '.($row_['popup'] ? 'target = "_blank" onclick="javascript:openWindow(this.href,this.target); return false;"' : '' ).'  href="'.$path_to_root.'/'.htmlspecialchars_decode($row_['url_']).'"> '.$row_['menu_label'].'</a>';

				echo '<!--<div class="dropdown-divider"></div> -->  </div>
						</div>';
		}
	}

      echo ' <span class="pageTitle">    '.   _($title) .'     </span>';
      include_once("$path_to_root/themes/".user_theme()."/notification.php");
            $noti = new show_notification();

      echo '<!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->';
          $noti->get_overdue_invoices();
          
          echo '<!-- Notifications: style can be found in dropdown.less -->';
          $noti->get_inventory_reorder();
          
          echo '<!-- Tasks: style can be found in dropdown.less -->';
          $noti->get_supplier_payments();
          
          echo '<!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="'.$path_to_root.'/themes/LTE/images/profiledefault.png" class="user-image" alt="User Image">
     
              <span class="hidden-xs">'.$_SESSION['wa_current_user']->name.'</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
               <img src="'.$path_to_root.'/themes/LTE/images/profiledefault.png" class="img-circle" alt="User Image"> 
                <p>
                  <a href="'.$path_to_root.'/admin/display_prefs.php?" >'.$_SESSION['wa_current_user']->name.'</a> <br><small>'.$role_name .'
                  <br> Last Seen -'.gmdate("Y-m-d H:i:s", $_SESSION['wa_current_user']->last_act).'</small>
                </p>
              </li>
          
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="'. $path_to_root.'/admin/display_prefs.php?" class="btn btn-default btn-flat">'._("Profile").'</a>
                </div>
                <div class="pull-right">
                  <a href="'. $path_to_root.'/themes/LTE/logout.php?" class="btn btn-default btn-flat">'._("Logout").'</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button --> ';

          $MasterRole = (isset($lte_options['theme']) ? $lte_options['theme'] : '' );

          if(!$MasterRole || in_array(770, $_SESSION["wa_current_user"]->role_set)){
            echo '<li><a href="'.$path_to_root.'/themes/LTE/theme-options.php" ><i class="fa fa-gears"></i></a> </li>';
          } 

          if ($SysPrefs->help_base_url != null && isset($lte_options['hide_help_link']) && $lte_options['hide_help_link']== 0 ){
             if(isset($_GET['application']) && $_GET['application'] == 'dashboard'){

			 } else 
            	echo  '<li><a href="'. help_url().'"  onclick="javascript:openWindow(this.href,this.target); return false;" ><i class="fa fa-life-ring"></i> </a></li>
                 ';
          }
            
          echo '  </ul>    </div>    </nav>
  </header>

  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="'.$path_to_root.'/themes/LTE/images/profiledefault.png" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>'.$db_connections[user_company()]["name"].'</p> 
                <a href="'. $path_to_root.'/admin/display_prefs.php?"><i class="fa fa-circle text-success"></i> '.$_SESSION['wa_current_user']->name.'</a>
            </div>
        </div>';
		if(isset($lte_options['enable_master_login']) && $lte_options['enable_master_login'] == 1) {
       		include_once($path_to_root."/themes/LTE/includes/users.php");
			$row = get_master_login($_SESSION['wa_current_user']->loginname);

			$companies = (isset($row['companies']) ? unserialize(base64_decode($row['companies'])) : '');
			$local_path_to_root = $path_to_root; 
			if(!empty($companies)){
				if(!in_array($_SESSION["wa_current_user"]->company, $companies)){
					$_SESSION['wa_current_user']->company = $companies[0];
					header("location:index.php");
				}
			}
        }
       echo '<div class="user-panel user-panel-dropdown">';
      if(isset($lte_options['enable_master_login']) && $lte_options['enable_master_login'] == 1) {
       echo '<select name="CompanyName" class="ChangeCompany" data-live-search="true">';
		echo '<option value="'.$_SESSION['wa_current_user']->company.'" selected> '.$db_connections[$_SESSION['wa_current_user']->company]['name'].'</option>';
		foreach($db_connections as $cid => $single){
			if(empty($companies) || (isset($companies) && in_array($cid, $companies) && $cid != $_SESSION['wa_current_user']->company))
				echo '<option value="'.$cid.'">' . $db_connections[$cid]["name"] .'</option>';
		}
        echo ' </select>';
    }
    echo '</div>
      
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">';
        if(isset($lte_options['hide_dashboard']) && $lte_options['hide_dashboard'] == 0){
			echo '<li class="'.((isset($_GET['application']) && $_GET['application']=='dashboard') ? 'active' : '').'" >  <a href="'.$path_to_root.'?application=dashboard" accesskey="D"> <i class="fa fa-dashboard"></i> <span style="margin-right: 7px;"> '._("لوحة القيادة").' </span></a> </li>';
		}
		if(is_array($installed_extensions) && !empty($installed_extensions) && count($installed_extensions) > 0) {
			foreach($installed_extensions as $ext){
				if($ext['package'] == 'Subscription'){
					echo "<li class='' >  <a  href='$local_path_to_root/modules/Subscription/inquiry/companies_subscription.php' >  <i class='fa fa-dashboard'></i> <span> "._("Subscription")." </span> </a> </li>";
				}
			}
		}

		foreach($applications as $app){
                    if ($_SESSION["wa_current_user"]->check_application_access($app))  {
                    	if(trim($app->id) == 'orders')
								$icon_root = 'tags';
							elseif(trim($app->id) == 'AP')
								$icon_root = 'shopping-cart';
							elseif(trim($app->id) == 'stock')
								$icon_root = 'cubes';
							elseif(trim($app->id) == 'manuf')
								$icon_root = 'industry';
							elseif(trim($app->id) == 'assets')
								$icon_root = 'home';
							elseif(trim($app->id) == 'proj')
								$icon_root = 'binoculars';
							elseif(trim($app->id) == 'GL')
								$icon_root = 'book';
							elseif(trim($app->id) == 'system')
								$icon_root = 'cogs';
							else
								$icon_root ='dashboard';

                        $acc = access_string($app->name);

						if($app->id == 'taxes') {
							echo '<li class="active" >  <a href="'.$path_to_root.'/taxes/vat_inquiry_sa.php" accesskey="t"> <i class="fa fa-dashboard"></i> <span> '._("VAT Inquiry").' </span></a> </li>';
							continue; 
						}
						if($app->id == 'reports') {
							echo '<li class="active" >  <a href="'.$path_to_root.'/reporting/reports_main.php?Class=6" accesskey="t"> <i class="fa fa-dashboard"></i> <span> '._("Reports").' </span></a> </li>';
							continue; 
						}
						// if ($_SESSION["wa_current_user"]->can_access_page($app->id))  {
						echo "<li class='treeview ' ><a class='".($sel_app == $app->id ? 'active' : '')
                            ."' href='$local_path_to_root/index.php?application=".$app->id
                            ."'$acc[1]> <i class='fa fa-".$icon_root."'></i> <span style=''>" .($app->id == 'GL' ? 'البنوك والاستاذ العام' : $acc[0] ). " </span>
                            	 </a> 

                            <ul class='treeview-menu andHere'>";
							echo "<div class='closeIcon' onclick='closeIcon()'>x</div>";
                            $kv_module_icon = array('building-o', 'circle-o', 'wrench', 'tag', 'cog' ); 
							$kv_small = 0 ; 
							
							foreach ($app->modules as $module)   {
								if (isset($module->name)) {// If parent
									echo "<li class='treeview '>
									<a href='#'><span class='pull-right-container'> </span><i class='fa fa-".$kv_module_icon[$kv_small]."'></i>  " . _($module->name) . "
									</a>";
									echo "	<ul class='treeview-menu'>";
								
								$lapps = $rapps = array();
								foreach ($module->lappfunctions as $lappfunction)
									$lapps[] = $lappfunction;
								foreach ($module->rappfunctions as $rappfunction)
									$rapps[] = $rappfunction;
								$lapplication = $rapplication = array();
								echo ' <li> <ul>' ; 
								foreach ($lapps as $lapplication)    {
									$lnk = access_string($lapplication->label);
									if ($_SESSION["wa_current_user"]->can_access_page($lapplication->access)) {
										if ($lapplication->label != "") {
											echo  "<li class='siblingMenus'><a href='".$path_to_root."/".$lapplication->link."' $lnk[1]><i class='fa fa-circle-o' ></i>   "._($lnk[0])."</a></li> \n";
										}
									}elseif (!$_SESSION["wa_current_user"]->hide_inaccessible_menu_items()){}
										//echo "<a href='#' class='disabled'>".$lnk[0]."</a>";																				
								}
								echo '</ul></li>  <li> <ul>' ; 
								foreach ($rapps as $rapplication)    {
									$lnk = access_string($rapplication->label);
									if ($_SESSION["wa_current_user"]->can_access_page($rapplication->access)) {
										if ($rapplication->label != "") {
											echo  "<li class='siblingMenus'><a href='".$path_to_root."/".$rapplication->link."'$lnk[1]><i class='fa fa-circle-o' ></i>   "._($lnk[0])."</a></li> \n";
										}
									}
									elseif (!$_SESSION["wa_current_user"]->hide_inaccessible_menu_items()){}										
								}
								echo '</ul></li> '; 
								}
								
								if (isset($module->name)) { // If parent
									echo "</ul>"; 
									echo "</li>";
								}	
								echo '<div style="clear:both"> </div> ' ;
								$kv_small++;
							}
							echo "</ul> </li>";  
						}
            }
            if(isset($lte_options['enable_master_login']) && $lte_options['enable_master_login'] == 1) 
       			echo ($_SESSION['wa_current_user']->company == 0 ? '<li><a href="'.$path_to_root.'/themes/LTE/master-login.php"> Master Login </a></li>' : '');

       		 echo ' </ul> 
    </section>
    <!-- /.sidebar -->
  </aside>';	
}

       echo '     <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper '.($no_menu ? 'noMenu' : '' ).'">
          <!-- Content Header (Page header) -->
          <section class="content-header"> ';

          //    if ($SysPrefs->help_base_url != null && $lte_options['hide_help_link']== 0 ){
          //     echo  '<ol class="breadcrumb">
          //         <li><a href="'. help_url().'"  onclick="javascript:openWindow(this.href,this.target); return false;" ><i class="fa fa-life-ring"></i> </a></li>
          //        </ol>' ;
          // }
          echo '</section>';    

			
			if ($no_menu){	// ajax indicator for installer and popups
				echo "<center><table class='tablestyle_noborder'>"
					."<tr><td><img id='ajaxmark' src='$indicator' align='center' style='visibility:hidden;' alt='ajaxmark'></td></tr>"
					."</table></center> ";
					echo '<div class="content">';
			} elseif ($title && !$is_index)	{
				/*echo "<center><table id='title'><tr><td width='100%' class='titletext'>$title</td>"
				."<td align=right>"
				.(user_hints() ? "<span id='hints'></span>" : '')
				."</td>"
				."</tr></table></center>";	*/			
			}
			
			echo "<img id='ajaxmark' src='$indicator' align='center' style='visibility:hidden;' alt='ajaxmark'>";
		}

		function menu_footer($no_menu, $is_index){
			global $version, $path_to_root, $Pagehelp, $Ajax, $SysPrefs, $lte_options;

			include_once($path_to_root . "/includes/date_functions.inc");			
			$app_title = (isset( $lte_options['powered_name']) ? $lte_options['powered_name'] : 'FrontAccounting') ;
			
			if(isset($lte_options['powered_url']) && $lte_options['powered_url'] == 1){
				$powered_url = addhttp($lte_options['powered_url']);
			}else 
				$powered_url = 'http://frontaccounting.com';			
			
			echo '</div></div>';
			if(isset($_GET['application']) && $_GET['application'] == 'stock')
					echo '</div>';
			echo '<footer class="main-footer '.($no_menu ? 'noMenu' : '' ).'"> ';
      if(isset($lte_options['hide_version']) && $lte_options['hide_version']== 0 ){		echo '<div class="pull-right hidden-xs"> <b>Version</b> '. $version.'</div>';		}
      echo '<strong>'._("Copyright").' &copy; '.date('Y').' <a href="'.$powered_url.'" target="_blank"></a>  '._("All rights  reserved.").'  </footer>';
    //} 
echo '</div>'; ?>
<!-- jQuery 3 -->
<script src="<?php echo $path_to_root."/themes/".user_theme(); ?>/js/jquery.min.js"></script>
<?php 

$color_scheme = (isset($lte_options['color_scheme']) ? $lte_options['color_scheme'] : 'black'); ?>


<!-- Bootstrap 3.3.7 -->
<script src="<?php echo $path_to_root."/themes/".user_theme(); ?>/js/bootstrap.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
<!-- FastClick -->
<script src="<?php echo $path_to_root."/themes/".user_theme(); ?>/js/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo $path_to_root."/themes/".user_theme(); ?>/js/adminlte.min.js"></script>
<!-- Sparkline -->
<script src="<?php echo $path_to_root."/themes/".user_theme(); ?>/js/jquery.sparkline.min.js"></script>
<!-- jvectormap  -->
<script src="<?php echo $path_to_root."/themes/".user_theme(); ?>/js/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?php echo $path_to_root."/themes/".user_theme(); ?>/js/jquery-jvectormap-world-mill-en.js"></script>
<!-- SlimScroll -->
<script src="<?php echo $path_to_root."/themes/".user_theme(); ?>/js/jquery.slimscroll.min.js"></script>
<!-- ChartJS -->
<script src="<?php echo $path_to_root."/themes/".user_theme(); ?>/js/Chart.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo $path_to_root."/themes/".user_theme(); ?>/js/dashboard2.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo $path_to_root."/themes/".user_theme(); ?>/js/demo.js"></script>
<script>
	function closeIcon() {
        var sidebarMenu = document.getElementsByClassName('treeview-menu');
        for (var i = 0; i < sidebarMenu.length; i++) {
            sidebarMenu[i].style.setProperty('display', 'none', 'important');
        }
    }

</script>
<script  type="text/javascript"> 
	$('body').addClass('skin-<?php echo $color_scheme; ?> sidebar-mini');
</script>
<script  type="text/javascript"> 
		//<![CDATA[
		function myFunction() {
			  var x = document.getElementById("CompanyList");
			  if (x.className.indexOf("w3-show") == -1) {
			    x.className += " w3-show";
			  } else { 
			    x.className = x.className.replace(" w3-show", "");
			  }
			}
				$(".ChangeCompany").on('change', function (e) {  			   		
			   		var Cid = $(this).val();			   		
			   		$.ajax({
				        type: "POST",
				        url: "<?php echo $path_to_root; ?>/themes/LTE/includes/ajax.php?ChangeCompany="+Cid,
				        data: 0,
				        success: function(data){        
				          if(data.trim()){
				          	window.location.reload();				           
				          }else{
				            alert("Failed to Delete". data);
				          }               
				        }
				    });
			   	});

			   // $('#ChangeCompany').select2({     // var itemName = $('select[name="itemName"]').val();
			   //      placeholder: "<?php echo _('Select a Company'); ?>",
			   //      allowClear: true,
			   //      ajax: {
			   //        url: "<?php //echo $path_to_root; ?>/uploads/includes/ajax.php?GetCompany=yes",
			   //        dataType: 'json',
			   //        delay: 250,
			   //        processResults: function (data) {            
			   //          return {
			   //            results: data
			   //          };           
			   //        },
			   //      }
			   //  });
		// 	});
		// });
		//]]> 
</script>

<?php if(get_company_pref('no_item_list') == 1 || get_company_pref('no_customer_list') ==1 || get_company_pref('no_supplier_list') == 1) { ?>
			<link rel="stylesheet" href="<?php echo $path_to_root.'/themes/LTE/css/select2.min.css'; ?>">
			
			<script src="<?php echo $path_to_root.'/themes/LTE/js/select2.full.min.js' ; ?>"></script>
			<script type="text/javascript">
				$(function() {
						<?php if(get_company_pref('no_item_list') == 1) {?>
						call_kvcodes_select2('stock_id');
						call_kvcodes_select2('gl_code');
						call_kvcodes_select2('code_id');
					<?php } if(get_company_pref('no_customer_list')) {?>
						call_kvcodes_select2('customer_id');
					<?php } if(get_company_pref('no_supplier_list')) {?>
						call_kvcodes_select2('supplier_id');
					<?php } ?>
					
				});
			</script>
		<?php } ?>
		<script type="text/javascript">
			
		function kvcodes_theme_refresh_select2(){
			<?php if(get_company_pref('no_item_list') == 1) {?>
						call_kvcodes_select2('stock_id');
						call_kvcodes_select2('gl_code');
						call_kvcodes_select2('code_id');
			<?php } if(get_company_pref('no_customer_list')) {?>
						call_kvcodes_select2('customer_id');
			<?php } if(get_company_pref('no_supplier_list')) {?>
						call_kvcodes_select2('supplier_id');
			<?php } ?>
			//call_kvcodes_select2('branch_id');
		}

		function call_kvcodes_select2(elementId){
			if($("#"+elementId).length > 0 ){
				if(elementId == 'stock_id' || elementId == 'gl_code' || elementId == 'code_id'){
					var type_string = $('#'+elementId).parent().next().next('img').attr('onclick');
					var index_pos = type_string.indexOf('type');
					type_string = type_string.substr(index_pos);
					index_pos = type_string.indexOf('&')-5;
					type_string = type_string.substr(5, index_pos);
				} else {
					var type_string = '';
				}
				//alert(type_string);
				$("#"+elementId).select2({
					allowClear :  true, 
					ajax : {
						url : "<?php echo $path_to_root.'/themes/LTE/includes/ajax.php?search_dropdown='; ?>"+elementId+"&type="+type_string,
						dataType: 'json',
						delay : 250,
						processResults : function(data){
							return { results : data };
						},
						cache : true
					}
				});
			}
		}
		</script>
<?php 
		}

		function display_applications(&$waapp)	{
			global $path_to_root;

			$selected_app = $waapp->get_selected_application();
			if (!$_SESSION["wa_current_user"]->check_application_access($selected_app))
				return;

			if (method_exists($selected_app, 'render_index'))	{
				$selected_app->render_index();
				return;
			}

			if( !isset($_GET['application']) || $_GET['application'] == 'dashboard'){	
				require("dashboard.php");
			}else{

				echo '<div class="MenuPage"> ';
				foreach ($selected_app->modules as $module)	{
	        		if (!$_SESSION["wa_current_user"]->check_module_access($module))
	        			continue;
					// image
					echo '<div class="MenuPart"><div class="subHeaders"> '.$module->name.'</div>';
					echo '<ul class="left">';

					foreach ($module->lappfunctions as $appfunction){
						$img = $this->get_icon($appfunction->category);
						if ($appfunction->label == "")
							echo "&nbsp;<br>";
						elseif ($_SESSION["wa_current_user"]->can_access_page($appfunction->access)) {
							echo '<li>'.$img.menu_link($appfunction->link, $appfunction->label)."</li>";
						}
						//elseif (!$_SESSION["wa_current_user"]->hide_inaccessible_menu_items())	{
							//echo '<li>'.$img.'<span class="inactive">'.access_string($appfunction->label, true)."</span></li>";
						//}
					}
					echo "</ul>";
					if (sizeof($module->rappfunctions) > 0)	{
						echo "<ul class='right'>";
						foreach ($module->rappfunctions as $appfunction){
							$img = $this->get_icon($appfunction->category);
							if ($appfunction->label == "")
								echo "&nbsp;<br>";
							elseif ($_SESSION["wa_current_user"]->can_access_page($appfunction->access)) {
								echo '<li>'.$img.menu_link($appfunction->link, $appfunction->label)."</li>";
							}
							//elseif (!$_SESSION["wa_current_user"]->hide_inaccessible_menu_items())	{
								//echo '<li>'.$img.'<span class="inactive">'.access_string($appfunction->label, true)."</span></li>";
							//}
						}
						echo "</ul>";
					}
					echo "<div style='clear: both;'></div>";
				}
				echo "</div></div> </div> </div>";
			}			
  		}
	}

	function lte_check_table($pref, $table, $field=null, $properties=null)
{
	$tables = @db_query("SHOW TABLES LIKE '".$pref.$table."'");
	if (!db_num_rows($tables))
		return 1;		// no such table or error

	$fields = @db_query("SHOW COLUMNS FROM ".$pref.$table);
	if (!isset($field)) 
		return 0;		// table exists

	while( $row = db_fetch_assoc($fields)) 
	{
		if ($row['Field'] == $field) 
		{
			if (!isset($properties)) 
				return 0;
			foreach($properties as $property => $value) 
			{
				if ($row[$property] != $value) 
					return 3;	// failed type/length check
			}
			return 0; // property check ok.
		}
	}
	return 2; // field not found
}