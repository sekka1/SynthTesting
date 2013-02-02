function DataSource(dataSourceId) {
    this.SDK = new AlgorithmsIO_SDK();
    this.myData = {};
    
    this.retrieve = function() {
        // Get the algorithm data
        var mythis = this; // so this survives
        return $.when(this.SDK.getDataSource(dataSourceId)).then(function(result){mythis.updateFromObj(result);});
    };
    
    this.updateFromObj = function(data) {
        console.log("DEBUG201208230950: ");
        console.log(data);
        console.log(this);
        this.myData = data;
        this.setName(data["name"]);
        this.setDescription(data["description"]);
        this.setTags(data["tags"]);
        if(typeof data["outputParams"] != "undefined") {
            this.setOutputParams(data["outputParams"]);
        }
    }
    
    this.setData = function(key, value) {
        this.myData[key] = value;
    }
    
    this.save = function() {
        // Save this beast
        this.SDK.saveDataSource(dataSourceId, this.myData)
        
    }
    
    this.setDataSource = function(data) {
        this.datasource = data;
        console.log(data);
        if($('#form_inputParams_from_DataSource').is(':checked')) {
            var inputparams = JSON.stringify(data["outputParams"], null, '\t');
            $('#form_inputparams').val(inputparams);
            this.setInputParams(inputparams);
        } else {
            console.log($('#form_inputParams_from_DataSource'));
            alert('ugh');
        }
    }
    
    this.setSampleDataSourceId = function(datasource_id) {
        return this.setDataSourceId(datasource_id);
    }
    
    this.setName = function(name) {
        this.myData["name"] = name;
        $('#form_datasource_name').val(name); // Popup form
    }
    
    this.setDescription = function(description) {
        this.myData["description"] = description;
        $('#form_datasource_description').val(description); // Popup form
    }

    this.setTags = function(tags) {
        this.myData["tags"] = tags;
        $('#form_datasource_tags').val(tags); // Popup form
    }
    
    this.setInputParams = function(inputparams) {
        alert("ERROR201208221013: A Data Source does not support input parameters.");
        this.myData["inputParams"]=inputparams;
        $('#form_datasource_inputparams').val(inputparams);
    }
    
    this.setOutputParams = function(outputparams) {
        this.myData["outputParams"]=outputparams;
        $('#form_datasource_outputparams').val(outputparams);
    }    
}   

/********************* IDE UI Helper Functions ************************/


function validateDataSourceProperties() {
    $('#button_properties_close').addClass('disabled');
    if($('#form_datasource_name').val().length > 10) {
        // Should really do ajax to validate name hasn't been taken? - MRR20120820
        if($('#form_datasource_description').val().length > 20) {
            $('#button_properties_close').removeClass('disabled');
        }
    } 
}

function closeDataSourceProperties() {
    datasourceobj.setName($('#form_datasource_name').val());
    datasourceobj.setDescription($('#form_datasource_description').val());
    datasourceobj.setData("tags", $('#form_datasource_tags').val());
    datasourceobj.save();
    $('#datasource_list').flexReload();
    restoreTabsDiv($('#div_datasource_properties'));
}


