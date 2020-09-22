jQuery(document).ready(function(){

	jQuery("#pmpromh_multisite_select").on("change", function(){

		var value = jQuery(this).val();

		var data = {
			action: 'pmpromh_get_blog_pages',
			site_id: value,
			security: pmpromh_nonce
		}

		jQuery.post( pmpromh_ajaxurl, data, function( response ){

			jQuery("#pmpromh_multisite_pages").html("");

			if( response ){
				response = JSON.parse( response );
				jQuery.each( response, function( key, val ){
					console.log(key);
					console.log(val);
					jQuery("#pmpromh_multisite_pages").append("<option value='"+key+"'>"+val+"</option>");
				});
			}

		});

	});

});