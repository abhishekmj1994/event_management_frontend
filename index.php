<?php
	include("config.php");
	session_start();
	$mldate = trim(file_get_contents("/opt/hpws/apache/cgi-bin/trials/bip/data/TESTING/MFLAGS_D"));
	if($_GET['date']!=''){
		$mldate=$_GET['date'];
	}
	if(isset($_SESSION['empid']) && $_GET['date'] != ''){
		$crdate = $_GET['date'];
		//close Ticket Logic---------------------------------------------
		
		if(isset($_POST['key']) && $_POST['key'] == 'saveTicket'){
			$EVENT_ID		= $_POST['EVENT_ID'];
			$EVENT_DESC		= $_POST['EVENT_DESC'];
			$ST_TIME		= $_POST['ST_TIME'];
			$NAME           = ucwords($_POST['NAME']);
			$EVENT_STATUS	= $_POST['EVENT_STATUS'];
			$TICKETRAISED	= $_POST['TICKETRAISED'];
			$END_TIME		= ($EVENT_STATUS == 'WIP' || $_POST['END_TIME'] == 'NA') ? 'NULL' :$_POST['END_TIME'] ;
			$SERVER_NAME    = $_POST['SERVER_NAME'];
			$MODULE_NAME	= $_POST['MODULE_NAME'];
			$TICKETCLOSED	= $_POST['TICKETCLOSED'];
			$REMARKS        = $_POST['REMARKS'];
			$EDATE          = date("Ymd",strtotime($_POST['EDATE']));
			if( $_POST['setPutValue'] == 1){
				
				$saveTicket = oci_parse($con,"INSERT INTO EVENTS_TICKETS ( EDATE, ST_TIME, END_TIME, SERVER_NAME, MODULE_NAME, EVENT_ID, EVENT_DESC, EVENT_STATUS, REMARKS, NAME, TICKETRAISED, TICKETCLOSED) VALUES('$EDATE', '$ST_TIME', '$END_TIME', '$SERVER_NAME', '$MODULE_NAME', '$EVENT_ID', '$EVENT_DESC', '$EVENT_STATUS', '$REMARKS', '$NAME','$TICKETRAISED', '$TICKETCLOSED')");
				
				$UpdateEventMaster = oci_parse($con, "Update EVENT_MASTER_TESTING SET EVENT_STATUS='$EVENT_STATUS',END_TIME='$END_TIME' WHERE EVENT_ID='$EVENT_ID' AND  ST_TIME='$ST_TIME' AND SERVER_NAME='$SERVER_NAME' AND MODULE_NAME='$MODULE_NAME'");
				
				oci_execute($saveTicket);
				oci_execute($UpdateEventMaster);
				if($saveTicket && $UpdateEventMaster)
				{
					echo "Record Saved";
				}
				else{
					echo "Record Not Saved !! ";
				}
			}
			elseif($_POST['setPutValue'] == 3){
				$saveTicket = oci_parse($con, "Update EVENTS_TICKETS SET REMARKS = '$REMARKS', EVENT_STATUS='$EVENT_STATUS',TICKETCLOSED='$TICKETCLOSED',END_TIME='$END_TIME' WHERE EVENT_ID='$EVENT_ID' AND  ST_TIME='$ST_TIME' AND SERVER_NAME='$SERVER_NAME' AND MODULE_NAME='$MODULE_NAME'");
				
				$UpdateEventMaster = oci_parse($con, "Update EVENT_MASTER_TESTING SET EVENT_STATUS='$EVENT_STATUS' ,END_TIME='$END_TIME' WHERE EVENT_ID='$EVENT_ID' AND  ST_TIME='$ST_TIME' AND SERVER_NAME='$SERVER_NAME' AND MODULE_NAME='$MODULE_NAME'");
				
				oci_execute($saveTicket);
				oci_execute($UpdateEventMaster);
				if($saveTicket && $UpdateEventMaster)
				{
					echo "Record Saved";
				}
				else{
					echo "Record Not Saved !! ";
				}
				
			}
			exit();
		}
		
		
		
		$ticCloseval = oci_parse($con,"SELECT count(*) as var FROM events_tickets WHERE EVENT_STATUS = 'CLOSE' and EDATE='$crdate'");
		oci_execute($ticCloseval);
		$resultClose = oci_fetch_assoc($ticCloseval);
		$ticWIPval = oci_parse($con,"SELECT count(*) as var FROM events_tickets WHERE EVENT_STATUS = 'WIP' and EDATE='$crdate'");
		oci_execute($ticWIPval);
		$resultWIP = oci_fetch_assoc($ticWIPval);
		$ticOpenval = oci_parse($con,"SELECT count(*) as var FROM EVENT_MASTER_TESTING WHERE EVENT_STATUS = 'OPEN' and EDATE='$crdate'");
		oci_execute($ticOpenval);
		$resultOpen = oci_fetch_assoc($ticOpenval);
		$EventsTickest = oci_parse($con,"SELECT count(*) as var FROM EVENT_MASTER_TESTING WHERE EDATE = '$crdate'");
		oci_execute($EventsTickest);
		$resultEvents = oci_fetch_assoc($EventsTickest);
		
		/*
$Database=oci_parse($con,'SELECT MODULE_NAME as Database,EVENT_SEVERITY FROM event_master where EDATE = \''.$crdate.'\' and EVENT_ID in (\''.rtrim(preg_replace("/\s+/","','",trim($DatabaseCard))).'\')');
*/
$AlertFetchAssoc = oci_parse($con,'SELECT COUNT(*) as count,MODULE_NAME,EVENT_SEVERITY FROM event_master_testing where EDATE = \''.$crdate.'\' and EVENT_STATUS=\'OPEN\' group by MODULE_NAME ,EVENT_SEVERITY order by MODULE_NAME desc');
oci_execute($AlertFetchAssoc);
while($alertFetchData=oci_fetch_assoc($AlertFetchAssoc)){
	$alertFetchData=array_values($alertFetchData);
	$makeArray[$alertFetchData[1]][$alertFetchData[2]]=$alertFetchData[0];
}
foreach($makeArray as $k => $data){
	$getTable.= "<tr class='text-center'>";
	$getTable.= "<td><span class='btn btn-sm btn-round btn-info'>".$k."</span></td>";
	$getTable.= "<td class='getModalAllDetails'><span class='btn btn-sm btn-round btn-danger'>".( ( $data['HIGH'] == '' ) ? 0 : $data['HIGH'] )."</span></td>";
	$getTable.= "<td class='getModalAllDetails'><span class='btn btn-sm btn-round btn-warning'>".( ( $data['MEDIUM'] == '' ) ? 0 : $data['MEDIUM'] )."</span></td>";
	$getTable.= "<td class='getModalAllDetails'><span class='btn btn-sm btn-round btn-primary'>".( ( $data['LOW'] == '' ) ? 0 : $data['LOW'] )."</span></td>";
	$getTable.= "</tr>";
}
if(isset($_POST['key']) && $_POST['key'] = 'showtickets'){
	$edate=$_GET['date'];
	$newArr = array();
	$evensever=$_POST['severitynumber'];
	$modulename=$_POST['module_name'];
	$EVENT_STATUS='OPEN';
	$query=oci_parse($con,'SELECT * FROM EVENT_MASTER_TESTING where EDATE = :edate and EVENT_STATUS= :EVENT_STATUS and MODULE_NAME = :modulename and  EVENT_SEVERITY = :evensever order by ST_TIME DESC');
	oci_bind_by_name($query, ':edate', $edate);
	oci_bind_by_name($query, ':EVENT_STATUS', $EVENT_STATUS);
	oci_bind_by_name($query, ':modulename', $modulename);
	oci_bind_by_name($query, ':evensever', $evensever);
	oci_execute($query);
	$printDataHEad = <<<TABLE
	<table class="table table-bordered table-striped" id="example">
		<thead>
			<tr>
				<th class="text-center">EVENT ID</th>
				<th class="text-center">Start Time</th>
				<th class="text-center">End Time</th>
				<th class="text-center">DATE</th>
				<th class="text-center">DESCRIPTION </th>
				<th class="text-center">SERVER NAME</th>
				<th class="text-center">EVENT SEVERITY</th>
				<th class="text-center">EVENT STATUS</th>
				<th class="text-center">MODULE NAME</th>
				<th class="text-center">OPEN?</th>
			</tr>
		</thead>
	<tbody>
TABLE;
	while($result=oci_fetch_assoc($query)){
		$setDate = date("Y-m-d",strtotime($result['EDATE']));
		$dataSet .= <<<TRROWS
		<tr>
			<td class="text-center">{$result['EVENT_ID']}</td>
			<td class="text-center">{$result['ST_TIME']}</td>
			<td class="text-center">{$result['END_TIME']}</td>
			<td class="text-center">{$setDate}</td>
			<td class="text-center">{$result['EVENT_DESC']}</td>
			<td class="text-center text-uppercase">{$result['SERVER_NAME']}</td>											
			<td class="text-center text-uppercase">{$result['EVENT_SEVERITY']}</td>
			<td class="text-center text-uppercase">{$result['EVENT_STATUS']}</td>
			<td class="text-center text-uppercase" >{$result['MODULE_NAME']}</td>
			<td class="text-center" onclick="putValuesModal(this,1)"><i class="fa fa-2x fa-ticket"></i></td>
		</tr>
TRROWS;
		$i++;
	}
	echo $printDataHEad.$dataSet."</tbody></table>";exit();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="refresh" content="120">
    <title>EventPortal | Dashboard </title>
	
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="css/scroller.bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">    
	<!-- Bootstrap -->
	<div id="dateModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					 <h4 class="panel-heading modal-title ">Enter Date </h4>
				  </div>
				  <div class="modal-body text-center">
					<input type="date" class="form-control" id="ll" onchange="pageRedirection()" required>	<br>
				  </div>
			</div>
		</div>
	</div>
	<div id="showOutput" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg" style="width:80vw !important;">
			<div class="modal-content">
				<form method="POST">
					<div class="modal-header">
						<h4 class="panel-heading modal-title"><span class="btn btn-info" style="color:white">SMART Monitoring</span><span class="btn btn-danger pull-right" style="cursor:none;color:white">Ctrl+F to search</span></h4>
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
	<style>
	.textarea {  
		width: 350px !important;
		height: 150px !important;
	}
	.sm-textarea {  
		width: 175px !important;
		height: 75px !important;
	}
	.glow{
		animation: glowing 1500ms infinite;
	}
	@keyframes glowing{
	0%{background-color:black;box-shadow:0 0 3px black;}
	50%{background-color:#FF0000;box-shadow:0 0 40px black;}
	100%{background-color:black;box-shadow:0 0 3px black;}
	}
	.modal-ankit{
		 width:89% !important;
	}
	</style>
	
	
	<div id="mymodalShowEvents" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg modal-ankit">
			<div class="modal-content">
					<div class="modal-header">
						<h4 class="panel-heading modal-title"><span class="btn btn-info" style="color:white">Today's Events </h4>
					</div>
					<div class="modal-body">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button></span>
					</div> 
			</div>
		</div>
	</div>
	<!--MOdal Show Tickets ENd Here-->
	<div id="myModal" class="modal fade" role="dialog" data-backdrop='static'>
	  <div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				 <h4 class="panel-heading modal-title"><span style="color:red">Ticket Registering</span></h4>
			</div>
			<div class="modal-body">
				<form method="post">
						<input type="hidden" name="setPutValue">
						<div class="form-group">
							<label class="control-label">EventID </label></td>
							<input type="text" name="EVENT_ID" class="form-control" id="EVENT_ID" readonly>
						</div>
						<div class="form-group">
							<label class=" control-label">Event Desc </label>
							<input type="text" name="EVENT_DESC" class="form-control" id="EVENT_DESC" readonly>
						</div>
						<div class="form-group">
							<label class=" control-label">Event Date </label>
							<input type="text" name="EDATE" class="form-control"  id="EDATE" readonly>
						</div>
						<div class="form-group">
							<label class="control-label">NAME </label>
							<input type="text" name="NAME"class="form-control" id="NAME" readonly>
						</div>
						<div class="form-group">
							<label class="control-label">EVENT STATUS (CLOSE and WIP only) </label>
							<input type="text" name="EVENT_STATUS" class="form-control" id="EVENT_STATUS">
						</div>
						<div class="form-group">
							<label class="control-label">Event Start Time (H:i)</label>
							<input type="text" name="ST_TIME"class="form-control" id="ST_TIME" readonly>
						</div>
						<div class="form-group">
							<label class="control-label">Event End Time (H:i)</label>
							<input type="text" name="END_TIME" class="form-control" id="END_TIME" placeholder="Please Enter END TIME" >
						</div>
						<div class="form-group">
							<label class="control-label">SERVER NAME </label>
							<td><input type="text" name="SERVER_NAME" class="form-control" id="SERVER_NAME" readonly>
						</div>
						<div class="form-group">
							<label class="control-label">MODULE NAME </label>
							<input type="text" name="MODULE_NAME" class="form-control" id="MODULE_NAME" readonly>
						</div>
						<div class="form-group">
							<label class="control-label">TICKET RAISED </label>
							<input type="text" name="TICKETRAISED" class="form-control" id="TICKETRAISED" readonly>  
						</div>
						<div class="form-group">
							<label class="control-label">TICKET CLOSED </label>
							<input type="text" name="TICKETCLOSED" class="form-control" id="TICKETCLOSED" >
						</div>
						<div class="form-group">
							<label class="control-label">REMARKS </label>
							<input type="text" name="REMARKS"  class="form-control" id="REMARKS" placeholder="Please Enter Remarks" required>
						</div>
						<div class="modal-footer">
							<input type="button" style="float-left" name="saveTicket" class="btn btn-primary" value="Save Ticket">
							<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
						</div>
				</form>
		  </div>
		</div>

	  </div>
	</div>
  </head>
  
  <body class="nav-sm purple">
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
        
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle" style="width:100px;">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
				<a data-toggle="tooltip" onclick="toggleFullScreen()" data-placement="down" title="FullScreen"><span style="font-size:20px;" class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span> </a>
              </div>
            <div class="page-title">
              <div class="title_middle">
					<center>
						<span>
							<img src="tata.png" alt="tata image"></img>
							<p class="btn btn-round btn-success">Welcome  <?php echo ucwords($_SESSION['userName']); ?></p>
							<p class="btn btn-round btn-warning showDateModal">MFLAGS <span style="color:black"><?php echo $mldate ; ?></span></p>
							<p class="btn btn-round btn-warning">Current Time <span id="timer" style="color:black"></span></p>
							<p class="btn btn-round btn-warning">Total Events - <?php echo $resultEvents['VAR']; ?></p>
							<p class="btn btn-round btn-danger">Open Events - <?php echo $resultOpen['VAR']; ?></p>
							<p class="btn btn-round btn-success">Auto-Closed Events - <?php echo ($resultEvents['VAR']-$resultOpen['VAR']); ?></p>
							<p class="btn btn-round btn-danger">WIP Events - <?php echo $resultWIP['VAR']; ?></p>
							<p class="btn btn-round btn-success">Closed Event - <?php echo $resultClose['VAR']; ?></p>
							<a href="logout.php" style='float:right' class="btn btn-round btn-danger">Logout</a>
						</span>
					</center>
              </div>
			</div>
            </nav>
          </div>
        </div>
		
		
		
		<div class="right_col" role="main">
				<div class="row">
					<div class="col-md-12">
						<div class="x_panel">
							<div class="x_title">
							<h2 class="red">Event Consolidated Open Status based On B@ancs SOD Timing<span class="rowCount"></span></h2>
							<span style="color:white;float:right" style="cursor:pointer" onclick="portalcheck()" title="Click and Wait For the MAGIC" class="btn btn-danger">SMART Monitor</span>
							<div class="clearfix"></div>
							</div>
							<div class="panel-body table-responsive">
								<table class="table table-bordered table-striped" id="example1">
									<thead>
									<tr>
										<th class="text-center">Service Name</th>
										<th class="text-center">High Severity Count</th>
										<th class="text-center">Medium Severity Count</th>
										<th class="text-center">Low Severity Count</th>
									</tr>
								</thead>
									<tbody>
										<?php echo $getTable; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<div class="x_panel">
							<div class="x_title">
							<h2 class="red">Auto Closed Events</h2>
							<div class="clearfix"></div>
							</div>
							<div class="panel-body table-responsive">
								<table class="table table-bordered table-striped" id="dataClosed">
									<thead>
										<tr>
											<th class="text-center">EVENT ID</th>
											<th class="text-center">Start Time</th>
											<th class="text-center">End Time</th>
											<th class="text-center">DATE</th>
											<th class="text-center">DESCRIPTION </th>
											<th class="text-center">SERVER NAME</th>
											<th class="text-center">MODULE NAME</th>
										</tr>   
									</thead>
									<tbody>
										<?php 
											//$i=1;
											$todayDate = $crdate;
											$queryW=oci_parse($con,"select * from event_master_testing where EDATE='$todayDate' and EVENT_STATUS='CLOSE'");
											oci_execute($queryW);
											while($resultW=oci_fetch_assoc($queryW)){	
										?>
										<tr>
											<td class="text-center"><?php echo $resultW['EVENT_ID']; ?></td>
											<td class="text-center"><?php echo $resultW['ST_TIME']; ?></td>
											<td class="text-center"><?php echo $resultW['END_TIME']; ?></td>
											<td class="text-center"><?php echo date("M, d Y",strtotime($resultW['EDATE'])); ?></td>
											<td class="text-center"><?php echo $resultW['EVENT_DESC']; ?></td>
											<td class="text-center"><?php echo $resultW['SERVER_NAME']; ?></td>
											<td class="text-center"><?php echo $resultW['MODULE_NAME']; ?></td>
										</tr>
										<?php  } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="x_panel">
							<div class="x_title">
							<h2 class="red">Event Tickets <span class="rowCount"></span></h2>
							<div class="clearfix"></div>
							</div>
							<div class="panel-body table-responsive">
								<table class="table table-bordered table-striped" id="dataExam">
									<thead>
										<tr>
											<th class="text-center">EVENT ID</th>
											<th class="text-center">Start Time</th>
											<th class="text-center">End Time</th>
											<th class="text-center">DATE</th>
											<th class="text-center">DESCRIPTION </th>
											<th class="text-center">SERVER NAME</th>
											<th class="text-center">Assigned To</th>
											<th class="text-center">EVENT STATUS</th>
											<th class="text-center">MODULE NAME</th>											
											<th class="text-center">Raised</th>											
											<th class="text-center">Closed</th>											
											<th class="text-center">Remarks</th>											
											<th class="text-center">Action</th>
										</tr>   
									</thead>
									<tbody>
										<?php 
											$i=1;
											$todayDate = $crdate;
											$queryW=oci_parse($con,"SELECT * FROM EVENTS_TICKETS WHERE EDATE='$todayDate' ORDER BY TICKETRAISED desc");
											oci_execute($queryW);
											while($resultW=oci_fetch_assoc($queryW)){	
										?>
										<tr style="background-color:#cdc">
											<td class="text-center"><?php echo $resultW['EVENT_ID']; ?></td>
											<td class="text-center"><?php echo $resultW['ST_TIME']; ?></td>
											<td class="text-center"><?php echo $resultW['END_TIME']; ?></td>
											<td class="text-center"><?php echo date("M, d y",strtotime($resultW['EDATE'])); ?></td>
											<td class="text-center"><?php echo $resultW['EVENT_DESC']; ?></td>
											<td class="text-center"><?php echo $resultW['SERVER_NAME']; ?></td>
											<td class="text-center"><?php echo ucwords($resultW['NAME']); ?></td>
											<td class="text-center" style="color:red;"><?php echo strtoupper($resultW['EVENT_STATUS']); ?></td>
											<td class="text-center"><?php echo $resultW['MODULE_NAME']; ?></td>
											<td class="text-center"><?php echo $resultW['TICKETRAISED']; ?></td>
											<td class="text-center"><?php echo $resultW['TICKETCLOSED']; ?></td>
											<td class="text-center"><?php echo $resultW['REMARKS']; ?></td>
											<?php if($resultW['EVENT_STATUS'] != 'CLOSE'){
													echo "<td class='text-center' onclick='putValuesModal(this,3)' >";
													echo "<i class='fa fa-fw fa-hand-o-up'></i></td>";}
												else{
													echo "<td class='text-center' onclick='putValuesModal(this,2)' >";
													echo "<i class='fa fa-eye'></i></td>";
												}
											?>
										</tr>
										<?php  } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			  </div>
			</div>
	
        <?php include("includes/footer.php"); ?>
        <!-- /footer content -->     
		</div>
    </div>
	<?php include("includes/headercp.php"); ?>
	<script language="JavaScript" type="text/javascript" >
	
	$(".getModalAllDetails").click(function(){
		var ser = ['HIGH','MEDIUM','LOW'];
		var makeString = "key=showtickets&severitynumber="+ser[($(this).index() - 1)]+"&module_name="+$(this).parent().find("td:first").text();
		var dataCount=$(this).children().text();
		//var dataCount=$(this).find('span')[0].firstChild.data; This will also work
		if(dataCount>0){
			$.ajax({
				type: "POST",
				url: window.location.href,
				data: makeString,
				success: function(d){
					$('#mymodalShowEvents').find(".modal-body").html(d);
					$('#mymodalShowEvents').modal('show',{ backdrop:'static' });
				}	
			})
		}
		else{
			alert('Count is less than 1');
		}
	})
	
	function putValuesModal(element,flag){
		console.log("asdas");
		var x = new Date();
		$('[name=EVENT_ID]').val($(element).parent().find("td").eq(0).text());
		$('[name=ST_TIME]').val($(element).parent().find("td").eq(1).text());
		$('[name=END_TIME]').val($(element).parent().find("td").eq(2).text());
		$('[name=EDATE]').val($(element).parent().find("td").eq(3).text());
		$('[name=EVENT_DESC]').val($(element).parent().find("td").eq(4).text());
		$('[name=SERVER_NAME]').val($(element).parent().find("td").eq(5).text());
		$('[name=MODULE_NAME]').val($(element).parent().find("td").eq(8).text());
		var etime=document.getElementById("END_TIME").value;
		if(etime == 'NA'){
			$("[name=END_TIME]").click(function(){
				$(this).prop('type', 'time');
		  });
		}
		if(flag == '2'){//----for viewing the inserted data
			$('#myModal').modal('show');
			$('#myModal').find('[name=saveTicket]').hide();
			$('[name=EVENT_STATUS]').val($(element).parent().find("td").eq(7).text()).attr('readOnly','true');
			$('[name=NAME]').val($(element).parent().find("td").eq(6).text()).attr('readOnly','true');
			$('[name=TICKETRAISED]').val($(element).parent().find("td").eq(9).text()).attr('readOnly','true');
			$('[name=TICKETCLOSED]').parent().show();
			$('[name=TICKETCLOSED]').val($(element).parent().find("td").eq(10).text()).attr('readOnly','true');
			$('[name=REMARKS]').val($(element).parent().find("td").eq(11).text()).attr('readOnly','true');
			$('[name=END_TIME]').attr('readOnly','true');
			
			
		}
		else if(flag == '1'){//-----for  insertion---
			
			$('[name=EVENT_STATUS]').val('WIP');
			$('[name=NAME]').val($(".title_middle").find("p").eq(0).text().split('Welcome  ')[1]);
			$('[name=TICKETRAISED]').val(x.toLocaleTimeString());
			$('[name=setPutValue]').val('1');
			//$('#mymodalShowEvents').find('[data-dismiss=modal]').trigger({type:"click"});
			$('#mymodalShowEvents').modal('hide');
			$('#myModal').modal('show');
			$('[name=TICKETCLOSED]').parent().hide();
			$('#myModal').find('[name=saveTicket]').show();
			
		}
		else if(flag == '3'){//--- for updation of inserted data---
			$('#myModal').modal('show');
			$('#myModal').find('[name=saveTicket]').hide();
			$('[name=EVENT_STATUS]').val($(element).parent().find("td").eq(7).text());
			$('[name=NAME]').val($(element).parent().find("td").eq(6).text());
			$('[name=TICKETRAISED]').val($(element).parent().find("td").eq(9).text());
			$('[name=REMARKS]').val($(element).parent().find("td").eq(11).text());
			$('[name=setPutValue]').val('3');
			
			$('[name=TICKETCLOSED]').parent().show();
			$('[name=TICKETCLOSED]').val(x.toLocaleTimeString());
			$('#myModal').find('[name=saveTicket]').show();
			
		}
	}
	
	$('[name=saveTicket]').click(function(){
		var x = new Date();
		if($('[name=EVENT_STATUS]').val() == 'CLOSE'){
			$('[name=TICKETCLOSED]').val(x.toLocaleTimeString());
		}
		var getFormData = "key=saveTicket&"+$(this).parent().parent().serialize();
		if( $('[name=EVENT_STATUS]').val() == 'CLOSE' || $('[name=EVENT_STATUS]').val() == 'WIP' ){
			$.ajax({
				type: "POST",
				url: window.location.href,
				data: getFormData,
				success: function(d){
					$('#myModal').modal('hide');
					alert(d);
					location.reload();
				}	
			})
		}
		else{
			alert("!!!WARNING!!! Kindly Provide Correct Status (WIP , CLOSE)");
		}
	})
	$('#dataExam').DataTable( {
		dom: 'Bfrtip',
		"iDisplayLength":20,
		"bLengthChange":false,
		buttons: [
			'copyHtml5',
			'pdfHtml5'
		]
	} );
	$('#dataClosed').DataTable( {
		dom: 'Bfrtip',
		"iDisplayLength":20,
		order : [ 1, 'desc' ],
		"bLengthChange":false,
		buttons: [
			'copyHtml5',
			'pdfHtml5'
		]
	} );
	
	$('.showDateModal').click(function(){
		$('#dateModal').modal('show');
	})
	function pageRedirection(){ 
		var data = document.getElementById('ll').value; 
		var date = data.replace(/-/g,"");
		window.location.assign(window.location.origin+window.location.pathname+'?date='+date ); 
	} 
	$('#example1').ready(function(){
		var sumHigh=0,sumMed=0,sumLow=0,totalData='';
		var cellsHigh =$('#example1').find('tbody tr td:nth-of-type(2) span');
		var cellsMed = $('#example1').find('tbody tr td:nth-of-type(3) span');
		var cellsLow = $('#example1').find('tbody tr td:nth-of-type(4) span');
		for (var i=0; i<cellsHigh.length;i++){
			sumHigh+= parseFloat(cellsHigh[i].firstChild.data);
			sumMed+= parseFloat(cellsMed[i].firstChild.data);
			sumLow+= parseFloat(cellsLow[i].firstChild.data);
		}
		totalData += "<tr class='text-center'>";
		totalData += "<td><span class='btn btn-sm btn-round btn-info'>Total Count</span></td>";
		totalData += "<td><span class='btn btn-sm btn-round btn-success'>"+sumHigh+"</span></td>";
		totalData += "<td><span class='btn btn-sm btn-round btn-success'>"+sumMed+"</span></td>";
		totalData += "<td><span class='btn btn-sm btn-round btn-success'>"+sumLow+"</span></td>";
		totalData += "</tr>";
		
		$('#example1 tbody').append(totalData);

		$('#example1').DataTable( {
			dom: 'Bfrtip',
			"bSort":false,
			//"order":['1','desc'],
			"iDisplayLength":8,
			"bLengthChange":false,
			buttons: [
				'copyHtml5',
				'pdfHtml5'
			]
		} );
	})
	function portalcheck(command){
		command  = '/var/www/html/pace/backend_portal/scripts/monitor.sh';
		if(command !=''){
			$.ajax({
				type:"POST",
				url:"db_insert.php",
				data:"key=100&command="+encodeURIComponent(command),
				success:function(d){
					console.log(d);
					$('#showOutput').modal('show');
					if(d == ''){
						d="Error in Command / File Not Present !";
					}
					$(".populateOutput").text(d);
					//$(".populateOutput").text(d.replace(/#/gi,"").replace(/^[ \r\n]+$/gi,""));
				}
			})
		}
	}
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
