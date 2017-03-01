/**
 * Historia clínica v1.0
 *
 * @link        http://www.medicadiz.com.co
 * @copyright   Copyright (c) 2014 Medicadiz S.A.S.
 * @license     Free to use under the MIT license. (http://www.opensource.org/licenses/mit-license.php)
 * @version     1.0
 *
 * Date:        2014-04-25
 * Autor:       Darío Rivera
 */

$(function()
{
    /******************************************************************
     *  FUNCIONES GENERALES DE LA APLICACIÓN
     *  Funciones generalizadas que se aplican a más de un módulo
     ******************************************************************/

     var HTML = new jRender.Html();
     var AJAX = new jRender.Ajax();

     /* Input de selección general y activar */
    var inputSelectionId = "input.general-input-selection";
    var inputTrigger = "#app-general-check";

    /* Acciones de chequeo (checkbox) */
    HTML.checked(inputSelectionId);
    HTML.checkedTrigger(inputTrigger,inputSelectionId);


    /* Eventos que activan la búsqueda
     * La búsqueda es activada cuando el elemento input cambia o el botón de búsqueda es activado
     */

    var inputSearch = "#general-search";
    var buttonSearch = "#run-general-search";
    var searchView = "#view-general-search";

    $("body").delegate(inputSearch, "change", function() {
        AJAX.search($(inputSearch), $(searchView));
    });

    $("body").delegate(buttonSearch, "click", function()
    {
        AJAX.search($(inputSearch), $(searchView));
    });


    /* Acción de agregar/registrar */
    var addSelectionButton = "#general-add";
    var formElementToProcess ="#general-form";

    AJAX.addAction(addSelectionButton, formElementToProcess, {
        title: "Registrar",
        position: "top",
        searchConfig: {
            input: inputSearch,
            button: buttonSearch,
            view: searchView,
        }
    });


    /* Acción de editar */
    var editSelectionButton = "#edit-general-selection";
    var editLoadData = "#general-load-data";

    AJAX.editAction(editSelectionButton, editLoadData, inputSelectionId, {
        position: "top",
        searchConfig: {
            input: inputSearch,
            button: buttonSearch,
            view: searchView,
        }
    });

    var editSelectionButton_tpk = "#edit-general-selection-tpk";
    var editLoadData = "#general-load-data";

    AJAX.editActionTpk(editSelectionButton_tpk, editLoadData, inputSelectionId, {
        position: "top",
        searchConfig: {
            input: inputSearch,
            button: buttonSearch,
            view: searchView,
        }
    });


    /* Acciones Habilitar, deshabilitar, eliminar (tablas con una llave primaria) */
    var disableButton = "#disable-general-selection";
    var enableButton = "#enable-general-selection";
    var deleteButton = "#del-general-selection";

    AJAX.postAction(disableButton, inputSelectionId, {
        title: "Deshabilitar selección", 
        content: $("<p>Desea realmente deshabilitar los ítems seleccionados ?</p>"),
        searchConfig: {
            input: inputSearch,
            button: buttonSearch,
            view: searchView,
        }
    });
    AJAX.postAction(enableButton, inputSelectionId,  {
        title: "Habilitar selección", 
        content: $("<p>Desea realmente habilitar los ítems seleccionados ?</p>"),
        className: "success",
        searchConfig: {
            input: inputSearch,
            button: buttonSearch,
            view: searchView,
        }
    });
    AJAX.postAction(deleteButton, inputSelectionId, {
        title: "Eliminar selección", 
        content: $("<p>Desea realmente eliminar los ítems seleccionados ?</p>"),
        className: "danger",
        searchConfig: {
            input: inputSearch,
            button: buttonSearch,
            view: searchView,
        }
    });    


    /* Acciones Habilitar, deshabilitar, eliminar (tablas con dos llaves primarias) */
    var delSelectionButton_tpk = "#del-general-selection-tpk";

    AJAX.postAction2(delSelectionButton_tpk, {
        title: "Eliminar selección", 
        content: $("<p><span class='glyphicon glyphicon-warning-sign' style='font-size: 50px; float:left; margin-right: 7px;''></span> \
                    Estos elementos serán eliminados permanentemente y no podrán ser restaurados. Estás seguro? \
                    </p>"),
        className: "danger",
        searchConfig: {
            input: inputSearch,
            button: buttonSearch,
            view: searchView,
        }
    });


    /* VISTA GENERAL */
    var context = "#view";

    /******************************************************************
     *  MÓDULO DE USUARIOS Y PERMISOS (auth module)
     *  Registro y modificación de usuarios, y parametrización de
     *  permisos
     ******************************************************************/

    AJAX.loadResource("#users-mode", context);
    AJAX.loadResource("#permissions-mode", context);


    /******************************************************************
     *  MÓDULO DE CONFIGURACIÓN (settings module)
     *  Registro y modificación de exámenes, entidades y medicamentos
     ******************************************************************/

    // loaded modules
    AJAX.loadResource("#exams-mode", context);
    AJAX.loadResource("#medications-mode", context);
    AJAX.loadResource("#entities-mode", context);

    /******************************************************************
     *  MÓDULO DE ADMISIONES (admissions module)
     *  Acciones para pacientes y admisiones
     ******************************************************************/

    // loaded modules
    AJAX.loadResource("#patients-mode", context);
    AJAX.loadResource("#admissions-mode", context);

    // Alternate view admisions table
    var alternateTable = "table#admissions-table";
    var alternateButton = "#general-alternation-table";
    Srvdata.alterTable(alternateTable,alternateButton);

    AJAX.postAction("#close-selected-admissions", inputSelectionId, {
        title: "Cerrar admisiones",
        content: $("<p>Desea realmente cerrar las admisiones seleccionadas ?</p>"),
        className: "warning",
        searchConfig: {
            input: inputSearch,
            button: buttonSearch,
            view: searchView,
        }
    });


    /******************************************************************
     *  MÓDULO DE HISTORIA CLÍNICA (medical history module)
     *  Registro de diagnósticos, exámenes, incapacidades, etc.
     ******************************************************************/

    /* Ver historia Clínica */
    AJAX.loadResource("#hc-folios-mode", "#view-hchistory");
    AJAX.loadResource("#hc-diagnostics-mode", "#view-hchistory");
    AJAX.loadResource("#hc-exams-mode", "#view-hchistory");
    AJAX.loadResource("#hc-medications-mode", "#view-hchistory");
    AJAX.loadResource("#hc-interconsultations-mode", "#view-hchistory");
    AJAX.loadResource("#hc-incapacities-mode", "#view-hchistory");
    AJAX.loadResource("#hc-certifications-mode", "#view-hchistory");

    /* FirstHistory table */
    var inputSelectionHistories = "input.first-history";
    var inputHistoriesTrigger = "#app-first-histories-check";

    /* Check and uncheck actions */
    HTML.checked(inputSelectionHistories);
    HTML.checkedTrigger(inputHistoriesTrigger, inputSelectionHistories);

    /* ControlHistory table */
    var inputSelectionHistories = "input.control-history";
    var inputHistoriesTrigger = "#app-control-histories-check";

    /* Check and uncheck actions */
    HTML.checked(inputSelectionHistories);
    HTML.checkedTrigger(inputHistoriesTrigger, inputSelectionHistories);

    /* Refresh action */
    //var diagnosticButtonRefresh = "#run-diagnostic-refresh";
    //var diagnosticView = "#view-diagnostics";

    //AJAX.loadResource(diagnosticButtonRefresh, diagnosticView);

    Srvdata.alterTable("table#first-history-table","#first-histories-alternation-table");
    Srvdata.alterTable("table#control-history-table","#control-histories-alternation-table");


    /******************
	 *  DIAGNÓSTICOS  *
	 ******************/

    /* Agregar un diagnóstico */
    var addDiagnosticButton = "#add-diagnostic";
    var diagnosticFormToProcess ="#diagnostic-form";
    var diagnosticInputSuggestion = "#diagnosticSuggestion";
    var diagnosticInputValue = "#diagnostic_id";

    AJAX.addAction(addDiagnosticButton, diagnosticFormToProcess, {
        id: "add-diagnostic-dialog",
        title: "Registrar Diagnóstico",
        width: "auto",
        position: "top",
        modal: false,
        width: 600,
        ajaxCallback: {
            get: {
                success: function() 
                {
                    $(diagnosticInputSuggestion).autocomplete({
                        source: $(diagnosticInputSuggestion)[0].getAttribute("data-resource"),
                        minLength: 1,
                        select: function(event, ui) {
                            $(diagnosticInputValue).attr("value", ui.item.id);
                        }
                    });
                }
            },
            post: {
                success: function() 
                {
                    if ($(diagnosticInputSuggestion).length) {
                        $(diagnosticInputSuggestion).autocomplete({
                            source: $(diagnosticInputSuggestion)[0].getAttribute("data-resource"),
                            minLength: 1,
                            select: function(event, ui) {
                                $(diagnosticInputValue).attr("value", ui.item.id);
                            }
                        });
                    }
                }
            },
        }
    });

    /* Diagnostics input selection and trigger button */
    var inputSelectionDiagnostics = "input.diagnostic";
    var inputDiagnosticsTrigger = "#app-diagnostics-check";

    /* Check and uncheck actions */
    HTML.checked(inputSelectionDiagnostics);
    HTML.checkedTrigger(inputDiagnosticsTrigger, inputSelectionDiagnostics);

    /* Refresh action */
    var diagnosticButtonRefresh = "#run-diagnostic-refresh";
    var diagnosticView = "#view-diagnostics";

    AJAX.loadResource(diagnosticButtonRefresh, diagnosticView);

    Srvdata.alterTable("table#diagnostics-table","#diagnostics-alternation-table");

    /* Eliminar diagnósticos */
    AJAX.postAction("#delete-selected-diagnostics", inputSelectionDiagnostics, {
        title: "Eliminar diagnósticos",
        content: $("<p>Desea realmente eliminar los diagnósticos seleccionados ?</p>"),
        className: "danger",
        ajaxCallback: {
            success: function() {
                $(diagnosticButtonRefresh).trigger("click");
            }
        }
    });

    /* Editar diagnósticos */
    var editSelectionButton = "#edit-selected-diagnostics";
    var editLoadData = "#diagnostics-load-data";

    AJAX.editAction(editSelectionButton, editLoadData, inputSelectionDiagnostics, {
        id: "edit-diagnostic-dialog",
        position: "top",
        modal: false,
    	ajaxCallback: {
    		post: {
	            success: function() {
	                $(diagnosticButtonRefresh).trigger("click");
	            }
	        }
    	}
    });


    /******************
	 *  MEDICAMENTOS  *
	 ******************/

    /* Agregar un medicamento */
    var addMedicationButton = "#add-medication";
    var medicationFormToProcess ="#medication-form";
    var medicationInputSuggestion = "#medicationSuggestion";
    var medicationInputValue = "#medication_id";

    AJAX.addAction(addMedicationButton, medicationFormToProcess, {
        id: "add-medication-dialog",
        title: "Registrar Medicamento",
        width: "auto",
        position: "top",
        modal: false,
        width: 600,
        ajaxCallback: {
            get: {
                success: function() 
                {
                    $(medicationInputSuggestion).autocomplete({
                        source: $(medicationInputSuggestion)[0].getAttribute("data-resource"),
                        minLength: 1,
                        select: function(event, ui) {
                            $(medicationInputValue).attr("value", ui.item.id);
                        }
                    });
                }
            },
            post: {
                success: function() 
                {
                    if ($(medicationInputSuggestion).length) {
                        $(medicationInputSuggestion).autocomplete({
                            source: $(medicationInputSuggestion)[0].getAttribute("data-resource"),
                            minLength: 1,
                            select: function(event, ui) {
                                $(medicationInputValue).attr("value", ui.item.id);
                            }
                        });
                    }
                }
            },
        }
    });

    /* Medications input selection and trigger button */
    var inputSelectionMedications = "input.medication";
    var inputMedicationsTrigger = "#app-medications-check";

    /* Check and uncheck actions */
    HTML.checked(inputSelectionMedications);
    HTML.checkedTrigger(inputMedicationsTrigger, inputSelectionMedications);

    /* Refresh action */
    var medicationButtonRefresh = "#run-medication-refresh";
    var medicationView = "#view-medications";

    AJAX.loadResource(medicationButtonRefresh, medicationView);

    Srvdata.alterTable("table#medications-table","#medications-alternation-table");

    /* Eliminar medicamentos */
    AJAX.postAction("#delete-selected-medications", inputSelectionMedications, {
        title: "Eliminar medicamentos",
        content: $("<p>Desea realmente eliminar los medicamentos seleccionados ?</p>"),
        className: "danger",
        ajaxCallback: {
            success: function() {
                $(medicationButtonRefresh).trigger("click");
            }
        }
    });

    /* Editar medicamentos */
    var medicationsEditSelectionButton = "#edit-selected-medications";
    var medicationsEditLoadData = "#medications-load-data";

    AJAX.editAction(medicationsEditSelectionButton, medicationsEditLoadData, inputSelectionMedications, {
        id: "edit-medication-dialog",
        position: "top",
        modal: false,
        ajaxCallback: {
            post: {
                success: function() {
                    $(medicationButtonRefresh).trigger("click");
                }
            }
        }
    });


    /******************
     *  INDICACIONES  *
     ******************/

    /* Agregar una indicación */
    var addIndicationButton = "#add-indication";
    var indicationFormToProcess ="#indication-form";
    var indicationInputSuggestion = "#medicationSuggestion";
    var indicationInputValue = "#indication_id";

    AJAX.addAction(addIndicationButton, indicationFormToProcess, {
        id: "add-indication-dialog",
        title: "Registrar indicación",
        width: "auto",
        modal: false,
        width: 600,
        position: "top",
    });

    /* Indications input selection and trigger button */
    var inputSelectionIndications = "input.indication";
    var inputIndicationsTrigger = "#app-indications-check";

    /* Check and uncheck actions */
    HTML.checked(inputSelectionIndications);
    HTML.checkedTrigger(inputIndicationsTrigger, inputSelectionIndications);

    /* Refresh action */
    var indicationButtonRefresh = "#run-indication-refresh";
    var indicationView = "#view-indications";

    AJAX.loadResource(indicationButtonRefresh, indicationView);

    /* Eliminar indicaciones */
    AJAX.postAction("#delete-selected-indications", inputSelectionIndications, {
        title: "Eliminar indicaciones",
        content: $("<p>Desea realmente eliminar las indicaciones seleccionados ?</p>"),
        className: "danger",
        ajaxCallback: {
            success: function() {
                $(indicationButtonRefresh).trigger("click");
            }
        }
    });

    /* Editar indicaciones */
    var indicationsEditSelectionButton = "#edit-selected-indications";
    var indicationsEditLoadData = "#indications-load-data";

    AJAX.editAction(indicationsEditSelectionButton, indicationsEditLoadData, inputSelectionIndications, {
        id: "edit-indication-dialog",
        position: "top",
        modal: false,
        ajaxCallback: {
            post: {
                success: function() {
                    $(indicationButtonRefresh).trigger("click");
                }
            }
        }
    });


    /******************
     *  SOLICITUDES  *
     ******************/

    /* Agregar una solicitud de exámen */
    var addExamSolicitudeButton = "#add-exam-solicitude";
    var examSolicitudeFormToProcess ="#exam-solicitude-form";
    var examSolicitudeInputSuggestion = "#examSolicitudeSuggestion";
    var examSolicitudeInputValue = "#exam_solicitude_id";

    AJAX.addAction(addExamSolicitudeButton, examSolicitudeFormToProcess, {
        id: "add-exam-solicitude-dialog",
        title: "Registrar Solicitud de exámen",
        width: 600,
        modal: false,
        position: "top",
        ajaxCallback: {
            get: {
                success: function() 
                {
                    $(examSolicitudeInputSuggestion).autocomplete({
                        source: $(examSolicitudeInputSuggestion)[0].getAttribute("data-resource"),
                        minLength: 1,
                        select: function(event, ui) {
                            $(examSolicitudeInputValue).attr("value", ui.item.id);
                        }
                    });
                }
            },
            post: {
                success: function() 
                {
                    if ($(examSolicitudeInputSuggestion).length) {
                        $(examSolicitudeInputSuggestion).autocomplete({
                            source: $(examSolicitudeInputSuggestion)[0].getAttribute("data-resource"),
                            minLength: 1,
                            select: function(event, ui) {
                                $(examSolicitudeInputValue).attr("value", ui.item.id);
                            }
                        });
                    }
                }
            },
        }
    });

    /* Indications input selection and trigger button */
    var inputSelectionExamSolicitude = "input.exam-solicitude";
    var inputExamSolicitudeTrigger = "#app-exam-solicitudes-check";

    /* Check and uncheck actions */
    HTML.checked(inputSelectionExamSolicitude);
    HTML.checkedTrigger(inputExamSolicitudeTrigger, inputSelectionExamSolicitude);

    /* Refresh action */
    var examSolicitudeButtonRefresh = "#run-exam-solicitude-refresh";
    var examSolicitudeView = "#view-exam-solicitudes";

    AJAX.loadResource(examSolicitudeButtonRefresh, examSolicitudeView);

    /* Eliminar */
    AJAX.postAction("#delete-selected-exam-solicitudes", inputSelectionExamSolicitude, {
        title: "Eliminar exámenes",
        content: $("<p>Desea realmente eliminar los exámenes seleccionados ?</p>"),
        className: "danger",
        ajaxCallback: {
            success: function() {
                $(examSolicitudeButtonRefresh).trigger("click");
            }
        }
    });

    /* Editar */
    var examSolicitudesEditSelectionButton = "#edit-selected-exam-solicitudes";
    var examSolicitudesEditLoadData = "#exam-solicitudes-load-data";

    AJAX.editAction(examSolicitudesEditSelectionButton, examSolicitudesEditLoadData, inputSelectionExamSolicitude, {
        id: "edit-exam-solicitude-dialog",
        position: "top",
        ajaxCallback: {
            post: {
                success: function() {
                    $(examSolicitudeButtonRefresh).trigger("click");
                }
            }
        }
    });


    /* Agregar una solicitud de patología */
    var addPathologySolicitudeButton = "#add-pathology-solicitude";
    var pathologySolicitudeFormToProcess ="#pathology-solicitude-form";
    var pathologySolicitudeInputSuggestion = "#pathologySolicitudeSuggestion";
    var pathologySolicitudeInputValue = "#pathology_solicitude_id";

    AJAX.addAction(addPathologySolicitudeButton, pathologySolicitudeFormToProcess, {
        id: "add-pathology-solicitude-dialog",
        title: "Registrar Solicitud de patología",
        width: 600,
        modal: false,
        position: "top",
        ajaxCallback: {
            get: {
                success: function() 
                {
                    $(pathologySolicitudeInputSuggestion).autocomplete({
                        source: $(pathologySolicitudeInputSuggestion)[0].getAttribute("data-resource"),
                        minLength: 1,
                        select: function(event, ui) {
                            $(pathologySolicitudeInputValue).attr("value", ui.item.id);
                        }
                    });
                }
            },
            post: {
                success: function() 
                {
                    if ($(pathologySolicitudeInputSuggestion).length) {
                        $(pathologySolicitudeInputSuggestion).autocomplete({
                            source: $(pathologySolicitudeInputSuggestion)[0].getAttribute("data-resource"),
                            minLength: 1,
                            select: function(event, ui) {
                                $(pathologySolicitudeInputValue).attr("value", ui.item.id);
                            }
                        });
                    }
                }
            },
        }
    });

    /* Indications input selection and trigger button */
    var inputSelectionPathologySolicitude = "input.pathology-solicitude";
    var inputPathologySolicitudeTrigger = "#app-pathology-solicitudes-check";

    /* Check and uncheck actions */
    HTML.checked(inputSelectionPathologySolicitude);
    HTML.checkedTrigger(inputPathologySolicitudeTrigger, inputSelectionPathologySolicitude);

    /* Refresh action */
    var pathologySolicitudeButtonRefresh = "#run-pathology-solicitude-refresh";
    var pathologySolicitudeView = "#view-pathology-solicitudes";

    AJAX.loadResource(pathologySolicitudeButtonRefresh, pathologySolicitudeView);

    /* Eliminar */
    AJAX.postAction("#delete-selected-pathology-solicitudes", inputSelectionPathologySolicitude, {
        title: "Eliminar patologías",
        content: $("<p>Desea realmente eliminar las patologías seleccionadas ?</p>"),
        className: "danger",
        ajaxCallback: {
            success: function() {
                $(pathologySolicitudeButtonRefresh).trigger("click");
            }
        }
    });

    /* Editar */
    var pathologySolicitudesEditSelectionButton = "#edit-selected-pathology-solicitudes";
    var pathologySolicitudesEditLoadData = "#pathology-solicitudes-load-data";

    AJAX.editAction(pathologySolicitudesEditSelectionButton, pathologySolicitudesEditLoadData, inputSelectionPathologySolicitude, {
        id: "edit-pathology-solicitude-dialog",
        position: "top",
        ajaxCallback: {
            post: {
                success: function() {
                    $(pathologySolicitudeButtonRefresh).trigger("click");
                }
            }
        }
    });


    /* Agregar una solicitud de procedimiento quirúrgico */
    var addProcQxSolicitudeButton = "#add-proc-qx-solicitude";
    var procQxSolicitudeFormToProcess ="#prox-qx-solicitude-form";
    var procQxSolicitudeInputSuggestion = "#procQxSolicitudeSuggestion";
    var procQxSolicitudeInputValue = "#proc_qx_solicitude_id";

    AJAX.addAction(addProcQxSolicitudeButton, procQxSolicitudeFormToProcess, {
        id: "add-proc-qx-solicitude-dialog",
        title: "Registrar Solicitud de procedimiento Qx",
        width: 600,
        modal: false,
        position: "top",
        ajaxCallback: {
            get: {
                success: function() 
                {
                    $(procQxSolicitudeInputSuggestion).autocomplete({
                        source: $(procQxSolicitudeInputSuggestion)[0].getAttribute("data-resource"),
                        minLength: 1,
                        select: function(event, ui) {
                            $(procQxSolicitudeInputValue).attr("value", ui.item.id);
                        }
                    });
                }
            },
            post: {
                success: function() 
                {
                    if ($(procQxSolicitudeInputSuggestion).length) {
                        $(procQxSolicitudeInputSuggestion).autocomplete({
                            source: $(procQxSolicitudeInputSuggestion)[0].getAttribute("data-resource"),
                            minLength: 1,
                            select: function(event, ui) {
                                $(procQxSolicitudeInputValue).attr("value", ui.item.id);
                            }
                        });
                    }
                }
            },
        }
    });

    /* Indications input selection and trigger button */
    var inputSelectionProcQxSolicitude = "input.proc-qx-solicitude";
    var inputProcQxSolicitudeTrigger = "#app-proc-qx-solicitudes-check";

    /* Check and uncheck actions */
    HTML.checked(inputSelectionProcQxSolicitude);
    HTML.checkedTrigger(inputProcQxSolicitudeTrigger, inputSelectionProcQxSolicitude);

    /* Refresh action */
    var procQxSolicitudeButtonRefresh = "#run-proc-qx-solicitude-refresh";
    var procQxSolicitudeView = "#view-proc-qx-solicitudes";

    AJAX.loadResource(procQxSolicitudeButtonRefresh, procQxSolicitudeView);

    /* Eliminar */
    AJAX.postAction("#delete-selected-proc-qx-solicitudes", inputSelectionProcQxSolicitude, {
        title: "Eliminar procedimientos Qx",
        content: $("<p>Desea realmente eliminar los procedimientos seleccionados ?</p>"),
        className: "danger",
        ajaxCallback: {
            success: function() {
                $(procQxSolicitudeButtonRefresh).trigger("click");
            }
        }
    });

    /* Editar */
    var procQxSolicitudesEditSelectionButton = "#edit-selected-proc-qx-solicitudes";
    var procQxSolicitudesEditLoadData = "#proc-qx-solicitudes-load-data";

    AJAX.editAction(procQxSolicitudesEditSelectionButton, procQxSolicitudesEditLoadData, inputSelectionProcQxSolicitude, {
        id: "edit-proc-qx-solicitude-dialog",
        position: "top",
        ajaxCallback: {
            post: {
                success: function() {
                    $(procQxSolicitudeButtonRefresh).trigger("click");
                }
            }
        }
    });


    /* Agregar una solicitud de procedimiento no quirúrgico */
    var addProcSolicitudeButton = "#add-proc-solicitude";
    var procSolicitudeFormToProcess ="#prox-solicitude-form";
    var procSolicitudeInputSuggestion = "#procSolicitudeSuggestion";
    var procSolicitudeInputValue = "#proc_solicitude_id";

    AJAX.addAction(addProcSolicitudeButton, procSolicitudeFormToProcess, {
        id: "add-proc-solicitude-dialog",
        title: "Registrar Solicitud de procedimiento no Qx",
        width: 600,
        modal: false,
        position: "top",
        ajaxCallback: {
            get: {
                success: function() 
                {
                    $(procSolicitudeInputSuggestion).autocomplete({
                        source: $(procSolicitudeInputSuggestion)[0].getAttribute("data-resource"),
                        minLength: 1,
                        select: function(event, ui) {
                            $(procSolicitudeInputValue).attr("value", ui.item.id);
                        }
                    });
                }
            },
            post: {
                success: function() 
                {
                    if ($(procSolicitudeInputSuggestion).length) {
                        $(procSolicitudeInputSuggestion).autocomplete({
                            source: $(procSolicitudeInputSuggestion)[0].getAttribute("data-resource"),
                            minLength: 1,
                            select: function(event, ui) {
                                $(procSolicitudeInputValue).attr("value", ui.item.id);
                            }
                        });
                    }
                }
            },
        }
    });

    /* Indications input selection and trigger button */
    var inputSelectionProcSolicitude = "input.proc-solicitude";
    var inputProcSolicitudeTrigger = "#app-proc-solicitudes-check";

    /* Check and uncheck actions */
    HTML.checked(inputSelectionProcSolicitude);
    HTML.checkedTrigger(inputProcSolicitudeTrigger, inputSelectionProcSolicitude);

    /* Refresh action */
    var procSolicitudeButtonRefresh = "#run-proc-solicitude-refresh";
    var procSolicitudeView = "#view-proc-solicitudes";

    AJAX.loadResource(procSolicitudeButtonRefresh, procSolicitudeView);

    /* Eliminar */
    AJAX.postAction("#delete-selected-proc-solicitudes", inputSelectionProcSolicitude, {
        title: "Eliminar procedimientos",
        content: $("<p>Desea realmente eliminar los procedimientos seleccionados ?</p>"),
        className: "danger",
        ajaxCallback: {
            success: function() {
                $(procSolicitudeButtonRefresh).trigger("click");
            }
        }
    });

    /* Editar */
    var procSolicitudesEditSelectionButton = "#edit-selected-proc-solicitudes";
    var procSolicitudesEditLoadData = "#proc-solicitudes-load-data";

    AJAX.editAction(procSolicitudesEditSelectionButton, procSolicitudesEditLoadData, inputSelectionProcSolicitude, {
        id: "edit-proc-solicitude-dialog",
        position: "top",
        ajaxCallback: {
            post: {
                success: function() {
                    $(procSolicitudeButtonRefresh).trigger("click");
                }
            }
        }
    });

    /***********
     *  OTROS  *
     ***********/

    /* Agregar interconsulta */
    var addInterconsultationButton = "#add-interconsultation";
    var interconsultationFormToProcess ="#interconsultation-form";

    var interconsultationInputSuggestion = "#interconsultationDiagnosticSuggestion";
    var interconsultationInputValue = "#interconsultation_diagnostic_id";
    var interInputSuggestion = "#interconsultationSpecialtySuggestion";
    var interInputValue = "#interconsultation_specialty_id";

    AJAX.addAction(addInterconsultationButton, interconsultationFormToProcess, {
        id: "add-interconsultation-dialog",
        title: "Registrar interconsulta",
        width: 600,
        modal: false,
        position: "top",
        ajaxCallback: {
            get: {
                success: function() 
                {
                    $(interconsultationInputSuggestion).autocomplete({
                        source: $(interconsultationInputSuggestion)[0].getAttribute("data-resource"),
                        minLength: 1,
                        select: function(event, ui) {
                            $(interconsultationInputValue).attr("value", ui.item.id);
                        }
                    });
                    $(interInputSuggestion).autocomplete({
                        source: $(interInputSuggestion)[0].getAttribute("data-resource"),
                        minLength: 1,
                        select: function(event, ui) {
                            $(interInputValue).attr("value", ui.item.id);
                        }
                    });
                }
            },
            post: {
                success: function() 
                {
                    if ($(interconsultationInputSuggestion).length) {
                        $(interconsultationInputSuggestion).autocomplete({
                            source: $(interconsultationInputSuggestion)[0].getAttribute("data-resource"),
                            minLength: 1,
                            select: function(event, ui) {
                                $(interconsultationInputValue).attr("value", ui.item.id);
                            }
                        });
                    }
                    if ($(interInputSuggestion).length) {
                        $(interInputSuggestion).autocomplete({
                            source: $(interInputSuggestion)[0].getAttribute("data-resource"),
                            minLength: 1,
                            select: function(event, ui) {
                                $(interInputValue).attr("value", ui.item.id);
                            }
                        });
                    }
                }
            },
        }
    });

    /* Indications input selection and trigger button */
    var inputSelectionInterconsultations = "input.interconsultation";
    var inputInterconsultationsTrigger = "#app-interconsultations-check";

    /* Check and uncheck actions */
    HTML.checked(inputSelectionInterconsultations);
    HTML.checkedTrigger(inputInterconsultationsTrigger, inputSelectionInterconsultations);

    /* Refresh action */
    var interconsultationsButtonRefresh = "#run-interconsultation-refresh";
    var interconsultationsView = "#view-interconsultations";

    AJAX.loadResource(interconsultationsButtonRefresh, interconsultationsView);

    /* Eliminar */
    AJAX.postAction("#delete-selected-interconsultations", inputSelectionInterconsultations, {
        title: "Eliminar interconsultas",
        content: $("<p>Desea realmente eliminar las interconsultas seleccionadas ?</p>"),
        className: "danger",
        ajaxCallback: {
            success: function() {
                $(interconsultationsButtonRefresh).trigger("click");
            }
        }
    });

    /* Editar */
    var interconsultationsEditSelectionButton = "#edit-selected-interconsultations";
    var interconsultationsEditLoadData = "#interconsultations-load-data";

    AJAX.editAction(interconsultationsEditSelectionButton, interconsultationsEditLoadData, inputSelectionInterconsultations, {
        id: "edit-interconsultations-dialog",
        position: "top",
        ajaxCallback: {
            post: {
                success: function() {
                    $(interconsultationsButtonRefresh).trigger("click");
                }
            }
        }
    });


    /* Agregar incapacidad */
    var addIncapacityButton = "#add-incapacity";
    var incapacityFormToProcess ="#incapacity-form";
    var incapacityInputSuggestion = "#incapacitiesDiagnosticSuggestion";
    var incapacityInputValue = "#incapacities_diagnostic_id";

    AJAX.addAction(addIncapacityButton, incapacityFormToProcess, {
        id: "add-incapacity-dialog",
        title: "Registrar incapacidad",
        width: 600,
        modal: false,
        position: "top",
        ajaxCallback: {
            get: {
                success: function() 
                {
                    $(incapacityInputSuggestion).autocomplete({
                        source: $(incapacityInputSuggestion)[0].getAttribute("data-resource"),
                        minLength: 1,
                        select: function(event, ui) {
                            $(incapacityInputValue).attr("value", ui.item.id);
                        }
                    });
                }
            },
            post: {
                success: function() 
                {
                    if ($(incapacityInputSuggestion).length) {
                        $(incapacityInputSuggestion).autocomplete({
                            source: $(incapacityInputSuggestion)[0].getAttribute("data-resource"),
                            minLength: 1,
                            select: function(event, ui) {
                                $(incapacityInputValue).attr("value", ui.item.id);
                            }
                        });
                    }
                }
            },
        }
    });

    /* Indications input selection and trigger button */
    var inputSelectionIncapacities = "input.incapacity";
    var inputIncapacitiesTrigger = "#app-incapacities-check";

    /* Check and uncheck actions */
    HTML.checked(inputSelectionIncapacities);
    HTML.checkedTrigger(inputIncapacitiesTrigger, inputSelectionIncapacities);

    /* Refresh action */
    var incapacitiesButtonRefresh = "#run-incapacities-refresh";
    var incapacitiesView = "#view-incapacities";

    AJAX.loadResource(incapacitiesButtonRefresh, incapacitiesView);

    Srvdata.alterTable("table#incapacities-table","#incapacities-alternation-table");

    /* Eliminar */
    AJAX.postAction("#delete-selected-incapacities", inputSelectionIncapacities, {
        title: "Eliminar incapacidad",
        content: $("<p>Desea realmente eliminar la incapacidad seleccionada ?</p>"),
        className: "danger",
        ajaxCallback: {
            success: function() {
                $(incapacitiesButtonRefresh).trigger("click");
            }
        }
    });

    /* Editar */
    var incapacitiesEditSelectionButton = "#edit-selected-incapacities";
    var incapacitiesEditLoadData = "#incapacities-load-data";

    AJAX.editAction(incapacitiesEditSelectionButton, incapacitiesEditLoadData, inputSelectionIncapacities, {
        id: "edit-incapacities-dialog",
        position: "top",
        ajaxCallback: {
            post: {
                success: function() {
                    $(incapacitiesButtonRefresh).trigger("click");
                }
            }
        }
    });


    /* Agregar certificación */
    var addCertificationButton = "#add-certification";
    var certificationFormToProcess ="#certification-form";
    var certificationInputSuggestion = "#incapacitiesDiagnosticSuggestion";
    var certificationInputValue = "#incapacities_diagnostic_id";

    AJAX.addAction(addCertificationButton, certificationFormToProcess, {
        id: "add-certification-dialog",
        title: "Registrar certificación",
        width: 600,
        modal: false,
        position: "top",
    });

    /* Indications input selection and trigger button */
    var inputSelectionCertifications = "input.certification";
    var inputCertificationsTrigger = "#app-certifications-check";

    /* Check and uncheck actions */
    HTML.checked(inputSelectionCertifications);
    HTML.checkedTrigger(inputCertificationsTrigger, inputSelectionCertifications);

    /* Refresh action */
    var certificationsButtonRefresh = "#run-certification-refresh";
    var certificationsView = "#view-certifications";

    AJAX.loadResource(certificationsButtonRefresh, certificationsView);

    /* Eliminar */
    AJAX.postAction("#delete-selected-certifications", inputSelectionCertifications, {
        title: "Eliminar certificaciones",
        content: $("<p>Desea realmente eliminar las certificaciones seleccionadas ?</p>"),
        className: "danger",
        ajaxCallback: {
            success: function() {
                $(certificationsButtonRefresh).trigger("click");
            }
        }
    });

    /* Editar */
    var certificationsEditSelectionButton = "#edit-selected-certifications";
    var certificationsEditLoadData = "#certifications-load-data";

    AJAX.editAction(certificationsEditSelectionButton, certificationsEditLoadData, inputSelectionCertifications, {
        id: "edit-certifications-dialog",
        position: "top",
        ajaxCallback: {
            post: {
                success: function() {
                    $(certificationsButtonRefresh).trigger("click");
                }
            }
        }
    });


    /* Agregar Antecedentes */
    var addBackgroundButton = "#add-background";
    var backgroundFormToProcess ="#background-form";
    var backgroundInputSuggestion = "#backgroundSuggestion";
    var backgroundInputValue = "#background_id";

    AJAX.addAction(addBackgroundButton, backgroundFormToProcess, {
        id: "add-background-dialog",
        title: "Registrar Antecedentes",
        width: 600,
        modal: false,
        position: "top",
    });

    /* Indications input selection and trigger button */
    var inputSelectionBackgrounds = "input.background";
    var inputBackgroundsTrigger = "#app-backgrounds-check";

    /* Check and uncheck actions */
    HTML.checked(inputSelectionBackgrounds);
    HTML.checkedTrigger(inputBackgroundsTrigger, inputSelectionBackgrounds);

    /* Refresh action */
    var backgroundsButtonRefresh = "#run-background-refresh";
    var backgroundsView = "#view-backgrounds";

    AJAX.loadResource(backgroundsButtonRefresh, backgroundsView);

    /* Eliminar */
    AJAX.postAction("#delete-selected-backgrounds", inputSelectionBackgrounds, {
        title: "Eliminar antecedentes",
        content: $("<p>Desea realmente eliminar los antecedentes seleccionadas ?</p>"),
        className: "danger",
        ajaxCallback: {
            success: function() {
                $(backgroundsButtonRefresh).trigger("click");
            }
        }
    });

    /* Editar */
    var backgroundsEditSelectionButton = "#edit-selected-backgrounds";
    var backgroundsEditLoadData = "#backgrounds-load-data";

    AJAX.editAction(backgroundsEditSelectionButton, backgroundsEditLoadData, inputSelectionBackgrounds, {
        id: "edit-backgrounds-dialog",
        position: "top",
        ajaxCallback: {
            post: {
                success: function() {
                    $(backgroundsButtonRefresh).trigger("click");
                }
            }
        }
    });

    /* view old histories */
    $('#view-old-histories').click(function(e){
        e.preventDefault();

        $.ajax({
            url: $(this)[0].getAttribute("data-resource"),
            type: "get",
            dataType: "html",
            beforeSend: function() {
                HTML.loader();
            },
            success: function(data) {
                HTML.dialog({
                    title: "Historias anteriores",
                    content: data,
                });
                HTML.loader();
            },
            error: function(jqXHR, textStatus, error) {
                DEBUG.ajaxError(jqXHR, textStatus, error);
                HTML.loader();                
            }
        });
    });

    /* NAVIGATION */
    /*var nav = $(".pseudo-nav");
    nav.children("li").addClass("dropdown");
    var firstList = nav.children("li");
    for (var i = firstList.length - 1; i >= 0; i--) {
        $(firstList[i]).children("a").attr("role", "button");
        $(firstList[i]).children("a").attr("data-toggle", "dropdown");
        $(firstList[i]).children("a").attr("data-target", "#");
        $(firstList[i]).children("ul").attr("class", "dropdown-menu");
        $(firstList[i]).children("ul").attr("role", "menu");
    };*/

    var tas = $("[name='tas']");
    var tad = $("[name='tad']");
    var tam = $("[name='tam']");

    tas.keyup(function() {
        var value = ( Math.floor(tas.val()) + 2 * Math.floor(tad.val()) ) / 3;
        tam.val(value.toFixed(1));
    });
    tad.keyup(function() {
        var value = ( Math.floor(tas.val()) + 2 * Math.floor(tad.val()) ) / 3;
        tam.val(value.toFixed(1));
    });
    tam.keyup(function() {
        var value = ( Math.floor(tas.val()) + 2 * Math.floor(tad.val()) ) / 3;
        tam.val(value.toFixed(1));
    });

    $(".print-function").click(function(){ window.print() });

    $(".view-print-version").click(function(){
        $("#rootNavigation").fadeToggle("slow");
        $("#rootBreadcrumbs").fadeToggle("slow");
        $("#wrapperFooter").fadeToggle("slow");
        $(".logo-empresa").fadeToggle("slow");
        $(".print-header").fadeToggle("slow");
        $(".back-button").fadeToggle("slow");
        $("#wrapper").toggleClass("documentToPrint");
    });

    $(".init-popover").popover();

    /******************************************************************
     *  MÓDULO DEL SISTEMA (Application)
     *  Desarrollo del sistema y copias de seguridad
     ******************************************************************/

    AJAX.loadResource("#exec-copy", context);
    AJAX.loadResource("#view-copies", context);

});