
function number_format(number, decimals, decPoint, thousandsSep)
{
    decimals = decimals || 0;
    number = parseFloat(number);

    if(!decPoint || !thousandsSep){
        decPoint = '.';
        thousandsSep = ',';
    }

    var roundedNumber = Math.round( Math.abs( number ) * ('1e' + decimals) ) + '';
    var numbersString = decimals ? roundedNumber.slice(0, decimals * -1) : roundedNumber;
    var decimalsString = decimals ? roundedNumber.slice(decimals * -1) : '';
    var formattedNumber = "";

    while(numbersString.length > 3){
        formattedNumber += thousandsSep + numbersString.slice(-3)
        numbersString = numbersString.slice(0,-3);
    }

    return (number < 0 ? '-' : '') + numbersString + formattedNumber + (decimalsString ? (decPoint + decimalsString) : '');
}

var loadScript;

$(function(){
    
    
    loadScript = function(url)
    {
    	$.ajax({
    		url: url,
    		type: 'get',
    		dataType: 'script',
    		async: false,
    		success: function() {
    			console.info('Loaded: '+url);
    		},
    		error: function(xhr, error, text) {
    			alert(error + ' - ' + text);
    		},
    	});
    } 
    
    

    writeLog = function(serverError, errorCode, type)
    {
        $.ajax({
            url: $("body").attr("data-path") + "/public/?module=Audit&controller=Log&view=writeLog",
            type: 'post',
            data: { msg: serverError, code: errorCode, type:  type},
            error: function(jqXHR, textStatus, errorThrown)
            {
                var UI = new JScriptRender.jquery.UI();
                UI.dialog({
                    id: 'ERROR-LOG',
                    title: "ERROR!",
                    width: 300,
                    content: "<p><span class='text-danger text-justify'>Ha ocurrido un error al escribir el registro de LOG.</span></p> \
                              <div class='alert alert-danger'><strong>" + textStatus + ":</strong>" + errorThrown + "</div> \
                              " + serverError
                });
            }
        });
    };

$( "body" ).delegate( ".texto", "keypress", function(event) { 
   return [8, 9, 32, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 65, 66, 67, 68, 69, 70, 71, 72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90, 97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,209].indexOf(event.which) > -1;  
    
 }); 
    
 $( "body" ).delegate( ".numeros", "keypress", function(event) { 
   return [8, 9, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57 ].indexOf(event.which) > -1;  
    
 }); 



    /* EXPORTAR DATOS A EXCEL */
    $("body").delegate(".general-export-button","click", function(event)
    {
        //title
       
        event.preventDefault();
        var div = document.createElement("div");

        var tbl = $(this).attr('data-table');
        var baseURL = $(this).attr('data-base-url');

        var _table = $("#" + tbl);

        _table = _table.clone();

        $.each(_table.children("tbody").children("tr").children("td"), function(){
            this.style.border = "solid 1px #06a2ec";
        });
        $.each(_table.children("thead").children("tr").children("th"), function(){
            this.style.border = "solid 1px #463265";
        });

        div.appendChild(_table[0]);
        table = $(div).html();

        var doc_title = "Report";

        if (_table.children("caption").length)
        {
            var caption = _table.children("caption");

            var div = document.createElement("div");
            var _title = caption.clone();
            div.appendChild(_title[0]);
            title = $(div).html();

            var data = title + "<br /><br />" + table;
            var doc_title = _title.text();
        }
        
       

        var _title = _table.attr('data-title') || "";
        
        

        var data = table;
        var _file_name = _title;
        var file_name = window.btoa(unescape(encodeURIComponent(_file_name)));
        var title = (_title == "") ? file_name : _title;

        $.ajax({
            type: "POST",
            url: baseURL + "/library/Calc-Writer/writer.php",
            data: "data=" + window.btoa(encodeURIComponent(data)),
            success: function(datos){
                window.location = baseURL + "/library/Calc-Writer/xls.php?name=" + file_name + "&title=" + title;
            }
        });
    });
    
    
   $("body").delegate("[data-action='ajax-request-blur']", "blur", function(event)
    {
        event.preventDefault();
    

        var url = $(this).attr('href');
        var type = $(this).attr('data-type');
        var box = $(this).attr('data-response');
        var data = $(this).attr('data-object');

        var call = eval($(this).attr('data-callback')) || {};
        call.success = call.success || new Function();
        call.before = call.before || new Function();
        call.error = call.error || new Function();

        $.ajax({
            url: url,
            type: type,
            data: eval(data),
            beforeSend: function() {
                $(box).html("<img class='responsive-image-320' src='img/preloader.gif' width='50' />");
                call.before();
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                $(box).html("<div class='alert alert-danger'>Ha ocurrido al procesar la petición!.</div>");

                var e = {};
                e.jqXHR = jqXHR;
                e.textStatus = textStatus;
                e.errorThrown = errorThrown;

                var traza = (e.jqXHR.readyState == 0) ? 'Request not initialized. ' : '';
                traza = traza + e.errorThrown;

                $("#message-fluid-bar").append("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button><strong>Error!</strong> Ha ocurrido un error al procesar la petición. </em>. " + traza + " </div>");
                call.error(e);
            },
            success: function(data)
            {
                $(box).html(data);
                call.success();
            }
        });
    });


      $("body").delegate("[data-action='ajax-request-keyup']", "keyup", function(event)
    {
        //event.preventDefault();
    

        var url = $(this).attr('href');
        var type = $(this).attr('data-type');
        var box = $(this).attr('data-response');
        var data = $(this).attr('data-object');

        var call = eval($(this).attr('data-callback')) || {};
        call.success = call.success || new Function();
        call.before = call.before || new Function();
        call.error = call.error || new Function();

        $.ajax({
            url: url,
            type: type,
            data: eval(data),
            beforeSend: function() {
                $(box).html("<img class='responsive-image-320' src='img/preloader.gif' width='50' />");
                call.before();
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                $(box).html("<div class='alert alert-danger'>Ha ocurrido al procesar la petición!.</div>");

                var e = {};
                e.jqXHR = jqXHR;
                e.textStatus = textStatus;
                e.errorThrown = errorThrown;

                var traza = (e.jqXHR.readyState == 0) ? 'Request not initialized. ' : '';
                traza = traza + e.errorThrown;

                $("#message-fluid-bar").append("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button><strong>Error!</strong> Ha ocurrido un error al procesar la petición. </em>. " + traza + " </div>");
                call.error(e);
            },
            success: function(data)
            {
                $(box).html(data);
                call.success();
            }
        });
    });
    
    

    $("body").delegate("[data-action='ajax-request']", "click", function(event)
    {
        event.preventDefault();
        

        var url = $(this).attr('href');
        var type = $(this).attr('data-type');
        var box = $(this).attr('data-response');
        var data = $(this).attr('data-object');
        
        console.info(box);
        
        

        var call = eval($(this).attr('data-callback')) || {};
        call.success = call.success || new Function();
        call.before = call.before || new Function();
        call.error = call.error || new Function();

        $.ajax({
            url: url,
            type: type,
            data: eval(data),
            beforeSend: function() {
                $(box).html("<img class='responsive-image-320' src='img/preloader.gif' width='50' />");
                call.before();
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                $(box).html("<div class='alert alert-danger'>Ha ocurrido un error al procesar la petición!.</div>");

                var e = {};
                e.jqXHR = jqXHR;
                e.textStatus = textStatus;
                e.errorThrown = errorThrown;

                var traza = (e.jqXHR.readyState == 0) ? 'Request not initialized. ' : '';
                traza = traza + e.errorThrown;

                $("#message-fluid-bar").append("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button><strong>Error!</strong> Ha ocurrido un error al procesar la petición. </em>. " + traza + " </div>");
                call.error(e);
            },
            success: function(data)
            {
                $(box).html(data);
                call.success();
            }
        });
    });

    $("body").delegate("[data-action='ajax-request-on-change']", "change", function(event)
    {
       
        console.info("Load 3");
        var url = $(this).attr('href');
        var type = $(this).attr('data-type');
        var box = $(this).attr('data-response');
        //var script = $(this).attr('data-load');
        
        
      
        var data = $(this).attr('data-object');

        var call = eval($(this).attr('data-callback')) || {};
        call.success = call.success || new Function();
        call.before = call.before || new Function();
        call.error = call.error || new Function();

        $.ajax({
            url: url,
            type: type,
            data: eval(data),
            beforeSend: function() {
                $(box).html("<img class='responsive-image-320' src='img/preloader.gif' width='50' />");
                call.before();
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                $(box).html("<div class='alert alert-danger'>Ha ocurrido al procesar la petición!.</div>");

                var e = {};
                e.jqXHR = jqXHR;
                e.textStatus = textStatus;
                e.errorThrown = errorThrown;

                var traza = (e.jqXHR.readyState == 0) ? 'Request not initialized. ' : '';
                traza = traza + e.errorThrown;

                $("#message-fluid-bar").append("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button><strong>Error!</strong> Ha ocurrido un error al procesar la petición. </em>. " + traza + " </div>");
                call.error(e);
            },
            success: function(data)
            {
                /*if(script!=""){
                    
                    loadScript(script);
                }*/
               
                $(box).html(data);
                call.success();
            }
        });
    });

    $("body").delegate("[data-action='show-dialog']", "click", function(event)
    {
        console.info("Load 4");
        event.preventDefault();

        var _url = $(this).attr('data-url');

        var _title = $(this).attr('data-title');

        

        var _id = $(this).attr('data-id');
        var _width = $(this).attr('data-width');

       // alert(_width);

        var _overlay = $(this).attr('data-overlay');

        var _type = $(this).attr('data-type');
        var _data = $(this).attr('data-object');
       
       
        _width = (_width == "small") ? "modal-sm" : _width;
        _width = (_width == "large") ? "modal-lg" : _width;

        if (!$('#'+_id).length)
            $("body").append("<div id='" + _id + "' class='modal fade' tabindex='-1' role='dialog'>" +
                                "<div class='modal-dialog " + _width + "'>" +
                                    "<div class='modal-content'>" +
                                      "<div class='modal-header'>" +
                                        "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" +
                                        "<h4 class='modal-title'>" + _title + "</h4>" +
                                      "</div>" +
                                      "<div class='modal-body'>" +
                                        "<p>One fine body&hellip;</p>" +
                                      "</div>" +
                                      "<div class='modal-footer'>" +
                                      "</div>" +
                                    "</div>" +
                                  "</div>" +
                                "</div>");

        var box = $('#'+_id);

        box.modal();

        var call = eval($(this).attr('data-callback')) || {};
        call.success = call.success || new Function();
        call.before = call.before || new Function();
        call.error = call.error || new Function();

        $.ajax({
            url: _url,
            type: _type,
            data: eval(_data),
            beforeSend: function() {
                $(box).find(".modal-body").html("<img class='responsive-image-320' src='img/preloader.gif' width='50' />");
                call.before();
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                $(box).html("<div class='alert alert-danger'>Ha ocurrido al procesar la petición!.</div>");

                var e = {};
                e.jqXHR = jqXHR;
                e.textStatus = textStatus;
                e.errorThrown = errorThrown;

                var traza = (e.jqXHR.readyState == 0) ? 'Request not initialized. ' : '';
                traza = traza + e.errorThrown;

                $("#message-fluid-bar").append("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button><strong>Error!</strong> Ha ocurrido un error al procesar la petición. </em>. " + traza + " </div>");
                call.error(e);
            },
            success: function(data)
            {
                
                $(box).find(".modal-body").html(data);
                call.success();
            }
        });
    });


    $("body").delegate("[data-action='show-dialog-focus']", "focus", function(event)
    {
        console.info("Load 4");
        event.preventDefault();

        var _url = $(this).attr('data-url');

        var _title = $(this).attr('data-title');

        

        var _id = $(this).attr('data-id-modal');
        var _width = $(this).attr('data-width');

        

        var _overlay = $(this).attr('data-overlay');

        var _type = $(this).attr('data-type');
        var _data = $(this).attr('data-object');
       
       
        _width = (_width == "small") ? "modal-sm" : _width;
        _width = (_width == "large") ? "modal-lg" : _width;

        if (!$('#'+_id).length)
            $("body").append("<div id='" + _id + "' class='modal fade' tabindex='-1' role='dialog'>" +
                                "<div class='modal-dialog " + _width + "'>" +
                                    "<div class='modal-content'>" +
                                      "<div class='modal-header'>" +
                                        "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" +
                                        "<h4 class='modal-title'>" + _title + "</h4>" +
                                      "</div>" +
                                      "<div class='modal-body'>" +
                                        "<p>One fine body&hellip;</p>" +
                                      "</div>" +
                                      "<div class='modal-footer'>" +
                                      "</div>" +
                                    "</div>" +
                                  "</div>" +
                                "</div>");

        var box = $('#'+_id);

        box.modal();

        var call = eval($(this).attr('data-callback')) || {};
        call.success = call.success || new Function();
        call.before = call.before || new Function();
        call.error = call.error || new Function();

        $.ajax({
            url: _url,
            type: _type,
            data: eval(_data),
            beforeSend: function() {
                $(box).find(".modal-body").html("<img class='responsive-image-320' src='img/preloader.gif' width='50' />");
                call.before();
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                $(box).html("<div class='alert alert-danger'>Ha ocurrido al procesar la petición!.</div>");

                var e = {};
                e.jqXHR = jqXHR;
                e.textStatus = textStatus;
                e.errorThrown = errorThrown;

                var traza = (e.jqXHR.readyState == 0) ? 'Request not initialized. ' : '';
                traza = traza + e.errorThrown;

                $("#message-fluid-bar").append("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button><strong>Error!</strong> Ha ocurrido un error al procesar la petición. </em>. " + traza + " </div>");
                call.error(e);
            },
            success: function(data)
            {
                
                $(box).find(".modal-body").html(data);
                call.success();
            }
        });
    });

    $("body").delegate("[data-action='show-dialog-on-change']", "change", function(event)
    {
        console.info("Load 5");
        event.preventDefault();

        var _url = $(this).attr('data-url');

        var _title = $(this).attr('data-title');
        var _id = $(this).attr('data-id');
        var _width = $(this).attr('data-width');
        var _overlay = $(this).attr('data-overlay');

        var _type = $(this).attr('data-type');
        var _data = $(this).attr('data-object');

        _width = (_width == "small") ? "modal-sm" : _width;
        _width = (_width == "large") ? "modal-lg" : _width;

        if (!$('#'+_id).length)
            $("body").append("<div id='" + _id + "' class='modal fade' tabindex='-1' role='dialog'>" +
                                "<div class='modal-dialog " + _width + "'>" +
                                    "<div class='modal-content'>" +
                                      "<div class='modal-header'>" +
                                        "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" +
                                        "<h4 class='modal-title'>" + _title + "</h4>" +
                                      "</div>" +
                                      "<div class='modal-body'>" +
                                        "<p>One fine body&hellip;</p>" +
                                      "</div>" +
                                      "<div class='modal-footer'>" +
                                      "</div>" +
                                    "</div>" +
                                  "</div>" +
                                "</div>");

        var box = $('#'+_id);

        box.modal();

        var call = eval($(this).attr('data-callback')) || {};
        call.success = call.success || new Function();
        call.before = call.before || new Function();
        call.error = call.error || new Function();

        $.ajax({
            url: _url,
            type: _type,
            data: eval(_data),
            beforeSend: function() {
                $(box).find(".modal-body").html("<img class='responsive-image-320' src='img/preloader.gif' width='50' />");
                call.before();
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                $(box).html("<div class='alert alert-danger'>Ha ocurrido al procesar la petición!.</div>");

                var e = {};
                e.jqXHR = jqXHR;
                e.textStatus = textStatus;
                e.errorThrown = errorThrown;

                var traza = (e.jqXHR.readyState == 0) ? 'Request not initialized. ' : '';
                traza = traza + e.errorThrown;

                $("#message-fluid-bar").append("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button><strong>Error!</strong> Ha ocurrido un error al procesar la petición. </em>. " + traza + " </div>");
                call.error(e);
            },
            success: function(data)
            {
                $(box).find(".modal-body").html(data);
                call.success();
            }
        });
    });

    $("body").delegate("[data-action='show-dialog-on-submit']", "submit", function(event)
    {
        console.info("Load 6");
        event.preventDefault();

        var _url = $(this).attr('action');

        var _title = $(this).attr('data-title');
        var _id = $(this).attr('data-id');
        var _width = $(this).attr('data-width');
        var _overlay = $(this).attr('data-overlay');

        var _type = $(this).attr('data-type');
        var _data = $(this).attr('data-object');

        _width = (_width == "small") ? "modal-sm" : _width;
        _width = (_width == "large") ? "modal-lg" : _width;

        if (!$('#'+_id).length)
            $("body").append("<div id='" + _id + "' class='modal fade' tabindex='-1' role='dialog'>" +
                                "<div class='modal-dialog " + _width + "'>" +
                                    "<div class='modal-content'>" +
                                      "<div class='modal-header'>" +
                                        "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" +
                                        "<h4 class='modal-title'>" + _title + "</h4>" +
                                      "</div>" +
                                      "<div class='modal-body'>" +
                                        "<p>One fine body&hellip;</p>" +
                                      "</div>" +
                                      "<div class='modal-footer'>" +
                                      "</div>" +
                                    "</div>" +
                                  "</div>" +
                                "</div>");

        var box = $('#'+_id);

      //  box.modal();
        
        $(box).modal({
            backdrop: 'static',
            keyboard: false  
        });
        

        var call = eval($(this).attr('data-callback')) || {};
        call.success = call.success || new Function();
        call.before = call.before || new Function();
        call.error = call.error || new Function();

        var form_data = $(this).serializeArray();

        var parsed = eval(_data);

        for (var i in parsed)
        {
            form_data.push({ name: i, value: parsed[i] });
        }

        $.ajax({
            url: _url,
            type: _type,
            data: form_data,
            beforeSend: function() {
                $(box).find(".modal-body").html("<img class='responsive-image-320' src='img/preloader.gif' width='50' />");
                call.before();
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                $(box).html("<div class='alert alert-danger'>Ha ocurrido al procesar la petición!.</div>");

                var e = {};
                e.jqXHR = jqXHR;
                e.textStatus = textStatus;
                e.errorThrown = errorThrown;

                var traza = (e.jqXHR.readyState == 0) ? 'Request not initialized. ' : '';
                traza = traza + e.errorThrown;

                $("#message-fluid-bar").append("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button><strong>Error!</strong> Ha ocurrido un error al procesar la petición. </em>. " + traza + " </div>");
                call.error(e);
            },
            success: function(data)
            {
                $(box).find(".modal-body").html(data);
                call.success();
            }
        });
    });

    $("body").delegate("[data-role='ajax-request']", "submit", function(event)
    {
        //console.info("Load 7");
        event.preventDefault();

        var url = $(this).attr('action');
        var type = $(this).attr('method');
        var box = $(this).attr('data-response');
        var data = $(this).attr('data-object');

        var call = eval($(this).attr('data-callback')) || {};
        call.success = call.success || new Function();
        call.before = call.before || new Function();
        call.error = call.error || new Function();

        var form_data = $(this).serializeArray();

        var parsed = eval(data);

        for (var i in parsed)
        {
            form_data.push({ name: i, value: parsed[i] });
        }

        $.ajax({
            url: url,
            type: type,
            data: form_data,
            beforeSend: function() {
                $(box).html("<img class='responsive-image-320' src='img/preloader.gif' width='50' />");
                call.before();
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                $(box).html("<div class='alert alert-danger'>Ha ocurrido al procesar la petición!.</div>");

                var e = {};
                e.jqXHR = jqXHR;
                e.textStatus = textStatus;
                e.errorThrown = errorThrown;

                var traza = (e.jqXHR.readyState == 0) ? 'Request not initialized. ' : '';
                traza = traza + e.errorThrown;

                $("#message-fluid-bar").append("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button><strong>Error!</strong> Ha ocurrido un error al procesar la petición. </em>. " + traza + " </div>");
                call.error(e);
            },
            success: function(data)
            {
                $(box).html(data);
                call.success();
            }
        });
    });

    $('[data-toggle="tooltip"]').tooltip();

    $("body").delegate("[data-role='ajax-push-request']", "click", function(event)
    {
        console.info("Load 8");
        event.preventDefault();

        Concurrent.Thread.create(function(that){

            var url = that.attr('data-url');
            var box = that.attr('data-response');
            var _html = that.attr('data-html');
            var data = that.attr('data-object');

            var call = eval(that.attr('data-callback')) || {};
            call.success = call.success || new Function();
            call.error = call.error || new Function();
            call.before = call.before || new Function();

            call.before();
            var comet = new $jS.jquery.Comet({ url: url });

            var settings = {
                url: url,
                data: eval(data),
                callback: {
                    success: function(data)
                    {
                        // Connection established
                        if (typeof data != "object")
                           data = $.parseJSON(data);

                        if (_html == "append")
                            $(box).append(data.message);
                        else
                            $(box).html(data.message);

                        call.success(data);
                    },
                    error: function(jqXHR, textStatus, errorThrown)
                    {
                        var e = {};
                        e.jqXHR = jqXHR;
                        e.textStatus = textStatus;
                        e.errorThrown = errorThrown;

                        comet.disconnect();
                        call.error(e);
                    },
                    complete: function()
                    {
                        // For each request
                    },
                    disconnect: function(){
                        console.info('disconnected');
                    }
                }
            }

            comet.connect(settings);
            call.success();

        }, $(this));

    });
});
