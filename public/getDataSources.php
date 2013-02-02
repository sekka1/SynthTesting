<?php
//TODO: Need to restrict to just the user (customer_id_seq) - MRR 20120527
//TODO: Need to add security
//TODO: Remove hard-coded authKey
//TODO: Get sort and searching to work

$page = isset($_POST['page']) ? $_POST['page'] : 1;
$rp = isset($_POST['rp']) ? $_POST['rp'] : 100; //was 10
$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'datetime_modified';
$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
$query = isset($_POST['query']) ? $_POST['query'] : false;
$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

require_once("../library/AlgorithmsIO/SDK/PHP/AlgorithmsIO.class.php");

$sort = "ORDER BY $sortname $sortorder";
$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";

//$where = "";
//if ($query) $where = " WHERE $qtype LIKE '%".mysql_real_escape_string($query)."%' ";

header("Content-type: application/json");

$authentication = new \AlgorithmsIO\Authentication(array(
		"authToken"		=>"541b393f52b097d3e589ea63ccdfd49e", // Default MRR Test Account // FIXME
));

$ds = new \AlgorithmsIO\DataSource();
$ds->authobj($authentication);
$ds_list = $ds->listAll();

if($sortname) {
	function cmp_obj($a, $b) {
		global $sortname;
		$av = $a->$sortname;
		$bv = $b->$sortname;

		if ($av == $bv) {
    		        return 0;
        	}
        	return ($av > $bv) ? +1 : -1;
    	}
	usort($ds_list,"cmp_obj");
}

$numrows = count($ds_list);

//error_log("DEBUG201205291633: num_rows=".$numrows);

$json_array = array(
	"page"	=> "1",
	"total" => $numrows,
	"rows"	=> $ds_list,
);
$json = json_encode($json_array);
//$json = substr($json, 1, strlen($json)-1);
//$json = '{"page":"1","total":'.$numrows.','.$json;

echo $json;
?>
