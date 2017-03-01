/*
 * jRender v0.15 - Javascript renderization tools
 * http://www.pleets.org
 * Copyright 2014, Pleets Apps
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2014-03-02
 *

  Release Notes

  - Nothing

 */

/* jRender namespace */
if (!(jRender instanceof Object))
    var jRender = {};

$(function () {

    /* jRender alias */
    var API = (jRender instanceof Object) ? jRender : window;

    /* General buffer */
    API.buffer = {};
    API.buffer.interval = {};        // Intervals

/* 
 *   AJAX Class
 */

    API.Ajax = function() {
        return this;
    }

    API.Ajax.prototype = {
        formProcess: function(formElementToProcess, settings)
        {
            var HTML = new jRender.Html();
            var DEBUG = new jRender.Debug();

            var set = settings || {};

            // Highlight inputs on error
            set.highlight = (set.highlight !== undefined) ? set.highlight : true; 

            set.id = set.id || "dialog-ui";
            set.title = set.title || "Server request";
            set.width = set.width || 400;
            set.modal = (set.modal !== undefined) ? set.modal : true;
            set.position = set.position || "center";

            set.debug = (set.debug !== undefined) ? set.debug : false;

            set.callback = (set.callback instanceof Object) ? set.callback: {};

            // Error callback
            set.callback.error = set.callback.error || new Function();

            // Debug Callbacks
            set.callback.debug = (set.callback.debug instanceof Object) ? set.callback.debug: {};
            set.callback.debug.success = set.callback.debug.success || new Function();
            set.callback.debug.error = set.callback.debug.error || new Function();

            // Ajax Callbacks
            set.callback.ajax = (set.callback.ajax instanceof Object) ? set.callback.ajax: {};
            set.callback.ajax.beforeSend = set.callback.ajax.beforeSend || new Function();
            set.callback.ajax.complete = set.callback.ajax.complete || new Function();
            set.callback.ajax.success = set.callback.ajax.success || new Function();
            set.callback.ajax.error = set.callback.ajax.error || new Function();

            $("body").delegate(formElementToProcess, "submit", function(event)
            {
                var that = $(this);
                event.preventDefault();

                var form_id = formElementToProcess.replace("#",'');

                var url = $(this)[0].getAttribute("action");
                var _url = (url == null || url.trim() == "") ? document.URL : url;

                var data = new Object();
                $.each($(formElementToProcess).serializeArray(), function() {
                    data[this.name] = this.value;
                });

                var _data = JSON.stringify(data);

                set.buttons = set.buttons || {
                    "Accept": function() {
                        $(this).dialog("close");
                    }
                };

                _validators = (set.validators !== "undefined" && set.validators) ? set.validators : false;

                if (_validators)
                {
                	var InputFilter = new API.InputFilter.InputFilter();
                	for (var input in set.validators)
                	{
                		InputFilter.add({ name: input, validators: set.validators[input]});
                	}

                	var invalid = (InputFilter.getInvalidInput().length);

                	// Refresh
                	if (set.highlight)
                	{
                		var inputs = InputFilter.getValidInput();
                		for (var i = inputs.length - 1; i >= 0; i--) {
                			var classes = inputs[i].className.split(" ");
                			var classString = "";
                			for (var j = classes.length - 1; j >= 0; j--) {
                				if (classes[j] != "input-error")
                					classString += " " +classes[j];
                			};
                			inputs[i].className = classString;
                		};
                	}                	

                    if (invalid && set.debug)
                    {
                        return HTML.dialog({
                            id: set.id,
                            title: set.title,
                            content: $("<div id='" + form_id + "'> \
                                            <div><h3>Warning!</h3></div> \
                                            <p>Message: <strong>Missing parameters!</strong></p> \
                                            Type: " + "validator" + "<br /> \
                                            Response: " + JSON.stringify(invalid) + "<br /> \
                                        </div>"),
                            width: set.width,
                            modal: set.modal,
                            position: set.position,
                            persistence: false,
                            buttons: set.buttons,
                        }, set.callback.debug.error());				// Debug error callback
                    }
                    else if (invalid)
                    {
                    	if (set.highlight)
                    	{
                    		var inputs = InputFilter.getInvalidInput();
                    		for (var i = inputs.length - 1; i >= 0; i--) {
                    			inputs[i].className = inputs[i].className + " input-error";
                    			if (i == 0)
                    				inputs[i].focus();
                    		};
                    	}
                        return set.callback.error();				// Error callback
                    }
                    else {
                    	set.callback.debug.success();
                    }
                }

                if (set.debug)
                {
                    HTML.dialog({
                        id: set.id,
                        title: set.title,
                        content: $("<div id='" + form_id + "'> \
                                        <div><h3>Request</h3></div> \
                                        Data: " + _data + "<br /> \
                                        Url: " + _url + "<br /> \
                                    </div>"),
                        width: set.width,
                        modal: set.modal,
                        position: set.position,
                        persistence: false,
                        buttons: set.buttons,
                    }, set.callback.debug.success());				// Debug success callback
                }
                else {
                    $.ajax({
                        url: _url,
                        type: 'post',
                        data: data,
                        dataType: 'html',
                        async: true,
                        error: function(jqXHR, textStatus, error) {
                            HTML.loader();
                            DEBUG.ajaxError(jqXHR, textStatus, error);
                            set.callback.ajax.error();
                        },
                        beforeSend: function() {
                            HTML.loader();
                            set.callback.ajax.beforeSend();
                        },
                        success: function(data) {
                            HTML.loader();
                            HTML.dialog({
                                id: set.id,
                                title: set.title,
                                content: data,
                                width: set.width,
                                modal: set.modal,
                                position: set.position,
                                persistence: false,
                                buttons: set.buttons,
                            });
                            set.callback.ajax.success();
                        },
                        complete: function() {
                        	set.callback.ajax.complete();
                        }
                    });
                }

            });

        },
        search: function(input, view, settings)
        // void search( jQuery_element, jQuery_element [, Object] )
        {
            var HTML = new jRender.Html();
            var DEBUG = new jRender.Debug();

            // Settings
            var set = settings || {};

            // Callbacks
            set.ajaxCallback = (set.ajaxCallback instanceof Object) ? set.ajaxCallback: {};
            set.ajaxCallback.success = set.ajaxCallback.success || new Function();
            set.ajaxCallback.complete = set.ajaxCallback.complete || new Function();

            var url = input[0].getAttribute("data-url");

            // Runtime parameters (Arrays and Objects)
            var parameters;

            try {
                if (input[0].getAttribute("data-runtime-parameters") != null && input[0].getAttribute("data-runtime-parameters").trim() != "")
                {
                    eval("var parameters = " + input[0].getAttribute("data-runtime-parameters"));
                    if (!(parameters instanceof Object) && !(parameters instanceof Array))
                        throw DEBUG.exception("Invalid type", "Runtime parameters should be an array or object");
                }
            }
            catch (e)
            {
                if (e.name == "Invalid type")
                    DEBUG.error(e);
                else
                    DEBUG.error(e, { 
                        width: 400,
                        suggestion: "Bad runtime parameters in <strong>" + input.selector + "</strong> element"
                    });
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: { request: input.val(), params: parameters },
                dataType: 'html',
                async: true,
                error: function(jqXHR, textStatus, error) {
                    view.empty().append(error);
                    DEBUG.ajaxError(jqXHR, textStatus, error);
                },
                beforeSend: function()
                {
                    view.empty();
                    HTML.loader({ context: view });
                },
                success: function(data)
                {
                    view.empty().append(data);
                    set.ajaxCallback.success();
                },
                complete: function() { set.ajaxCallback.complete() }
            });
        },
        addAction: function(delegateButton, formElementToProcess, settings)
        {
            var HTML = new jRender.Html();
            var DEBUG = new jRender.Debug();

            var set = settings || {};
            set.id = set.id || "dialog-ui";
            set.title = set.title || "Add Action";
            set.width = set.width || 400;
            set.modal = (set.modal !== undefined) ? set.modal : true;
            set.position = set.position || "center";

            set.searchConfig = (set.searchConfig instanceof Object) ? set.searchConfig: {};

            set.searchConfig.input = set.searchConfig.input || undefined;
            set.searchConfig.button = set.searchConfig.button || undefined;
            set.searchConfig.view = set.searchConfig.view || undefined;

            set.ajaxCallback = (set.ajaxCallback instanceof Object) ? set.ajaxCallback: {};

            // GET Callbacks
            set.ajaxCallback.get = (set.ajaxCallback.get instanceof Object) ? set.ajaxCallback.get: {};
            set.ajaxCallback.get.success = set.ajaxCallback.get.success || new Function();
            set.ajaxCallback.get.complete = set.ajaxCallback.get.complete || new Function();

            // POST Callbacks
            set.ajaxCallback.post = (set.ajaxCallback.post instanceof Object) ? set.ajaxCallback.post: {};
            set.ajaxCallback.post.success = set.ajaxCallback.post.success || new Function();
            set.ajaxCallback.post.complete = set.ajaxCallback.post.complete || new Function();

            $("body").delegate(delegateButton, "click", function(event)
            {
                var that = $(this);
                event.preventDefault();

                var getData = function() 
                {
                    var form = $(formElementToProcess);
                    $.ajax({
                        url: $(delegateButton)[0].getAttribute("data-resource"),
                        type: 'GET',
                        data: { request: 'data' },
                        dataType: 'html',
                        async: true,
                        error: function(jqXHR, textStatus, error) {
                            HTML.loader();
                            DEBUG.ajaxError(jqXHR, textStatus, error);
                        },
                        beforeSend: function() {
                            HTML.loader();
                        },
                        success: function(data) {
                            form.empty();
                            form.append(data);
                            HTML.loader();
                            set.ajaxCallback.get.success();
                        },
                        complete: function() {
                            set.ajaxCallback.get.complete();
                        }
                    });
                }

                var form_id = formElementToProcess.replace("#",'');

                HTML.dialog({
                    id: set.id,
                    title: set.title,
                    content: $("<div id='" + form_id + "'></div>"),
                    width: set.width,
                    modal: set.modal,
                    position: set.position,
                    buttons: {
                        "Registrar": function()
                        {
                            var form = $(formElementToProcess);
                            var formElement = $(form.children("form"));

                            if (formElement.length)
                            {
                                var url = $(delegateButton)[0].getAttribute("data-resource");
                                var that = $(this);

                                var data = new Object();
                                $.each(formElement.serializeArray(), function() {
                                    data[this.name] = this.value;
                                });

                                $.ajax({
                                    url: url,
                                    type: 'POST',
                                    data: data,
                                    async: true,
                                    error: function(jqXHR, textStatus, error) {
                                        HTML.loader();
                                        DEBUG.ajaxError(jqXHR, textStatus, error);
                                    },
                                    beforeSend: function() {
                                        HTML.loader();
                                    },
                                    success: function(data) 
                                    {
                                        form.empty();
                                        form.append(data);

                                        if (!form.children("form").length) 
                                        {
                                            if (set.searchConfig.view !== undefined)
                                               $(set.searchConfig.view).empty();
                                            if (set.searchConfig.input !== undefined)
                                               $(set.searchConfig.input).val('');
                                            if (set.searchConfig.button !== undefined)
                                               $(set.searchConfig.button).trigger("click");
                                        }
                                        HTML.loader();
                                        set.ajaxCallback.post.success();
                                    },
                                    complete: function() {
                                        set.ajaxCallback.post.complete();
                                    }
                                });
                            } else {
                                getData();
                            }
                        },
                        "Cancelar": function() {
                            $(this).dialog("close");
                        }
                    }
                }, getData);
            });            
        },
        editAction: function(editSelectionButton, editLoadData, inputSelection, settings)
        {
            var HTML = new jRender.Html();
            var DEBUG = new jRender.Debug();
            var ANIMATION = new jRender.Animation();

            var set = settings || {}
            set.id = set.id || "ui-dialog";
            set.title = set.title || "Editar";
            set.width = set.width || 700;
            set.modal = (set.modal !== undefined) ? set.modal : true;
            set.position = set.position || "center";

            set.searchConfig = (set.searchConfig instanceof Object) ? set.searchConfig: {};

            set.searchConfig.input = set.searchConfig.input || undefined;
            set.searchConfig.button = set.searchConfig.button || undefined;
            set.searchConfig.view = set.searchConfig.view || undefined;

            set.ajaxCallback = (set.ajaxCallback instanceof Object) ? set.ajaxCallback: {};

            // GET Callbacks
            set.ajaxCallback.get = (set.ajaxCallback.get instanceof Object) ? set.ajaxCallback.get: {};
            set.ajaxCallback.get.success = set.ajaxCallback.get.success || new Function();

            // POST Callbacks
            set.ajaxCallback.post = (set.ajaxCallback.post instanceof Object) ? set.ajaxCallback.post: {};
            set.ajaxCallback.post.success = set.ajaxCallback.post.success || new Function();

            $("body").delegate(editSelectionButton, "click", function(event)
            {
                event.preventDefault();
                var selection = $(inputSelection + ":checked");
                var row = selection.parent().parent();

                if (!selection.length)
                    return alert("Debe seleccionar mínimo un elemento!");

                for (var i = selection.length - 1; i >= 0; i--) {
                        $(selection[i]).parent().parent().attr("class", "warning");
                };

                $(editLoadData + " div.item").remove();

                HTML.dialog({
                    title: set.title,
                    id: set.id,
                    content: $('<div class="$ jrender-slide" id="' + editLoadData.replace("#","") + '"> \
                                    <div class="toolbar"> \
                                        <div class="button-left"><span class="glyphicon glyphicon-chevron-left"></span></div> \
                                        <div class="button-right"><span class="glyphicon glyphicon-chevron-right"></span></div> \
                                    </div> \
                                </div>'),
                    width: set.width,
                    modal: set.modal,
                    position: set.position,
                    buttons: {
                        "Guardar": function()
                        {
                            var form = $(editLoadData);
                            var formElements = $(form.children("div").children("form"));

                            var loadData = function (data, callback) {

                                elements = data.length;
                                if (!elements)
                                    return callback();

                                var url = data[elements - 1].getAttribute("action");
                                var item = $(data[elements - 1]).parent();
                                var that = $(this);

                                var ajax_data = new Object();
                                $.each($(data[elements - 1]).serializeArray(), function() {
                                    ajax_data[this.name] = this.value;
                                });

                                $.ajax({
                                    url: url,
                                    type: 'POST',
                                    data: { request: ajax_data, action: "edit"},
                                    async: true,
                                    error: function(jqXHR, textStatus, error) {
                                        DEBUG.ajaxError(jqXHR, textStatus, error);
                                    },
                                    success: function(response) {
                                        item.empty();
                                        item.append(response);
                                        data.splice(data.length - 1 , 1);
                                        loadData(data, callback);
                                    }
                                });
                            }

                            HTML.loader();
                            loadData(formElements, function () { 
                                HTML.loader(); 
                                if (set.searchConfig.view !== undefined)
                                   $(set.searchConfig.view).empty();
                                if (set.searchConfig.input !== undefined)
                                   $(set.searchConfig.input).val('');
                                if (set.searchConfig.button !== undefined)
                                   $(set.searchConfig.button).trigger("click");
                                set.ajaxCallback.post.success();
                            });

                        },
                        "Cancelar": function() {
                            $(this).dialog("close");
                        }
                    }
                });

                var selection = $(inputSelection);
                var url = $(editSelectionButton)[0].getAttribute("data-resource");
                var data = new Array();

                $.each(selection,function(key){
                    if ($(this).is(":checked"))
                        data.push($(this)[0].getAttribute("data-selection-id"));
                });

                var loadData = function (data, callback) {

                    elements = data.length;
                    if (!elements)
                        return callback();

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: { request: data[elements - 1], action: "get" },
                        dataType: 'html',
                        async: true,
                        error: function(jqXHR, textStatus, error) {
                            DEBUG.ajaxError(jqXHR, textStatus, error);
                        },
                        success: function(response)
                        {
                            if (!$(editLoadData + " div.item").length)
                                var item = $("<div class='item'></div>");
                            else
                                var item = $("<div class='item' style='display: none'></div>");
                            item.attr("data-id", data[elements - 1]);
                            var content = $(response);
                            var items = content.length;
                            for (var j = 0; j < items; j++) {
                                item.append(content[j]);
                            };
                            $(editLoadData).append(item);
                            data.splice(data.length - 1 , 1);

                            return loadData(data, callback);
                        }
                    });
                }

                HTML.loader();
                loadData(data, function () { 
                    HTML.loader();
                    new ANIMATION.slider($(editLoadData),{effect: "fade", keys: true});
                    set.ajaxCallback.get.success();
                });
            });
        },
        editActionTpk: function(editSelectionButton_tpk, editLoadData, inputSelection, settings)
        {
            var HTML = new jRender.Html();
            var DEBUG = new jRender.Debug();
            var ANIMATION = new jRender.Animation();

            var set = settings || {};
            set.id = set.id || "dialog-ui-tpk";
            set.title = set.title || "Editar";
            set.width = set.width || 700;

            set.searchConfig = (set.searchConfig instanceof Object) ? set.searchConfig: {};

            set.searchConfig.input = set.searchConfig.input || undefined;
            set.searchConfig.button = set.searchConfig.button || undefined;
            set.searchConfig.view = set.searchConfig.view || undefined;

            set.ajaxCallback = (set.ajaxCallback instanceof Object) ? set.ajaxCallback: {};

            // GET Callbacks
            set.ajaxCallback.get = (set.ajaxCallback.get instanceof Object) ? set.ajaxCallback.get: {};
            set.ajaxCallback.get.success = set.ajaxCallback.get.success || new Function();

            // POST Callbacks
            set.ajaxCallback.post = (set.ajaxCallback.post instanceof Object) ? set.ajaxCallback.post: {};
            set.ajaxCallback.post.success = set.ajaxCallback.post.success || new Function();

            $("body").delegate(editSelectionButton_tpk, "click", function(event)
            {
                event.preventDefault();
                var selection = $(inputSelection + ":checked");
                var row = selection.parent().parent();

                if (!selection.length)
                    return alert("Debe seleccionar mínimo un elemento!");

                for (var i = selection.length - 1; i >= 0; i--) {
                        $(selection[i]).parent().parent().attr("class", "warning");
                };

                $(editLoadData + " div.item").remove();

                HTML.dialog({
                    title: set.title,
                    id: set.id,
                    content: $('<div class="$ jrender-slide" id="' + editLoadData.replace("#","") + '"> \
                                    <div class="toolbar"> \
                                        <div class="button-left"><span class="glyphicon glyphicon-chevron-left"></span></div> \
                                        <div class="button-right"><span class="glyphicon glyphicon-chevron-right"></span></div> \
                                    </div> \
                                </div>'),
                    width: set.width,
                    buttons: {
                        "Guardar": function()
                        {
                            var form = $(editLoadData);
                            var formElements = $(form.children("div").children("form"));

                            var loadData = function (data, callback) {

                                elements = data.length;
                                if (!elements)
                                    return callback();

                                var url = data[elements - 1].getAttribute("action");
                                var item = $(data[elements - 1]).parent();
                                var that = $(this);

                                var ajax_data = new Object();
                                $.each($(data[elements - 1]).serializeArray(), function() {
                                    ajax_data[this.name] = this.value;
                                });

                                $.ajax({
                                    url: url,
                                    type: 'POST',
                                    data: { request: ajax_data, action: "edit"},
                                    async: true,
                                    error: function(jqXHR, textStatus, error) {
                                        DEBUG.ajaxError(jqXHR, textStatus, error);
                                    },
                                    success: function(response) {
                                        item.empty();
                                        item.append(response);
                                        data.splice(data.length - 1 , 1);
                                        loadData(data, callback);
                                    }
                                });
                            }

                            HTML.loader();
                            loadData(formElements, function () { 
                                HTML.loader(); 
                                if (set.searchConfig.view !== undefined)
                                   $(set.searchConfig.view).empty();
                                if (set.searchConfig.input !== undefined)
                                   $(set.searchConfig.input).val('');
                                if (set.searchConfig.button !== undefined)
                                   $(set.searchConfig.button).trigger("click");
                            });

                        },
                        "Cancelar": function() {
                            $(this).dialog("close");
                        }
                    }
                });

                var selection = $(inputSelection);
                var url = $(editSelectionButton_tpk)[0].getAttribute("data-resource");

                var id_array = new Array();
                var type_array = new Array();

                $.each(selection,function(key){
                    if ($(this).is(":checked")) {
                        id_array.push($(this)[0].getAttribute("data-selection-id")); 
                        type_array.push($(this)[0].getAttribute("data-selection-type"));
                    }
                });

                var loadData = function (key1, key2, callback) {

                    elements = key1.length;
                    if (!elements)
                        return callback();

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: { id: key1[elements - 1], type: key2[elements - 1], action: "get" },
                        dataType: 'html',
                        async: true,
                        error: function(objeto, errno, e) {
                            alert(errno);
                        },
                        success: function(response) {
                            if (!$(editLoadData + " div.item").length)
                                var item = $("<div class='item'></div>");
                            else
                                var item = $("<div class='item' style='display: none'></div>");
                            item.attr("data-id", key1[elements - 1]);
                            item.attr("data-type", key2[elements - 1]);
                            var content = $(response);
                            var items = content.length;
                            for (var j = 0; j < items; j++) {
                                 item.append(content[j]);
                            };
                            $(editLoadData).append(item);
                            key1.splice(key1.length - 1 , 1);
                            key2.splice(key2.length - 1 , 1);

                            return loadData(key1, key2, callback);
                        }
                    });
                }

                HTML.loader();
                loadData(id_array, type_array, function () { 
                    HTML.loader(); 
                    new ANIMATION.slider($(editLoadData),{effect: "fade", keys: true});
                });

            });
        },
        postAction: function(delegateButton, inputSelection, settings)
        {
            var HTML = new jRender.Html();
            var DEBUG = new jRender.Debug();

            var set = settings || {};
            set.title = set.title || "Action";
            set.content = set.content || $("<p>Start action ?<p>");
            set.className = set.className || "warning";

            set.searchConfig = (set.searchConfig instanceof Object) ? set.searchConfig: {};

            set.searchConfig.input = set.searchConfig.input || undefined;
            set.searchConfig.button = set.searchConfig.button || undefined;
            set.searchConfig.view = set.searchConfig.view || undefined;

            // Callbacks
            set.ajaxCallback = (set.ajaxCallback instanceof Object) ? set.ajaxCallback: {};
            set.ajaxCallback.success = set.ajaxCallback.success || new Function();
            set.ajaxCallback.complete = set.ajaxCallback.complete || new Function();

            $("body").delegate(delegateButton, "click", function(event)
            {
                event.preventDefault();
                var selection = $(inputSelection + ":checked");
                var row = selection.parent().parent();

                if (!selection.length)
                    return alert("Debe seleccionar mínimo un elemento!");

                for (var i = selection.length - 1; i >= 0; i--) {
                        $(selection[i]).parent().parent().attr("class", set.className);
                };

                HTML.dialog({
                    title: set.title,
                    content: set.content,
                    buttons: {
                        "Aceptar": function()
                        {
                            var selection = $(inputSelection);
                            var url = $(delegateButton)[0].getAttribute("data-resource");
                            var data = new Array();
                            var that = $(this);

                            $.each(selection, function(key) {
                                if ($(this).is(":checked"))
                                    data.push($(this)[0].getAttribute("data-selection-id")); 
                            });

                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: { request: data },
                                dataType: 'json',
                                async: true,
                                error: function(jqXHR, textStatus, error) {
                                    $.ajax({
                                        url: url,
                                        type: 'POST',
                                        data: { request: data },
                                        dataType: 'html',
                                        async: true,
                                        error: function(jqXHR, textStatus, error) {
                                            HTML.loader();
                                            DEBUG.ajaxError(jqXHR, textStatus, error);
                                        },
                                        success: function(data) {
                                            HTML.loader();
                                            DEBUG.phpError(data);
                                        }
                                    });

                                    DEBUG.ajaxError(jqXHR, textStatus, error);
                                },
                                beforeSend: function() {
                                    HTML.loader();
                                },
                                success: function(data)
                                {
                                    if (set.searchConfig.view !== undefined)
                                       $(set.searchConfig.view).empty();
                                    if (set.searchConfig.input !== undefined)
                                       $(set.searchConfig.input).val('');
                                    if (set.searchConfig.button !== undefined)
                                       $(set.searchConfig.button).trigger("click");
                                    that.dialog("close");
                                    HTML.loader();
                                    set.ajaxCallback.success();
                                },
                                complete: function()
                                {
                                    set.ajaxCallback.complete();
                                }
                            });
                        },
                        "Cancelar": function() {
                            $(this).dialog("close");
                        }
                    }
                });
            });
        },
        postAction2: function(delegateButton, settings)
        {
            var HTML = new jRender.Html();
            var DEBUG = new jRender.Debug();

            var set = settings || {};
            set.title = set.title || "Action";
            set.content = set.content || $("<p>Start action ?<p>");
            set.className = set.className || "warning";

            set.searchConfig = (set.searchConfig instanceof Object) ? set.searchConfig: {};

            set.searchConfig.input = set.searchConfig.input || undefined;
            set.searchConfig.button = set.searchConfig.button || undefined;
            set.searchConfig.view = set.searchConfig.view || undefined;

            $("body").delegate(delegateButton, "click", function(event)
            {
                event.preventDefault();
                var selection = $("input[data-selection-id][data-selection-type]:checked");
                var row = selection.parent().parent();

                if (!selection.length)
                    return alert("Debe seleccionar mínimo un elemento!");

                for (var i = selection.length - 1; i >= 0; i--) {
                        $(selection[i]).parent().parent().attr("class", set.className);
                };

                HTML.dialog({
                    title: set.title,
                    content: set.content,
                    width: 400,
                    buttons: {
                        "Aceptar": function()
                        {
                            var selection = $("input[data-selection-id][data-selection-type]");
                            var url = $(delegateButton)[0].getAttribute("data-resource");
                            
                            var id_array = new Array();
                            var type_array = new Array();
                            
                            var that = $(this);

                            $.each(selection,function(key){
                                if ($(this).is(":checked")) {
                                    id_array.push($(this)[0].getAttribute("data-selection-id")); 
                                    type_array.push($(this)[0].getAttribute("data-selection-type"));
                                }
                            });

                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: { id: id_array, type: type_array },
                                dataType: 'json',
                                async: true,
                                error: function(jqXHR, textStatus, error) {
                                    DEBUG.ajaxError(jqXHR, textStatus, error);
                                },
                                beforeSend: function() {
                                    HTML.loader();
                                },
                                success: function(data) {
                                    if (set.searchConfig.view !== undefined)
                                       $(set.searchConfig.view).empty();
                                    if (set.searchConfig.input !== undefined)
                                       $(set.searchConfig.input).val('');
                                    if (set.searchConfig.button !== undefined)
                                       $(set.searchConfig.button).trigger("click");
                                    that.dialog("close");
                                    HTML.loader();
                                }
                            });
                        },
                        "Cancelar": function() {
                            $(this).dialog("close");
                        }
                    }
                });
            });
        },
        loadResource: function(delegateButton, context)
        {
            var HTML = new jRender.Html();
            var DEBUG = new jRender.Debug();

            $("body").delegate(delegateButton, "click", function(event)
            {
                event.preventDefault();

                var url = $(this)[0].getAttribute("data-resource");

                var container = $(context);
                container.empty();

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { request: '', simulateXmlHttpRequest: 0 },
                    dataType: 'html',
                    async: true,
                    error: function(jqXHR, textStatus, error) {
                        container.empty().append(error);
                        DEBUG.ajaxError(jqXHR, textStatus, error);
                    },
                    beforeSend: function() {
                        HTML.loader({ context: container });
                    },
                    success: function(data) {
                        container.empty().append(data);
                    }
                });
            });
        }
    }

