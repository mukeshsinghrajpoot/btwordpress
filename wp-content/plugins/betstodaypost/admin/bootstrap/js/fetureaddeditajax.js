jQuery(document).on('click', '#submit', function(e){
    e.preventDefault();
    jQuery(".error1").remove();
    var fd = new FormData();
    var matchup = jQuery("#matchup").val();
    fd.append("matchup", matchup);

    var match_id = jQuery("#match_id").val();
    fd.append("match_id", match_id);
    
    var oddsoption = jQuery("#oddsoption").val();
    fd.append("oddsoption", oddsoption);

    var odds = jQuery("#odds").val();
    fd.append("odds", odds);

    var links = jQuery("#links").val();
    fd.append("links", links);

    var result1 = jQuery("#result1").val();
    fd.append("result1", result1); 

    var matchdate = jQuery("#matchdate").val();
    fd.append("matchdate", matchdate);

    if (!matchup) 
    {
     var message = 'matchup name is required';
     jQuery('#matchup').after('<div class="error1">'+message+'</div>');
    }
    if (!odds) 
    {
     var message = 'odds name is required';
     jQuery('#odds').after('<div class="error1">'+message+'</div>');
    }
    if (!links) 
    {
     var message = 'links is required';
     jQuery('#links').after('<div class="error1">'+message+'</div>');
    }
    if (!result1) 
    {
     var message = 'result name is required';
     jQuery('#result1').after('<div class="error1">'+message+'</div>');
    }
    if (!matchdate) 
    {
     var message = 'matchdate name is required';
     jQuery('#matchdate').after('<div class="error1">'+message+'</div>');
    }
    if (!matchup || !odds || !result1 || !matchdate || !links) {
            return false;
        }
    fd.append('action', 'feture_key_editresponce');  
    jQuery.ajax({
        type: 'POST',
        url: fetureaddeditajax_object.ajax_url,
        data: fd,
        contentType: false,
        processData: false,
        success: function(response){
            response = jQuery.trim(response);
            console.log(response);
            if(response=="Done")
            {
                jQuery("#result").html('<p style="color:green;font-size: 21px;font-weight:900;text-align: center;">successfully Update data.</p>');
                jQuery("#result").fadeTo(2000, 500).slideUp(500, function(){
                 jQuery("#result").slideUp(500);
             });
            function reload() {
              document.location.reload();
            }
            setTimeout(reload, 1000);  
            }
            else
            {
                jQuery("#result").html('<p style="color:red;font-size: 21px;font-weight:900;text-align: center;">'+response+'<p>');
            }
            //console.log(response);
        }
    });
});