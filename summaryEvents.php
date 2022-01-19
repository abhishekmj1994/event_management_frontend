<?php
	include("config.php");
	session_start();
	error_reporting(1);
	if(isset($_SESSION['empid']) && $_SESSION['power'] == 2){
		if(isset($_POST['search_incidences']))
			{
				$Date_1 = date('Ymd',strtotime($_POST['Date_1']));
				$Date_2 = date('Ymd',strtotime($_POST['Date_2']));
				header("location: summary_events.php?Date_1=$Date_1&&Date_2=$Date_2");
			}
			$date1=$_GET['Date_1'];
			$date2=$_GET['Date_2'];
			if($date1==''){$date1=date("Ymd",strtotime("-7 Days",strtotime(date("Ymd"))));}
			if($date2==''){$date2=date("Ymd");}
			$date_1_post= date('Y-m-d',strtotime($date1));
			$date_2_post= date('Y-m-d',strtotime($date2));
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<!--<meta http-equiv="refresh" content="30">-->
    <title>EventPortal | Dashboard </title>
	<?php include("Cssplugins.php"); ?>
    <!-- Bootstrap -->
	<div id="showOutput" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<form method="POST">
					<div class="modal-header">
						<h4 class="panel-heading modal-title"><span class="btn btn-info" style="color:white">See Command Output <span id="command_given"></span></h4>
					</div>
					<div class="modal-body">
						<pre class="disabled populateOutput"></pre>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button></span>
					</div> 
				</form>
			</div>
		</div>
	</div>
  </head>
  <body class="nav-sm blue">
    <div class="container body">
      <div class="main_container">
	  
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="createEvent.php" class="site_title"><i class="fa fa-dashboard"></i> <span class="sessionUSer">WELCOME !</span></a>
            </div>
            <div class="clearfix"></div>
            <!-- menu -->
            <div class="profile clearfix">
              <div class="profile_pic">
               <!-- <img src="images/profile.jpg" alt="..." class="img-circle profile_img">-->
              </div>
              <div class="profile_info">
                <span>Welcome Admin</span>
                <h2 class="SessionUser"></h2>
              </div>
            </div>
            <br />
			<?php include("includes/sidebar.php");?>	
          </div>
        </div>
        <?php include("includes/header.php");?>
        <div class="right_col" role="main">
			<div class="">
				<div class="row">
				
					<div class="col-md-6">
						<div class="x_panel">
						  <div class="x_title">
							<h2 class="red"> Sort Events as per Date</h2>
							<div class="clearfix"></div>
						  </div>
						  <form method="post" >
							<table id="eventPortal" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th class="text-center"><i class="fa fa-calendar-o text-red"></i> Select Start Date </th>
										<th class="text-center"><i class="fa fa-calendar-o text-red"></i> Select End Date </th>
										<th class="text-center"><i class="fa fa-circle-o text-blue"></i> Preview</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="text-center"><input type="date" class="form-control" name="Date_1" style="width:300px" value="<?php echo $date_1_post; ?>"></td>
										<td class="text-center"><input type="date" class="form-control" name="Date_2" style="width:300px" value="<?php echo $date_2_post; ?>"></td>
										<td class="text-center"><input type="submit" class="btn btn-success" align="right" name="search_incidences" value="Submit" ></td>
									</tr>
								</tbody>
							</table>
							</form >
						</div>
					</div>
					<?php if($date1!='' && $date2!='' ){
						$currdate=strtotime(date('Ymd'));
						$lesserdate=strtotime($date1);
						$greaterdate=strtotime($date2);
						if($lesserdate<=$currdate && $greaterdate<=$currdate){
					?>
					<div class="col-md-6">
						<div class="x_panel">
							<div class="x_title">
							<h2 class="red">Event Tickets for date  <?php  echo "<span style=color:black>".$date1 ."</span>  and <span style=color:black>".  $date2 ;  ?></h2>
							<div class="clearfix"></div>
							</div>
							<div class="panel-body table-responsive">
								<table class="table table-bordered table-striped" >
									<thead>
										<tr>
											<th class="text-center">Open</th>
											<th class="text-center">WIP</th>
											<th class="text-center">Self Closed </th>
											<th class="text-center">Manually Closed </th>
											<th class="text-center">Total Closed</th>
										</tr>   
									</thead>
									<tbody>
										<?php 
											$queryW=oci_parse($con,"select count(*) as num from event_master_testing where EDATE between '$date1' and '$date2' and EVENT_STATUS='OPEN'");
											oci_execute($queryW);
											$resultW=oci_fetch_assoc($queryW);
											
											$queryW1=oci_parse($con,"select count(*) as num from events_tickets where EDATE between '$date1' and '$date2' and EVENT_STATUS='WIP' ");
											oci_execute($queryW1);
											$resultW1=oci_fetch_assoc($queryW1);
											
											$queryW2=oci_parse($con,"select count(*) as num from event_master_testing where EDATE between '$date1' and '$date2' and EVENT_STATUS='CLOSE' ");
											oci_execute($queryW2);
											$resultW2=oci_fetch_assoc($queryW2);
											
											$queryW3=oci_parse($con,"select count(*) as num  from event_master_testing where EDATE between '$date1' and '$date2' and EVENT_STATUS='CLOSE' ");
											oci_execute($queryW3);
											$resultW3=oci_fetch_assoc($queryW3);
											
											$queryW4=oci_parse($con,"select count(*) as num  from events_tickets where EDATE between '$date1' and '$date2' and EVENT_STATUS='CLOSE' ");
											oci_execute($queryW4);
											$resultW4=oci_fetch_assoc($queryW4);
											
											$result_selfclosed=array();
											$result_selfclosed=$resultW3['NUM']-$resultW4['NUM'];
											$canvasData2=array($resultW['NUM'],$resultW1['NUM'],$result_selfclosed,$resultW4['NUM']);
										?>
										<tr>
											<td class="text-center"><?php echo $resultW['NUM']; ?></td>
											<td class="text-center"><?php echo $resultW1['NUM']; ?></td>
											<td class="text-center"><?php echo $result_selfclosed; ?></td>
											<td class="text-center"><?php echo $resultW4['NUM']; ?></td>
											<td class="text-center"><?php echo $resultW2['NUM']; ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div> 
				<!---	<div class="col-md-12" >
						<div class="x_panel">
							<div class="x_title">
							<h2 class="red">Event ticket Status for Date <?php  echo "<span style=color:black>".$date1 ."</span>  and <span style=color:black>".  $date2 ;  ?> </h2>
							<div class="clearfix"></div>
							</div>
							<canvas  class="canvasDoughnut2" height="300" width="800" style="margin: 5px 10px 10px 0"></canvas>
							<canvas style="margin-left:100px;" class="canvasDoughnut4" height="300" width="300" style="margin: 5px 10px 10px 0"></canvas>
							<span class="btn btn-xs btn-success">Ticket status</span><canvas style="margin-left:100px;" class="canvasDoughnut5" height="300" width="300" style="margin: 5px 10px 10px 0"></canvas>
						</div>
					</div>-->
					
					
					
					<div class="col-md-12">
						<div class="x_panel">
							  <div class="x_title">
							  <h2 class="red">Event ticket Status for Date <?php  echo "<span style=color:black>".$date1 ."</span>  and <span style=color:black>".  $date2 ;  ?></h2>
							  <div class="clearfix"></div>
							</div>
							<div class="x_content">
							  <table class="table table-bordered table-striped" style="width:100%">
								<tr>
									<th>
										<p class="btn btn-primary">Horizontal Bar graph</p>
									</th>
									<th>
										<p class="btn btn-primary">Pie Chart</p>
									</th>
									<th>
										<p class="btn btn-primary">Line graph</p>
									</th>
									<th>
										<p class="btn btn-primary">Legends</p>
									</th>
								</tr>
								<tr>
									<td>
										<canvas  class="canvasDoughnut2" height="300" width="500" style="margin: 5px 10px 10px 0"></canvas>
									</td>
									<td>
										<canvas style="margin-left:100px;" class="canvasDoughnut4" height="280" width="280" style="margin: 5px 10px 10px 0"></canvas>
									</td>
									<td>
										<canvas style="margin-left:100px;" class="canvasDoughnut5" height="300" width="300" style="margin: 5px 10px 10px 0"></canvas>
									</td>
									
									<td >	
										<div style="margin-left:10px;"><p> <i class="fa fa-2x fa-square" style="color:#E74C3C;"></i><b> Open - <?php echo $resultW['NUM']; ?></b></p>
											 <p> <i class="fa fa-2x fa-square" style="color:#d0ea08;"></i><b> WIP - <?php echo $resultW1['NUM']; ?></b></p>
											<p> <i class="fa fa-2x fa-square" style="color:#09d4cc;"></i> <b>Self Closed - <?php echo $result_selfclosed; ?></b></p>
											<p> <i class="fa fa-2x fa-square" style="color:#38af2b;"></i> <b>Manually Closed - <?php echo $resultW4['NUM']; ?></b></p>
										</div>
									</td>
								</tr>
								
							  </table>
							</div>
						  </div>
						</div>
					
					
					
					
					
					<div class="col-md-6">
						<div class="x_panel">
							<div class="x_title">
							<h2 class="red">Event Tickets Datewise </h2>
							<div class="clearfix"></div>
							</div>
							<div class="panel-body table-responsive">
								<table class="table table-bordered table-striped"  >
									<thead>
										<tr>
											<th class="text-center">Date</th>
											<th class="text-center">Open</th>
											<th class="text-center">WIP</th>
											<th class="text-center">Self Closed </th>
											<th class="text-center">Manually Closed </th>
											<th class="text-center">Total Closed</th>
										</tr>   
									</thead>
									<tbody>
										<?php 
											
											$queryW4=oci_parse($con,"select count(*) as num,EDATE,EVENT_STATUS from events_tickets where EDATE between '$date1' and '$date2' group by EVENT_STATUS ,EDATE UNION ALL select count(*) as num,EDATE,EVENT_STATUS from event_master_testing where EDATE between '$date1' and '$date2' group by EVENT_STATUS ,EDATE");
											oci_execute($queryW4);										
											while($resultW4=oci_fetch_assoc($queryW4)) { $x[] = $resultW4; }
											
											$duration=(strtotime($date2)-strtotime($date1))/86400;
											$curdate=$date1;
											for($i=0;$i<=$duration;$i++){
												foreach($x as $data){
													if($data['EDATE']==$curdate && $data['EVENT_STATUS']=='CLOSE'){
														$minus[$curdate][]=$data['NUM'];
													}
													elseif($data['EDATE']==$curdate && $data['EVENT_STATUS']=='OPEN'){
														$minusopen[$curdate]['OPEN']=$data['NUM'];
													}
													elseif($data['EDATE']==$curdate && $data['EVENT_STATUS']=='WIP'){
														$minuswip[$curdate]['WIP']=$data['NUM'];
													}
												}
												if($minus[$curdate][1]!=''){
													$close[$curdate]['SELF_CLOSE']=intval(abs($minus[$curdate][0]-$minus[$curdate][1]));
													$close[$curdate]['MANUALLY_CLOSE']=intval($minus[$curdate][0]);
													$close[$curdate]['OPEN']=intval($minusopen[$curdate]['OPEN']);
													$close[$curdate]['WIP']=intval($minuswip[$curdate]['WIP']);
													$close[$curdate]['TOTAL_CLOSE']=intval($close[$curdate]['SELF_CLOSE']+$close[$curdate]['MANUALLY_CLOSE']);
												}
												else{
													$close[$curdate]['SELF_CLOSE']=intval($minus[$curdate][0]);
													$close[$curdate]['MANUALLY_CLOSE']=0;
													$close[$curdate]['OPEN']=intval($minusopen[$curdate]['OPEN']);
													$close[$curdate]['WIP']=intval($minuswip[$curdate]['WIP']);
													$close[$curdate]['TOTAL_CLOSE']=intval($close[$curdate]['SELF_CLOSE']);
												}
												$curdate=date('Ymd',strtotime('+1 day',strtotime($curdate)));
											}
											//print_r($close);
											foreach($close as $index=>$data){
												echo "<tr>";
													echo "<td class='text-center chartdata'><button class='btn btn-xs btn-success' data-toggle='tooltip' title='Click here to see the graph'>".$index."</button></td>";
													echo "<td class='text-center'>".$data['OPEN']."</td>";
													echo "<td class='text-center'>".$data['WIP']."</td>";
													echo "<td class='text-center'>".$data['SELF_CLOSE']."</td>";
													echo "<td class='text-center'>".$data['MANUALLY_CLOSE']."</td>";
													echo "<td class='text-center'>".$data['TOTAL_CLOSE']."</td>";
												echo "</tr>";
												$canvasData1=array($data['OPEN'],$data['WIP'],$data['SELF_CLOSE'],$data['MANUALLY_CLOSE']);
											}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div> 
					
					<!--<div class="col-md-6"  id ="seegraph" style="display:none;">
						<div class="x_panel">
							<div class="x_title">
							<h2 class="red">Event Ticket Status Date wise <span style="color:green;"class ="dateonhover"> </span></h2>
							<div class="clearfix"></div>
							</div>
							<span class ="btn btn-primary"><canvas style="margin-left:30px;" class="canvasDoughnut1" height="290" width="290" ></canvas>
							<span class="btn btn-xs btn-success">Ticket status</span><canvas style="margin-left:30px;" class="canvasDoughnut3" height="300" width="400" ></canvas></span>
						</div>
					</div>--->
					
					
					
					
			<div class="col-md-6 " id ="seegraph" style="display:none;">
            <div class="x_panel"  style="height:450px;">
                  <div class="x_title">
                  <h2 >Event Ticket Status Date wise <span style="color:green;"class ="dateonhover"> </span></h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <table class="table table-bordered table-striped" style="width:100%">
                    <tr>
						<th>
							<p class="btn btn-primary">Pie Chart</p>
						</th>
                        <th>
							<p class="btn btn-primary">Bar graph</p>
						</th>
						<th>
							<p class="btn btn-primary">Legends</p>
						</th>
                    </tr>
                    <tr>
						<td>
							<canvas class="canvasDoughnut1" height="280" width="280" style="margin: 15px 10px 10px 0"></canvas>
						</td>
						<td>
							<canvas class="canvasDoughnut3" height="280" width="280" style="margin: 10px 9px 9px 0"></canvas>
						</td>
						
						<td>	
							<div style="margin-left:10px;"><p> <i class="fa fa-2x fa-square" style="color:#E74C3C;"></i><b> Open - <span class="valueTicket1"></span></b></p>
								 <p> <i class="fa fa-2x fa-square" style="color:#d0ea08;"></i><b> WIP - <span class="valueTicket2"></span></b></p>
								<p> <i class="fa fa-2x fa-square" style="color:#09d4cc;"></i><b> Self Closed - <span class="valueTicket3"></span></b></p>
								<p> <i class="fa fa-2x fa-square" style="color:#38af2b;"></i><b> Manually Closed - <span class="valueTicket4"></span></b></p>
							</div>
						</td>
					</tr>
					
                  </table>
                </div>
              </div>
            </div>
					<?php }
					
					else
						echo "<script>alert('!!! No data available for this date !!!....Kindly choose the correct date');window.location.href=window.location.origin+window.location.pathname;</script>";
				}   ?>
					
					
				</div> 
			</div>
		</div>
	  </div>
	</div>
        <!-- /page content -->
		<?php include("includes/footer.php"); ?>
      </div>
    </div>
	<?php include("Jsplugins.php"); ?>
	
	<script type="text/javascript">
	
	function init_chart_pie(array1){
		console.log(array1);
		if( typeof (Chart) === 'undefined'){ return; }
		
		//console.log('init_chart_doughnut');
	 
		if ($('.canvasDoughnut1').length){
			
		var chart_doughnut_settings = {
				type: 'pie',
				tooltipFillColor: "rgba(51, 51, 51, 0.55)",
				data: {
					labels: [
						"OPEN",
						"WIP",
						"Self Closed",
						"Manually Closed"
					],
					datasets: [{
						data: array1,
						backgroundColor: [
							"#E74C3C",
							"#d0ea08",
							"#09d4cc",
							"#38af2b"
						],
						hoverBackgroundColor: [
							"#FFF",
							"#FFF",
							"#FFF",
							"#FFF"
						]
					}]
				},
				options: { 
					legend: true,
					events: false,
					responsive: false 
					
				}
			}
		
			$('.canvasDoughnut1').each(function(){
				
				var chart_element = $(this);
				var chart_doughnut = new Chart( chart_element, chart_doughnut_settings);
				
			});			
		
		}   
	}
	
	
	function init_chart_bar1(array1){
		
		console.log(array1);
		if( typeof (Chart) === 'undefined'){ return; }
		
		//console.log('init_chart_doughnut');
	 
		if ($('.canvasDoughnut3').length){
			
		var chart_bar_settings = {
				type: 'bar',
				tooltipFillColor: "rgba(51, 51, 51, 0.55)",
				data: {
					labels: [
						"OPEN",
						"WIP",
						"Self Closed",
						"Manually Closed"
					],
					datasets: [{
						data: array1,
						backgroundColor: [
							"#E74C3C",
							"#d0ea08",
							"#09d4cc",
							"#38af2b"
						],
						hoverBackgroundColor: [
							"#FFF",
							"#FFF",
							"#FFF",
							"#FFF"
						]
					}]
				},
				options: { 
						legend: true, 
						events: false,
						responsive: false,
						scales:{
							xAxes:[{ticks:{fontColor:"black"},gridLines:{display:false}}],
							yAxes:[{ticks:{fontColor:"black"},gridLines:{display:false}}]						
						}
				}
			}
		
			$('.canvasDoughnut3').each(function(){
				
				var chart_element1 = $(this);
				var chart_doughnut = new Chart( chart_element1, chart_bar_settings);
				
			});			
		
		}   
	}
	
	
	// chart for bar chart
	
	function init_chart_bar(){
		var array1=JSON.parse('<?php echo json_encode($canvasData2);?>');
		console.log(array1);
		if( typeof (Chart) === 'undefined'){ return; }
		
		//console.log('init_chart_doughnut');
	 
		if ($('.canvasDoughnut2').length){
			
		var chart_bar_settings = {
				type: 'horizontalBar',
				tooltipFillColor: "rgba(51, 51, 51, 0.55)",
				data: {
					labels: [
						"OPEN",
						"WIP",
						"Self Closed",
						"Manually Closed"
					],
					datasets: [{
						data: array1,
						backgroundColor: [
							"#E74C3C",
							"#d0ea08",
							"#09d4cc",
							"#38af2b"
						],
						hoverBackgroundColor: [
							"#FFF",
							"#FFF",
							"#FFF",
							"#FFF"
						]
					}]
				},
				options: { 
					legend: false, 
					responsive: false 
				}
			}
		
			$('.canvasDoughnut2').each(function(){
				
				var chart_element1 = $(this);
				var chart_doughnut = new Chart( chart_element1, chart_bar_settings);
				
			});			
		
		}   
	}
	
	function init_chart_pie1(){
		var array1=JSON.parse('<?php echo json_encode($canvasData2);?>');
		console.log(array1);
		if( typeof (Chart) === 'undefined'){ return; }
		
		//console.log('init_chart_doughnut');
	 
		if ($('.canvasDoughnut4').length){
			
		var chart_doughnut_settings = {
				type: 'doughnut',
				tooltipFillColor: "rgba(51, 51, 51, 0.55)",
				data: {
					labels: [
						"OPEN",
						"WIP",
						"Self Closed",
						"Manually Closed"
					],
					datasets: [{
						data: array1,
						backgroundColor: [
							"#E74C3C",
							"#d0ea08",
							"#09d4cc",
							"#38af2b"
						],
						hoverBackgroundColor: [
							"#FFF",
							"#FFF",
							"#FFF",
							"#FFF"
						]
					}]
				},
				options: { 
					legend: false, 
					responsive: false 
				}
			}
		
			$('.canvasDoughnut4').each(function(){
				
				var chart_element = $(this);
				var chart_doughnut = new Chart( chart_element, chart_doughnut_settings);
				
			});			
		
		}   
	}
	function init_chart_doughnut(){
		var array1=JSON.parse('<?php echo json_encode($canvasData2);?>');
		console.log(array1);
		if( typeof (Chart) === 'undefined'){ return; }
		
		//console.log('init_chart_doughnut');
	 
		if ($('.canvasDoughnut5').length){
			
		var chart_doughnut_settings = {
				type: 'line',
				tooltipFillColor: "rgba(51, 51, 51, 0.55)",
				data: {
					labels: [
						"OPEN",
						"WIP",
						"Self Closed",
						"Manually Closed"
					],
					datasets: [{
						data: array1,
						backgroundColor: [
							"#E74C3C",
							"#d0ea08",
							"#09d4cc",
							"#38af2b"
						],
						borderColor : [
							"red",
						],
						fill: false,
						pointStyle : 'circle',
						pointRadius: 7, 
						pointHoverBackgroundColor : 'black',						
						pointBackgroundColor : 'yellow',						
						hoverBackgroundColor: [
							"#FFF",
							"#FFF",
							"#FFF",
							"#FFF"
						]
					}]
				},
				options: { 
					legend: false, 
					responsive: false 
				}
			}
		
			$('.canvasDoughnut5').each(function(){
				
				var chart_element = $(this);
				var chart_doughnut = new Chart( chart_element, chart_doughnut_settings);
				
			});			
		
		}   
	}
	
	/*$(document).ready(function(){
		var date = $('.chartdata').parent().find('td').eq(0).text();
		var open = $('.chartdata').parent().find('td').eq(1).text();
		var wip = $('.chartdata').parent().find('td').eq(2).text();
		var self = $('.chartdata').parent().find('td').eq(3).text();
		var manual = $('.chartdata').parent().find('td').eq(4).text();
		array1= [open, wip, self, manual];
		init_chart_pie(array1);
		init_chart_bar1(array1);
		$(".dateonhover").text(date);
	})*/
	
	$('.chartdata').click(function(){
		var date = $(this).parent().find('td').eq(0).text();
		var open = $(this).parent().find('td').eq(1).text();
		var wip = $(this).parent().find('td').eq(2).text();
		var self = $(this).parent().find('td').eq(3).text();
		var manual = $(this).parent().find('td').eq(4).text();
		$(".dateonhover").text(date);
		$(".valueTicket1").text(open);
		$(".valueTicket2").text(wip);
		$(".valueTicket3").text(self);
		$(".valueTicket4").text(manual);
		array1= [open, wip, self, manual];	
		init_chart_pie(array1);
		init_chart_bar1(array1);
	})
	
	
	
	$('.chartdata').click(function(){
		$('#seegraph').show();
	})
	

	init_chart_pie1();
	init_chart_doughnut();
	init_chart_bar();
	function toggleFullScreen() {
		if (!document.fullscreenElement &&    // alternative standard method
			!document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {  // current working methods
			if (document.documentElement.requestFullscreen) {
				document.documentElement.requestFullscreen();
			} else if (document.documentElement.msRequestFullscreen) {
				document.documentElement.msRequestFullscreen();
			} else if (document.documentElement.mozRequestFullScreen) {
				document.documentElement.mozRequestFullScreen();
			} else if (document.documentElement.webkitRequestFullscreen) {
				document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
			}
		} else {
			if (document.exitFullscreen) {
				document.exitFullscreen();
			} else if (document.msExitFullscreen) {
				document.msExitFullscreen();
			} else if (document.mozCancelFullScreen) {
				document.mozCancelFullScreen();
			} else if (document.webkitExitFullscreen) {
				document.webkitExitFullscreen();
			}
		}
	}
	</script>
  </body>
</html>
<?php } else{ echo "<script>alert(' !!! Login Again!!! '); window.location.assign('https://10.0.6.125:8080/')</script>";  } ?>