/*
 *   FORMS
 */

    API.Form = {}

    API.Form.Form = function(formObject)
    {
        if (typeof formObject !== "undefined")
        {
    		if (typeof formObject !== "object")
    			throw "The argument must be an Html Object";
            
    		API.Form.Form.prototype._form = formObject;
    		API.Form.Form.prototype._elements = document.querySelectorAll('#'+ this._form.id +' input[name]');
        }

        // Array of all elements
        API.Form.Form.prototype.elements = [];

        // Current element to appy changes
        API.Form.Form.prototype.element = null;
    }

    API.Form.Form.prototype = {
    	add: function(object)
    	{
    		for (var i = this.elements.length - 1; i >= 0; i--) {
    			if (this.elements[i].name == object.name)
    				throw "The element " + object.name + " already exists in the form";
    		};
    		this.elements.push(object);

            return this;
    	},
    	bind: function(data)
    	{
            for (var i = this.elements.length - 1; i >= 0; i--) {
                for (var element in data)
                {
                    if (data[element].name == this.elements[i].name)
                    {
                        if (typeof data[element].attributes !== "undefined" && typeof data[element].attributes !== "object")
                            throw "Parsing error, attributes must be object type";

                        if (typeof data[element].attributes !== "undefined")
                        {
                            var value = data[element].attributes.value;

                            if (typeof this.elements[i].attributes === "undefined")
                                this.elements[i].attributes = {};
                            this.elements[i].attributes.value = value;                            
                        }
                    }
                }
            };
    	},
    	count: function()
    	{
    		return this.elements.length;
    	},
    	get: function(element)
    	{
    		if (typeof element !== "string")
    			throw "The argument must be an string";
            var match = false;
    		for (var i = this.elements.length - 1; i >= 0; i--) {
    			if (this.elements[i].name == element)
                {
    				this.element = this.elements[i];
                    match = true;
                }
    		};
            if (!match)
                throw "Element "+ element +" not found";
    		return this;
    	},
        getAttribute: function(attribute)
        {
            if (typeof element !== "string")
                throw "The argument must be an string";
            if (this.element === null)
                throw "There is not any matched element";
            
            if (this.element.attributes !== "undefined")
                return this.element.attributes[attribute];
            return null;
        },
        getAttributes: function()
        {
            if (this.element === null)
                throw "There is not any matched element";
            
            return this.element.attributes;
        },
    	getData: function()
    	{
    		var data = new Object();
    		for (var i = this.elements.length - 1; i >= 0; i--) {
    			var value = (typeof this.elements[i].attributes !== "undefined" && typeof this.elements[i].attributes.value !== "undefined") ? this.elements[i].attributes.value : null;
    			data[this.elements[i].name] = value;
    		};
    		return data;
    	},
    	getElements: function()
    	{
    		return this.elements;
    	},
    	getInputFilter: function()
    	{
    		//
    	},
        getLabel: function()
        // Returns the string label or null if not found
        {
            if (this.element === null)
                throw "There is not any matched element";

            if (this.element.hasOwnProperty('options'))
            {
                if (this.element.options.hasOwnProperty('label'))
                    return this.element.options.label;
            }
            return null;
        },
        getName: function()
        {
            if (this.element === null)
                throw "There is not any matched element";
            
            return this.element.name;
        },
        getValue: function()
        {
            return this.getAttribute("value");
        },
    	has: function(element)
    	{
    		if (typeof element !== "string")
    			throw "The argument must be an string";
    		for (var i = this.elements.length - 1; i >= 0; i--) {
    			if (this.elements[i].name == element)
    				return true;
    		};
    		return false;
    	},
        hasAttribute: function(attribute)
        {
            if (typeof element !== "string")
                throw "The argument must be an string";
            if (this.element === null)
                throw "There is not any matched element";

            if (this.element.attributes !== "undefined")
            {
                if (this.element.attributes.hasOwnProperty(attribute))
                    return true;
            }
            return false;
        },
    	isValid: function()
    	{
    		//
    	},
        prepare: function()
        {
            //
        },
        setAttribute: function(attribute, value)
        {
            if (typeof attribute !== "string")
                throw "The argument must be an string";
            if (this.element === null)
                throw "There is not any matched element";

            if (this.element.attributes !== "undefined")
                return this.element.attributes[attribute] = value;
            return this;
        },
    	remove: function(element)
    	{
    		if (typeof element !== "string")
    			throw "The argument must be an string";
    		for (var i = this.elements.length - 1; i >= 0; i--) {
    			if (this.elements[i].name == element)
    			{
    				var idx = this.elements.indexOf(element);
    				this.elements.splice(idx, 1);
    			}
    		};
    	},
    	setData: function()
    	{
    		//
    	},
        setLabel: function(label)
        {
            if (this.element === null)
                throw "There is not any matched element";
            
            this.element.label = label;
            return this;
        },
    	setInputFilter: function()
    	{
    		//
    	},
    	setValidationGroup: function()
    	{
    		//
    	}
    }

