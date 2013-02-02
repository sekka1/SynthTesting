<?php ?>
<SCRIPT>
$(function (){
   /* This is an AlgorithmsIO object containing InputParams as defined by the Visualization
    * Generally, the data should have already been mapped to the InputParams by this point.
    */
    var inputParams = new VisualizationInputParams(); 
   /*
    * Currently, we grab all of the data using Ajax initially. Later we will want lazy-loading capabilities.
    */
    inputParams.retrieve();
    
   /*
    * // Get the data from a named inputParam in JSON format
    * var jsonDS = JSON.stringify(inputParams.getData("datasource1"));
    * // Get the data from the first datasource found in the inputParams and return as a regular Javascript Object/Array
    * var dsObj = inputParams.getData(0);
    * // Get an inputParameter value by name
    * var maxResults = inputParams.getParam("Maximum Results");
    * // Get Meta informatin regarding a specific parameter
    * var amountMeta = inputParams.getParamMeta("amount");
    * // Get all of the inputParams (including data and meta information) as one big array structure
    * var allParams = inputParams.getAll(); // Not recommended
    * // Get an array in key=>value of the inputParams 
    */
    
    
});

/******************************* YOUR OBJECT CODE GOES HERE ********************************/
var myCustomVisualization = function(inputParams) {
    
}
</SCRIPT>