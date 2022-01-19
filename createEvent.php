<?php
	include("config.php");
	session_start();
	if(isset($_SESSION['empid']) && $_SESSION['power'] == 2){
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
			<div>
			  <div class="col-md-12">
				<div class="x_panel">
				  <div class="x_title">
					<h2 class="red">Add Event Information [No Spaces Before Words]<span class="rowCount"></span></h2>
						<!--<span style="color:white;float:right" style="cursor:pointer" onclick="portalcheck()" class="btn btn-danger">Monitor</span>-->
						<span style="color:white;float:right" style="cursor:pointer" class="btn btn-success addrow">Add Row</span>
					<div class="clearfix"></div>
				  </div>
					<table id="eventPortal" class="table table-striped table-hover table-bordered">
						<thead>
							<tr>
								<th>Describe Event:			</th>
								<th>Enter Event Module:			</th>
								<th>Enter Command (Please Put Absolute Path) Default path: <span class="red">/opt/hpws/apache/cgi-bin/trials/bip/data/TESTING/</span></th>
								<th>Enter Location:				</th>
								<th>Enter Threshold (Level 1 ):	</th>
								<th>Enter Threshold (Level 2 ):	</th>
								<th>Enter Threshold (Level 3 ):	</th>
								<th>Maturity Time				</th>
								<th>Test Command				</th>
							</tr>
						</thead>
						<tbody id="addedrow">
						<form method="post" id="addedData">
							<tr>
								<td><input class="form-control" type="text" value="" name="event_name" placeholder="Describe Event Shortly"></td>
								<td><input class="form-control" value="" type="text" name="event_module" placeholder="Enter Module Name"></td>
								<td><textarea style="margin: 0px 10px 0px 0px; width: 388px; height: 110px;" class="form-control" name="event_command" placeholder="Enter Command"></textarea></td>
								<td><input class="form-control" type="text" value="" name="occur_place" placeholder="Enter Servername"></td>
								<td><input class="form-control" type="text" value="" name="event_threshold_1" placeholder="Enter Threshold Low"></td>
								<td><input class="form-control" type="text" value="" name="event_threshold_2" placeholder="Enter Threshold Medium"></td>
								<td><input class="form-control" type="text" value="" name="event_threshold_3" placeholder="Enter Threshold High"></td>
								<td><input class="form-control" type="time" value="" name="event_maturity" placeholder="Enter Maturity Time"></td>
								<td><span class="btn btn-danger fire">Test Command</span></td>
							</tr>
						</form>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="9"><span style="float:right" onclick="DBSave()"  class="btn btn-round btn-success">Final Submit the Commands</span></td>
							</tr>
						</tfoot>
					</table>
				</div>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	</div>
        <!-- /page content -->
		<?php include("includes/footer.php"); ?>
      </div>
    </div>
	<?php include("includes/headercp.php"); ?>
	<script type="text/javascript">
	//(function(){ 
	$(".addrow").on("click",function(){
	
		var mem='';colData=Array();
		$('#addedrow tr:last td').each(function(i,v){
			colData[i]=$(this).children().val();
		})
		console.log(colData);
		colData.pop();
		
		mem+="<tr>";
			mem+='<td><input class="form-control" type="text" name="event_name" value="'+colData[0]+'" placeholder="Describe Event Shortly"></td>';
			mem+='<td><input class="form-control" type="text" name="event_module" value="'+colData[1]+'" placeholder="Enter Module Name"></td>';
			mem+='<td><textarea style="margin: 0px 10px 0px 0px; width: 388px; height: 110px;" class="form-control" name="event_command" placeholder="Enter Command">'+colData[2]+'</textarea></td>';
			mem+='<td><input class="form-control" type="text" value="'+colData[3]+'" name="occur_place" placeholder="Enter Servername"></td>';
			mem+='<td><input class="form-control" type="text" value="'+colData[4]+'" name="event_threshold_1" placeholder="Enter Threshold Low"></td>';
			mem+='<td><input class="form-control" type="text" value="'+colData[5]+'" name="event_threshold_2" placeholder="Enter Threshold Medium"></td>';
			mem+='<td><input class="form-control" type="text" value="'+colData[6]+'" name="event_threshold_3" placeholder="Enter Threshold High"></td>';
			mem+='<td><input class="form-control" type="time" value="'+colData[7]+'" name="event_maturity" placeholder="Enter Maturity Time"></td>';
			mem+='<td><span class="btn btn-danger fire">Test</span><span class="btn btn-danger delete">Delete Row</span></td>';
		mem+="</tr>";
		$("#addedrow").append(mem);
		$('.rowCount').html('<span style="color:green">('+$('#addedrow tr').length+' Rows Added)'+'</span>');
	})
	
	//appended rows click function
	function portalcheck(command){
		command  = '/opt/hpws/apache/htdocs/pace/backend_portal/scripts/monitor.sh';
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
				}
			})
		}
	}
	//fire and test command
	$('#eventPortal tbody').on('click','tr td span.fire',function(){
		var path,command,s_height,check;
		command  = $(this).closest('tr').find('td:eq(2)').children().val();
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
				}
			})
		}
	})
	//fire command End 
	//Delete Start
	$('#eventPortal tbody').on('click','tr td span.delete',function(){
		var path,command,s_height,check;
		command  = $(this).closest('tr').remove();
		$('.rowCount').html('<span style="color:green">('+$('#addedrow tr').length+' Rows Added)'+'</span>');
	})
	//delete End
	//DB SAVE START
	function DBSave(){
		var flag;
		$("#addedrow").find("input").each(function(){
			if( $(this).val() == '' ) {
				msg = "Please Fill All Values!!!";
				flag = 1;
			}
		})
		if(flag == 1){
			alert(msg);
		}
		else{
			var data=Array();
			$('#addedrow tr').each(function(i,v){
				data[i]=Array();
				$(this).find('td').each(function(ii,vv){
					data[i][ii]=$(this).children().val();
				})

				data[i].pop();
			})
			console.log(data);
			$.ajax({
				type:"POST",
				url:"db_insert.php",
				data:"key=125&insertDBdata="+JSON.stringify(data),
				success:function(d){
					console.log(d);
					alert(d);
				}
			})
			window.reload;
			
		}
		/*$(getval).each(function(i,vl){
			var d = {};
			$('[name="'+vl.name+'"]').each( (i,v) => { 
				d[i]=$(v).val(); 
			}) 
			dataj[vl.name]=d;
			d={};
		});*/
		
	//DB SAVE END
	}
	//})();
	
	
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
