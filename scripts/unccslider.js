/**
 * 
 * @author Crystal Barton
 */

( function( $ ) {
			
	$.fn.UNCCSlider = function(settings) {

		var slider = this;
		var active_index = 0;
		var new_index = 0;
		var num_slides = 0;
		var last_time = null;

		var options = {
			height:null,
			width:null,
			url:null
		};

		$.extend(options, settings);
		
		//
		// Setup the css for the slides and create a wrapper for the slides.
		//
		function setup_slider()
		{
			var slides = $(slider).find('.slide');
			if( (slides.length == undefined) || (slides.length < 2) ) return;

			num_slides = slides.length;

			var first_slide = slides[0];
/*			$(slides).wrapAll('<div class="wrapper">');

			var wrapper = $(slider).children('.wrapper');
*/
			if( options.width == null )
				options.width = $(first_slide).width();
				
			if( options.height == null )
				options.height = $(first_slide).height();
		
/*			$(slider)
				.css('overflow', 'hidden')
				.css('height', options.height)
				.css('position', 'relative');
		
			$(wrapper)
				.css('position', 'relative')
				.css('left', 0)
				.css('width', ((num_slides+1) * (options.width+2))+'px');
*/
			for( var i = 0; i < num_slides; i++ )
			{
				$(slides[i])
					.css('z-index', num_slides-i)
					.attr('slide', i);
			}
			
			if( options.url != null )
			{
				var nav = $('<div class="slider-navigation" />')
					.css('z-index', 9999)
					.css('position','absolute')
					.css('bottom', '10px')
					.css('right', '10px')
					.css('padding', '3px')
					.css('height', '16px')
					.css('vertical-align', 'middle')
					.css('border-radius', '5px')
					.css('background-color', '#CCC');
					//.css('background-color', 'rbga(0,0,0,0.5)');
				
				for( var i = 0; i < num_slides; i++ )
					$(nav).append( create_selector(i) );
			
				$(nav).prependTo(slider);
			}
			
			last_time = new Date().valueOf();
			setTimeout( change_slide, 10000 );
			setTimeout( update_slide_size, 50 );
		}
		
		
		function update_slide_size()
		{
			var width = $(slider).width();
			
			if( width != options.width )
			{
				options.width = width;
		
				$(slider).children('.wrapper')
					.css('width', ((num_slides+1) * (options.width+2))+'px');
				
				$(slider).find('.slide')
					.css('width', options.width);
			}
			
			setTimeout( update_slide_size, 50 );
		}
		
		
		function create_selector( index )
		{
			if( index == 0 )
			{
				var image = $('<img />')
					.attr( 'src', options.url+'/btn-circle-on.png' )
					.addClass( 'active' );
			}
			else
			{
				var image = $('<img />')
					.attr( 'src', options.url+'/btn-circle-off.png' )
			}
			
			$(image)
				.css( 'cursor', 'pointer' )
				.css( 'margin', '3px' )
				.attr( 'slide', index )
				.click( function() {
					var index = parseInt($(this).attr('slide'));
					change_slide(index);
				});
				
			return image;
		}

		//
		// Scroll to reveal the next slide.
		//
		function change_slide(index)
		{	
			if( index == undefined )
			{
				if( (new Date().valueOf() - 10000) < last_time )
					return;
	
				index = active_index + 1;
				if( index >= num_slides ) index = 0;
			}
			else
			{
				if( active_index == index ) return;		
			}

			last_time = new Date().valueOf();

			var wrapper = $(slider).children('.wrapper');
			var slides = $(slider).find('.slide');
			
			$(wrapper).append( $(slides[0]).clone() );
			$(slides[0]).wrap('<div delete="me" />');

			var offset = index - active_index;
			if( offset < 0 ) offset = num_slides + offset;
			offset--;
			
			var slides = $(slider)
				.find('.slide:gt(0):lt('+offset+')')
				.remove()
				.appendTo(wrapper);

			new_index = index;

			$(wrapper)
				.animate(
					{ "left": -(options.width) }, 
					1500, 
					function()
					{
						var delete_me = $(slider).find('div[delete="me"]');
						var old_slide = $(delete_me).find('.slide');
						
						$(delete_me).remove();
						$(wrapper).css("left", 0);
						
						active_index = new_index;
						
						if( options.url != null )
						{
							var circles = $(slider)
								.children('.slider-navigation')
								.children('img');
							$(circles)
								.attr('src', options.url+'/btn-circle-off.png');
							$(circles[active_index])
								.attr('src', options.url+'/btn-circle-on.png');
						}
					}
				);

			setTimeout( change_slide, 10000 );
		}
		
		//
		// Initialize the plugin.
		//
		return this.each( function() { setup_slider(); } );
	}
	
})( jQuery )
