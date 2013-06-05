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



if (empty($_GET['id'])){
	die('Missing Attachments ID');
}

$id = $_GET['id'];
$query  = " SELECT * FROM Attachments where ID = '$id' ";
//$query  = " SELECT * FROM BlobMetaData where ID = '$id' ";
//echo "<SMALL>$query</SMALL>";
$result = odbc_exec($connection, $query) or die("<p>".odbc_errormsg());
while(odbc_fetch_row($result)) {
		//echo '<li>FileExtension: '.odbc_result($result, 'FileExtension').'</li>';
		//$FileExtension =  odbc_result($result, 'FileExtension');
		//$Location =  odbc_result($result, 'Location');
		$Description =  odbc_result($result, 'Description');
		$BlobID =  odbc_result($result, 'BlobID');
		
}


if (empty($BlobID)){
	die('Missing Blob ID');
}

$id = $BlobID; // Atachments.BlobID = BlobMetaData.ID
$query  = " SELECT * FROM BlobMetaData where ID = '$id' ";
//$query  = " SELECT * FROM BlobMetaData where ID = '$id' ";
//echo "<SMALL>$query</SMALL>";
$result = odbc_exec($connection, $query) or die("<p>".odbc_errormsg());
while(odbc_fetch_row($result)) {
		//echo '<li>FileExtension: '.odbc_result($result, 'FileExtension').'</li>';
		$FileExtension =  odbc_result($result, 'FileExtension');
		$Location =  odbc_result($result, 'Location');
		//$Description =  odbc_result($result, 'Description');
		
}

if ($Location == ''){
	die('Missing Binary Data Storage Location');
}

//echo "<li>FileExtension: $FileExtension</li>";
//echo "<li>Location: $Location</li>";
//echo "<li>Description: $Description</li>";

//die('pause...');
// second connection to Blob Data 1-3+
$mdbFilename = "c:/dbs/pst/Blobs".$Location.".ps5";
/* Connect using Windows Authentication. */
try
{
	$connection = odbc_connect("Driver={Microsoft Access Driver (*.mdb)};Dbq=$mdbFilename", $user, $password);
}
	catch(Exception $e)
{ 
	die( print_r( $e->getMessage() ) ); 
}

$query  = " SELECT * FROM Blobs where ID = '$id' ";
//$query  = " SELECT * FROM BlobMetaData where ID = '$id' ";
//echo "<SMALL>$query</SMALL>";
$result = odbc_exec($connection, $query) or die("<p>".odbc_errormsg());
odbc_binmode($result,ODBC_BINMODE_PASSTHRU);
odbc_longreadlen($result,4096);
while(odbc_fetch_row($result)) {
		$Blob =  odbc_result($result, 'Blob');
}
echo strtoupper(trim($FileExtension));
?>

