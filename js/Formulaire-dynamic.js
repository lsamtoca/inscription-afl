$(document).ready ( function () {
    
    if(debug) console.log('>> Ajout de la dymanique au formulaire');
    
    $('#radio_ffv').click( function(){
        // Licence num isaf num 
        $('#l_lic').show();
        $('#lic').show();
        $('#l_isaf_no').show();
        $('#isaf_no').show();
   
        // Messages
        $('#licencie_ffv').show();
        $('#etranger').hide();
        $('#non_licencie').hide();
    });

    $('#radio_etranger').click( function(){
        // Hide Licence num, show isaf num

        $('#l_lic').show();
        $('#lic').show();
        $('#l_lic').hide();
        $('#lic').val("");
        $("#mainform").validate().element("#lic");
        $('#lic').hide();

        $('#l_isaf_no').show();
        $('#isaf_no').show();

        
        $('#licencie_ffv').hide();
        $('#etranger').show();
        $('#non_licencie').hide();
    });

    $('#radio_autre').click( function(){
        $('#l_lic').hide();
        $('#lic').val("");
        $("#mainform").validate().element("#lic");
        $('#lic').hide();

        $('#l_isaf_no').hide();
        $('#isaf_no').val("");
        $("#mainform").validate().element("#isaf_no");
        $('#isaf_no').hide();


        $('#licencie_ffv').hide();
        $('#etranger').hide();
        $('#non_licencie').show();
    });
    
    
    //    $('#radio_ffv').click();
    //    $('#radio_LA4').click();
    //    $('#radio_F').click();
    //    $('#radio_adherant_non').click();
    //    console.log($('#mainform input[name=sexe]:checked').val());
    //    console.log($('#mainform input[name=serie]:checked').val());
    //    console.log($('#mainform input[name=statut]:checked').val());
    //    console.log($('#mainform input[name=adherant]:checked').val());
    $('#mainform input[name=sexe]:checked').click();
    $('#mainform input[name=serie]:checked').click();
    $('#mainform input[name=statut]:checked').click();
    $('#mainform input[name=adherant]:checked').click();
    
    if(debug) console.log('<< Ajout de la dynamique terminé');
    
});