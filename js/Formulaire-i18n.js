var ids=[
'lang',
         
'liste_preinscrits',
'deadline',
        
'search_legend',
'l_search_lic',
'l_search_isaf',
        
'mainform_legend',
'l_Nom',
'l_Prenom',
'l_naissance',
'l_homme',
'l_femme',
'l_mail',
'l_lic',
'l_isaf_no',
'l_afl',
'l_ffv',
'l_autre',
'l_etranger',
'l_oui',
'l_non',
'l_Nvoile',
'l_num_club',
'l_nom_club',
'licencie_ffv',
'non_licencie',
'etranger',
'message_confirmation',
'preregistration_form',
'preregistered_sailors'
];

var document_language='fr';    

$(document).ready(function() {
    
    if(debug) console.log('>> Ajout du i18n');
    loadBundles('fr');
    $('#lang').live('click',changerLangue);
    if(debug) console.log('<< i18n ajouté');
  
});

function loadBundles(lang) {
    $.i18n.properties({
        name:'Messages', 
        path:'bundle/', 
        mode:'both',
        //            encoding: 'utf-8',
        //            cache:true,
        language: lang, 
        callback: function() {
            doUpdate();
        }
    });
}

function changerLangue () {

    if (document_language == 'fr') 
    {                
        loadBundles('en');
        document_language = 'en';
    }
    else    
    {
        loadBundles('fr');
        document_language = 'fr';
    }        
}

function doUpdate() {

    
    updateFields();
            
    // Mise a jour du datepicker
    if( langue == 'fr')
        $.datepicker.setDefaults( $.datepicker.regional['fr'] );
    else
        $.datepicker.setDefaults( $.datepicker.regional[''] );


    // A venir, mise a jour des messages d'erreur de la validation
    updateValidationMessages();
    $('form').validate();
            
}


function updateFields (){

    for(var i=0; i<ids.length; i++){

        var selector = '#'+ids[i];
        var msg = 'msg_'+ids[i];
        
        if($(selector).length == 0) {
            console.warn(selector+" n'est pas un id valide");
            continue;
        }
        if($.i18n.prop(msg) == '['+msg+']') {
            console.warn(msg +" pas defini ou vide");
            continue;
        }
            
        $(selector).empty().append(eval(msg));	
    };
    
    // Mise a jour des buttons valider 
    if($('#searchform').length != 0)
        $('#searchform input[type=submit]').attr('value',msg_chercher);
    
    if($('#mainform').length != 0){
        $('#mainform input[type=submit]').attr('value',msg_valider);   
        // mise a jour des deux points apres les etiquettes
        $("label.left").append(label_termination);
    }
    // Mise à jour du field input_lang (hidden)
    if( langue == 'fr')
        $('#input_lang').attr('value','fr');
    else
        $('#input_lang').attr('value','en');

}


function updateValidationMessages(){

    set_validations();
    if($('form').length != 0)
        $('form').validate().resetForm();
    

}