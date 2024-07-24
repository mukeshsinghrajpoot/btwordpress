jQuery(document).ready(function($){
    jQuery('#select_all').on('click',function(){

        if(this.checked){
            jQuery('.checkbox').each(function(){
                this.checked = true;
            });
        }else{
             jQuery('.checkbox').each(function(){
                this.checked = false;
            });
        }
    });
    
    jQuery('.checkbox').on('click',function(){
        if(jQuery('.checkbox:checked').length == jQuery('.checkbox').length){
            jQuery('#select_all').prop('checked',true);
        }else{
            jQuery('#select_all').prop('checked',false);
        }
    });
});