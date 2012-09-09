jQuery(document).ready( function($) {
	$('#scroll').carouFredSel({
		direction: 'left',
		align: 'center',
		prev: '#previous',
		next: '#next',
		auto: {play: false},
		items: {start: true}

	});
});