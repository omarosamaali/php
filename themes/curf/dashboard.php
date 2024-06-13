<?php
/**********************************************************************
   Theme:Curf 
   Developer:Dinesh

***********************************************************************/


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

function audit_logs()
{
	global $systypes_array;
	$date = Today();
	$fromdate = date2sql(begin_month($date)) . " 00:00:00";
	$todate = date2sql(end_month($date)). " 23:59.59";
	// $user =$_SESSION['wa_current_user']->user;
	$user =-1;
	$type =-1;
	$sql = "SELECT a.*, 
		SUM(IF(ISNULL(g.amount), NULL, IF(g.amount > 0, g.amount, 0))) AS amount,
		u.user_id,u.real_name,
		UNIX_TIMESTAMP(a.stamp) as unix_stamp
		FROM ".TB_PREF."audit_trail AS a JOIN ".TB_PREF."users AS u
		LEFT JOIN ".TB_PREF."gl_trans AS g ON (g.type_no=a.trans_no
			AND g.type=a.type)
		WHERE a.user = u.id ";
	if ($type != -1)
		$sql .= "AND a.type=$type ";
	if ($user != -1)	
		$sql .= "AND a.user='$user' ";
	$sql .= "AND a.stamp >= '$fromdate'
			AND a.stamp <= '$todate'
		GROUP BY a.trans_no,a.gl_seq,a.stamp	
		ORDER BY a.stamp ASC LIMIT 5";
    $trans =db_query($sql,"No transactions were returned");


    $html ='<div class="col-md-6"><div class="card">
        <div class="card__header">
          <div class="card__header-title text-light">Recent <strong>Activities</strong>
            
          </div>
        </div>
        <div class="card__main">';

    while ($myrow=db_fetch($trans))
	{
		if ($myrow['gl_seq'] == null)
        	$action = _('Changed');
        else
        	$action = _('Closed');

        	 $html .='<div class="card__row">
            <div class="card__icon"><i class="fas fa-file-alt"></i></div>
            <div class="card__time">
              <div>'.get_time_ago(strtotime($myrow['gl_date'])).'</div>
            </div>
            <div class="card__detail">
              <div class="card__source font-weight-bold">'.get_trans_view_str($myrow["type"], $myrow["trans_no"],$systypes_array[$myrow['type']].' #'.$myrow['trans_no']).'</div>
              <div class="card__note">'.sql2date($myrow['gl_date']).' , '.$myrow['real_name'].'</div>
            </div>
          </div>';
	}

	 $html .='</div></div>
      </div>';

      return $html;
}


function dashboard_headers()
{
	return '<div class="main-overview">
	  <div class="overviewCard">
        <div class="overviewCard-icon overviewCard-icon--users">
           <i class="far fa-user"></i>
        </div>
        <div class="overviewCard-description">
          <h3 class="overviewCard-title"><strong>Users</strong></h3>
          <p class="overviewCard-subtitle">'.GetSingleValue('users','count(id)').'</p>
        </div>
      </div>

      <div class="overviewCard">
        <div class="overviewCard-icon overviewCard-icon--customer">
           <i class="fas fa-users"></i>
        </div>
        <div class="overviewCard-description">
          <h3 class="overviewCard-title"><strong>Customers</strong></h3>
          <p class="overviewCard-subtitle">'.GetSingleValue('debtors_master','count(debtor_no)').'</p>
        </div>
      </div>
      <div class="overviewCard">
        <div class="overviewCard-icon overviewCard-icon--supplier">
           <i class="fas fa-people-arrows"></i>
        </div>
        <div class="overviewCard-description">
          <h3 class="overviewCard-title"><strong>Suppliers</strong></h3>
          <p class="overviewCard-subtitle">'.GetSingleValue('suppliers','count(supplier_id)').'</p>
        </div>
      </div>
      <div class="overviewCard">
        <div class="overviewCard-icon overviewCard-icon--inventory">
           <i class="fas fa-cubes"></i>
        </div>
        <div class="overviewCard-description">
          <h3 class="overviewCard-title"><strong>Items</strong></h3>
          <p class="overviewCard-subtitle">'.GetSingleValue('stock_master','count(stock_id)').'</p>
        </div>
      </div>
    </div>';
}

