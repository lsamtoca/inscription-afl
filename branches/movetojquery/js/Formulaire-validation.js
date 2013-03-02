var mainform_required_fields = [
'Nom',
'Prenom',
'naissance',
'sexe',
'mail',
'serie',
'Nvoile',
'statut',
'adherant'
];

var mainform_email_fields = [
'mail'
];

var mainform_crequired_fields = {
    'lic':'radio_ffv',
    'num_club':'radio_ffv',
    'isaf_no':'etranger'
};

var mainform_regexp_fields = {
    'naissance':/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/,
    'num_club':/^[0-9]{5}$/,
    'Cvoile':/^[a-z]{3}$/i,
    'Nvoile':/^[0-9]{1,6}$/,
    'lic':/^[0-9]{7}[a-z]$/i,
    'isaf_no':/^[a-z]{5}[0-9]+$/i
};

var searchform_regexp_fields = {
    'search_lic':/^[0-9]{7}[a-z]$/i,
    'search_isaf':/^[a-z]{5}[0-9]+$/i
};

$(document).ready(function (){
    
    // We do not need this
    // set_validations is called when we set the i18n
    //set_validations();   

    });

var debug;
//debug=false;
debug=true;

function set_validations (){

    if(debug) 
        console.log('>> Ajout des validations aux formulaires');    
    
    // Mainform
    $("#mainform").validate({
        // Put the debug option for debugging only
        // -- the form won't be sent
        //      debug:true
        });
    add_email("mainform",mainform_email_fields);     
    add_required("mainform",mainform_required_fields);
    add_crequired("mainform",mainform_crequired_fields);
    add_regexp("mainform",mainform_regexp_fields);
    //   console.log('Ajouté validations mainform');

    // Searchform
    if($('#searchform').val() != undefined){
        
        $('#searchform').validate({
            // Put the debug option for debugging
            // -- the form won't be sent
            // debug:true,
            submitHandler: function(form) {
                if(
                    ($('#searchform :input[name="search_lic"]')).val() == ''
                    &&
                    ($('#searchform :input[name="search_isaf"]')).val() == ''
                    ){
                    var message=msg_validate_search;
                    alert(message);
                    return false;
                }
                // do other stuff for a valid form
                form.submit();
            }
        });

        add_regexp('searchform',searchform_regexp_fields);
                       
    }
    else {
        if(debug)
            console.log('Formulaire recherche n\'existe pas!');
    }
    

    if(debug)
        console.log('<< Ajout des validations terminé');

};


function check_selector(str){
    
    if($(str) == undefined){
        console.log('Selector '+str+' not found');
    }
    else {
        console.log('Selector '+str+' OK');        
    }
    
};


function add_required(form_id,fields){


    for (var i=0;i< fields.length;i++){

        var field = fields[i];
        
        var selector = 
        '#'+form_id+' ' +
        ':input[name="'+field+'"]';
        
        if ($(selector).length == 0){
            console.error(selector+' pas trouve');
            continue;
        }
        
        //       $(selector).rules('remove');            
        $(selector).rules('add',
        {
            'required':true,
            'messages'  :{
                'required':msg_validate_required
            }
        }
        );
    }
    
}

function add_crequired(form_id,fields){

    for (var field in fields){
    
        var selector = 
        '#'+form_id+' ' +
        ':input[name="'+field+'"]';
        var radio_selector ='#'+fields[field];        

        if ($(selector).length == 0){
            console.error('Selector '+selector+' pas trouve');
            continue;
        }

        if ($(radio_selector).length == 0){
            console.error('Selector '+radio_selector+' pas trouve');
            continue;
        }
        
        var val_radio_selector =
        '$(\''+radio_selector+'\').is(":checked")';
        
        var code =  'var callback = '
        +   'function () { '
        +   'return '+val_radio_selector+' ;};'
    
        eval(code);

        //        $(selector).rules('remove');
        $(selector).rules(
            'add',
            {
                'required':callback,
                'messages'  :{
                    'required':msg_validate_required
                }
            }
            );

    }

}

// This does not wok anymore -- why ?

function add_email (form_id,fields){

    for (var i=0;i< fields.length;i++){

        var field = fields[i];
        
        var selector = 
        '#'+form_id+' ' +
        ':input[name="'+field+'"]';
        
        if ($(selector).length == 0){
            console.error(selector+' pas trouve');
            continue;
        }
        
        //        $(selector).rules('remove');            
        $(selector).rules('add',
        {
            'email':true,
            'messages'  :{
                'email':msg_validate_email
            }
        }
        );
    }

}

function add_regexp(form_id,fields){
        
    for (var field in fields){

        var selector = 
        '#'+form_id+' ' +
        ':input[name="'+field+'"]';

        var pattern = fields[field];
        var method_name=form_id+'_'+field;
        var method_code = "";
        var code =  "method_code = "
        +   "function (value,element) { "
        +   "return this.optional(element) || "
        +   pattern +".test(value); "
        +   "};"

        var message = 
        "Please check your input (expected pattern: "
        + pattern + ")";
        
        var msg = 'msg_validate_'+field;
        
        if($.i18n.prop(msg) != '['+msg+']')
            message=eval(msg);
        else {
            console.warn('Could not find '+msg);
        }
                
           
        eval(code);
        $.validator.addMethod(
            method_name,
            method_code,
            message
            );

        $(selector).rules("add",method_name);
    
    }   
}