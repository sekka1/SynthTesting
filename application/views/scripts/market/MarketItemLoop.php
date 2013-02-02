<?php ?>
<?

$page = 1;
$pagination = 10;
$items = $this->marketitems;
$totalitems = count($items);
$startitem = $pagination*($page-1);
$remainingItems = $totalitems - $startitem;
if($remainingItems > $pagination) {
    $maxitems = $page*$pagination;
} else {
    $maxitems = $remainingItems;
}

$defaultIconClass = "AlgorithmIcon";
switch($this->marketType) {
    case "Algorithms": 
        $defaultIconClass = "AlgorithmIcon";
        break;
    case "DataSources":
        $defaultIconClass = "DBIcon";
        break;
    case "Flows":
        $defaultIconClass = "FlowsIcon";
        break;
    case "Visualizations":
        $defaultIconClass = "InterfaceIcon";
        break;        
}

?>

<DIV class="well">
    <h1>Showing <?=($startitem+1); ?>-<?=$maxitems;?> of <?=count($items); ?> <?=$this->marketType; ?></h1>
    <BR>
<? for ($i=$startitem; $i<$maxitems; $i++) { 
        $item = $items[$i];
        $itemruncount = rand(0, 9000);
        $description = $item->get_description();
        if(!$description) {
            $description = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
        }
        $slogan = $item->get_slogan();
        if(!$slogan) {
            $slogan = "An Algorithm for Everyone";
        }
        $tags = array("Facial Recognition", "Image Processing", "Machine Learning");
        if($item->get_tags()) {
            $tags = explode(",",$item->get_tags());
        }
        $name = "ERROR201209042241: No Name";
        if($item->get_name()) {
            $name = $item->get_name();
        }
        $username = "ERROR201209042242: No User Name";
        if($item->get_user()) {
            $username = $item->get_user()->get_name();
        }
    ?>    
    <div class="well market-item" style="background-color: #fff;">
        <div class="marketIconContainer">
            <div class="marketIcon <?=$defaultIconClass; ?>"></div>
            <div class="market-cost bggreen">FREE</div>
            <div class="market-rating"><DIV class="star"></DIV><DIV class="star"></DIV><DIV class="star"></DIV><DIV class="star"></DIV><DIV class="star"></DIV></div>
            <div class="market-numrun"><img src="/images/running_man.png" /><?=number_format($itemruncount); ?></div>
        </div>
        <div class="market-item-summary">
            <?if ($item->get_details()) { ?>
            <div class="btn btn-primary zbutton-market-item-details" style="float: right;" onclick="$('#<?=$item->get_id();?>_details').slideDown('slow');">Learn More</div>
            <?}?>
            <div class="market-item-title-container"><span class="market-item-title"><?=$name; ?></span> by <span class="market-item-author"><?=$username; ?></span><div class="medal gold"></div></div>
            <div class="market-item-slogan"><?=$slogan; ?></div>
            <div class="market-item-description"><?=$description; ?></div>
            <div class="market-item-tags"><? foreach($tags as $tag) { ?><span class="label tag"><?=$tag; ?></span><? } ?></div>
        </div>
        <?if ($item->get_details()) { ?>
        <div id="<?=$item->get_id(); ?>_details" class="marketDetailsContainer">
            <?=$item->get_details(); ?>
             <BR>
            <a href="javascript:void(0);" class="btn btn-primary" onclick="$('#<?=$item->get_id();?>_details').slideUp('slow');">Close Details</a>
            <div class="btn btn-primary">Add</div>
        </div>
        <?} ?>
    </div>
<? } ?>    

