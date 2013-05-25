

function myaccordion_get_active(){
    
    var hash = window.location.hash.substring(1);
    // Big hack :-(
    var active=($('#accordion').children("h3[id="+hash+"]").index())/2;        
    if(active<0) 
        active=true;
    
    return active;
  
}

function myaccordion_set_accordion(){
    
    $('#accordion').accordion({ 
        collapsible:true,
        active: myaccordion_get_active(), 
        heightStyle: "content",
        event: "click hoverintent"
    });


    $.fn.textWidth = function(){
        var html_org = $(this).html();
        var html_calc = '<span>' + html_org + '</span>';
        $(this).html(html_calc);
        var width = $(this).find('span:first').width();
        $(this).html(html_org);
        return width;
    };
        
    var max_width = -1;
    $("#accordion>h3").each(function(){
        max_width = Math.max(max_width, $(this).textWidth());
    });
    console.log(max_width );
    $("#accordion>h3").width(max_width + 50);

}