<?

$FILE = "/tmp/mad_fm.json";

header("Content-Type: application/json");
header("Cache-Control: no-cache");
header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");

$json = file_get_contents($FILE);
print $json;

?>

