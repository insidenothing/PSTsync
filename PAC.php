<?php
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

if (isset($_GET['PaperSerialNum'])){
	// perform the query
	$query  = "SELECT * FROM PapersAttachmentsCross where PaperSerialNum = ".$_GET['PaperSerialNum']." ";
	//echo $query;
	$result = odbc_exec($connection, $query);
	while(odbc_fetch_row($result)) {
			echo "<li><a href='http://mdwestserve.com/assets/legacy/pst_document_transfer.php?id=".odbc_result($result, 'AttachmentID')."&action=view' target='_Blank'>View</a><a href='http://mdwestserve.com/assets/legacy/pst_document_transfer.php?id=".odbc_result($result, 'AttachmentID')."&action=save' target='_Blank'>Transfer</a></li>";
	}
}

if (isset($_GET['SerialNum'])){
	// perform the query
	$query  = "SELECT * FROM PapersAttachmentsCross where PaperSerialNum = ".$_GET['SerialNum']." ";
	//echo $query;
	$result = odbc_exec($connection, $query);
	while(odbc_fetch_row($result)) {
			die('{'.odbc_result($result, 'AttachmentID').'}');
	}
}

