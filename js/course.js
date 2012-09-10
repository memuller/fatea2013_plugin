jQuery(document).ready( function($) {
	$('#scroll').carouFredSel({
		direction: 'left',
		align: 'center',
		prev: '#previous',
		next: '#next',
		auto: {play: false},
		items: {start: true}

	});

	$('#previous').hover( function(event){
		$(this).addClass('previous-hover');
	},
	function(event){
		$(this).removeClass('previous-hover');
	});

	$('#next').hover( function(event){
		$(this).addClass('next-hover');
	},
	function(event){
		$(this).removeClass('next-hover');
	});

});