<h1>Server Directory</h1>
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
// perform the query
$query  = "SELECT * FROM DBSDirectory";
$result = odbc_exec($connection, $query);
?>
<table>
	<tr>
		<td>DBS Code</td>
		<td>DBS Version</td>
		<td>Firm Name</td>
		<td>Address1</td>
		<td>Address2</td>
		<td>City</td>
		<td>State</td>
		<td>Zip</td>
		<td>Phone</td>
		<td>ChangeNum</td>
		<td>Hide</td>
	</tr>
<?PHP
while(odbc_fetch_row($result)) {
    echo "<tr>
				<td>" . odbc_result($result, 'DBSCode') . "</td>
				<td>" . odbc_result($result, 'PSTCompatibilityVersion') . "</td>
				<td>" . odbc_result($result, 'FirmName') . "</td>
				<td>" . odbc_result($result, 'Address1') . "</td>
				<td>" . odbc_result($result, 'Address2') . "</td>
				<td>" . odbc_result($result, 'City') . "</td>
				<td>" . odbc_result($result, 'State') . "</td>
				<td>" . odbc_result($result, 'Zip') . "</td>
				<td>" . odbc_result($result, 'Phone') . "</td>
				<td>" . odbc_result($result, 'ChangeNumber') . "</td>
				<td>" . odbc_result($result, 'HideFromCustList') . "</td>
			</tr>";
}
?> 
</table>