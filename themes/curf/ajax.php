<?php
/**********************************************************************
   Theme:Curf 
   Developer:Dinesh

***********************************************************************/
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/themes/curf/theme.inc");
include_once($path_to_root . "/includes/ui.inc");


function get_time_ago( $time )
{
    $time_difference = time() - $time;
    if( $time_difference < 1 ) { return 'less than 1 second ago'; }
    $condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
    );

    foreach( $condition as $secs => $str )
    {
        $d = $time_difference / $secs;

        if( $d >= 1 )
        {
            $t = round( $d );
            return $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
        }
    }
}


if(isset($_GET['noti_id'])){
	Update('curf_push_notification_users',array('notification_id'=>$_GET['noti_id'],'user_id'=>$_SESSION['wa_current_user']->user),array('inactive'=>1));
	if(GetSingleValue('curf_push_notification_users','count(id)',array('user_id'=>$_SESSION['wa_current_user']->user,'inactive'=>0))>0){
		echo json_encode(['has_noti'=>true]);
	}else{
		echo json_encode(['has_noti'=>false]);
	}
}
if(isset($_GET['noti_mark_all'])){
	Update('curf_push_notification_users',array('user_id'=>$_SESSION['wa_current_user']->user),array('inactive'=>1));
}

if(isset($_GET['get_notis'])){
	$notifications =GetDataJoin('curf_push_notification_users AS users',array(
  	array('join'=>'LEFT','table_name'=>'curf_push_notifications AS message','conditions'=>'users.notification_id=message.id')),
  	array('message.*'),
  	array('users.user_id'=>$_SESSION['wa_current_user']->user,'users.inactive'=>0),array('message.id'=>'DESC')
		);

	$html ='';
  	if($notifications){
      	foreach ($notifications as $row) {
	      	$link =str_replace('{$path_to_root}', $path_to_root, $row['link']);
	      	$message =str_replace('{$link}', $link, $row['message']);
	      	$html .='<div class="dropdown-item dropdown-item-unread">
	          <div class="dropdown-item-desc nofication-read" data-url="'.$path_to_root.'/themes/curf/ajax.php?noti_id='.$row['id'].'">
	            '.html_entity_decode($message,ENT_QUOTES).'
	            <div class="time font-weight-light font-italic">'.get_time_ago(strtotime($row['_date'])).'</div>
	          </div>
	        </div>';
	    }
	 }else{
	  	$html .='<div class="no-notification d-flex align-items-center"><div class="mx-auto text-center font-weight-light"><i class="fas fa-exclamation-triangle d-block mb-4" style="font-size: 48px; font-weight-light"></i><span>No notifications</span></div></div>';
	}
	echo json_encode(array('html'=>$html));
}

if(isset($_GET['sidebar'])){
	if(!isset($_SESSION['wa_current_user']->sidebar)){
		$_SESSION['wa_current_user']->sidebar =false;
	}else{
		$_SESSION['wa_current_user']->sidebar =!$_SESSION['wa_current_user']->sidebar;
	}
}