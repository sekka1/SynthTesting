<?php

/*
This is an authenticated controller
*/
require_once("AlgorithmsIO/classes/AlgorithmsIO_Zend_Controller_Action.php");
class DataSourcesController extends \AlgorithmsIO\AlgorithmsIO_Zend_Controller_Action
{

    public function init()
    {
	//$this->debug=true;
	//$this->debug=false;
	$this->debug("DEBUG201207281159: DatasourcesController Initialized");
        $this->_helper->layout()->disableLayout();

        parent::init();
    }
    
    public function postAction() 
    {
        $this->indexAction();
        exit;
        
        $this->debug("DEBUG201207261418: In postaction");
        $this->getResponse()
            ->setHeader('Pragma: no-cache')
            ->setHeader('Cache-Control: no-store, no-cache, must-revalidate')
            ->setHeader('Content-Disposition: inline; filename="files.json"')
            ->setHeader('X-Content-Type-Options: nosniff')
            ->setHeader('Access-Control-Allow-Origin: *')
            ->setHeader('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE')
            ->setHeader('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

        if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
            $this->upload_handler->delete();
        } else {
            $this->upload_handler->post();
            $files_objs=$this->upload_handler->get_file_objects();
            //ob_start();
            //var_dump($files_objs);
            //error_log(ob_get_contents());
            //ob_end_clean();
            //error_log("DEBUG201205291109: ".$upload_handler->options["upload_dir"]);
        }

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
        $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'last_modified';
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
                        "authToken"		=>$this->authToken()->get_token(), // Default MRR Test Account // FIXME
        ));
        
        $ds = new \AlgorithmsIO\DataSource(array("authobj"=>$authentication));
        $ds->authobj($authentication);
        $ds_list = $ds->listAll();
        
        $numrows = count($ds_list);
        $this->debug("DEBUG201208031259: num_rows=".$numrows);
        $this->debug("DEBUG201208031307: sortname=".$sortname);
        if($numrows && $sortname) {
                function cmp_obj($a, $b) {
                        global $sortname;
                        if(!isset($a->$sortname)) {
                            //error_log("ERROR201208031302: Sorted column $sortname could not be found in the dataset ".print_r($a,true));
                            return 0; // Error silently for now
                        }
                        $av = $a->$sortname;
                        $bv = $b->$sortname;

                        if ($av == $bv) {
                                return 0;
                        }
                        return ($av > $bv) ? +1 : -1;
                }
                usort($ds_list,"cmp_obj");
        }

        $json_array = array(
                "page"	=> "1",
                "total" => $numrows,
                "rows"	=> $ds_list,
        );
        $json = json_encode($json_array);

        echo $json;
    }

}
