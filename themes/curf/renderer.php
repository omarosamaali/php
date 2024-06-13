<?php
/**********************************************************************
   Theme:Curf 
   Developer:Dinesh

***********************************************************************/

include_once($path_to_root . "/themes/curf/theme.inc");
global $theme_options;
if(isset($_SESSION['wa_current_user']) || $_SESSION['wa_current_user']->logged_in())
	$theme_options = theme_get_all_theme_options();
else
	$theme_option =array();
	class renderer
	{
		function wa_get_apps($title, $applications, $sel_app)
		{
			foreach($applications as $app)
			{
				foreach ($app->modules as $module)
				{
					$apps = array();
					foreach ($module->lappfunctions as $appfunction)
						$apps[] = $appfunction;
					foreach ($module->rappfunctions as $appfunction)
						$apps[] = $appfunction;
					$application = array();	
					foreach ($apps as $application)	
					{
						$url = explode('?', $application->link);
						$app_lnk = $url[0];					
						$pos = strrpos($app_lnk, "/");
						if ($pos > 0)
						{
							$app_lnk = substr($app_lnk, $pos + 1);
							$lnk = $_SERVER['REQUEST_URI'];
							$url = explode('?', $lnk);
							$asset = false;
							if (isset($url[1]))
								$asset = strstr($url[1], "FixedAsset");
							$lnk = $url[0];					
							$pos = strrpos($lnk, "/");
							$lnk = substr($lnk, $pos + 1);
							if ($app_lnk == $lnk)  
							{
								$acc = access_string($app->name);
								$app_id = ($asset != false ? "assets" : $app->id);
								return array($acc[0], $module->name, $application->label, $app_id);
							}	
						}	
					}
				}
			}
			return array("", "", "", $sel_app);
		}
		
		function wa_header()
		{
			page(_($help_context = "Main Menu"), false, true);
		}

		function wa_footer()
		{
			end_page(false, true);
		}
		function shortcut($url, $label) 
		{
			echo "<li>";
			echo menu_link($url, $label);
			echo "</li>";
		}

		function apps_icon($app){
			if($app =='orders'){
				$icon ='<i class="fa fa-chart-line"></i>';
			}elseif($app =='AP'){
				$icon ='<i class="fa fa-shopping-cart"></i>';
			}elseif($app =='stock'){
				$icon ='<i class="fa fa-box-open"></i>';		
			}elseif($app =='manuf'){
				$icon ='<i class="fa fa-industry"></i>';
			}elseif($app =='assets'){
				$icon ='<i class="fa fa-hand-holding-usd"></i>';
			}elseif($app =='proj'){
				$icon ='<i class="fa fa-ruler-combined"></i>';
			}elseif($app =='GL'){
				$icon ='<i class="fa fa-university" ></i>';
				
			}elseif($app =='system'){
				$icon ='<i class="fa fa-cogs"></i>';
				
			}else{
				$icon ='<i class="fa fa-plug"></i>';
			}
			return $icon;
		}

		function menu_header($title, $no_menu, $is_index)
		{
			global $path_to_root, $SysPrefs, $version,$theme_options;

			$version =1.0;
			echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
			if(isset($theme_options['font_api_link']) && $theme_options['font_api_link'])
				echo htmlspecialchars_decode($theme_options['font_api_link']);
			else
				echo "<link href='".$path_to_root ."/themes/curf/assets/css/default-font.css' rel='stylesheet' type='text/css'> \n";

			echo "<link href='".$path_to_root ."/themes/curf/assets/bootstrap/css/bootstrap.min.css' rel='stylesheet' type='text/css'> \n";
			echo "<link href='".$path_to_root ."/themes/curf/assets/fontawesome/css/all.min.css' rel='stylesheet' type='text/css'> \n";
			echo "<link href='".$path_to_root ."/themes/curf/assets/css/style.css?ver=1.0' rel='stylesheet' type='text/css'> \n";
			echo "<link href='".$path_to_root ."/themes/curf/assets/fontawesome/css/all.min.css' rel='stylesheet' type='text/css'> \n";
		    echo "<link href='".$path_to_root ."/themes/curf/assets/css/select2.min.css' rel='stylesheet' type='text/css'> \n";


			echo '<script type="text/javascript" src="'.$path_to_root.'/themes/curf/assets/js/jquery.min.js"></script>';
			echo '<script type="text/javascript" src="'.$path_to_root.'/themes/curf/assets/js/popper.min.js"></script>';
			echo '<script type="text/javascript" src="'.$path_to_root.'/themes/curf/assets/bootstrap/js/bootstrap.min.js"></script>';
			echo '<script src="'.$path_to_root.'/themes/curf/assets/js/Chart.min.js"></script>';
			// echo '<script type="text/javascript" src="'.$path_to_root.'/themes/curf/assets/js/popper.min.js"></script>';

			echo '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">';
			echo '<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>';
			echo '<script type="text/javascript" src="'.$path_to_root.'/themes/curf/assets/js/jquery.nicescroll.min.js"></script>';
			echo '<script type="text/javascript" src="'.$path_to_root.'/themes/curf/assets/js/select2.min.js"></script>';

			echo '<script type="text/javascript" src="'.$path_to_root.'/themes/curf/assets/js/script.js?ver=1.1"></script>';


			if(isset($theme_options['pusher_app_id']) && $theme_options['pusher_app_id'] && isset($theme_options['pusher_app_id']) && $theme_options['pusher_app_id'] && isset($theme_options['pusher_app_id']) && $theme_options['pusher_app_id']){
				echo "<link href='".$path_to_root ."/themes/curf/assets/css/toastr.min.css' rel='stylesheet' type='text/css'> \n";
				echo '<script type="text/javascript" src="'.$path_to_root.'/themes/curf/assets/js/toastr.min.js"></script>';

				if(is_module_active('CurfPusher')){

					echo '<script src="https://js.pusher.com/7.0/pusher.min.js"></script>';
					echo '<script>
						toastr.options.positionClass = "toast-'.$theme_options['pusher_toastr_position'].'";
						toastr.options.progressBar =true;
					    Pusher.logToConsole = true;

					    var pusher = new Pusher("10d469107e9917fd24a3", {
					      cluster: "ap2"
					    });

					    var channel = pusher.subscribe("curfPusher-channel");
					    channel.bind("curfPusher-event", function(data) {
					      toastr.success(data.message);
					       $("#pushNotifications").addClass("beep");
					    });
					  </script>';
				}
			}

			


			include_once($path_to_root . "/themes/curf/colors.inc");
			$sel_app = $_SESSION['sel_app'];
			
			if(isset($_SESSION['wa_current_user']->sidebar) && $_SESSION['wa_current_user']->sidebar ==false)
				echo "<div id='wrapper' class='fa-main sidebar-close'>\n";
			elseif(!$no_menu)
				echo "<div id='wrapper' class='fa-main'>\n";
			else
				echo "<div id='wrapper' class='fa-main sidebar-close'>\n";
			if(!isset($_SESSION['wa_current_user']->sidebar) || $_SESSION['wa_current_user']->sidebar ==true){
				echo '<script>
						$(document).ready(function(){
					  if ($(window).width() < 768) {
				       $("#sidebarClose").trigger("click");
				    }
			    });
					</script>';
			}

			echo '<div id="ajaxmark" style="visibility:hidden;">
				<div class="cssload-loadingwrap">
					<ul class="cssload-bokeh">
						<li></li>
						<li></li>
						<li></li>
						<li></li>
					</ul>
				</div>
			</div>';
			if (!$no_menu)
			{
				$applications = $_SESSION['App']->applications;
				$local_path_to_root = $path_to_root;
				
				$i = 0;
				$account = $this->wa_get_apps($title, $applications, $sel_app);
				$apps_html ='';
				$menus_html ='';
				foreach($applications as $app)
				{
          if ($_SESSION["wa_current_user"]->check_application_access($app))
          {
						$acc = access_string($app->name);
						$class = ($account[3] == $app->id ? "active" : "");
						$n = count($app->modules);
						
						$dashboard = "";	
    				$link = "$local_path_to_root/themes/curf/dashboard.php?sel_app=$app->id";

    				$icon =$this->apps_icon($app->id);

    				$apps_html .='<a data-app="'.$app->id.'" id="app'.$app->id.'" class="app-link nav-link '.$class.'" data-toggle="tooltip" title="'.access_string($app->name,true).'" data-placement="top" href="'.$link.'">'.$icon.'</i></a>';
					}

					$display =($class ? "show" : "");

					$menus_html .='<ul class="list-unstyled page-menu '.$display.'" id="menu'.$app->id.'">';
					$menus_html .='<li class="header-menu text-muted my-2">
					            <span>'.access_string($app->name,true).'</span>
					        </li>';

			    if(strpos($_SERVER['REQUEST_URI'], "/themes/curf/dashboard.php?sel_app=$app->id") !==false){
						$class ='active';
					}else{
						$class ='';
					}
					$menus_html .='<li class="menu '.$class.'">
		                  <a href="'.$link.'" class="text-decoration-none p-3 align-self-stretch d-flex" ><i class="fas fa-chart-pie pr-3 my-auto"></i>'._("Dashboard").'
		                  </a>
		                </li>';
					foreach ($app->modules as $module)
					{
    					if (!$_SESSION["wa_current_user"]->check_module_access($module))
    						continue;
    					
    					$menus_html .='<li class="header-menu text-muted my-2">
					            <span>'.$module->name.'</span>
					        </li>';
							$apps2 = array();
						foreach ($module->lappfunctions as $appfunction)
							$apps2[] = $appfunction;
						foreach ($module->rappfunctions as $appfunction)
							$apps2[] = $appfunction;
						$application = array();	
   						$n = count($apps2);
 
						if (!$n)
							continue;	
						foreach ($apps2 as $application)	
						{
							$lnk = access_string($application->label);
							if ($_SESSION["wa_current_user"]->can_access_page($application->access))
							{
								if ($application->label != "")
								{
									if(strpos($_SERVER['REQUEST_URI'], $application->link) !==false){
										$class ='active';
									}else{
										$class ='';
									}
									$menus_html .='<li class="menu '.$class.'">
					                  <a href="'.$path_to_root.'/'.$application->link.'" class="  text-decoration-none align-self-stretch d-flex p-3" ><i class="fas fa-scroll pr-3 my-auto"></i><span>'.$lnk[0].'</span>
					                  </a>
					                </li>';
								}
							}
						}
						
					}
					$menus_html .='</ul>';
					$i++;
				}

				echo '<div id="sidebar" class="">
				<div class="sidebar-primary">
				<div class="sidebar-header d-flex justify-content-center">
					<div class="my-auto">
				        <a class="nav-link p-0" href="'.$path_to_root.'/admin/company_preferences.php?"><img width="100%" height="100%" src="'.$path_to_root.'/themes/curf/images/favicon.ico" /></a>
				    </div>
				</div>';
				echo '<div class="sidebar-apps nicescroll-box d-flex justify-content-center">
					<div class="my-auto">';
				echo $apps_html;
				echo '</div>
				</div>
					<div class="sidebar-footer d-flex justify-content-center">
						<div class="my-auto">
					        <a class="nav-link text-danger" data-toggle="tooltip" title="Log Out" data-placement="top" href="'.$path_to_root.'/access/logout.php?"><i class="fas fa-power-off"></i></a>
					    </div>
					</div>
				</div>';

				echo '<div class="sidebar-secondary">
				<button class="navbar-toggler d-sm-block d-md-none mr-2" id="sidebarClose">
			      <i class="fas fa-times"></i>
			    </button>
				<div class="sidebar-secondary-header d-flex justify-content-center">
					<div class="my-auto">';
					if(file_exists(company_path().'/images/'.get_company_pref('coy_logo'))){
				        echo '<a class="navbar-brand font-weight-bold px-1" href="'.$path_to_root.'/admin/company_preferences.php?"><img class="img-fluid" alt="'.get_company_pref('coy_name').'" src="'.company_path().'/images/'.get_company_pref('coy_logo').'"/></a>';
					}else{
						echo '<a class="navbar-brand font-weight-bold" href="'.$path_to_root.'/admin/company_preferences.php?">'.get_company_pref('coy_name').'</a>';
					}
				    echo'</div>
				</div>
				<div class="sidebar-secondary-menus nicescroll-box">
					<div class="p-1">
				        ';
				echo '<input class="form-control sticky-top" id="searchSecondaryMenu" type="text" placeholder="Search..">';
				echo $menus_html;
				echo '</div>
						</div>
						<div class="sidebar-secondary-footer d-flex justify-content-center">
						<div class="mx-auto my-auto">';
						if ($SysPrefs->help_base_url != null)
						{
							echo '<a target = "_blank" onclick="javascript:openWindow(this.href,this.target); return false;" class="nav-link d-inline-block" data-toggle="tooltip" title='. _("Help") .' data-placement="top" href="'.help_url().'"><i class="fas fa-lightbulb"></i></a>';
						}
					        
					    echo'<a class="nav-link d-inline-block" data-toggle="tooltip" title="'._("Theme").'" data-placement="top" href="'.$path_to_root.'/themes/curf/options.php"><i class="fas fa-paint-roller"></i></a>';

					        // <a class="nav-link d-inline-block" data-toggle="tooltip" title="Hooray!" data-placement="top" href="#"><i class="fas fa-store"></i></a>
					        // <a class="nav-link d-inline-block" data-toggle="tooltip" title="Hooray!" data-placement="top" href="#"><i class="fas fa-store"></i></a>
					    echo'</div>
					</div>
				</div>
			</div>';

			}
			echo "<div id='mainPage'>\n";

			if(!$no_menu){
				echo '<nav class="navbar sticky-top mx-md-2 ">
			    <button class="navbar-toggler" id="sidebarToggler" data-url="'.$path_to_root.'/themes/curf/ajax.php?sidebar=1">
			      <i class="fas fa-bars"></i>
			    </button>
			    <ul class="navbar-nav"><li class="nav-item d-inline-block navbar-toggler font-weight-bold">'.$title.'</li></ul>

			   <ul class="navbar-nav navbar-nav-right  d-inline-block">';

	            echo '<li class="nav-item d-inline-block navbar-toggler">
	                <a class="nav-link d-flex" href="#"><i class="fas fa-user pr-3"></i><span class="d-none d-md-block">'.$_SESSION['wa_current_user']->name.'</a>
	            </li>
	        </ul>
			  </nav>';
			}
			if ($no_menu)
				echo "<br>";
			elseif ($title && !$no_menu && !$is_index)
			{
				echo "<div id='pageBody' class=''>\n";
			}

			
					
		}

		function menu_footer($no_menu, $is_index)
		{
			global $path_to_root, $SysPrefs, $version, $db_connections,$theme_options,$Ajax;
			include_once($path_to_root . "/includes/date_functions.inc");

			if (!$no_menu && !$is_index)
				echo "</div>\n"; // fa-content
			
			if(in_ajax()){
				$Ajax->addScript(true,'$(document).ready(function () { $("select").select2(); });');
			}
			
			if (!$no_menu)
			{
   				if(!isset($theme_options['footer_hide']) || $theme_options['footer_hide'] ==0){
					echo "<div class='fa-footer'>\n";
					if (isset($_SESSION['wa_current_user']))
					{
						if(isset($theme_options['site_name']) && $theme_options['site_name']){
							$site_name =$theme_options['site_name'];
						}else{
							$site_name =$SysPrefs->power_by;
						}
						if(isset($theme_options['site_url']) && $theme_options['site_url']){
							$site_url =$theme_options['site_url'];
						}else{
							$site_url =$SysPrefs->power_url;
						}

						if(!isset($theme_options['footer_hide_version']) || $theme_options['footer_hide_version'] ==0)
							$appversion =$version;
						else
							$appversion ='';
						echo "<span class=''><a target='_blank' href='".$site_url."'>".$site_name." $appversion</a></span>\n";
						if(!isset($theme_options['footer_date_time']) || $theme_options['footer_date_time'] ==0)
							echo "<span class='date'>".Today() . "&nbsp;" . Now()."</span>\n";
						if(!isset($theme_options['footer_hide_companyname']) || $theme_options['footer_hide_companyname'] ==0)
							echo "<span class='date'>" . $db_connections[$_SESSION["wa_current_user"]->company]["name"] . "</span>\n";
						if(!isset($theme_options['footer_hide_servername']) || $theme_options['footer_hide_servername'] ==0)
							echo "<span class='date'>" . $_SERVER['SERVER_NAME'] . "</span>\n";
						if(!isset($theme_options['footer_hide_username']) || $theme_options['footer_hide_username'] ==0)
							echo "<span class='date'>" . $_SESSION["wa_current_user"]->name . "</span>\n";
						if(!isset($theme_options['footer_hide_themename']) || $theme_options['footer_hide_themename'] ==0)
							echo "<span class='date'>" . _("Theme:") . " " . user_theme() . "</span>\n";
						echo "<span class='date'>".show_users_online()."</span>\n";
					}
					echo "</div>\n"; // footer
				}
			}
			echo "</div>\n"; // fa-body
			echo "</div>\n"; // fa-main
		}

		function display_applications(&$waapp)
		{
			global $path_to_root;

			$sel = $waapp->get_selected_application();
			meta_forward("$path_to_root/themes/curf/dashboard.php", "sel_app=$sel->id");	
        	end_page();
        	exit;
		}	
	}
	