/* 
 *   Standard Class
 */

    API.StdClass = function() {
        return this;
    }


/* 
 *   Html Class
 */

    API.Html = function() {
        return this;
    }

    API.Html.prototype = {

        overlayState: false,
        loaderState: false,
        intervals: {},

        overlay: function()
        {
            var that = this;

            if (this.overlayState)
                return setTimeout(function(){ that.overlay(); }, 500);

            this.overlayState = true;

            var overlaySelector = "jrender-overlay";
            var overlay = $("#" + overlaySelector);

            if (overlay.length)
            {
                overlay.effect("fade", 500);
                setTimeout(function(){
                    if (overlay.length)
                        overlay.remove();
                }, 500);
            }
            else
            {
                if (document.querySelector("html").style.height != "100%")
                    $("html").css("height", "100%");

                $("body").append("<div id='" + overlaySelector + "' class='overlay'></div>");
                $("#" + overlaySelector).effect("fade", 500);
            }
            setTimeout(function() { that.overlayState = false; }, 500);
        },
        loader: function(settings)
        {
            var that = this;            // save a class reference

            var set = settings || {};
            set.context = (set.context !== undefined && set.context.length) ? set.context : false;      // jQuery selector
            set.height = set.height || 50;

            var loaderSelector = "jrenderLoader";
            var loader = $("#" + loaderSelector);

            function renderLoader(that, context)
            {
                loader_ctx = (context !== undefined && context.length);

                if (loader_ctx)
                {
                    context = set.context;
                    context.addClass("context");
                    loaderSelector = "anyThing"; 			// Prevent BUG
                }
                else
                    context = $("body");

                var newCanvas = $("<canvas id='" + loaderSelector + "' height='" + set.height + "' style='display: none; background: white' class='base-loader base-loader-style'>Loading...</canvas>");
                context.append(newCanvas);

                canvas = $("#" + loaderSelector);

                if (!loader_ctx)
                {
                    that.overlay();
                    canvas.css("margin-left", "-" + (newCanvas[0].width / 2) + "px");
                    canvas.css("margin-top", "-" + (newCanvas[0].height / 2) + "px");                    
                }

                $(canvas).effect("fade", 500);

                var x = 0, y = 2, speed = 5, direction = speed;
                var height = newCanvas[0].height - 4;

                ctx = canvas[0].getContext("2d");
                ctx.fillStyle = 'rgb(0,150,200)';
                ctx.fillRect(x,y,10,height);
                ctx.fill();

                interval = setInterval(function(){
                    if (x > canvas[0].width) 
                    {
                        direction = -speed;
                        x = 2; y = 2;
                        canvas[0].width = canvas[0].width;
                    }
                    if (x < 0) direction = speed;
                    x += direction;
                    ctx.fillStyle = 'rgb(0,150,200)';
                    ctx.fillRect(x,y,10,height);
                    ctx.fill();
                },20);

                that.intervals.loader = interval;
            }

            if (!set.context)
            {
                // If loader is locked return again the function after 0.5 seconds
                if (this.loaderState)
                    return setTimeout(function(){ that.loader(); }, 500);

                // Block loader for 0.5 seconds
                this.loaderState = true;
                setTimeout(function() { that.loaderState = false; }, 500);

                if (!loader.length)
                    return renderLoader(this);

                // Close loader
                loader.effect("fade", 500); this.overlay();
                setTimeout(function(){ loader.remove(); window.clearInterval(that.intervals.loader); }, 500);
            }
            else
            {
                if (!set.context.children("canvas").length)
                    return renderLoader(this, set.context);

                var loader = set.context.children("canvas");
                loader.effect("fade", 500);
                setTimeout(function(){ loader.remove(); }, 1000);
            }
        },
        dialog: function(settings, callback)
        {
            var set = settings || {};
            set.id = set.id || "";                  // String type           
            set.title = set.title || "";            // String type
            set.content = set.content || "";        // jQuery element

            set.persistence = (set.persistence !== undefined) ? set.persistence : true;

            // Callbacks
            callback = callback || function(){};                        // General callback

            // jQuery dialog settings
            set.overlay = (set.overlay !== undefined) ? set.overlay : true;
            set.width = set.width || "auto";
            set.height = set.height || "auto";
            set.autoOpen = (set.autoOpen !== undefined) ? set.autoOpen : true;
            set.position = set.position || "center";
            set.draggable = (set.draggable !== undefined) ? set.draggable : true;
            set.resizable = (set.resizable !== undefined) ? set.resizable : true;

            set.buttons = set.buttons || true;

            if ($("#"+set.id).length && set.persistence)
                $("#"+set.id).dialog("open");
            else
            {
                if ($("#"+set.id).length)
                    $("#"+set.id).dialog("destroy");

                var dialog = $("<div></div>");
                dialog.attr("id", set.id);
                dialog.attr("title", set.title);
                dialog.attr("class", "$ fade-in");
                if (set.content.length)
                    dialog.append(set.content);

                dialog.dialog({
                    overlay: set.overlay,
                    width: set.width,
                    height: set.height,
                    autoOpen: set.autoOpen,
                    position: set.position,
                    draggable: set.draggable,
                    resizable: set.resizable,
                    dialogClass: "css-render-nm black-shadow infinite",
                    show: {
                        effect: "fade",
                        duration: 500,
                    },
                    hide: {
                        effect: "fade",
                        duration: 500,
                    },
                    buttons: set.buttons
                });
            }
            callback();
        },
        checked: function(inputSelection, className)
        {
            $("body").delegate(inputSelection, "change", function()
            {
                    var row = $(this).parent().parent();
                    if ($(this).is(":checked")) {
                        if (!row.hasClass(className))
                            row.attr("class", className);
                    }
                    else
                        row.removeAttr("class");
            });
        },
        checkedTrigger: function(inputTrigger, inputSelection, className)
        {
            $("body").delegate(inputTrigger, "change", function(event)
            {
                var selection = $(inputSelection);

                if ($(inputTrigger).is(':checked')) {
                    for (var i = selection.length - 1; i >= 0; i--) {
                        if (!$(selection[i]).is(":checked"))
                            $(selection[i]).trigger("click");
                    };
                }
                else {
                    for (var i = selection.length - 1; i >= 0; i--) {
                        if ($(selection[i]).is(":checked"))
                            $(selection[i]).trigger("click");
                    };
                }
            });            
        }
    }


