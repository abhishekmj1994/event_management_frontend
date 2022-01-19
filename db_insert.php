<?php
	error_reporting(1);
	//Connection Class Start
	
	class connection{
		function connectivity()
		{
			$con = oci_connect("MONITORP","password","opdb");
			return $con;
		}
	}
	//Connection Class End
	
	//Main Class Start
	
	class dbins extends connection{
		function rec_insert($arguments){
			$var=parent::connectivity();
			//echo $arguments;
			$getargs = explode("@",$arguments);
			$event_name=$getargs[0];
			$event_module=trim($getargs[1]);
			
			$queryCount=oci_parse($var,"select event_module from event_params where EVENT_MODULE = '$event_module' ");		//query goes here 			
			oci_execute($queryCount);
			oci_fetch_all($queryCount,$out);
			$countOfModule=oci_num_rows($queryCount);
			$countOfNextModule=$countOfModule+1;
			//$countOfModule=self::rec_fetch($event_module)+1;
			$event_id=$getargs[1].'-'.$countOfNextModule;
			$event_command=str_replace("'","''",$getargs[2]);
			$location=strtoupper($getargs[3]);
			$threshold_1=$getargs[4];
			$threshold_2=$getargs[5];
			$threshold_3=$getargs[6];
			$maturity_time=str_replace(":","",$getargs[7]);
			
			//echo "insert into event_params (event_id,event_name,event_module,event_command,location,threshold_1,threshold_2,threshold_3,maturity_time) VALUES ('$event_id','$event_name','$event_module','$event_command','$location','$threshold_1','$threshold_2','$threshold_3','$maturity_time')";
			
			$query=oci_parse($var,"insert into event_params (event_id,event_name,event_module,event_command,location,threshold_1,threshold_2,threshold_3,maturity_time,EXCEPTION) VALUES ('$event_id','$event_name','$event_module','$event_command','$location','$threshold_1','$threshold_2','$threshold_3','$maturity_time','')");
			$queryRes=oci_execute($query);
			if($queryRes){return "$event_module Events has been inserted !!";}
			else{return "$event_module Events failed to insert !!";}
		}
		function rec_fetch_all(){
			$var=parent::connectivity();
			$query=oci_parse($var,"select * from event_params");	
			oci_execute($query);
			while($data=oci_fetch_assoc($query)){
				$array[]=$data['EVENT_NAME'].'@'.$data['EVENT_ID'].'@'.$data['EVENT_MODULE'].'@'.$data['EVENT_COMMAND'].'@'.$data['LOCATION'].'@'.$data['THRESHOLD_1'].'@'.$data['THRESHOLD_2'].'@'.$data['THRESHOLD_3'].'@'.$data['MATURITY_TIME'].'@'.$data['EXCEPTION'];
			}
			return $array;
		}
		function rec_fetch_all_mails($crdate){
			$var=parent::connectivity();
			$query=oci_parse($var,"select * from mailpush where EDATE='$crdate' order by ROWNUM");	
			oci_execute($query);
			while($data=oci_fetch_assoc($query)){
				$array[]=$data['EVENT_ID'].'@'.$data['EVENT_MODULE'].'@'.$data['EVENT_DESC'].'@'.$data['SENT_TIME'].'@'.$data['EDATE'].'@'.$data['EVENT_SEVERITY'];
			}
			return $array;
		}
		function rec_update($arguments){
			$var=parent::connectivity();
			//return $arguments;
			$getargs = explode("@",$arguments);
			$event_id=$getargs[0];
			$event_name=trim($getargs[1]);
			$event_module=trim($getargs[2]);
			$event_command=str_replace("'","''",$getargs[3]);
			$location=strtoupper($getargs[4]);
			$threshold_1=$getargs[5];
			$threshold_2=$getargs[6];
			$threshold_3=$getargs[7];
			$maturity_time=str_replace(":","",$getargs[8]);
			//return "update event_params set event_name='$event_name',event_module='$event_module',event_command='$event_command',location='$location',threshold_1='$threshold_1',threshold_2='$threshold_2',threshold_3='$threshold_3',maturity_time='$maturity_time' where event_id='$event_id'";
			$query=oci_parse($var,"update event_params set event_name='$event_name',event_module='$event_module',event_command='$event_command',location='$location',threshold_1='$threshold_1',threshold_2='$threshold_2',threshold_3='$threshold_3',maturity_time='$maturity_time' where event_id='$event_id'");
			$queryRes=oci_execute($query);
			if($queryRes){return "$event_module module with id -> $event_id has been updated !!";}
			else{return "$event_module module with id -> $event_id failed to update !!";}
		}
		function rec_delete($event_id,$event_module){
			$var=parent::connectivity();
			//return $event_id;
			//return "delete from event_params where event_id='$event_id'";
			$query=oci_parse($var,"delete from event_params where event_id='$event_id'");
			$queryRes=oci_execute($query);
			if($queryRes){return "$event_module module with $event_id has been deleted !!";}
			else{return "$event_module module with $event_id deletion falied !!";}
		}
		
		//exception_update
		function exception_update($getargs){
			$var=parent::connectivity();
			//return $arguments;
			$event_id=$getargs[0];
			$st_time=$getargs[7];
			$et_time=$getargs[8];
			$days_neglect=$getargs[9];
			$exception=$st_time.'-||-'.$et_time.'-||-'.$days_neglect;
			//echo "update event_params set EXCEPTION='$exception' where event_id='$event_id'";
			/*$query=oci_parse($var,"update event_params set EXCEPTION='$exception' where event_id='$event_id'");
			$queryRes=oci_execute($query);
			if($queryRes){return "Exception of $event_module module with id -> $event_id has been updated !!";}
			else{return "Exception of $event_module module with id -> $event_id failed to update !!";}*/
		}
		
	}
	
	//Main Class End
	
	$DBins = new dbins();
	
	//Insert DB Start
	
	if(isset($_POST['key']) && $_POST['key'] == '125'){
		$dbData=json_decode(str_replace("\\","",$_POST['insertDBdata']),true);
		//$dbData=json_decode($_POST['insertDBdata']);
		//$dbData=$DBins->rec_fetch_all();
		$i=0;
		foreach($dbData as $index=>$dbCol){	
			$msg=$DBins->rec_insert(implode("@",$dbCol));
			$i++;
		}
		echo $msg.". $i rows affected !!";
		exit();
	}
	//Insert DB End
	
	//Fire Command Start
	
	if(isset($_POST['key']) && $_POST['key'] == '100'){
		$command=str_replace("\\","",$_POST['command']);
		$commandReal=str_replace("YYYYMMDD",date("Ymd"),$command);
		$hersd =<<<ED
		{$commandReal}
ED;
		$output=shell_exec($hersd);
		//$output=shell_exec($hersd"|perl -pe 's/\e\[[0-9;]*m//g'");
		//echo json_encode($output);
		echo $output;
		exit();
	}
	
	//Fire Command End
	
	if(isset($_POST['key']) && $_POST['key'] == '120'){
		$dbData=$DBins->rec_fetch_all();
		echo json_encode($dbData);
		exit();
	}
	
	//Edit Start
	
	
	if(isset($_POST['key']) && $_POST['key'] == 'mailsee'){
		$crdate=$_POST['crdate'];
		$dbData=$DBins->rec_fetch_all_mails($crdate);
		echo json_encode($dbData);
		exit();
	}
	
	
	if(isset($_POST['key']) && $_POST['key'] == '150'){
		$fullData=json_decode(str_replace("\\","",$_POST['fullData']));
		$msg=$DBins->rec_update(implode("@",$fullData));
		echo json_encode($msg);
		exit();
	}
	
	//Edit End
	
	//Delete Start
	
	if(isset($_POST['key']) && $_POST['key'] == '175'){
		$event_id=$_POST['event_id'];
		$event_module=$_POST['event_module'];
		$msg=$DBins->rec_delete($event_id,$event_module);
		echo json_encode($msg);
		exit();
	}
	
	//Delete End
	
	//Exeception Start
	
	if(isset($_POST['key']) && $_POST['key'] == '200'){
		$fullData=json_decode(str_replace("\\","",$_POST['exceptions']));
		$msg=$DBins->exception_update($fullData);
		echo $msg;
		//echo json_encode($fullData);
		//print_r($fullData);
		exit();
	}
	
	//Exeception End
?>
	

	
