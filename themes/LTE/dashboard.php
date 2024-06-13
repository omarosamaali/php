<?php

$page_security = 'SA_SETUPDISPLAY'; 

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/gl/includes/db/gl_db_trans.inc");

include_once("kvcodes.inc");
global $installed_extensions; 

$crm_installed = false;
foreach($installed_extensions as $module) {
	if(isset($module['package']) && $module['package'] == 'KvcodesCRM'){
		$crm_installed = true;
		break;
	}
}

function lte_get_gl_balance_from_to($from_date, $to_date, $account, $dimension=0, $dimension2=0)
{
	$from = date2sql($from_date);
	$to = date2sql($to_date);

    $sql = "SELECT SUM(amount) FROM ".TB_PREF."gl_trans
		WHERE account='$account'";
	if ($from_date != "")
		$sql .= "  AND tran_date >= '$from'";
	if ($to_date != "")
		$sql .= "  AND tran_date <= '$to'";
	if ($dimension != 0)
  		$sql .= " AND dimension_id = ".($dimension<0 ? 0 : db_escape($dimension));
	if ($dimension2 != 0)
  		$sql .= " AND dimension2_id = ".($dimension2<0 ? 0 : db_escape($dimension2));

	$result = db_query($sql, "The starting balance for account $account could not be calculated");

	$row = db_fetch_row($result);
	return round2($row[0], user_price_dec());
}