/* 
 *   VALIDATORS
 */

    API.Validator = {}

    API.Validator.StringLength = function(settings)
    {
		var set = settings || {};

		if (typeof set.min !== "number")
			throw ("The minimun value must be number type");
		if (typeof set.max !== "number" && typeof set.max !== "undefined")
			throw ("The maximum value must be number type");

		// Get a natural number as parameter or set the default parameters to zero 
		set.min = (set.min >= 0) ? set.min : 0;

		// Get a natural number as parameter grater tha the minumun value or set the maximum value to undefined
		set.max = (set.max > set.min) ? set.max : undefined;

		API.Validator.StringLength.prototype.min = set.min;
		API.Validator.StringLength.prototype.max = set.max;
    }

	API.Validator.StringLength.prototype = {

		isValid: function(string, whitespaces) 
		{
			// Allow whitespaces
			whitespaces = (typeof whitespaces === "undefined") ? false: whitespaces;

			if (typeof string !== "string")
				throw "The first argument must be string type!";
			if (typeof whitespaces !== "boolean")
				throw "The second argument must be boolean type!";

			if (whitespaces)
			{
				if (typeof this.max !== "undefined")
				{
					if (string.length < this.min || string.length > this.max)
						return false;					
				}
				else {
					if (string.length < this.min)
						return false;						
				}
			}
			else {
				if (typeof this.max !== "undefined")
				{
					if (string.trim().length < this.min || string.trim().length > this.max)
						return false;					
				}
				else {
					if (string.trim().length < this.min)
						return false;					
				}
			}
			return true;
		}
	}

