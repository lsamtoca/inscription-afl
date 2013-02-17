function check_selector(str){
    if($(str) == undefined)
        console.log('Selector '+str+' not found');
    else
        console.log('Selector '+str+' OK');
    
};


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
    
    set_validations();    
    console.log('Ajouté validation');

});

function set_validations (){
    
    // Mainform
    $("#mainform").validate({
        debug:true
    });
 
    add_email("mainform",mainform_email_fields);     
    add_required("mainform",mainform_required_fields);
    add_crequired("mainform",mainform_crequired_fields);
    add_regexp("mainform",mainform_regexp_fields);

    // Searchform
    $("#searchform").validate({
//        debug:true
    });
 
     check_selector('#searchform :input[name="search_isaf"]');
    
//            $('#searchform :input[name="search_isaf"]').rules('remove');
//    $('#searchform :input[name="search_isaf"]')
//    .rules('add',{
//        'required': function (){
//            return
//            true ;
//            $('#searchform :input[name="search_lic"]').val()
//            == "";
//        },
//        'messages'  :{
//            'required':msg_validate_required
//        }
//    }
//    );
    
    add_regexp("searchform",searchform_regexp_fields);
    
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



/*
$(document).ready(function() {


    $('#searchform').validate({
        rules : {
            search_lic : {
                //size : 8,
                //maxlength : 8,
                required : true,
                licence : true             
            }
        },
        messages : {
            search_lic : {
                required : "J'ai besoin de ce numero"
            }
        }
    });
    




    $('#searchform_isaf').validate({
        rules : {
            search_isaf :{
                //                size: 8,
                //                maxlength:8,
                required : true,
                isafno : true
            }
        },
        messages : {
            search_isaf : {
                required: "J'ai besoin de ce numero"
            }
        }
    });


    $('#mainform').validate({
        rules : {
            Nom : {
                required : true
            },
            Prenom : {
                required : true
            },
            mail : {
                required : true,
                email : true
            },
            naissance : {
                required : true,
		naissance : true
            }
        },
        messages : {
    }
    });

});


/* Les validations anciennes

    <script  type="text/javascript" xml:space="preserve">
        //<![CDATA[

        function add_validations_searchform(){

            var searchvalidator  = new Validator('searchform');
            // Choix de l'affichage des messages d'erreur
            searchvalidator.EnableOnPageErrorDisplay();
            // search_validator.EnableOnPageErrorDisplaySingleBox();
            //search_validator.EnableMsgsTogether();

            searchvalidator.clearAllValidations();
            searchvalidator.formobj.old_onsubmit = null;


            my_validation_required(
            'searchform',
            'search_lic',
            searchvalidator,
            'Champ nécessaire',
            'Required');

            my_validation_regexp(
            'searchform',
            'search_lic',
            searchvalidator,
            '[0-9]{7,7}[A-Za-z]',
            'NNNNNNNL (7 chiffres et 1 lettre)',
            'NNNNNNNL (7 digits and 1 letter)');
        }

        //]]>
    </script>
    <script  type="text/javascript" xml:space="preserve">
        //<![CDATA[

        function add_validations_searchform_isaf(){

            var searchvalidator  = new Validator('searchform_isaf');
            // Choix de l'affichage des messages d'erreur
            searchvalidator.EnableOnPageErrorDisplay();
            // search_validator.EnableOnPageErrorDisplaySingleBox();
            //search_validator.EnableMsgsTogether();

            searchvalidator.clearAllValidations();
            searchvalidator.formobj.old_onsubmit = null;


            my_validation_required(
            'searchform_isaf',
            'search_isaf',
            searchvalidator,
            'Champ demandé',
            'Field required');

            my_validation_regexp(
            'searchform_isaf',
            'search_isaf',
            searchvalidator,
            '[A-Za-z]{5}[0-9]+',
            'LLLLLN... (5 lettres et au moins 1 chiffre)',
            'LLLLLN... (5 letters and at least 1 digit)');

        }

        //]]>
    </script>

    function add_validations_mainform(){

        var frmvalidator  = new Validator("mainform");
        // Choix de l'affichage des messages d'erreur
        frmvalidator.EnableOnPageErrorDisplay();
        // frmvalidator.EnableOnPageErrorDisplaySingleBox();
        //frmvalidator.EnableMsgsTogether();

        frmvalidator.clearAllValidations();
        frmvalidator.formobj.old_onsubmit = null;

        my_validation_required('mainform','Nom',frmvalidator);
        my_validation_required('mainform','Prenom',frmvalidator);
        //  my_validation_required('mainform','naissance',frmvalidator);

        
  Why this does not wotk anymore ?
  var year="[1-2][0-9]{3}";
  var mois="0[1-9]|1[0-2]";
  var jour="0[1-9]|[1-2][0-9]|3[0-1]";
  var date= year + "-" + mois + "-" + jour;
  my_validation_regexp('mainform','naissance',frmvalidator,date,'AAAA-MM-JJ','YYYY-MM-DD'); 
        
        my_validation_radio('mainform','sexe',frmvalidator,'Etes vous homme ou femme ?','Are you Male or Female ?');

        my_validation_required('mainform','mail',frmvalidator);
        my_validation_email('mainform','mail',frmvalidator);

        my_validation_required_condition('mainform','num_club',frmvalidator,
        "VWZ_IsChecked(document.forms['mainform'].elements['statut'],'Licencie')",
        'Vous êtes licencié FFV',
        'You have an FFV licence');


        my_validation_regexp('mainform','num_club',frmvalidator,'[0-9]{5}','NNNNN (5 chiffres)','NNNNN (5 digits)');

        my_validation_radio('mainform','serie',frmvalidator,'Choisssez : Standard, Radial, ou 4.7','Choose : Standard, Radial, or 4.7');

        my_validation_required('mainform','Nvoile',frmvalidator);
        my_validation_regexp('mainform','Nvoile',frmvalidator,'[0-9]{1,6}','NNNNNN (au plus 6 chiffres)','NNNNNN (at most 6 digits)');
        my_validation_regexp('mainform','Cvoile',frmvalidator,'[A-Z]{3,3}','LLL (3 lettres)','LLL (3 letters');

        my_validation_radio('mainform','statut',frmvalidator,'Licencié FFV ?','Do you have an FFV licence?');
        my_validation_radio('mainform','adherant',frmvalidator,'Adhérant AFL ?','Are you member of the AFL?');

        my_validation_required_condition('mainform','lic',frmvalidator,
        "VWZ_IsChecked(document.forms['mainform'].elements['statut'],'Licencie')",
        'Vous êtes licencié FFV',
        'You have an FFV licence');

        my_validation_regexp('mainform','lic',frmvalidator,'[0-9]{7,7}[A-Za-z]',
        'NNNNNNNL (7 chiffres et 1 lettre)',
        'NNNNNNNL (7 digits and 1 letter)');



        my_validation_required_condition('mainform','isaf_no',frmvalidator,
        "VWZ_IsChecked(document.forms['mainform'].elements['statut'],'Etranger')",
        'Vous êtes coureur étranger',
        'You are an international sailor');

        my_validation_regexp('mainform','isaf_no',frmvalidator,'[A-Za-z]{5}[0-9]+',
        'LLLLLN... (5 lettres et au moins 1 chiffre)',
        'LLLLLN... (5 letters and at least 1 digit)');

    }


*/
