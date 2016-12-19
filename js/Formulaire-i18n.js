// Global variables imported:
// debug
if (typeof documentLanguage == 'undefined') {
    var documentLanguage = 'fr';
}

var messagesFileName = 'Messages';
var messagesPath = 'bundle/';
var debug = true;
var allowI18Cache = true;
if (debug) {
    allowI18Cache = false;
}

$(document).ready(function () {
    if (debug)
        console.log('>> Ajout du i18n');

    $('.languageSelector').each(function () {
        $(this).live('click',
                function () {
                    documentLanguage = $(this).attr('title');
                    $('.languageSelector').css({'text-decoration': 'none'});
                    $(this).css({'text-decoration': 'underline'});
                    loadBundles(documentLanguage);
                })
    });

    var userLang = documentLanguage;
    var selector = '.languageSelector[title="' + userLang + '"]';
    if (debug)
        console.log(selector);

    $(selector).trigger('click');

    if (debug)
        console.log('<< i18n ajouté');
});


function loadBundles(lang) {
    $.i18n.properties({
        name: messagesFileName,
        path: messagesPath,
        mode: 'both',
        encoding: 'utf-8',
        cache: allowI18Cache,
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

    // Hack to distinguish AFL from OTHERS
    if (typeof iAmAFL != 'undefined') {
        if (iAmAFL)
        {
            if (!msgUndefinedLog('msg_non_licencieAFL'))
                msg_non_licencie = msg_non_licencieAFL;
            if (!msgUndefinedLog('msg_l_adherantAFL'))
                msg_l_adherant = msg_l_adherantAFL;
            if (!msgUndefinedLog('msg_l_serieAFL'))
                msg_l_serie = msg_l_serieAFL;
        }
    }

    // For each element of class .msg of 
    // identifier $id
    // look for the value of msg_$id 
    // and update the content

    $('.msg').each(function () {
        var id = $(this).attr('id');
        var msg = 'msg_' + id;
        if (!msgUndefinedLog(msg)) {
            $(this).empty().append(eval(msg));
        }
    });
}

function updateSubmits() {// Mise a jour des buttons valider 
    if ($('#searchform').length != 0 &&
            !msgUndefinedLog('msg_chercher'))
        $('#searchform input[type=submit]').attr('value', msg_chercher);

    if ($('#mainform').length != 0
            && !msgUndefinedLog('msg_valider'))
        $('#mainform input[type=submit]').attr('value', msg_valider);

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
    if (typeof set_validations == 'function') {
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