//display_error(json_encode($_SESSION['wa_current_user']));
if((int)kv_get_option('hide_dashboard') == 0){ 
	$sql_cust_count = "SELECT COUNT(*) FROM `".TB_PREF."debtors_master`" ;
	$sql_cust_count_result = db_query($sql_cust_count, "could not get sales type");
	$cust_coubt = db_fetch_row($sql_cust_count_result);
	$sql_supp_count = "SELECT COUNT(*) FROM `".TB_PREF."suppliers`" ;
	$sql_supp_count_result = db_query($sql_supp_count, "could not get sales type");
	$sup_count= db_fetch_row($sql_supp_count_result);
	$class_balances = class_balances();
	if(kv_get_option('color_scheme') == 'dark' ){
		$color_scheme = '#ffffff'; 
	}else{
		$color_scheme= '#000000';
	}
	
	$receivables = lte_get_gl_balance_from_to('', Today(), get_company_pref('debtors_act'));
	$payables = lte_get_gl_balance_from_to('', Today(), get_company_pref('creditors_act'));

	$ShowReceivables = price_format( empty($receivables) ? 0 : round2($receivables, user_price_dec()));
	$ShowPayables = number_format2( empty($payables) ? 0 : round2($payables, user_price_dec()));
	?>

	<link rel="stylesheet" href='<?php echo $path_to_root."/themes/".user_theme()."/css/morris.css"; ?>'>
	<!-- <link rel="stylesheet" href='<?php //echo $path_to_root."/themes/".user_theme()."/css/grid.css"; ?>'> -->
	<script src='<?php echo $path_to_root."/themes/".user_theme()."/js/jquery.min.js"; ?>'></script>
	<script src="<?php echo $path_to_root."/themes/".user_theme()."/js/raphael-min.js"; ?>"></script>
	<script src="<?php echo $path_to_root."/themes/".user_theme()."/js/morris.min.js"; ?>"></script>
	<style> 
	.box-body { overflow-x: auto;}
	</style>
 <!-- Main content -->
    <section class="content">
      <!-- Info boxes -->
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
			<a href="<?= ($crm_installed && file_exists($path_to_root.'/modules/KvcodesCRM/inquires/customers_list.php') ? $path_to_root.'/modules/KvcodesCRM/inquires/customers_list.php' : $path_to_root.'/sales/manage/customers.php'); ?>" >
				<div class="info-box">
					<span class="info-box-icon bg-aqua"><i class="ion ion-ios-gear-outline"></i></span>

					<div class="info-box-content">
					<span class="info-box-text"><?php echo _("Customers"); ?></span>
					<span class="info-box-number"><?php echo $cust_coubt[0]; ?></span>
					</div>
					<!-- /.info-box-content -->
				</div>
			</a>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-4 col-sm-6 col-xs-12">
		<a href="<?=  $path_to_root.'/gl/inquiry/bank_inquiry.php?'; ?>" >
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-bank"></i></span>

            <div class="info-box-content">
              <span class="info-box-text"><?php echo _("Current Balance"); ?></span>
              <span class="info-box-number"><?php echo kv_get_current_balance(); ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
		</a>
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-4 col-sm-6 col-xs-12">
		<a href="<?= ($crm_installed && file_exists($path_to_root.'/modules/KvcodesCRM/inquires/suppliers_list.php') ? $path_to_root.'/modules/KvcodesCRM/inquires/suppliers_list.php' : $path_to_root.'/purchasing/manage/suppliers.php'); ?>" >
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="ion ion-ios-people-outline"></i></span>

            <div class="info-box-content">
              <span class="info-box-text"><?php echo _("Suppliers"); ?></span>
              <span class="info-box-number"><?php echo $sup_count[0]; ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
			</a>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
		</div> <!-- / row -->
		<div class="row" > 
			<div class="col-md-4 col-sm-6 col-xs-12">
			<a href="<?= (file_exists($path_to_root.'/inventory/inquiry/item_inquiry.php') ? $path_to_root.'/inventory/inquiry/item_inquiry.php' : $path_to_root.'/sales/manage/customers.php'); ?>" >
			  <div class="info-box">
				<span class="info-box-icon bg-yellow"><i class="fa fa-database"></i></span>

				<div class="info-box-content">
				  <span class="info-box-text"><?php echo _("Items and Inventory"); ?></span>
				  <span class="info-box-number"><?php 

						$sql_count_items = "SELECT COUNT(*) FROM `".TB_PREF."item_codes`"; 
						$sql_items_count_result = db_query($sql_count_items, "could not get sales type");

						$items_count= db_fetch_row($sql_items_count_result);
						echo $items_count[0];?></span>
				</div>
				<!-- /.info-box-content -->
			  </div>
				</a>
			  <!-- /.info-box -->
			</div> <!-- /.col -->
			
			<div class="col-md-4 col-sm-6 col-xs-12">
			  <div class="info-box">
				<span class="info-box-icon bg-purple"><i class="fa fa-mail-forward"></i></span>

				<div class="info-box-content">
				  <span class="info-box-text"><?php echo _("Payables"); ?></span>
				  <span class="info-box-number"><?php echo $ShowPayables;?></span>
				</div>
				<!-- /.info-box-content -->
			  </div>
			  <!-- /.info-box -->
			</div> <!-- /.col -->
			
			<div class="col-md-4 col-sm-6 col-xs-12">
			  <div class="info-box">
				<span class="info-box-icon bg-orange"><i class="fa fa-mail-reply"></i></span>

				<div class="info-box-content">
				  <span class="info-box-text"><?php echo _("Receivables"); ?></span>
				  <span class="info-box-number"><?php echo $ShowReceivables;?></span>
				</div>
				<!-- /.info-box-content -->
			  </div>
			  <!-- /.info-box -->
			</div> <!-- /.col -->
      </div> <!-- /.row -->

	  	<div class="row"> 
		<div class="col-md-6">
			<div class="box box-info">
				<div class="box-header with-border">
				  <h3 class="box-title"><?php echo _("Class Balances"); ?> </h3>

				  <div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
					<!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
				  </div>
				</div>
				<div class="box-body chart-responsive">
					<?php echo '<div class="row" ><div class="col-md-4"> </div><div class="col-md-8" > <select id="ClassPeriods" class="form-control"> <option value="Till Now">'._("Till Now").'</option><option value="Last Week">'._("Last Week").'</option><option value="Last Month">'._("Last Month").'</option> <option value="This Month">'._("This Month").'</option> <option value="Last Quarter Year">'._("Last Quarter Year").'</option></select></div> </div> '; ?>
				  <div class="chart" id="Class_Balance_chart" style="height: 300px; position: relative;"></div>
				</div>
				<!-- /.box-body -->
			</div>
		</div>
		
		<div class="col-md-6">
			<div class="box box-success">
				<div class="box-header with-border">
				  <h3 class="box-title"><?php echo _("Sales"); ?> </h3>
				  <div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
				  </div>
				</div>
				<div class="box-body chart-responsive">
					<?php echo '<div class="row" ><div class="col-md-4"> </div><div class="col-md-8" > <select id="SalesPeriods" class="form-control"> <option value="Till Now">'._('Till Now').'</option><option value="Last Week">'._('Last Week').'</option><option value="Last Month">'._('Last Month').'</option> <option value="This Month">'._('This Month').'</option> <option value="Last Quarter Year">'._('Last Quarter Year').'</option></select></div> </div> '; ?>
				  <div class="chart" id="Area_chart" style="height: 300px; position: relative;"></div>
				</div>
				<!-- /.box-body -->
			</div>
		</div>
	</div>
	
	
	<div class="row"> 
		<div class="col-md-6">
			<div class="box box-info">
				<div class="box-header with-border">
				  <h3 class="box-title"><?php echo _("Customers"); ?> </h3>

				  <div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
					<!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
				  </div>
				</div>
				<div class="box-body chart-responsive">
					<?php echo '<div class="row" ><div class="col-md-4"> </div><div class="col-md-8" > <select id="CustomerPeriods" class="form-control"> <option value="Till Now">'._('Till Now').'</option><option value="Last Week">'._('Last Week').'</option><option value="Last Month">'._('Last Month').'</option> <option value="This Month">'._('This Month').'</option> <option value="Last Quarter Year">'._('Last Quarter Year').'</option></select></div> </div> '; ?>
				  <div class="chart" id="donut-customer" style="height: 300px; position: relative;"></div>
				</div>
				<!-- /.box-body -->
			</div>
		</div>
		
		<div class="col-md-6">
			<div class="box box-info">
				<div class="box-header with-border">
				  <h3 class="box-title"><?php echo _("Suppliers"); ?> </h3>
				  <div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
				  </div>
				</div>
				<div class="box-body chart-responsive">
					<?php echo '<div class="row" ><div class="col-md-4"> </div><div class="col-md-8" > <select id="SupplierPeriods" class="form-control"> <option value="Till Now">'._('Till Now').'</option><option value="Last Week">'._('Last Week').'</option><option value="Last Month">'._('Last Month').'</option> <option value="This Month">'._('This Month').'</option> <option value="Last Quarter Year">'._('Last Quarter Year').'</option></select></div> </div> '; ?>
				  <div class="chart" id="donut-supplier" style="height: 300px; position: relative;"></div>
				</div>
				<!-- /.box-body -->
			</div>
		</div>
	</div>
	
	<div class="row"> 
		<div class="col-md-6">
			<div class="box box-danger">
				<div class="box-header with-border">
				  <h3 class="box-title"><?php echo _("Expenses"); ?> </h3>

				  <div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
					<!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
				  </div>
				</div>
				<div class="box-body chart-responsive">
					<?php echo '<div class="row" ><div class="col-md-4"> </div><div class="col-md-8" > <select id="ExpensesPeriods" class="form-control"> <option value="Till Now">'._('Till Now').'</option><option value="Last Week">'._('Last Week').'</option><option value="Last Month">'._('Last Month').'</option> <option value="This Month">'._('This Month').'</option> <option value="Last Quarter Year">'._('Last Quarter Year').'</option></select></div> </div> '; ?>
				  <div class="chart" id="expenses_chart" style="height: 300px; position: relative;"></div>
				</div>
				<!-- /.box-body -->
			</div>
		</div>
		
		<div class="col-md-6">
			<div class="box box-danger">
				<div class="box-header with-border">
				  <h3 class="box-title"><?php echo _("Taxes"); ?> </h3>
				  <div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
				  </div>
				</div>
				<div class="box-body chart-responsive">
					<?php echo '<div class="row" ><div class="col-md-4"> </div><div class="col-md-8" > <select id="TaxPeriods" class="form-control"> <option value="Till Now">'._('Till Now').'</option><option value="Last Week">'._('Last Week').'</option><option value="Last Month">'._('Last Month').'</option> <option value="This Month">'._('This Month').'</option> <option value="Last Quarter Year">'._('Last Quarter Year').'</option></select></div> </div> '; ?>
				  <div class="chart" id="donut-Taxes" style="height: 300px; position: relative;"></div>
				</div>
				<!-- /.box-body -->
			</div>
		</div>
	</div><!-- /.row -->
	
	
	<?php 
	// Payments
		 $Sales_payment_5 = Top_five_invoices(12); 
		 $payment_lines= '';
		 foreach($Sales_payment_5 as $payment){
			$payment_lines .='<tr><th scope="row">'.$payment['reference'].'</th> <td>'.$payment['name'].'</td> <td><span class="label-info" style="padding:0 5px;">'.$payment['curr_code'].'</span></td> <td style="text-align: right">'.price_format($payment['TotalAmount'], user_price_dec()).'</td> </tr>'; 
		 }
		echo'<div class="col-sm-6" id="item-16"><div class="box box-primary"> <div class="box-header with-border" data-background-color="orange"><h3 class="box-title">'._('Payments').'</h3><div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
								  </div></div><div class="box-body table-responsive"> <table class="table "><thead> <tr> <th>#</th><th>'._('Name').'</th> <th>'._('Currency').'</th> <th>'._('Total Amount').'</th></tr> </thead><tbody>'.$payment_lines.'</tbody></table></div></div></div> '; 
		//Sales Invoice
		$Sales_invoice_5 = Top_five_invoices(); 
		$invoice_lines = ''; 
		foreach($Sales_invoice_5 as $invoice){
			$invoice_lines .= '<tr><th scope="row">'.$invoice['reference'].'</th> <td>'.$invoice['name'].'</td> <td><span class="label-info" style="padding:0 5px;">'.$invoice['curr_code'].'</span></td> <td style="text-align: right;">'.price_format($invoice['TotalAmount'], user_price_dec()).'</td> </tr>';
		}
		echo'<div class="col-sm-6" id="item-16"><div class="box box-success"> <div class="box-header with-border" data-background-color="orange"><h3 class="box-title">'._('Sales Invoices').'</h3><div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
								  </div></div><div class="box-body table-responsive"><table class="table "><thead> <tr> <th>#</th><th>'._('Name').'</th> <th>'._('Currency').'</th> <th>'._('Total Amount').'</th></tr> </thead><tbody>'.$invoice_lines.'</tbody></table></div></div></div>';
		 ?>
					<!--<div class="row"> -->
						<div class="col-md-6 col-sm-12">
							
							<div class="box box-danger">
								<div class="box-header with-border">
								  <h3 class="box-title"><?php echo _("Overdue Sales Invoices"); ?> </h3>
								  <div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
								  </div>
								</div>
								<div class="box-body chart-responsive">
									<?php  kv_customer_trans(); 	?>
								</div>
								<!-- /.box-body -->
							</div>
						</div>

						<div class="col-md-6 col-sm-12">
							<div class="box box-info">
								<div class="box-header with-border">
								  <h3 class="box-title"><?php echo _("Bank Account Balances"); ?></h3>
								  <div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
								  </div>
								</div>
								<div class="box-body chart-responsive">
									<?php  kv_bank_balance(); 	?>
								</div>
								<!-- /.box-body -->
							</div>
						</div>
					<!--</div>

					<div class="row">-->
						<div class="col-md-6 col-sm-12">
							<div class="box box-danger">
								<div class="box-header with-border">
								  <h3 class="box-title"><?php echo _("Overdue Recurrent Sales Invoices"); ?></h3>
								  <div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
								  </div>
								</div>
								<div class="box-body chart-responsive">
									<?php  kv_customer_recurrent_invoices(); 	?>
								</div>
								<!-- /.box-body -->
							</div>
						</div>

						<div class="col-md-6 col-sm-12">
							<div class="box box-success">
								<div class="box-header with-border">
								  <h3 class="box-title"><?php echo _("Average Daily Sales"); ?></h3>
								  <div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
								  </div>
								</div>
								<div class="box-body chart-responsive">
									<?php  kv_weekly_sales(); 	?>
								</div>
								<!-- /.box-body -->
							</div>
						</div>
					<!--</div>

					<div class="row"> -->
						
						<div class="col-md-6 col-sm-12">
							<div class="box box-danger">
								<div class="box-header with-border">
								  <h3 class="box-title"><?php echo _("Overdue Purchase Invoices"); ?></h3>
								  <div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
								  </div>
								</div>
								<div class="box-body chart-responsive">
									<?php  kv_supplier_trans(); 	?>
								</div>
								<!-- /.box-body -->
							</div>							
						</div>
						<div class="col-md-6 col-sm-12">
							<div class="box box-info">
								<div class="box-header with-border">
								  <h3 class="box-title"><?php echo _("Top 10 Selling Items and Inventory"); ?></h3>
								  <div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
								  </div>
								</div>
								<div class="box-body chart-responsive">
									<?php  kv_stock_top(); 	?>
								</div>
								<!-- /.box-body -->
							</div>								
						</div>
						
						<div class="col-md-6 col-sm-12">
							<div class="box box-info">
								<div class="box-header with-border">
								  <h3 class="box-title"><?php echo _("Most Stagnant Items"); ?></h3>
								  <div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
								  </div>
								</div>
								<div class="box-body chart-responsive">
									<?php $th = array(_("Item Code"), _("Description"),_("Stock"), _("Sales Qty"), _("Last Sale"));

									start_table(TABLESTYLE, "width='100%'");
									table_header($th);
									$stagnants = most_stagnant_items(Today()); 
									if(count($stagnants)>0){
										foreach($stagnants as $single) {
											echo '<tr><td>'.$single[0].'</td><td>'.$single[1].'</td> <td> '.$single[2].'</td><td>'.$single[3].'</td><td> '.sql2date($single[4]).'</td></tr>';
										}
									} else {
										start_row();
										echo '<td colspan="5"> No Stagnant items </td>';
										end_row();
									}
									end_table(); ?>
								</div>
								<!-- /.box-body -->
							</div>								
						</div>
					<!-- </div>

					<div class="row"> -->
						<div class="col-md-6 col-sm-12">
							<div class="box box-success">
								<div class="box-header with-border">
								  <h3 class="box-title"><?php echo _("Class Balances"); ?> </h3>
								  <div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
								  </div>
								</div>
								<div class="box-body chart-responsive">
									<?php  kv_gl_top(); 	?>
								</div>
								<!-- /.box-body -->
							</div>								
						</div>
						

						<div class="col-md-6 col-sm-12">
							<!--<div class="card">
	                            <div class="card-header" data-background-color="orange">
	                                <h4 class="title">Top 10 Selling Items and Inventory</h4>
	                            </div>
	                            <div class="card-content table-responsive">
	                            	<?php  //kv_stock_top(); 	?>
	                            </div>
	                        </div> -->
						</div>
						</div>
			<!-- </div> -->		
    </section>

	<?php 

	$protocol = $protocol = $_SERVER['REQUEST_SCHEME'] . '://'; //stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
	$actual_link = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

	$actual_link =  strtok($actual_link, '?');
	$actual_link = str_replace("index.php", "", $actual_link);
	?>

	<script>

	if($("#donut-Taxes").length){ //  #################  
		var Tax_Donut_Chart = Morris.Donut({
			element: 'donut-Taxes',
			behaveLikeLine: true,
			parseTime : false,
			data: [{"value":"","label":"", labelColor: '<?php echo $color_scheme; ?>'}],  
			colors: [ '#f26c4f', '#00a651', '#00bff3', '#0072bc','#ff6264', '#455064',  '#707f9b', '#b92527',  '#242d3c', '#d13c3e', '#d13c3e',  '#ff6264', '#ffaaab', '#b92527'],
			redraw: true,
		});
		$("#TaxPeriods").on("change", function(){
			var option = $(this).val(); 
			$.ajax({
				type: "POST",
				url: "<?php echo $actual_link; ?>themes/LTE/includes/ajax.php?Tax_chart="+option,
				data: 0,
				dataType: 'json',
				success: function(taxdata){      
						//var grandtotal = data.grandtotal;	 // delete data.grandtotal;	  //delete data[4];
						console.log(taxdata);
					Tax_Donut_Chart.setData(taxdata);
						// var arr = $.parseJSON(data);  //alert(data.grandtotal);	  //$("#GrandTaxTotal").html(grandtotal);
					/* setCookie('numbers',data,3); $('.flash').show(); $('.flash').html("Template Updated")*/
				}
			});
		}); 
 	}

 	if($("#expenses_chart").length){ //   ######### 
	var barColors_  = ['#f26c4f', '#00a651', '#00bff3', '#0072bc', '#707f9b', '#455064', '#242d3c', '#b92527', '#d13c3e', '#ff6264', '#ffaaab'];
	var Expenses_Bar_Chart = Morris.Bar({
	  element: 'expenses_chart',
	  behaveLikeLine: true,
	  parseTime : false,
	  data: [{ "y": "<?php echo _('Nothing'); ?>" , "a" : "0", "labelColor": '<?php echo $color_scheme; ?>'} ],
	  xkey: 'y',
	  ykeys: ['a'],
	  labels: ['<?php echo _("Expenses"); ?>' ],
	  barColors: function (row, series, type) {
        return barColors_[row.x];
    }, 
	  redraw: true
	});


	 	$("#ExpensesPeriods").on("change", function(){
			var option = $(this).val(); 
			$.ajax({
					type: "POST",
					url: "<?php echo $actual_link; ?>themes/LTE/includes/ajax.php?Expense_chart="+option,
					data: 0,
					dataType: 'json',
					success: function(data){
						console.log(data);
						Expenses_Bar_Chart.setData(data);
					/* setCookie('numbers',data,3); $('.flash').show(); $('.flash').html("Template Updated")*/
					}
		  });
	  }); 
  	}

    //  ##########################################
   if($("#Class_Balance_chart").length){
	var arrColors = ['#f26c4f', '#00a651', '#00bff3', '#0072bc', '#707f9b', '#455064', '#242d3c', '#b92527', '#d13c3e', '#ff6264', '#ffaaab'];
      var Line_Chart =	Morris.Bar({
        element: 'Class_Balance_chart',
        behaveLikeLine: true,
        parseTime : false,
        data: [ <?php foreach($class_balances as $balance) { echo " { class: '".$balance['class_name']."', value: ".abs($balance['total'])." }," ; } ?> ],    
        xkey: 'class',
        ykeys: ['value'],
        labels: ['Value'],
        barColors: function (row, series, type) {
			return arrColors[row.x];
		}, 
	
        redraw: true,
      });

      $("#ClassPeriods").on("change", function(){
         var type = $(this).val(); 
         $.ajax({
            type: "POST",
            url: "<?php echo $actual_link; ?>themes/LTE/includes/ajax.php?Line_chart="+type,
            data: 0,
            dataType: 'json',
            success: function(data){    console.log(data);
              Line_Chart.setData(data);
              /* setCookie('numbers',data,3); $('.flash').show(); $('.flash').html("Template Updated")*/
            }
        });
      }); 
   }	

	if($("#Area_chart").length){//  ################   
		var Area_chart = Morris.Area({
		element: 'Area_chart',  
		behaveLikeLine: true,
		parseTime : false, 
		data: [ ],
		xkey: 'y',
		ykeys: ['a', 'b'],
		labels: ['Sales', 'Cost'],
		pointFillColors: ['#707f9b'],
		pointStrokeColors: ['#ffaaab'],
		lineColors: ['#f26c4f', '#00a651', '#00bff3'],
		redraw: true      
		});

		$("#SalesPeriods").on("change", function(){
		var selected_user_ID = $(this).val(); 
		$.ajax({
				type: "POST",
				url: "<?php echo $actual_link; ?>themes/LTE/includes/ajax.php?Area_chart="+selected_user_ID,
				data: 0,
				dataType: 'json',
				success: function(data){
				console.log(data);
				Area_chart.setData(data);
				}
			});
	}); 
	}

	if($("#donut-customer").length){//  #################  
		var Customer_Donut_Chart = Morris.Donut({
		element: 'donut-customer',
		behaveLikeLine: true,
		parseTime : false,
		data: [{"value":"","label":"", "labelColor": '<?php echo $color_scheme; ?>'}],
		colors: ['#f26c4f', '#00a651', '#00bff3', '#0072bc', '#707f9b', '#455064', '#242d3c', '#b92527', '#d13c3e', '#ff6264', '#ffaaab'],
		redraw: true,
		});
		$("#CustomerPeriods").on("change", function(){
			var option = $(this).val(); 
			$.ajax({
				type: "POST",
				url: "<?php echo $actual_link; ?>themes/LTE/includes/ajax.php?Customer_chart="+option,
				data: 0,
				dataType: 'json',
				success: function(data){
				console.log(data);
				Customer_Donut_Chart.setData(data);
				/* setCookie('numbers',data,3); $('.flash').show(); $('.flash').html("Template Updated")*/
				}
			});
		});
	}

	if($("#donut-supplier").length){ //  #################  
	var Supplier_Donut_Chart = Morris.Donut({
	element: 'donut-supplier',
	behaveLikeLine: true,
	parseTime : false,
	data: [{"value":"","label":"", "labelColor": '<?php echo $color_scheme; ?>'}],  
	colors: [  '#ff6264', '#455064', '#d13c3e', '#d13c3e',  '#ff6264', '#ffaaab', '#f26c4f', '#00a651', '#00bff3', '#0072bc', '#b92527', '#707f9b', '#b92527',  '#242d3c'],
	redraw: true,
	});
	$("#SupplierPeriods").on("change", function(){
		var option = $(this).val(); 
		$.ajax({
			type: "POST",
			url: "<?php echo $actual_link; ?>themes/LTE/includes/ajax.php?Supplier_chart="+option,
			data: 0,
			dataType: 'json',        
			success: function(data){
			console.log(data);
			Supplier_Donut_Chart.setData(data);
			/* setCookie('numbers',data,3); $('.flash').show(); $('.flash').html("Template Updated")*/
			}
		});
	}); 
	}

 // $(document).ready(function(e){
	 
      $("#SalesPeriods").trigger("change");
      $("#CustomerPeriods").trigger("change");
      $("#SupplierPeriods").trigger("change");
      $("#ExpensesPeriods").trigger("change");
      $("#TaxPeriods").trigger("change");
 // }); 
 </script>
 <div style="clear:both;" > </div>
<?php } else { 

echo '<div style="line-height:200px; text-align:center;font-size:24px; vertical-align:middle;" > '._('Page not found').' </div>'; 

} ?>