/* 
 *   INPUT FILTERS
 */

 	API.InputFilter = {};

	API.InputFilter.InputFilter = function ()
	{
		API.InputFilter.InputFilter.prototype.filters = {};
		API.InputFilter.InputFilter.prototype.validator = API.Validator;			// this must be private
	}

    API.InputFilter.InputFilter.prototype = {
    	add: function(inputFilter)
    	{
    		if (typeof inputFilter !== "object")
    			throw "The argument must be object type";
    		this.filters[inputFilter.name] = inputFilter;
    	},
    	count: function()
    	{
    		return this.inputFilter.length;
    	},
    	get: function(name)
    	{
    		if (typeof name !== "string")
    			throw "The argument must be string type";

    		for (var input in this.filters)
    		{
    			if (name == this.filters[input].name)
    				return document.querySelector('[name=\'' + this.filters[input].name + '\']');
    		}
    		throw "Element not found!";

    	},
    	getInputs: function()
    	{
    		var inputs = [];
    		for (var input in this.filters)
    		{
    			inputs.push(document.querySelector('[name=\'' + this.filters[input].name + '\']'));
    		}
    		return inputs;
    	},
    	getInvalidInput: function()
    	{
    		var inputs = [];
    		for (var input in this.filters)
    		{
    			inputElement = document.querySelector('[name=\'' + this.filters[input].name + '\']');
    			for (var validator in this.filters[input].validators)
    			{
    				if (typeof this.validator[validator] === "undefined")
    					throw "Undefined validator " + validator;
    				var validatorInstance = new this.validator[validator](this.filters[input].validators[validator]);
    				if (!validatorInstance.isValid(inputElement.value))
    					inputs.push(inputElement);
    			}
    		}
    		return inputs;
    	},
    	getValidInput: function()
    	{
    		var inputs = [];
    		for (var input in this.filters)
    		{
    			inputElement = document.querySelector('[name=\'' + this.filters[input].name + '\']');
    			for (var validator in this.filters[input].validators)
    			{
    				var validatorInstance = new this.validator[validator](this.filters[input].validators[validator]);
    				if (validatorInstance.isValid(inputElement.value))
    					inputs.push(inputElement);
    			}
    		}
    		return inputs;
    	}
    }

