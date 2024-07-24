jQuery(document).on('click', '#titlesubmit', function(e){
    e.preventDefault();
    jQuery(".error1").remove();
    var fd = new FormData();
    var bets_title = jQuery("#bets_title").val();
    fd.append("bets_title", bets_title);

    if (!bets_title) 
    {
     var message = 'Title is required';
     jQuery('#bets_title').after('<div class="error1">'+message+'</div>');
    }
    if (!bets_title) {
            return false;
        }
    fd.append('action', 'feture_key_title');  
    jQuery.ajax({
        type: 'POST',
        url: headertitle_object.ajax_url,
        data: fd,
        contentType: false,
        processData: false,
        success: function(response){
            response = jQuery.trim(response);
            console.log(response);
            if(response=="Done")
            {
                jQuery("#result").html('<p style="color:green;font-size: 21px;font-weight:900;text-align: center;">successfully data save.</p>');
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