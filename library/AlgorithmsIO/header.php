<?php 
    $view = $pageargs["view"];
    $security = $view->security;
    $localization = $view->localization;
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Algorithms</title>
<? echo $pageargs["htmllinktags"]; ?>
<? /* <link href="/css/ui-lightness/jquery-ui-1.8.21.custom.css" rel="stylesheet" type="text/css" /> */ ?>
<link href="/css/style.css" rel="stylesheet" type="text/css" />
<script src="/js/html5.js" type="text/javascript"></script>
<? /* <script type="text/javascript" src="/js/jquery-1.7.2.min.js" charset="utf-8"></script> */ ?>
<? /* <script type="text/javascript" src="/js/jquery-ui.custom.min.js"></script> */ ?>
<script type="text/javascript" src="/js/jquery-1.8.2.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.9.0.custom.js"></script>
<? if(isset($pageargs["javascript"])) { echo $pageargs["javascript"]; } ?>
<? if(isset($pageargs["nav_tab"])) {
    echo '<script type="text/javascript">';
    echo '$(function (){';
    echo "$('#nav_".$pageargs["nav_tab"]."_tab').addClass('active');";
    echo '});</script>';
}?>
</head>
<body style="overflow-x: hidden;"><!-- MRR20120522: Note we need overflow-x:hidden to stop scrollbar from appearing in wizard. -->
<?
function getHeaderNav($pageargs) {
    $view = $pageargs["view"];     
    $security = $view->security;
    $localization = $view->localization;        
    $headerstart = <<<EOS
<div class="layout">
  <!-- header starts -->    
  <header>
    <div class="header-inner" style="height: 45px;">
      <div class="dashboard-logo npl"><a href="/index/index"><img width="244" height="44" src="/images/logo.png" alt="logo" /></a></div><!-- 325x58 -->
      <div class="dashboard-right-div">
        <!-- <div class="sign-in"><a href="/login/logout">Logout</a> </div> -->
        <nav>
          <ul id="links">
            <li><a href="/docs">Documentation</a></li>                                                                                      
            <li><a href="/index/aboutus">About Us</a></li> 
            <li><a href="/login/logout">Sign Out</a></li>
          </ul>
        </nav>
      </div>
    </div>
    <BR clear="both">
    
EOS;
    $navigation = array(
        0=>array(
            "id"    =>"dashboard",
            "sec"   =>$security->canRead("Dashboards"),
            "text"  =>$localization->navigation->dashboard,
        ),
        1=>array(
            "id"    =>"datasources",
            "sec"   =>$security->canRead("DataSources"),       
            "text"  =>$localization->navigation->datasources,            
        ),
        2=>array(
            "id"    =>"algorithms",
            "sec"   =>$security->canRead("Algorithms"),    
            "text"  =>$localization->navigation->algorithms,            
        ), 
        3=>array(
            "id"    =>"visualizations",
            "sec"   =>$security->canRead("Visualizations"),
            "text"  =>$localization->navigation->visualizations,            
        ),
        4=>array(
            "id"    =>"workflows",
            "sec"   =>$security->canRead("Flows"),         
            "text"  =>$localization->navigation->workflows,            
        ),        
        5=>array(
            "id"    =>"jobs",
            "sec"   =>$security->canRead("Jobs"),          
            "text"  =>$localization->navigation->jobs,            
        ), 
        6=>array(
            "id"    =>"results",
            "sec"   =>$security->canRead("Results"),       
            "text"  =>$localization->navigation->results,            
        ),
        7=>array(
            "id"    =>"preferences",
            "sec"   =>$security->canEdit("Preferences"),   
            "text"  =>$localization->navigation->preferences,            
        ),                
    );

    $headerstart .= <<<EOS
    <div id="algo-dash-nav">
        <ul id="algo-dash-links">
        
EOS;

    foreach ($navigation as $i=>$nav) {
        if($nav['sec']) {
            if(isset($pageargs["nav_as_tabs"])) {
                $headerstart .= "<li><a id='nav_".$nav["id"]."_tab' href='#".$nav["id"]."_tab' data-toggle='tab'>".$nav["text"]."</a></li>";
            } else {
                $headerstart .= "<li><a id='nav_".$nav["id"]."_tab' href='/dashboard/index#".$nav["id"]."'>".$nav["text"]."</a></li>";
            }
        }
    }
    $headerstart .= <<<EOS
        </ul>
    </div>      
  </header>
EOS;
    return $headerstart;
} // End function getHeaderNav()

if(isset($pageargs["header_off"])) { 
} else { 
    echo getHeaderNav($pageargs);
} 

if(!isset($pageargs["skip_body"])) { ?>  
<!-- header ends -->
  <!-- body starts -->
    <? if (isset($pageargs["body_css"])) { ?>
  <div class="body-outer <?=$pageargs["body_css"];?>">
    <? } else { ?>
  <div class="body-outer">  
    <? } ?>
    <div class="body" style="min-width: 500px;">
<? } ?>