/* 
 *   Debug Class
 */

    API.Debug = function() {
        return this;
    }

    API.Debug.prototype = {
        ajaxError: function(jqXHR, textStatus, error) 
        {
            errorContent = $("<div><h3>Error</h3></div>");
            errorContent.append("<p>Type: <strong>" + textStatus +"</strong></p>");
            errorContent.append("status: " + jqXHR.status +"<br />");
            errorContent.append("statusText: " + jqXHR.statusText +"<br />");
            jRender.stdClass.dialog({ title: "An error ocurred!", content: errorContent, width: 300 });
        },
        error: function(e, settings) 
        {
            var set = settings || {};
            set.suggestion = set.suggestion || "";

            // Dialog parameters
            set.width = set.width || 300;

            errorContent = $("<div><h3>Error</h3></div>");
            errorContent.append("<p>Type: <strong>" + e.name +"</strong></p>");
            errorContent.append("Message: <em>" + e.message +"</em><br />");
            if (set.suggestion.length)
                errorContent.append("Suggestion: <em>" + set.suggestion +"</em><br />");
            jRender.stdClass.dialog({ title: "An error ocurred!", content: errorContent, width: set.width });
        },
        phpError: function(phpCode, settings)
        {
            var set = settings || {};
            set.suggestion = set.suggestion || "";

            // Dialog parameters
            set.width = set.width || "auto";

            errorContent = $("<div><h3>Error</h3></div>");
            errorContent.append(phpCode);
            if (set.suggestion.length)
                errorContent.append("Suggestion: <em>" + set.suggestion +"</em><br />");
            jRender.stdClass.dialog({ title: "An error ocurred!", content: errorContent, width: set.width });
        },
        exception: function(name, message) {
            this.name = name;
            this.message = message;
            return this;            
        }
    }