function recent_documents()
{
	// print_document_link($row['order_no'], _("Print"), true, $trans_type, ICON_PRINT);

	global $systypes_array,$path_to_root;
	include_once($path_to_root.'/reporting/includes/reporting.inc');
	$date = Today();
	$fromdate = date2sql(begin_month($date)) . " 00:00:00";
	$todate = date2sql(end_month($date)). " 23:59.59";
	// $user =$_SESSION['wa_current_user']->user;
	$user =-1;
	$type =-1;
	$sql = "SELECT a.*, 
		SUM(IF(ISNULL(g.amount), NULL, IF(g.amount > 0, g.amount, 0))) AS amount,
		u.user_id,u.real_name,
		UNIX_TIMESTAMP(a.stamp) as unix_stamp
		FROM ".TB_PREF."audit_trail AS a JOIN ".TB_PREF."users AS u
		LEFT JOIN ".TB_PREF."gl_trans AS g ON (g.type_no=a.trans_no
			AND g.type=a.type)
		WHERE a.user = u.id ";
	if ($type != -1)
		$sql .= "AND a.type=$type ";
	if ($user != -1)	
		$sql .= "AND a.user='$user' ";
	$sql .= "AND a.stamp >= '$fromdate'
			AND a.stamp <= '$todate'
		GROUP BY a.trans_no,a.gl_seq,a.stamp	
		ORDER BY a.stamp ASC LIMIT 4";
    $trans =db_query($sql,"No transactions were returned");

	$html ='<div class="col-md-6"><div class="card">
        <div class="card__header">
          <div class="card__header-title text-light">Recent <strong>Documents</strong>
          </div>
        </div>
          <div class="documents">';

	while ($myrow=db_fetch($trans))
	{
		if ($myrow['gl_seq'] == null)
        	$action = _('Changed');
        else
        	$action = _('Closed');


        $html .='<div class="document">
        		<div class="document__title mb-2">'.$systypes_array[$myrow['type']].'</div>
              '.print_document_link($myrow['trans_no'], '<div class="document__img"><i class="fas fa-file-pdf my-auto"></i></div>', true, $myrow['type']).' 
              
              <div class="document__title mt-2"> #'.$myrow['trans_no'].'</div>
              <div class="document__date">'.sql2date($myrow['gl_date']).' , '.$myrow['real_name'].'</div>
            </div>';
	}
           

           $html .='</div></div>
      </div>';
      return $html ;
}

function chart_top_customers($today, $limit=10, $width="33", &$pg=null)
{
	global $path_to_root,$theme_options;
	$begin = begin_fiscalyear();
	$begin1 = date2sql($begin);
	$today1 = date2sql($today);
	$sql = "SELECT SUM((ov_amount + ov_discount) * rate * IF(trans.type = ".ST_CUSTCREDIT.", -1, 1)) AS total,d.debtor_no, d.name FROM
		".TB_PREF."debtor_trans AS trans, ".TB_PREF."debtors_master AS d WHERE trans.debtor_no=d.debtor_no
		AND (trans.type = ".ST_SALESINVOICE." OR trans.type = ".ST_CUSTCREDIT.")
		AND tran_date >= '$begin1' AND tran_date <= '$today1' GROUP by d.debtor_no ORDER BY total DESC, d.debtor_no 
		LIMIT $limit";
	$result = db_query($sql);
	$title = sprintf(_("Top %s customers in fiscal year"), $limit);
	
	$th = array(_("Customer"), _("Amount"));

	$k = 0; //row colour counter
	$i = 0;

	$labels =array();
    $data1 =array();
    $data2 =array();
    $id ="topCust";
	while ($myrow = db_fetch($result))
	{
		$name = $myrow["debtor_no"]." ".htmlspecialchars_decode($myrow["name"]);
		$labels []=$name;
		$data1 []=$myrow['total'];
		$i++;
	}

	if($labels){
		$html ='<div class="col-md-6"><div class="card">
	        <div class="card__header">
	          <div class="card__header-title text-light">'.$title.'
	          </div>
	        </div>
	          <div class="document">';
	    $html .=' <canvas id="'.$id.'" class="col-12"></canvas>
	      </div></div>
	      </div>';

		// bar chart data
	     $html .='<script>
	            var barData = {
	                labels : '.json_encode($labels).',
	                datasets : [
	                    {
	                        fillColor : "'.$theme_options['color_scheme'].'",
	                        strokeColor : "'.$theme_options['color_scheme'].'",
	                        data : '.json_encode($data1).'
	                    }
	                ]
	            }
	            // get bar chart canvas
	            var income = document.getElementById("'.$id.'").getContext("2d");
	            // draw bar chart
	            new Chart(income).Bar(barData);
	        </script>';
	    return $html;
	}
}

