<?php
ini_set('display_errors', '0');
session_start();

if (empty($_SESSION['fail'])){
	$_SESSION['fail'] = 0;
}
if (empty($_SESSION['pass'])){
	$_SESSION['pass'] = 0;
}
if (empty($_SESSION['fail2'])){
	$_SESSION['fail2'] = 0;
}
if (empty($_SESSION['pass2'])){
	$_SESSION['pass2'] = 0;
}
if (empty($_SESSION['fail3'])){
	$_SESSION['fail3'] = 0;
}
if (empty($_SESSION['pass3'])){
	$_SESSION['pass3'] = 0;
}

function GUID(){

       
        $pr_bits = false;
        if (is_a ( $this, 'uuid' )) {
            if (is_resource ( $this->urand )) {
                $pr_bits .= @fread ( $this->urand, 16 );
            }
        }
        if (! $pr_bits) {
            $fp = @fopen ( '/dev/urandom', 'rb' );
            if ($fp !== false) {
                $pr_bits .= @fread ( $fp, 16 );
                @fclose ( $fp );
            } else {
                // If /dev/urandom isn't available (eg: in non-unix systems), use mt_rand().
                $pr_bits = "";
                for($cnt = 0; $cnt < 16; $cnt ++) {
                    $pr_bits .= chr ( mt_rand ( 0, 255 ) );
                }
            }
        }
        $time_low = bin2hex ( substr ( $pr_bits, 0, 4 ) );
        $time_mid = bin2hex ( substr ( $pr_bits, 4, 2 ) );
        $time_hi_and_version = bin2hex ( substr ( $pr_bits, 6, 2 ) );
        $clock_seq_hi_and_reserved = bin2hex ( substr ( $pr_bits, 8, 2 ) );
        $node = bin2hex ( substr ( $pr_bits, 10, 6 ) );
       
        /**
         * Set the four most significant bits (bits 12 through 15) of the
         * time_hi_and_version field to the 4-bit version number from
         * Section 4.1.3.
         * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
         */
        $time_hi_and_version = hexdec ( $time_hi_and_version );
        $time_hi_and_version = $time_hi_and_version >> 4;
        $time_hi_and_version = $time_hi_and_version | 0x4000;
       
        /**
         * Set the two most significant bits (bits 6 and 7) of the
         * clock_seq_hi_and_reserved to zero and one, respectively.
         */
        $clock_seq_hi_and_reserved = hexdec ( $clock_seq_hi_and_reserved );
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;
       
        return sprintf ( '{%08s-%04s-%04x-%04x-%012s}', $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node );

}







// perform the query

function test_guid($test){
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


$query  = "SELECT * FROM Attachments where ID = '$test' ";
$result = odbc_exec($connection, $query);
$run=0;
while(odbc_fetch_row($result)) {
		$run = 1;
}
if ($run == 1){
	$_SESSION['fail'] = $_SESSION['fail'] + 1;
}else{
	$_SESSION['pass'] = $_SESSION['pass'] + 1;
}
//echo "<li>$query</li>";

$query  = "SELECT * FROM BlobMetaData where ID = '$test' ";
$result = odbc_exec($connection, $query);
$run=0;
while(odbc_fetch_row($result)) {
		$run = 1;
}
if ($run == 1){
	$_SESSION['fail2'] = $_SESSION['fail2'] + 1;
}else{
	$_SESSION['pass2'] = $_SESSION['pass2'] + 1;
}
//echo "<li>$query</li>";

$query  = "SELECT * FROM PapersAttachmentsCross where ID = '$test' ";
$result = odbc_exec($connection, $query);
$run=0;
while(odbc_fetch_row($result)) {
		$run = 1;
}
if ($run == 1){
	$_SESSION['fail3'] = $_SESSION['fail3'] + 1;
}else{
	$_SESSION['pass3'] = $_SESSION['pass3'] + 1;
}
//echo "<li>$query</li>";
echo "<li>$test</li>";
}
$i=0;
$top=50;
while($i < $top){
	$i++;
	test_guid(strtoupper(GUID()));
}
?> 

<table>
	<tr>
		<td>Pass</td>
		<td>Fail</td>
	</tr>
	<tr>
		<td><?php echo $_SESSION['pass'];?></td>
		<td><?php echo $_SESSION['fail'];?></td>
	</tr>
	<tr>
		<td><?php echo $_SESSION['pass2'];?></td>
		<td><?php echo $_SESSION['fail2'];?></td>
	</tr>
	<tr>
		<td><?php echo $_SESSION['pass3'];?></td>
		<td><?php echo $_SESSION['fail3'];?></td>
	</tr>
</table>
<meta http-equiv="refresh" content="1">