/* 
 *   Animation Class
 */

    API.Animation = function() {
        return this;
    }

    API.Animation.prototype =
    {
        slider: function(slide, settings) 
            {
            var set = settings || {};

            set.effect = set.effect || "fade";
            set.keys = set.keys || false;

            // Right button functionality
            slide.children(".toolbar").children(".button-right").click(function()
            {
                var cssHeight = slide.css("height");
                slide.css("height", cssHeight);
                setTimeout(function(){slide.css("height", "")},1000);

                var items = [];
                $.each(slide.children(".item"),function(){
                   items.push($(this));
                });

                iters = items.length;
                for (var i = 0; i < iters; i++) {
                    // Detectar el elemento visible y si existe un elemento próximo
                    // Si esta comprobación es verdadera el elemento visible se oculta y el próximo se muestra
                    if (items[i].css("display") == "block" && (i+1) in items) {
                        items[i].hide(set.effect,{direction: "left"},400);
                        var nextElement = items[i+1];
                            nextElement.delay(400).show(set.effect,{direction: "right"},400);
                        break;
                    }
                    // Si se detecta el elemento visible como último ítem se visualiza de nuevo el primer elemento
                    else if (items[i].css("display") == "block") {
                        items[i].hide(set.effect,{direction: "right"},400);
                            items[0].delay(400).show(set.effect,{direction: "left"},400);
                    }
                }
            });

            // Left button functionality
            slide.children(".toolbar").children(".button-left").click(function()
            {
                var cssHeight = slide.css("height");
                slide.css("height", cssHeight);
                setTimeout(function(){slide.css("height", "")},1000);

                var items = [];
                $.each(slide.children(".item"),function(){
                   items.push($(this));
                });

                iters = items.length;
                for (var i=0;i<iters;i++) {
                    // Detectar el elemento visible y si existe un elemento anterior
                    // Si esta comprobación es verdadera el elemento visible se oculta y el anterior se muestra
                    if (items[i].css("display") == "block" && (i-1) in items) {
                        items[i].hide(set.effect,{direction: "right"},400);
                        var nextElement = items[i-1];
                        nextElement.delay(400).show(set.effect,{direction: "left"},400);
                        break;
                    }
                    // Si se detecta el elemento visible como primer ítem se visualiza el último elemento
                    else if (items[i].css("display") == "block") {
                        items[i].hide(set.effect,{direction: "left"},400);
                        items[items.length-1].delay(400).show(set.effect,{direction: "right"},400);
                        break;      // Es necesario, puesto que se detecta el primer elemento y no el último
                    }
                }
            });

            // Down button functionality
            slide.children(".button-down").click(function(){
                var slide = $(".slide");
                //var table = $("table[data-id="+opt.pointer+"]").css("height");
                if (slide[0].style.height != "auto") {
                    slide.css({"height": "auto"});
                    $(this).children("span").removeClass("glyphicon-chevron-down");
                    $(this).children("span").addClass("glyphicon-chevron-up");
                }
                else {
                    slide.animate({height: "400px"},1300);
                    $(this).children("span").removeClass("glyphicon-chevron-up");
                    $(this).children("span").addClass("glyphicon-chevron-down");
                }
            });

            // Keys functionality
            if (set.keys) {
                $("body").keydown(function(event){
                    var keycode = event.which;
                    if (keycode == 37)
                        slide.children(".toolbar").children(".button-left").trigger("click");
                    if (keycode == 39)
                        slide.children(".toolbar").children(".button-right").trigger("click");
                });
            }
        }
    }

    // Supports data-script atribute to execute javascript code
    var elements = $("[data-script]");

    for (var i = elements.length - 1; i >= 0; i--) {
        eval(elements[i].getAttribute("data-script"));
    };

});