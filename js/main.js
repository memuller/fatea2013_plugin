jQuery(document).ready( function($) {
	$('#menu .menu-item-object-page a').colorbox({
		inline: true, speed: 200,
		href: function(){ 
			return "#"+$.colorbox.element().attr('href').split('/').slice(-2)[0] ;

		}		

	});

});