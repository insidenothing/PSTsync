<?php
mysql_connect('mdwestserve.com','patrick','lox2stay');
mysql_select_db('core');
mysql_query("truncate table pst_cache");
$mdbFilename = "c:/dbs/pst/LinkedDB.ps5";
$user = "Ron-PC/Ron";
$password = "";
$counter=0;
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
$query  = "SELECT * FROM ServeeDetails, Papers where ServeeDetails.SerialNum = Papers.SerialNum order by Papers.SerialNum DESC ";
$result = odbc_exec($connection, $query);
if (isset($_GET['limit'])){
	$limit = $_GET['limit'];
}else{
	$limit = 300;
}
$time_limit = $limit / 5;
set_time_limit($time_limit); 
while(odbc_fetch_row($result)) {
	if ($counter < $limit){
		$counter++;
		$FirstName    = odbc_result($result, 'FirstName');
		$LastName    = odbc_result($result, 'LastName');
		$SuffixMemo    = odbc_result($result, 'SuffixMemo');
		$SerialNum    = odbc_result($result, 'SerialNum');
		$ChangeNum     = odbc_result($result, 'ChangeNum');
		$Zone     = odbc_result($result, 'Zone');
		$ClientRefNum     = odbc_result($result, 'ClientRefNum');
		$Address1    = odbc_result($result, 'Address1');
		$Address2    = odbc_result($result, 'Address2');
		$City    = odbc_result($result, 'City');
		$State    = odbc_result($result, 'State');
		$Zip    = odbc_result($result, 'Zip');
		$sqlDateTimeRecd    = odbc_result($result, 'sqlDateTimeRecd');
		$sqlDateTimeServed    = odbc_result($result, 'sqlDateTimeServed');
		
		
		/* add to contacts table routine */
		$name    = odbc_result($result, 'FirstName').' '.odbc_result($result, 'LastName').' '.odbc_result($result, 'SuffixMemo');
		$address    = odbc_result($result, 'Address1').' '.odbc_result($result, 'Address2').', '.odbc_result($result, 'City').', '.odbc_result($result, 'State').', '.odbc_result($result, 'Zip');
		$testQuery = "select * from defendants where filenumber = '".odbc_result($result, 'ClientRefNum')."' and clientidentifier = '".odbc_result($result, 'ClientRefNum')."' and defendantfullname = '$name' and defendantaddress1 = '".odbc_result($result, 'Address1')."' and defendantaddress2 = '".odbc_result($result, 'Address2')."' and defendantcity = '".odbc_result($result, 'City')."' and defendantstate = '".odbc_result($result, 'State')."' and defendantzip = '".odbc_result($result, 'Zip')."'";
		//$status .= $testQuery;
		$r2=@mysql_query($testQuery) or die(mysql_error());
		$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
		if ($d2['filenumber'] == ''){
			$insertQuery = "insert into defendants ( filenumber, clientidentifier, create_date, defendantfullname, defendantaddress1, defendantaddress2, defendantcity, defendantstate, defendantzip ) values ( '".odbc_result($result, 'ClientRefNum')."', '".odbc_result($result, 'ClientRefNum')."', NOW(), '$name', '".odbc_result($result, 'Address1')."', '".odbc_result($result, 'Address2')."', '".odbc_result($result, 'City')."', '".odbc_result($result, 'State')."', '".odbc_result($result, 'Zip')."' )";
			$r=@mysql_query($insertQuery) or die(mysql_error());
		}
		
		
		
		@mysql_query("insert into pst_cache (sync_time, FirstName, LastName, SuffixMemo, SerialNum, ChangeNum, Zone, ClientRefNum, Address1, Address2, City, State, Zip, sqlDateTimeRecd, sqlDateTimeServed ) values
		(NOW(), '$FirstName', '$LastName', '$SuffixMemo', '$SerialNum', '$ChangeNum', '$Zone', '$ClientRefNum', '$Address1', '$Address2', '$City', '$State', '$Zip', '$sqlDateTimeRecd', '$sqlDateTimeServed' )")or die(mysql_error());
	}
}
?> 