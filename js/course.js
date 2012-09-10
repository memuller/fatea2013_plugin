jQuery(document).ready( function($) {
	$('#scroll').carouFredSel({
		direction: 'left',
		start: true,
		align: 'center',
		prev: '#previous',
		next: '#next',
		auto: {play: false},
		items: {start: true},
		onCreate: function(items, sizes){
			$('#name h2').html($(items).attr('title'));
			
			$previous = $(items).find('.navigation a.previous');
			$next = $(items).find('.navigation a.next');

			$('#previous p').html($previous.html());
			$('#next p').html($next.html());

			$('#previous').attr('href', $previous.attr('href'));
			$('#next').attr('href', $next.attr('href'));
		},
		scroll: {
			onAfter: function(oldItems, newItems, newSizes){
				$('#name h2').html($(newItems).attr('title'));
				
				$previous = $(newItems).find('.navigation a.previous');
				$next = $(newItems).find('.navigation a.next');

				$('#previous p').html($previous.html());
				$('#next p').html($next.html());

				$('#previous').attr('href', $previous.attr('href'));
				$('#next').attr('href', $next.attr('href'));

				document.location.hash = $(newItems).attr('id');
				
			}
		}

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