jQuery(document).on('click', '#submit', function(e){
    e.preventDefault();
    jQuery(".error1").remove();
    var fd = new FormData();
    var bets_title = jQuery("#bets_title").val();
    fd.append("bets_title", bets_title);

    var titletable_id = jQuery("#titletable_id").val();
    fd.append("titletable_id", titletable_id);
    
    if (!bets_title) 
    {
     var message = 'title is required';
     jQuery('#bets_title').after('<div class="error1">'+message+'</div>');
    }
    if (!bets_title) {
            return false;
        }
    fd.append('action', 'feture_key_titleeditresponce');  
    jQuery.ajax({
        type: 'POST',
        url: titleeditajax_object.ajax_url,
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