function chart_top_suppliers($today, $limit=10, $width="33", &$pg=null)
{
	global $path_to_root,$theme_options;
	$begin = begin_fiscalyear();
	$begin1 = date2sql($begin);
	$today1 = date2sql($today);
	$sql = "SELECT SUM((trans.ov_amount + trans.ov_discount) * rate) AS total, s.supplier_id, s.supp_name FROM
		".TB_PREF."supp_trans AS trans, ".TB_PREF."suppliers AS s WHERE trans.supplier_id=s.supplier_id
		AND (trans.type = ".ST_SUPPINVOICE." OR trans.type = ".ST_SUPPCREDIT.")
		AND tran_date >= '$begin1' AND tran_date <= '$today1' GROUP by s.supplier_id ORDER BY total DESC, s.supplier_id 
		LIMIT $limit";
	$result = db_query($sql);
	$title = sprintf(_("Top %s suppliers in fiscal year"), $limit);
	$k = 0; //row colour counter
	$i = 0;

	$labels =array();
    $data1 =array();
    $data2 =array();
    $id ="topSuppliers";
	while ($myrow = db_fetch($result))
	{
		$name = $myrow["supplier_id"]." ".htmlspecialchars_decode($myrow["supp_name"]);
		$labels []=$name;
		$data1 []=$myrow['total'];
		$i++;
	}

	if($labels){
		$html ='<div class="col-md-6"><div class="card">
	        <div class="card__header">
	          <div class="card__header-title text-light">'.$title.'
	          </div>
	        </div>
	          <div class="document">';
	    $html .=' <canvas id="'.$id.'" class="col-12"></canvas>';
	   	$html .='</div></div>
	      </div>';

		// bar chart data
	     $html .='<script>
	            var barData = {
	                labels : '.json_encode($labels).',
	                datasets : [
	                    {
	                        fillColor : "'.$theme_options['color_scheme'].'",
	                        strokeColor : "'.$theme_options['color_scheme'].'",
	                        data : '.json_encode($data1).'
	                    }
	                ]
	            }
	            // get bar chart canvas
	            var income = document.getElementById("'.$id.'").getContext("2d");
	            // draw bar chart
	            new Chart(income).Bar(barData);
	        </script>';
	    return $html;
	}
}
function rand_color($i) {
    return sprintf('#%06X', mt_rand($i, 0xFFFFFF));
}
function chart_gl_top($today, $width="33", &$pg=null)
{
	$begin = begin_fiscalyear();
	$begin1 = date2sql($begin);
	$today1 = date2sql($today);
	$sql = "SELECT SUM(amount) AS total, c.class_name, c.ctype FROM
		".TB_PREF."gl_trans,".TB_PREF."chart_master AS a, ".TB_PREF."chart_types AS t, 
		".TB_PREF."chart_class AS c WHERE
		account = a.account_code AND a.account_type = t.id AND t.class_id = c.cid
		AND IF(c.ctype > 3, tran_date >= '$begin1', tran_date >= '0000-00-00') 
		AND tran_date <= '$today1' GROUP BY c.cid ORDER BY c.cid"; 
	$result = db_query($sql, "Transactions could not be calculated");
	$title = _("Class Balances");
	$i = 0;
	$total = 0;
	$id ="chartClasses";
	$charts =array();
	$colors =["BlueViolet","Navy","SkyBlue","MediumSpringGreen","Aquamarine","OldLace","LawnGreen","Indigo","SlateGrey","MediumAquaMarine","OrangeRed","Wheat","Blue","Gray","Tomato","MistyRose","Crimson","DarkSlateGrey","RebeccaPurple","PaleVioletRed","LightGoldenRodYellow","LightYellow","Yellow","Bisque","SeaGreen","Maroon","DarkKhaki","CadetBlue","SlateGray","GreenYellow","PaleGreen","LightPink","Red","LightGreen","Snow","PaleGoldenRod","MediumPurple","Linen","LightBlue","DarkSalmon","DarkCyan","Beige","DarkSlateBlue","WhiteSmoke","DarkGray","MediumBlue","YellowGreen","BlanchedAlmond","Fuchsia","Cyan","MediumTurquoise","Cornsilk","DarkViolet","DarkRed","Green","PowderBlue","FireBrick","LavenderBlush","Gold","Thistle","LemonChiffon","RosyBrown","Chocolate","DarkMagenta","DarkBlue","Peru","SteelBlue","Sienna","HotPink","DarkSeaGreen","LightSalmon","Chartreuse","Khaki","Olive","BurlyWood","DodgerBlue","LightCyan","DarkGrey","Pink","Azure","Silver","SandyBrown","ForestGreen","LightSteelBlue","SlateBlue","DarkTurquoise","AntiqueWhite","MediumSeaGreen","White","DimGray","Moccasin","Plum","DarkOrange","DarkGreen","PeachPuff","Orange","HoneyDew","Gainsboro","Brown","LightSlateGray","NavajoWhite","MediumVioletRed","Teal","PapayaWhip","Violet","Lime","DarkSlateGray","LightGrey","Turquoise","GoldenRod","FloralWhite","Tan","DimGrey","Magenta","SpringGreen","Aqua","DarkGoldenRod","Coral","Salmon","MintCream","PaleTurquoise","LightCoral","Orchid","Black","DeepSkyBlue","Grey","CornflowerBlue","Lavender","LightGray","LightSkyBlue","DeepPink","LightSeaGreen","SeaShell","IndianRed","MidnightBlue","Ivory","MediumSlateBlue","LightSlateGrey","LimeGreen","AliceBlue","MediumOrchid","OliveDrab","RoyalBlue","Purple","DarkOliveGreen","SaddleBrown","DarkOrchid","GhostWhite"];

	while ($myrow = db_fetch($result))
	{
		if ($myrow['ctype'] > 3)
		{
			$total += $myrow['total'];
			$myrow['total'] = -$myrow['total'];


			$charts []=array('label'=>$myrow['class_name'],'value'=>abs($myrow['total']),'color'=>$colors[$i]);
			$i++;
		}	
		
	}
	$charts []=array('label'=>_("Calculated Return"),'value'=>abs(number_format2(-$total, user_price_dec())),'color'=>rand_color($i));
	if($charts){
		$html ='<div class="col-md-6"><div class="card">
	        <div class="card__header">
	          <div class="card__header-title text-light">'.$title.'
	          </div>
	        </div>
	          <div class="document">';
	    $html .=' <canvas id="'.$id.'" class="col-12"></canvas>';
	   	$html .='</div></div>
	      </div>';

		// bar chart data
	     $html .='<script>
	                var pieData = '.json_encode($charts).';
            // pie chart options
            var pieOptions = {
                 segmentShowStroke : false,
                 animateScale : true
            }
            // get pie chart canvas
            var countries= document.getElementById("chartClasses").getContext("2d");
            // draw pie chart
            new Chart(countries).Pie(pieData, pieOptions);
	        </script>';
	     
	    return $html;
	}
}

