<?

$FILE = "/tmp/mad_fm_genre_data.json";

header("Content-Type: application/json");
header("Cache-Control: no-cache");
header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
header("Access-Control-Allow-Origin: *");

$json = file_get_contents($FILE);
print $json;

?>

