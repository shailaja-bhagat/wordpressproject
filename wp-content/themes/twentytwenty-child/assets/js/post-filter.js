jQuery(document).ready(function() { 
    jQuery(document).on('click', '.category-filter', function(e) {

        jQuery(this).siblings().removeClass('active');
        jQuery(this).addClass('active')
        // jQuery(this).addClass("active");
        e.preventDefault();

        var category = jQuery(this).data('category');

        jQuery.ajax({
            url     : wp_ajax.ajax_url,
            data    : { action : 'filter', category : category},
            type    : "post",
            success : function(result){
                // alert("result: "+result);
                jQuery('.post-row').html(result);
            },
            error: function(result) {
                console.warn(result);
            }
        });
        
    });
});