function chart_stock_top($today, $limit=10, $width="33", $type=0, &$pg=null)
{
	global $path_to_root,$theme_options;
	if ($type == 2){
		$sec = 'SA_ASSETSANALYTIC';

	}
	elseif ($type == 1)
		$sec = 'SA_WORKORDERANALYTIC';
	else
		$sec = 'SA_ITEMSTRANSVIEW';

	$begin = begin_fiscalyear();
	$begin1 = date2sql($begin);
	$today1 = date2sql($today);
	if ($type == 0)
	{
		$sql = "SELECT SUM((trans.unit_price * trans.quantity) * d.rate) AS total, s.stock_id, s.description, 
			SUM(trans.quantity) AS qty, SUM((s.material_cost + s.overhead_cost + s.labour_cost) * trans.quantity) AS costs FROM
			".TB_PREF."debtor_trans_details AS trans, ".TB_PREF."stock_master AS s, ".TB_PREF."debtor_trans AS d 
			WHERE trans.stock_id=s.stock_id AND trans.debtor_trans_type=d.type AND trans.debtor_trans_no=d.trans_no
			AND (d.type = ".ST_SALESINVOICE." OR d.type = ".ST_CUSTCREDIT.") ";
	}
	else
	{
		$sql = "SELECT SUM(m.qty * (s.material_cost + s.labour_cost + s.overhead_cost)) AS total, s.stock_id, s.description, 
			SUM(qty) AS qty FROM ".TB_PREF."stock_master AS s, ".TB_PREF."stock_moves AS m 
			WHERE s.stock_id=m.stock_id ";
		if ($type == 1)
			$sql .= "AND s.mb_flag='M' AND m.type <> ".ST_CUSTDELIVERY." AND m.type <> ".ST_CUSTCREDIT." ";
		elseif ($type == 2)	
			$sql .= "AND s.mb_flag='F' ";
	}
	if ($type != 2)
		$sql .= "AND tran_date >= '$begin1' ";
	$sql .= "AND tran_date <= '$today1' GROUP by s.stock_id ORDER BY total DESC, s.stock_id 
		LIMIT $limit";
	$result = db_query($sql);
	if ($type == 1){
		$id ='manuItemChart';
		$title = sprintf(_("Top %s Manufactured Items in fiscal year"), $limit);
	}
	elseif ($type == 2){
		$id ='fixedAssetsChart';
		$title = sprintf(_("Top %s Fixed Assets"), $limit);
	}
	else{	
		$id ='soldItemChart';
		$title = sprintf(_("Top %s Sold Items in fiscal year"), $limit);
	}
	
	$k = 0; //row colour counter
	$i = 0;

	

    $labels =array();
    $data1 =array();
    $data2 =array();
	while ($myrow = db_fetch($result))
	{

		$name = $myrow["description"];
		$labels []=$name;
		$data1 []=$myrow['total'];
		if ($type == 0){
			$data2 []=$myrow['costs'];
		}
		if ($pg != NULL)
		{
			$pg->x[$i] = $name; 
			$pg->y[$i] = $myrow['total'];
			if ($type == 0)
				$pg->z[$i] = $myrow['costs'];
		}	
		$i++;
	}

	if($labels){
		$html ='<div class="col-md-6"><div class="card">
	        <div class="card__header">
	          <div class="card__header-title text-light">'.$title.'
	          </div>
	        </div>
	          <div class="document">';
	    $html .=' <canvas id="'.$id.'" class="col-12"></canvas>';
	   	$html .='</div></div>
	      </div>';

		// bar chart data
	     $html .='<script>
	            var barData = {
	                labels : '.json_encode($labels).',
	                datasets : [
	                    {
	                        fillColor : "'.$theme_options['color_scheme'].'",
	                        strokeColor : "'.$theme_options['color_scheme'].'",
	                        data : '.json_encode($data1).'
	                    },
	                    {
	                        fillColor : "#'.color_blend_by_opacity($theme_options['color_scheme'],20).'",
	                        strokeColor : "#'.color_blend_by_opacity($theme_options['color_scheme'],20).'",
	                        data : '.json_encode($data2).'
	                    }
	                ]
	            }
	            // get bar chart canvas
	            var income = document.getElementById("'.$id.'").getContext("2d");
	            // draw bar chart
	            new Chart(income).Bar(barData);
	        </script>';
	     
	    return $html;
	}
	
}

