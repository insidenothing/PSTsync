<?php
mysql_connect('mdwestserve.com','patrick','lox2stay');
mysql_select_db('core');
mysql_query("truncate table pst_attachments");
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
$query  = "SELECT * FROM Attachments where CreatorDBSCode = 'CGD'";
$result = odbc_exec($connection, $query);
if (isset($_GET['limit'])){
	$limit = $_GET['limit'];
}else{
	$limit = 2000;
}
$time_limit = $limit / 5;
set_time_limit($time_limit); 
echo "<table>";
while(odbc_fetch_row($result)) {
	if ($counter < $limit){
		$counter++;
		$ID    = odbc_result($result, 'ID');
		$ChangeNum    = odbc_result($result, 'ChangeNum');
		$Description    = odbc_result($result, 'Description');
		$BlobID    = odbc_result($result, 'BlobID');
		$CreatorDBSCode    = odbc_result($result, 'CreatorDBSCode');
		/*
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
		*/
		@mysql_query("insert into pst_attachments (sync_time, ID, ChangeNum, Description, BlobID, CreatorDBSCode ) values
		(NOW(), '$ID', '$ChangeNum', '$Description', '$BlobID', '$CreatorDBSCode' )")or die(mysql_error());
		echo "<tr><td>$counter</td><td>$ID</td><td>$ChangeNum</td><td>$Description</td><td><a href='http://mdwestserve.com/assets/legacy/pst_document_transfer.php?id=$BlobID&action=view' target='_Blank'>View</a></td><td>$CreatorDBSCode</td></tr>";
	}
}
echo "</table>";
?> 