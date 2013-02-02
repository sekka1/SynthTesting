<?php

/*
This is an authenticated controller
*/
require_once("AlgorithmsIO/classes/AlgorithmsIO_Zend_Controller_Action.php");
class AlgorithmsController extends \AlgorithmsIO\AlgorithmsIO_Zend_Controller_Action
{

    public function init()
    {
	//$this->debug=true;
	//$this->debug=false;
	$this->debug("DEBUG201208031649: AlgorithmsController Initialized");
        $this->_helper->layout()->disableLayout();
        parent::init();
    }
    
    public function postAction() 
    {
        $this->indexAction();
        exit;
    }
    
    public function delete() {
        $this->upload_handler->delete();
    }
    
    public function get() {
        $this->upload_handler->get();    
    }
    
    public function indexAction()
    {
        global $sortname;
        $this->_helper->viewRenderer->setNoRender();
        
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $rp = isset($_POST['rp']) ? $_POST['rp'] : 100; //was 10
        $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'name';
        $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
        $query = isset($_POST['query']) ? $_POST['query'] : false;
        $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

        require_once("AlgorithmsIO/SDK/PHP/AlgorithmsIO.class.php");

        $sort = "ORDER BY $sortname $sortorder";
        $start = (($page-1) * $rp);

        $limit = "LIMIT $start, $rp";

        //$where = "";
        //if ($query) $where = " WHERE $qtype LIKE '%".mysql_real_escape_string($query)."%' ";

        header("Content-type: application/json");

        $authentication = new \AlgorithmsIO\Authentication(array(
                        "authToken"		=>$this->authToken()->get_token(), 
        ));

        $algo = new \AlgorithmsIO\Algorithm(array("authobj"=>$authentication));
        $algo->authobj($authentication);
        $algo_list = $algo->listAll();

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
                usort($algo_list,"cmp_obj");
        }

        $numrows = count($algo_list);

        $this->debug("DEBUG201205291633: num_rows=".$numrows);

        $json_array = array(
                "page"	=> "1",
                "total" => $numrows,
                "rows"	=> $algo_list,
        );
        $json = json_encode($json_array);

        echo $json;
    }

}