function chart_gl_performance($today, $width="33", $weeks=5)
{
	global $SysPrefs,$theme_options;
	$begin = begin_fiscalyear();
	$begin1 = date2sql($begin);
	$today1 = date2sql($today);
	$sep = $SysPrefs->dateseps[user_date_sep()];
	$sql = "SELECT week_name, sales, costs 
		FROM(SELECT DATE_FORMAT(tran_date, '%Y{$sep}%u') AS week_name, 
			SUM(IF(c.ctype = 4, amount * -1, 0)) AS sales, 
			SUM(IF(c.ctype = 6, amount, 0)) AS costs FROM 
			".TB_PREF."gl_trans, ".TB_PREF."chart_master AS a, ".TB_PREF."chart_types AS t, 
			".TB_PREF."chart_class AS c WHERE(c.ctype = 4 OR c.ctype = 6) 
			AND account = a.account_code AND a.account_type = t.id AND t.class_id = c.cid 
			AND tran_date >= '$begin1' AND tran_date <= '$today1' 
			GROUP BY week_name ORDER BY week_name DESC LIMIT 0, $weeks) b 
		GROUP BY week_name ORDER BY week_name ASC";
	$result = db_query($sql, "Transactions could not be calculated");
	$sales_title = _("Last $weeks weeks sales Performance");
	$costs_title = _("Last $weeks weeks costs Performance");
	$i = 0;
	$sales_id ="salesPerformance";
	$costs_id ="scostsPerformance";

	$labels =array();
	$sales_data =array();
	$costs_data =array();
	while ($myrow = db_fetch($result))
	{
		$labels [] =$myrow['week_name'];
		$sales_data [] =$myrow['sales'];
		$costs_data [] =$myrow['costs'];
		$i++;
	}	
	
	$html ='<div class="col-md-6"><div class="card">
	        <div class="card__header">
	          <div class="card__header-title text-light">'.$sales_title.'
	          </div>
	        </div>
	          <div class="document">';
	    $html .=' <canvas id="'.$sales_id.'" class="col-12"></canvas>';
	   	$html .='</div></div>
	      </div>';

	 $html .='<div class="col-md-6"><div class="card">
	        <div class="card__header">
	          <div class="card__header-title text-light">'.$costs_title.'
	          </div>
	        </div>
	          <div class="document">';
	    $html .=' <canvas id="'.$costs_id.'" class="col-12"></canvas>';
	   	$html .='</div></div>
	      </div>';

     $html .='<script>
            var buyerData = {
                labels : '.json_encode($labels).',
                datasets : [
                {
                    fillColor : "#'.color_blend_by_opacity($theme_options['color_scheme'],20).'",
                    strokeColor : "'.$theme_options['color_scheme'].'",
                    pointColor : "'.$theme_options['color_scheme'].'",
                    pointStrokeColor : "'.$theme_options['color_scheme'].'",
                    data : '.json_encode($sales_data).'
                }
            ]
            }
            // get line chart canvas
            var buyers = document.getElementById("'.$sales_id.'").getContext("2d");
            // draw line chart
            new Chart(buyers).Line(buyerData);
        </script>'; 
    $html .='<script>
            var buyerData = {
                labels : '.json_encode($labels).',
                datasets : [
                {
                    fillColor : "#'.color_blend_by_opacity($theme_options['color_scheme'],20).'",
                    strokeColor : "'.$theme_options['color_scheme'].'",
                    pointColor : "'.$theme_options['color_scheme'].'",
                    pointStrokeColor : "'.$theme_options['color_scheme'].'",
                    data : '.json_encode($costs_data).'
                }
            ]
            }
            // get line chart canvas
            var buyers = document.getElementById("'.$costs_id.'").getContext("2d");
            // draw line chart
            new Chart(buyers).Line(buyerData);
        </script>';
     
    return $html;
}


