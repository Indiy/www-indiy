<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

$remote_ip=$_SERVER['REMOTE_ADDR'];
$query_string=urldecode($_SERVER['QUERY_STRING']);
$server_time=date('Y-m-d h:m:s',time());
$fld_sep=chr(1);
$rec_sep=chr(13);
$grp_marker='*';

function get_qvalue($name)
	{
	global $query_string;
	$search=$name.'=';
	$ipos=$ipos2=$ipos_start=null;
	$ipos=stripos($query_string,$search);
	$res=null;
	if($ipos!==false)
		{
		$ipos2=stripos($query_string,'&',$ipos+strlen($search));
		$ipos_start=$ipos+strlen($search);
		if($ipos2!==false)
			{
			return substr($query_string,$ipos_start,$ipos2-$ipos_start);
			}
		else
			return substr($query_string,$ipos_start);	
		}
		
	return null;	
	}
	
function get_artists($conn)
	{	
	global $rec_sep;
	global $fld_sep;
	global $grp_marker;
	$sql="SELECT id,artist,url,logo,website FROM mydna_musicplayer";
	$result=mysql_query($sql,$conn);
	echo $grp_marker.'artists'.$rec_sep;
	while($row = mysql_fetch_assoc($result))
		{		
		echo stripslashes($row['id'].$fld_sep.$row['artist'].$fld_sep.$row['url'].$fld_sep.$row['logo'].$fld_sep.$row['website'].$rec_sep);
		}
	}
	
function get_songs($conn,$like)
	{	
	global $rec_sep;
	global $fld_sep;
	global $grp_marker;
	$sql="select id,artistid,name,image from mydna_musicplayer_audio";
	if($like!=null)
		$sql.=" WHERE name like '$like%'";
	$result=mysql_query($sql,$conn);
	echo $grp_marker.'songs'.$rec_sep;
	while($row = mysql_fetch_assoc($result))
		{		
		echo stripslashes($row['id'].$fld_sep.$row['artistid'].$fld_sep.$row['name'].$fld_sep.$row['image'].$rec_sep);
		}
	}
	
function get_media($conn,$like)
	{	
	global $rec_sep;
	global $fld_sep;
	global $grp_marker;
	$sql="select id,artistid,name,image from mydna_musicplayer_content";
	if($like!=null)
		$sql.=" WHERE name like '$like%'";
	$result=mysql_query($sql,$conn);
	echo $grp_marker.'media'.$rec_sep;
	while($row = mysql_fetch_assoc($result))
		{		
		echo stripslashes($row['id'].$fld_sep.$row['artistid'].$fld_sep.$row['name'].$fld_sep.$row['image'].$rec_sep);
		}
	}



// $record=$email_address.','.$remote_ip.','.$server_time.','.$client_time."\r\n";

if($_SESSION['SEARCHBOX']==null)
	{
	$_SESSION['SEARCHBOX']=123;
	$fetch_flags=7;
	}
else
	$fetch_flags=intval(get_qvalue('ff'));

$link=mysql_connect('localhost','madcom_user','MyartistDNA!') or die('open failed='.mysql_error());
mysql_select_db('myartist_mysql');

$like=get_qvalue('pfx');

if($fetch_flags & 1)
	get_artists($link);
if($fetch_flags & 2)
	get_songs($link,$like);
if($fetch_flags & 4)
	get_media($link,$like);


	
mysql_close($link);
?>