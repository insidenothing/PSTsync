<?php
if (isset($_GET['field']) && isset($_GET['table']) && isset($_GET['where_field']) && isset($_GET['where_value'])){
	set_time_limit(2); 
	$mdbFilename = "c:/dbs/pst/LinkedDB.ps5";
	$user = "";
	$password = "";
	$field = $_GET['field'];
	$table= $_GET['table'];
	$where_field = $_GET['where_field'];
	$where_value = $_GET['where_value'];
	if (!file_exists($mdbFilename)) {
		die();
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
	$query  = "SELECT $field FROM $table where $where_field = $where_value ";
	//echo $query;
	$result = odbc_exec($connection, $query);
	while(odbc_fetch_row($result)) {
	 echo odbc_result($result, $field);
	}
}
?>