function _customer_trans($today)
{
	$today = date2sql($today);

	$sql = "SELECT trans.trans_no, trans.reference,	trans.tran_date, trans.due_date, debtor.debtor_no, 
		debtor.name, branch.br_name, debtor.curr_code,
		(trans.ov_amount + trans.ov_gst + trans.ov_freight 
			+ trans.ov_freight_tax + trans.ov_discount)	AS total,  
		(trans.ov_amount + trans.ov_gst + trans.ov_freight 
			+ trans.ov_freight_tax + trans.ov_discount - trans.alloc) AS remainder,
		DATEDIFF('$today', trans.due_date) AS days 	
		FROM ".TB_PREF."debtor_trans as trans, ".TB_PREF."debtors_master as debtor, 
			".TB_PREF."cust_branch as branch
		WHERE debtor.debtor_no = trans.debtor_no AND trans.branch_code = branch.branch_code
			AND trans.type = ".ST_SALESINVOICE." AND (trans.ov_amount + trans.ov_gst + trans.ov_freight 
			+ trans.ov_freight_tax + trans.ov_discount - trans.alloc) > ".FLOAT_COMP_DELTA." 
			AND DATEDIFF('$today', trans.due_date) > 0 ORDER BY days DESC LIMIT 5";
	$result = db_query($sql);
	$title = db_num_rows($result) . _(" overdue Sales Invoices");
  	echo '<div class="col-md-6"><div class="card">
	        <div class="card__header">
	          <div class="card__header-title text-light">'.$title.'
	          </div>
	        </div>
	          <div class="table-responsive">';
	$th = array("#", _("Ref."), _("Date"), _("Due Date"), _("Customer"), _("Branch"), _("Currency"), 
		_("Total"), _("Remainder"),	_("Days"));
	start_table(TABLESTYLE);
	table_header($th);
  	$k = 0; //row colour counter
	while ($myrow = db_fetch($result))
	{
		alt_table_row_color($k);
		label_cell(get_trans_view_str(ST_SALESINVOICE, $myrow["trans_no"]));
		label_cell($myrow['reference']);
		label_cell(sql2date($myrow['tran_date']));
		label_cell(sql2date($myrow['due_date']));
		$name = $myrow["debtor_no"]." ".$myrow["name"];
		label_cell($name);
		label_cell($myrow['br_name']);
		label_cell($myrow['curr_code']);
		amount_cell($myrow['total']);
		amount_cell($myrow['remainder']);
		label_cell($myrow['days'], "align='right'");
		end_row();
	}
	end_table();
	echo'</div></div>
	      </div>';
}

function _customer_recurrent_invoices($today)
{
	$result = get_recurrent_invoices($today);
	$title = _("Overdue Recurrent Invoices");
	echo '<div class="col-md-6"><div class="card">
	        <div class="card__header">
	          <div class="card__header-title text-light">'.$title.'
	          </div>
	        </div>
	          <div class="table-responsive">';
 
	$th = array(_("Description"), _("Template No"),_("Customer"),_("Branch")."/"._("Group"),_("Next invoice"));
	start_table(TABLESTYLE, "width=70%");
  	table_header($th);
	$k = 0;
	while ($myrow = db_fetch($result)) 
	{
		if (!$myrow['overdue'])
			continue;
		alt_table_row_color($k);

		label_cell($myrow["description"]);
		label_cell(get_customer_trans_view_str(ST_SALESORDER, $myrow["order_no"]));
		if ($myrow["debtor_no"] == 0)
		{
			label_cell("");

			label_cell(get_sales_group_name($myrow["group_no"]));
		}
		else
		{
			label_cell(get_customer_name($myrow["debtor_no"]));
			label_cell(get_branch_name($myrow['group_no']));
		}
		label_cell(_calculate_next_invoice($myrow),  "align='center'");
		end_row();
	}
	end_table();
	echo'</div></div>
	      </div>';
}


function _calculate_next_invoice($myrow)
{
	if ($myrow["last_sent"] == '0000-00-00')
		$next = sql2date($myrow["begin"]);
	else
		$next = sql2date($myrow["last_sent"]);
	$next = add_months($next, $myrow['monthly']);
	$next = add_days($next, $myrow['days']);
	return add_days($next,-1);
}

function _supplier_trans($today)
{
	$today = date2sql($today);
	$sql = "SELECT trans.trans_no, trans.reference, trans.tran_date, trans.due_date, s.supplier_id, 
		s.supp_name, s.curr_code,
		(trans.ov_amount + trans.ov_gst + trans.ov_discount) AS total,  
		(trans.ov_amount + trans.ov_gst + trans.ov_discount - trans.alloc) AS remainder,
		DATEDIFF('$today', trans.due_date) AS days 	
		FROM ".TB_PREF."supp_trans as trans, ".TB_PREF."suppliers as s 
		WHERE s.supplier_id = trans.supplier_id
			AND trans.type = ".ST_SUPPINVOICE." AND (ABS(trans.ov_amount + trans.ov_gst + 
				trans.ov_discount) - trans.alloc) > ".FLOAT_COMP_DELTA."
			AND DATEDIFF('$today', trans.due_date) > 0 ORDER BY days DESC LIMIT 5";
	$result = db_query($sql);
	$title = db_num_rows($result) . _(" overdue Purchase Invoices");
	echo '<div class="col-md-6"><div class="card">
	        <div class="card__header">
	          <div class="card__header-title text-light">'.$title.'
	          </div>
	        </div>
	          <div class="table-responsive">';
	$th = array("#", _("Ref."), _("Date"), _("Due Date"), _("Supplier"), _("Currency"), _("Total"), 
		_("Remainder"),	_("Days"));
	start_table(TABLESTYLE);
	table_header($th);
	$k = 0; //row colour counter
	while ($myrow = db_fetch($result))
	{
		alt_table_row_color($k);
		label_cell(get_trans_view_str(ST_SUPPINVOICE, $myrow["trans_no"]));
		label_cell($myrow['reference']);
		label_cell(sql2date($myrow['tran_date']));
		label_cell(sql2date($myrow['due_date']));
		$name = $myrow["supplier_id"]." ".$myrow["supp_name"];
		label_cell($name);
		label_cell($myrow['curr_code']);
		amount_cell($myrow['total']);
		amount_cell($myrow['remainder']);
		label_cell($myrow['days'], "align='right'");
		end_row();
	}
	end_table();
	echo'</div></div>
	      </div>';
}

