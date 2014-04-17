

/**
 *
 */
jQuery(document).ready(function()
{
	jQuery('#slider-banner-list').JqueryBannerList();
});


/**
 *
 */
( function( $ ) {
			
	$.fn.JqueryBannerList = function() {

		var banner_list = null;
		var slider_list = null;


		function get_nonce()
		{
			var nonce = $('input[name="nh-banner-options-nonce"]').val();
			return nonce;
		}

		//--------------------------------------------------------------------------------
		// 
		//--------------------------------------------------------------------------------
		function add_to_slider_list( banner_img )
		{
			var banner_id = $(banner_img).attr('banner_id');
			var banner_src = $(banner_img).attr('src');
		
			$(slider_list)
				.append('<li class="banner">'+
					'<img src="'+banner_src+'" /><br/>'+
					'<input type="hidden" name="banner_id[]" value="'+banner_id+'" />'+
					'<label>URL</label>'+
					'<input type="text" name="banner_url[]" value="" />'+
					'<label>ALT</label>'+
					'<input type="text" name="banner_alt[]" value="" />'+
					'</li>');
			
			refresh_slider_list();
		}
		
		function refresh_slider_list()
		{
			$(slider_list).sortable(
			{
				receive: function(event, ui) { sortableIn = 1; $(ui.item).removeClass('out'); },
				over: function(event, ui) { sortableIn = 1; $(ui.item).removeClass('out'); },
				out: function(event, ui) { sortableIn = 0; $(ui.item).addClass('out'); },
				beforeStop: function(event, ui) { if (sortableIn == 0) { ui.item.remove(); } },
				stop: function(event, ui) { $(ui.item).removeClass('out'); }
			});
		}
		
		function delete_banner( banner_id )
		{
			if( confirm("Are you sure you want to delete this banner?\nThis action cannot be reversed.") != true ) return;

			var data = {};
			data['action'] = 'nh-banner-options';
			data['nonce'] = get_nonce();
			data['ajax-action'] = 'delete-banner';
			data['banner_id'] = banner_id;
			
			$.ajax( {
				type: "POST",
				url: ajaxurl,
				data: data,
				dataType: "json"
			})
			.done(function( data ) {
				if( data['status'] == false )
				{
					alert( "Failed: " + data['message'] );
				}
				else
				{
					$('.banner[banner_id="'+banner_id+'"]')
						.parent().remove();
					$('.banner input[name="banner_id[]"][value="'+banner_id+'"]')
						.parent().remove();
				}
			})
			.fail(function( jqXHR, textStatus ) {
				alert( "Request failed: " + jqXHR.responseText + " - " + textStatus );
			});
		}
		
		//--------------------------------------------------------------------------------
		// 
		//--------------------------------------------------------------------------------
		function setup( container )
		{
			// find banner-list
			banner_list = $(container).find('.banner-list');
			if( !banner_list ) return;

			// find slider-list
			slider_list = $(container).find('.slider-list ol');
			if( !slider_list ) return;
			
			$(banner_list).find('.banner').click(function()
			{
				add_to_slider_list(this);
			});
			
			$(banner_list).find('.delete-button').click(function()
			{
				delete_banner( $(this).attr('banner_id') );
			});
			
			refresh_slider_list();
		}
		
		return this.each( function() { setup(this); } );
	}
	
})( jQuery );

