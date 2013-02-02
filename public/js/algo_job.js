function Job() {
    this.SDK = new AlgorithmsIO_SDK();
    this.myData = {};
    this.dirty = true;
    this.myid;

    this.id = function(newId) {
        if(newId) {
            this.myid = newId;
        }
        return this.myid;
    }
    
    this.isDirty = function() {
        return this.dirty;
    }
    
    this.setClean = function() {
        console.log("DEBUG201209091624: Setting Clean");
        this.dirty = false;
        $('#button_saveAlgorithm').addClass("disabled"); // No need to save as we are clean
    }
    
    this.setDirty = function() {
        console.log("DEBUG201209091625: Setting Dirty");
        this.dirty = true;
        $('#button_saveAlgorithm').removeClass("disabled"); // Show Save Button
    }
    
    this.get = function(key) {
        if(key) {
            if(this.myData[key]) {
                return this.myData[key];
            } else {
                return "";
            }
        }
        return this.myData;
    }
    
    this.set = function(key, value) {
        this.myData[key] = value;
        this.setDirty();
    }
    
    this.save = function() {
        // Save this beast
        var me = this;
        var afterSave = function(result) {
            console.log("DEBUG201209091626: afterSave response: ");
            console.log(result); 
            if(me.id()) {
                console.log("DEBUG2012090091627: Already have an id="+algoIDE.id());
                // Do nothing if we have an id already 
            } else {
                if(result["job_id"]) {
                    me.id(result["id"]);
                    //var curlocation = window.location;
                    //window.location.search = '?algorithm_id='+result["id"];
                    window.location = "/dashboard";
                } else {
                    alert("ERROR201209091628: Saving of the job failed, as we did not get a result.");
                    console.log("ERROR201209091629: Saving of the job did not return an ID:");
                    console.log(result);
                }
            }
            me.setClean();            
        }
        var flowData = $.extend({},this.myData, {id: this.myData["flow_id"]});
        var jobData = {job:{flow: flowData}};
        $.when(this.SDK.saveJob(null, jobData)).then([afterSave]);
        
    }    
}