function _bank_balance($today, $width)
{
	$today = date2sql($today);
	$sql = "SELECT bank_act, bank_account_name, bank_curr_code, SUM(amount) balance FROM ".TB_PREF."bank_trans bt 
	            INNER JOIN ".TB_PREF."bank_accounts ba ON bt.bank_act = ba.id
	            WHERE trans_date <= '$today'
	            AND inactive <> 1
	            GROUP BY bank_act, bank_account_name
				ORDER BY bank_account_name";
	$result = db_query($sql);
	$title = _("Bank Account Balances");
	echo '<div class="col-md-6"><div class="card">
	        <div class="card__header">
	          <div class="card__header-title text-light">'.$title.'
	          </div>
	        </div>
	          <div class="table-responsive">';
	$th = array(_("Account"), _("Currency"), _("Balance"));
	start_table(TABLESTYLE, "width='$width%'");
	table_header($th);
	$k = 0; //row colour counter
	while ($myrow = db_fetch($result))
	{
		alt_table_row_color($k);
		label_cell(viewer_link($myrow["bank_account_name"], 'gl/inquiry/bank_inquiry.php?bank_account='.$myrow["bank_act"]));
		label_cell($myrow["bank_curr_code"]);
		amount_cell($myrow['balance']);
		end_row();
	}
	end_table();
	echo'</div></div>
	      </div>';
}	
if (isset($_GET['sel_app']))
{
	$page_security = 'SA_OPEN'; // A very low access level. The real access level is inside the routines.
	$path_to_root = "../..";

	include_once($path_to_root . "/includes/session.inc");
	include_once($path_to_root . "/includes/ui.inc");
	include_once($path_to_root . "/reporting/includes/class.graphic.inc");
	include_once($path_to_root . "/includes/dashboard.inc"); // here are all the dashboard routines.

	$js = "";
	if ($SysPrefs->use_popup_windows)
		$js .= get_js_open_window(800, 500);

	page(_($help_context = "Dashboard"), false, false, "", $js);
	
	?>

  <main class="main">
    <!-- <?php echo dashboard_headers(); ?> -->
    <div class="main__cards row">
      <?php echo audit_logs()?>
      <?php echo recent_documents()?>
      <?php $today = Today(); echo chart_gl_performance($today, 66, 5)?>
      
      <?php $today = Today(); echo chart_stock_top($today, 3, 66, 0)?>
      <?php $today = Today(); _customer_trans($today);?>
      <?php $today = Today(); _supplier_trans($today);?>
      <?php $today = Today(); _customer_recurrent_invoices($today);?>
      <?php echo chart_gl_top($today, 66, $pg);?>
      <?php $today = Today(); echo chart_stock_top($today, 3, 66, 2);?>
      <?php $today = Today(); echo _bank_balance($today, 66);;?>
      <?php $today = Today(); echo chart_top_customers($today, 3, 66);?>
      <?php $today = Today(); echo chart_top_suppliers($today, 3, 66);?>
      <?php $today = Today(); echo chart_stock_top($today, 3, 66, 1);?>
    </div> <!-- /.main-cards -->
  </main>

 
<style type="text/css">


.main {
  grid-area: main;
  background-color: var(--bg-secondary);
  color: var(--text-primary);
}
.main__cards {
  margin-top: 20px;
}
.quickview {
  display: grid;
  grid-auto-flow: column;
  grid-gap: 60px;
}
.quickview__item {
  display: flex;
  align-items: center;
  flex-direction: column;
}
.quickview__item-total {
  margin-bottom: 2px;
  font-size: 32px;
}
.quickview__item-description {
  font-size: 16px;
  text-align: center;
}

.main-overview {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(265px, 1fr));
  grid-auto-rows: 94px;
  grid-gap: 30px;
}

