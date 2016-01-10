// Global variables imported:
// debug
var documentLanguage = 'fr';
var messagesFileName = 'Messages';
var messagesPath = 'bundle/';
var debug=true;
var allowI18Cache=true;
if(debug){
 allowI18Cache=false;   
}

$(document).ready(function () {
    if (debug)
        console.log('>> Ajout du i18n');
    loadBundles(documentLanguage);
    //$('#lang').live('click', switchLanguage);
    $('.languageSelector').each(function () {
        $(this).live('click',
                function () {
                    documentLanguage = $(this).attr('title');
                    loadBundles(documentLanguage);
                })
    });
    if (debug)
        console.log('<< i18n ajouté');
});

/*
 function switchLanguage() {
 if (documentLanguage == 'fr')
 documentLanguage = 'en';
 else
 documentLanguage = 'fr';
 loadBundles(documentLanguage);
 }
 */

function loadBundles(lang) {
    $.i18n.properties({
        name: messagesFileName,
        path: messagesPath,
        mode: 'both',
        //            encoding: 'utf-8',
        cache:allowI18Cache,
        language: lang,
        callback: function () {
            doUpdate();
        }
    });
}

// doUpdate is the main function we execute 
// when #lang or .languageSelector are clicked
function doUpdate() {
    updateMultipleMessages();
    updateLeftLabels();
    updateSubmits();
    updateDatePickers();
    updateValidationMessages();
    updateInput_lang();
}

function msgUndefinedLog(msg) {
    if ($.i18n.prop(msg) == '[' + msg + ']') {
        if (debug) {
            console.warn("Message " + msg + " undefined or empty");
        }
        return true;
    }
    return false;
}

function updateMultipleMessages() {
    $('.msg').each(function () {
        var id = $(this).attr('id');
        var msg = 'msg_' + id;
        if (!msgUndefinedLog(msg)) {
            $(this).empty().append(eval(msg));
        }
    });
}

function updateSubmits() {
// Mise a jour des buttons valider 
    if (!msgUndefinedLog(msg_chercher)) {
        if ($('#searchform').length != 0)
            $('#searchform input[type=submit]').attr('value', msg_chercher);
    }
    if (!msgUndefinedLog(msg_valider)) {
        if ($('#mainform').length != 0) {
            $('#mainform input[type=submit]').attr('value', msg_valider);
        }
    }
}

function  updateLeftLabels() {
// mise a jour des deux points apres les etiquettes
  //  if (!msgUndefinedLog(label_termination)) {
        $("label.left>.msg").append(label_termination);
    //}
}

function updateDatePickers() {
// Mise a jour du datepicker
    switch (documentLanguage) {
        case 'it':
        case 'fr':
            $.datepicker.setDefaults($.datepicker.regional['fr']);
            break;
        default:
            $.datepicker.setDefaults($.datepicker.regional['']);
            break;
    }
}

function updateValidationMessages() {
    if (typeof set_validations == 'function'){
        // This is defined in Formulaire-Validation
        set_validations();
        if ($('form').length != 0) {
            $('form').validate().resetForm();
            $('form').validate();
        }
    }
}

function updateInput_lang() {
    // Mise à jour du field input_lang (hidden)
    $('#input_lang').val(documentLanguage);
    if (debug)
        console.log($('#input_lang').val());

    $('#search_input_lang').val(documentLanguage);
    if (debug)
        console.log($('#search_input_lang').val());
}
