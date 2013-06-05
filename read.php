<?php
session_start();
$_SESSION['active']=0;
$_SESSION['update']=0;
$_SESSION['closed']=0;
mysql_connect('mdwestserve.com','patrick','lox2stay');
mysql_select_db('core');
$mdbFilename = "c:/dbs/pst/LinkedDB.ps5";
$user = "Ron-PC/Ron";
$password = "";
if (!file_exists($mdbFilename)) {
    die("Could not find database file. '$mdbFilename' ");
}
/* Connect using Windows Authentication. */
try
{
	$connection = odbc_connect("Driver={Microsoft Access Driver (*.mdb)};Dbq=$mdbFilename", $user, $password);
}
	catch(Exception $e)
{ 
	die( print_r( $e->getMessage() ) ); 
}

 // perform the query
$query3  = "SELECT * FROM ServeeDetails, Papers where ServeeDetails.SerialNum = Papers.SerialNum and Papers.Zone = '' order by Papers.SerialNum DESC";
$query  = "SELECT * FROM ServeeDetails, Papers where ServeeDetails.SerialNum = Papers.SerialNum order by Papers.SerialNum DESC ";
$result = odbc_exec($connection, $query);
$result3 = odbc_exec($connection, $query3);


$new=0;
while(odbc_fetch_row($result3)) {
$new++; //echo "new";
}
if ($new > 0){
	$q="select * from toolbar where graph_description = 'New PST File'";
	$r=mysql_query($q);
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if(!$d['graph_description']){
		@mysql_query(" insert into toolbar (do_not_truncate, reported, file_count, graph_description, alert_description, staff_lead) values ( '1', NOW(), '$new', 'New PST File', 'PST New files to enter', 'Drew') "); 
	}else{
		@mysql_query(" update toolbar set file_count = '$new' where graph_description = 'New PST File' ");
	}
}else{
	@mysql_query("delete from toolbar where graph_description = 'New PST File'");
}
if (isset($_GET['limit'])){
	$limit = $_GET['limit'];
}else{
	$limit = 300;
}
$time_limit = $limit / 5;
set_time_limit($time_limit); 
$counter = 0;
?>
<style>
td { white-space:nowrap; }
</style>
<script src="sorttable.js"></script>
<style>
/* Sortable tables */
table.sortable thead {
    background-color:#eee;
    color:#666666;
    font-weight: bold;
    cursor: default;
}
a{ font-weight:bold; color:black;
</style>
<div>PST -> MDWS SYNC: Limit <?php echo $limit; ?> lines in <?php echo $time_limit;?> seconds</div>
<table class="sortable" cellspacing="0" cellpadding="2" border="1" width="100%" style="border-colapse:colaspe;">
<thead>
	<tr>
		<td>SerialNum</td>
		<td>ChangeNum</td>
		<td>Name</td>
		<td>Address</td>
		<td>MDWS Packet</td>
		<td>Client File</td>
		<td>Sync Status</td>
		<td>Attachments</td>
		<td>Received Data</td>
		<td>Packet Started</td>
		<td>sqlDateTimeRecd</td>
		<td>sqlDateTimeServed</td>
	</tr>
</thead>
<?PHP
while(odbc_fetch_row($result)) {
	if ($counter < $limit){
		$counter++;
		
		$name    = odbc_result($result, 'FirstName').' '.odbc_result($result, 'LastName').' '.odbc_result($result, 'SuffixMemo');
		
		$q="select * from ps_packets where client_file = '".odbc_result($result, 'ClientRefNum')."'";
		$r=mysql_query($q);
		$d=mysql_fetch_array($r,MYSQL_ASSOC);
		if($d['client_file'] == odbc_result($result, 'ClientRefNum')){
			$status = $d['process_status']; 
		}else{
			$status = "NOT UPLOADED YET";
		}

		/* add to contacts table routine */
		$testQuery = "select * from defendants where filenumber = '".odbc_result($result, 'ClientRefNum')."' and clientidentifier = '".odbc_result($result, 'ClientRefNum')."' and defendantfullname = '$name' and defendantaddress1 = '".odbc_result($result, 'Address1')."' and defendantaddress2 = '".odbc_result($result, 'Address2')."' and defendantcity = '".odbc_result($result, 'City')."' and defendantstate = '".odbc_result($result, 'State')."' and defendantzip = '".odbc_result($result, 'Zip')."'";
		//$status .= $testQuery;
		$r2=@mysql_query($testQuery) or die(mysql_error());
		$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
		if ($d2['filenumber'] == ''){
			$insertQuery = "insert into defendants ( filenumber, clientidentifier, create_date, defendantfullname, defendantaddress1, defendantaddress2, defendantcity, defendantstate, defendantzip ) values ( '".odbc_result($result, 'ClientRefNum')."', '".odbc_result($result, 'ClientRefNum')."', NOW(), '$name', '".odbc_result($result, 'Address1')."', '".odbc_result($result, 'Address2')."', '".odbc_result($result, 'City')."', '".odbc_result($result, 'State')."', '".odbc_result($result, 'Zip')."' )";
			$r=@mysql_query($insertQuery) or die(mysql_error());
			$status .= ", DATA TRANSFERRED ";
		}
		
		
		$id    = odbc_result($result, 'SerialNum');
		
		$ChangeNum     = odbc_result($result, 'ChangeNum');
		$packet     = odbc_result($result, 'Zone');
		$client     = odbc_result($result, 'ClientRefNum');
		$name    = odbc_result($result, 'FirstName').' '.odbc_result($result, 'LastName').' '.odbc_result($result, 'SuffixMemo');
		$address    = odbc_result($result, 'Address1').' '.odbc_result($result, 'Address2').', '.odbc_result($result, 'City').', '.odbc_result($result, 'State').', '.odbc_result($result, 'Zip');
		ob_start();
		if ($d['dbs_status'] == 'Unfinished' && $d['process_status'] == 'INVOICED'){
			echo "<tr style='background-color:blue;'>";
			$test=1;
			$_SESSION['closed'] = $_SESSION['closed'] + 1;
		}elseif ($d['dbs_status'] == 'Unfinished' && $d['service_status'] == 'IN PROGRESS'){
			echo "<tr style='background-color:green;'>";
			$test=1;
			$_SESSION['active'] = $_SESSION['active'] + 1;
		}elseif ($d['dbs_status'] == 'Unfinished' && ( $d['service_status'] == 'MAILING AND POSTING' || $d['service_status'] == 'PERSONAL DELIVERY' )){
			echo "<tr style='background-color:red;'>";
			$test=1;
			$_SESSION['update'] = $_SESSION['update'] + 1;
		}elseif ($d['dbs_status'] == 'Unfinished'){
			echo "<tr style='background-color:yellow;'>";
			$test=1;
		}else{
			echo "<tr>";
			$test=0;
		}
		
		echo "<td><a target='_Blank' href='http://146.145.55.198/PAC.php?PaperSerialNum=" . $id . "'>Documents</a></td><td>" . $ChangeNum . "</td><td>" . $name . "</td><td>" . $address . "</td><td><a href='http://mdwestserve.com/detail/presale/$packet' target='_Blank'>" . $packet . "</a></td><td>" . $client . "</td><td>" . $status . "</td><td>";
	//	if ($ChangeNum != ''){
	//		$query2  = "SELECT * FROM Attachments where ChangeNum = $ChangeNum ";
	//		$result2 = odbc_exec($connection, $query2);
	//		while(odbc_fetch_row($result2)) {
				//echo "<li>".odbc_result($result2, 'Description')."</li>";
	//		}
	//	}
		echo "</td>";
		
		echo "<td>".$d2['create_date']."</td>";
		echo "<td>".$d['date_received']."</td>";
		echo "<td>".odbc_result($result, 'sqlDateTimeRecd')."</td>";
		echo "<td>".odbc_result($result, 'sqlDateTimeServed')."</td>";
		
		echo "</tr>";
		$row = ob_get_clean();
		if (isset( $_GET['active']) && $test != 0 ){
			echo $row;
		}elseif(empty($_GET['active'])){
			echo $row;
		}
	}
}
?> 
</table>
<title>Active Jobs: <?php echo $_SESSION['active'];?> - Review: <?php echo $_SESSION['update'];?> - Close: <?php echo $_SESSION['closed'];?></title>
<?php
if ($_SESSION['closed'] > 0){
	$q="select * from toolbar where graph_description = 'Close PST File'";
	$r=mysql_query($q);
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if(!$d['graph_description']){
		@mysql_query(" insert into toolbar (do_not_truncate, reported, file_count, graph_description, alert_description, staff_lead) values ( '1', NOW(), '".$_SESSION['closed']."', 'Close PST File', 'PST files to enter and close', 'Drew') "); 
	}else{
		@mysql_query(" update toolbar set file_count = '".$_SESSION['closed']."' where graph_description = 'Close PST File' ");
	}
}else{
	@mysql_query("delete from toolbar where graph_description = 'Close PST File'");
}
if ($_SESSION['update'] > 0){
	$q="select * from toolbar where graph_description = 'QC PST File'";
	$r=mysql_query($q);
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if(!$d['graph_description']){
		@mysql_query(" insert into toolbar (do_not_truncate, reported, file_count, graph_description, alert_description, staff_lead) values ( '1', NOW(), '".$_SESSION['update']."', 'QC PST File', 'PST files to QC', 'Drew') "); 
	}else{
		@mysql_query(" update toolbar set file_count = '".$_SESSION['update']."' where graph_description = 'QC PST File' ");
	}
}else{
	@mysql_query("delete from toolbar where graph_description = 'QC PST File'");
}
?>