.overviewCard {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px;
  background-color: var(--bg-primary);
  border-radius: 5px;
  box-shadow: 0 5px 5px rgba(0, 0, 0, 0.1);
}
.overviewCard-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 60px;
  width: 60px;
  border-radius: 50%;
  font-size: 21px;
  color: var(--text-secondary);
}
.overviewCard-icon--customer {
  background-color: #e67e22;
}
.overviewCard-icon--supplier {
  background-color: #27ae60;
}
.overviewCard-icon--inventory {
  background-color: #e74c3c;
}
.overviewCard-icon--users {
  background-color: #af64cc;
}
.overviewCard-description {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.overviewCard-title {
  font-size: 18px;
  color: var(--primary-color);
  margin: 0;
}
.overviewCard-subtitle {
  margin: 2px;
  font-size: 32px;
  color: var(--primary-color);
}


.card {
  display: flex;
  flex-direction: column;
  width: 100%;
  background-color: var(--bg-primary);
  margin-bottom: 20px;
  -webkit-column-break-inside: avoid;
  border-radius: 5px;
  border:none;
  box-shadow: 0 5px 5px rgba(0, 0, 0, 0.1);
}
.card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 50px;
  background-color: var(--primary-color);
  color: var(--text-primary);
  border-radius: 5px;
}
.card__header-title {
  margin: 0 20px;
  font-size: 20px;
  letter-spacing: 1.2px;
}
.card__header-link {
  font-size: 16px;
  color: #1BBAE1;
  letter-spacing: normal;
  display: inline-block;
}
.card__main {
  position: relative;
  padding-right: 20px;
  background-color: var(--bg-primary);
  border-radius: 5px;
}
.card__main:after {
  content: "";
  position: absolute;
  top: 0;
  left: 160px;
  bottom: 0;
  width: 2px;
  background-color: var(--primary-color);
}
.card__secondary {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
  grid-auto-rows: 100px;
  grid-gap: 25px;
  padding: 20px;
  background-color: #FFF;
}
.card__photo {
  background-image: url("../../img/pumpkin-carving.jpg");
  background-size: cover;
  background-repeat: no-repeat;
  background-color: slategray;
  transform: scale(1);
  transition: transform 0.3s ease-in-out;
  width: 100%;
  height: 100%;
}
.card__photo:hover {
  transform: scale(1.1);
  cursor: pointer;
}
.card__photo-wrapper {
  overflow: hidden;
}
.card__row {
  position: relative;
  display: flex;
  flex: 1;
  margin: 15px 0 20px;
}
.card__icon {
  position: absolute;
  display: flex;
  align-items: center;
  justify-content: center;
  content: "";
  width: 30px;
  height: 30px;
  top: 0;
  left: 161px;
  transform: translateX(-50%);
  border-radius: 50%;
  color: #FFF;
  background-color: #1BBAE1;
  z-index: 1;
}
.card__row:nth-child(even) .card__icon {
  background-color: #e74c3c;
}
.card__time {
  display: flex;
  flex: 1;
  justify-content: flex-end;
  max-width: 120px;
  margin-left: 15px;
  text-align: right;
  font-size: 14px;
  line-height: 2;
}
.card__detail {
  display: flex;
  flex: 1;
  flex-direction: column;
  padding-left: 12px;
  margin-left: 88px;
  transform: translateX(0);
  transition: all 0.3s;
}
.card__detail:hover {
  background-color: var(--bg-secondary);
  transform: translateX(4px);
  border-radius: 5px;
}
.card__source {
  line-height: 1.8;
  color: #1BBAE1;
}
.card__source a{
  color: var(--primary-color);
}
.card__note {
  margin: 10px 0;
}
.card--finance {
  position: relative;
}

.settings {
  display: flex;
  margin: 8px;
  align-self: flex-start;
  background-color: rgba(255, 255, 255, 0.5);
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 2px;
}
.settings__block {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4px;
  color: #394263;
  font-size: 11px;
}
.settings__block:not(:last-child) {
  border-right: 1px solid rgba(0, 0, 0, 0.1);
}
.settings__icon {
  padding: 0px 3px;
  font-size: 12px;
}
.settings__icon:hover {
  background-color: rgba(255, 255, 255, 0.8);
  cursor: pointer;
}
.settings:hover {
  background-color: #fff;
  cursor: pointer;
}

.documents {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(105px, 1fr));
  grid-auto-rows: 214px;
  grid-gap: 12px;
  height: auto;
  background-color: var(--bg-primary);
}

.document {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 15px 0 0;
  flex-direction: column;
  background-color: var(--bg-primary);
}
.document__img {
 /* width: 105px;
  height: 136px;*/
  box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);
  cursor: pointer;
  transition: transform 0.3s ease;
  text-align: center;
  font-size: 70px;
  color:var(--primary-color);
}
.document__img:hover {
  transform: translateY(-4px);
}
.document__title {
  font-size: 14px;
}
.document__date {
  font-size: 12px;
}

#chartdiv {
  width: 100%;
  height: 300px;
  font-size: 11px;
  min-width: 0;
}


@media only screen and (min-width: 46.875em) {
  .grid {
    display: grid;
    grid-template-columns: 240px calc(100% - 240px);
    grid-template-rows: 50px 1fr 50px;
    grid-template-areas: "sidenav header" "sidenav main" "sidenav footer";
    height: 100vh;
  }

  .sidenav {
    position: relative;
    transform: translateX(0);
  }
  .sidenav__brand-close {
    visibility: hidden;
  }

  .header__menu {
    display: none;
  }
  .header__search {
    margin-left: 20px;
  }
  .header__avatar {
    width: 40px;
    height: 40px;
  }
}
@media only screen and (min-width: 65.625em) {
  .main-header__intro-wrapper {
    flex-direction: row;
  }
  .main-header__welcome {
    align-items: flex-start;
  }
}

.table-responsive tbody{
  display:block;
  max-height:300px;
  overflow-y:auto;
}

.table-responsive thead, tbody tr {
  display:table;
  min-width: 100%;
  table-layout:fixed;
}

</style>
	<?php
	// dashboard($_GET['sel_app']);
	end_page();
	exit;
}
