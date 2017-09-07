
function KEPollJQuery(module,object,lastPoll,interval,duration,callback){
    console.log(lastPoll);

    if(typeof pollBreak == 'undefined'){
        pollBreak = Array();
    }
    if(typeof pollBreak[module] == 'undefined'){
        pollBreak[module] = Array();
        pollBreak[module]['_base'] = false;
    }

/*    if(lastPoll == undefined){
        lastPoll = Array();
        lastPoll[module] = Array();
        lastPoll[module]['_base'] = '';
    }
    var cLastPoll = lastPoll[module]['_base'];*/
    if(object != null){
        pollBreak[module][object] = false;
       /* lastPoll[module][object] = '';
        cLastPoll = lastPoll[module][object];*/
    }else{
        pollBreak[module]['_base'] = false;
    }



    $.ajax({
        url: "/Systeme/Utils/pollAll.json",
        method: "POST",
        data: {
            pollModule : module,
            pollObject : object,
            pollStart : lastPoll,
            pollInterval : interval,
            pollDuration : duration
        },
        success: function(data,code,jqXhr){
           /* if(object != null){
                lastPoll[module][object] = data.lastSearch;
            }else{
                lastPoll[module]['_base'] = data.lastSearch;
            }*/
            lastPoll = data.lastSearch;
            callback(data);
        },
        error :function(jqXhr,error,exception){
                console.log(error);
        }
    }).always(function(){
        console.log(pollBreak);
        var test = pollBreak[module]['_base'];
        if(object != null){
            test = pollBreak[module][object];
        }

        if(!test && typeof fullPollBreak == 'undefined')
            KEPollJQuery(module,object,lastPoll,interval,duration,callback);
    });

    return false;
}

function KEPollAngular(module,object,lastPoll,interval,duration,callback){
    pollBreak = false;

    var data = {
        pollModule : module,
        pollObject : object,
        pollStart : lastPoll,
        pollInterval : interval,
        pollDuration : duration
    };
    $http.post("/Systeme/Utils/pollEvents.json",data)
        .success(function(data) {
            callback(data);
            KEPollAngular(module,object,lastPoll,interval,duration,callback);
        });


    return false;
}