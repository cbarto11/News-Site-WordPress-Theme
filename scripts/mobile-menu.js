/**
 * 
 * @author Crystal Barton
 */

( function( $ ) {
			
	$.fn.MobileMenu = function()
	{
		var menu_wrapper = this;
		var menu_items = {};
		
		//
		// Setup the css for the slides and create a wrapper for the slides.
		//
		function setup_menu( menu_wrapper )
		{
			hide_all_menu_containers();
			
			var items = $(menu_wrapper).children('ul').children();
			if( (items.length == undefined) || (items.length < 1) ) return;
			
			for( var i = 0; i < items.length; i++ )
			{
				var menu_item_link = $(items[i]).children('a');
				if( !menu_item_link ) continue;

				var menu_item_id = $(menu_item_link).attr('href');
				if( !menu_item_id ) continue;
				
				var menu_item_container = $(menu_wrapper).find(menu_item_id);
				if( !menu_item_container ) continue;

				menu_items[menu_item_id] = {
					'item': items[i],
					'container': menu_item_container
				};
				
				$(menu_item_link).click(function()
				{
					click_menu_item( this );
					return false;
				});
			}
		}
		
		function click_menu_item( menu_item_link )
		{
			var menu_item_id = $(menu_item_link).attr('href');
			if( !menu_item_id ) return;
			
			var menu_item = menu_items[menu_item_id]['item'];
			var menu_container = menu_items[menu_item_id]['container'];

			var currently_showing = false;
			if( $(menu_container).hasClass('mm-show') )
				currently_showing = true;
			
			for( id in menu_items )
			{
				var item = menu_items[id]['item'];
				var container = menu_items[id]['container'];
				
				if( $(container).hasClass('mm-show') )
				{
					$(item).removeClass('mm-show').addClass('mm-hide');
					$(container).slideUp( 100, function()
					{
						$(this).removeClass('mm-show').addClass('mm-hide').hide();
					});
				}
			}
			
			if( !currently_showing )
			{
				$(menu_item).removeClass('mm-hide').addClass('mm-show');
				$(menu_container).slideDown( 100, function()
				{
					$(menu_container).removeClass('mm-hide').addClass('mm-show');
				});
			}
		}
		
		
		function hide_all_menu_containers()
		{
			$(menu_wrapper).children('ul').children('li').removeClass('mm-show').addClass('mm-hide');
			$(menu_wrapper).children('div').removeClass('mm-show').addClass('mm-hide').hide();
		}
		
		//
		// 
		//
		
		//
		// Initialize the plugin.
		//
		return this.each( function() { setup_menu(this); } );
	}
	
})( jQuery )
