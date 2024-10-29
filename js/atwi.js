jQuery(document).ready(function($){

	/*
	 * Makes an AJAX call to set the comment status of a post
	 */
  function ajaxSetComments(postID, status) {

    var close_comments = 'tr#atwi-post-'+postID+' a.atwi-close-comments';
    var open_comments = 'tr#atwi-post-'+postID+' a.atwi-open-comments';
    var get_winner = 'tr#atwi-post-'+postID+' a.atwi-get-winner';
    var thinking = 'tr#atwi-post-'+postID+' div.atwi-thinking';

		// hide functionality to prevent further user interaction
    $(close_comments).hide();
    $(open_comments).hide();
    $(get_winner).hide();

		// we're busy
    $(thinking).slideDown();

    // make the AJAX call
    $.post(ajaxurl,
      {
        action: 'atwi_set_comments',
        'cookie': encodeURIComponent(document.cookie),
        'post_id': postID,
        'comment_status': status
      },
      function(data) {
        $(thinking).slideUp('slow', function() {
					if (data.error) {
						alert(data.message);
					}

					// show the proper functionality
					if ('closed' == data.comment_state) {
						$(close_comments).hide();
						$(open_comments).show();
						$(get_winner).show();
					} else if ('open' == data.comment_state) {
						$(close_comments).show();
						$(open_comments).hide();
						$(get_winner).hide();
					}
				});
      },
      'json'
    );
  } // ajaxSetComments

	/*
	 * Makes the AJAX call to retrieve a possible winner for a given post
	 */
  function ajaxGetAWinner(postID) {

    var open_comments = 'tr#atwi-post-'+postID+' a.atwi-open-comments';
    var get_winner = 'tr#atwi-post-'+postID+' a.atwi-get-winner';
    var thinking = 'tr#atwi-post-'+postID+' div.atwi-thinking';
		var number_of_winners = 'span#atwi-winner-'+postID+'-number-of-winners';
		var number_of_possible_winners = 'td#atwi-winner-'+postID+'-number-of-possible-winners';
    var winner = 'div#atwi-winner-'+postID+'-'+String(parseInt($(number_of_winners).html()) + 1);
    var winner_number = winner+' span.atwi-winner-number';

		// hide functionality to prevent further user interaction
    $(open_comments).hide();
    $(get_winner).hide();

		// we're busy
    $(thinking).slideDown();

    // make the AJAX call
    $.post(ajaxurl,
      {
        action: 'atwi_get_winner',
        'cookie': encodeURIComponent(document.cookie),
        'post_id': postID
      },
      function(data) {
        $(thinking).slideUp('slow', function() {
					if (data.error) {
						// restore functionality
						if (0 == parseInt($(number_of_winners).html())) {
							$(open_comments).show();
						}
						$(get_winner).show();

						// display the error
						alert(data.message);
					} else {
						// increment the current number of winners
						$(number_of_winners).html(parseInt($(number_of_winners).html()) + 1);

            if (parseInt($(number_of_winners).html()) < parseInt($(number_of_possible_winners).html())) {
              $(get_winner).show();
            }

						// display the comment
            $(winner_number).after(data.message);
						$(winner).slideDown();

            bindConfirmRejectEvents();
					}
				});
      },
      'json'
    );
  } // ajaxGetWinner

	/*
	 * Makes the AJAX call to confirm the winner for a contest.
	 */
  function ajaxConfirmWinner(postID, commentID, winnerNumber) {
    
    var confirm_winner = 'div#atwi-winner-'+postID+'-'+winnerNumber+' a.atwi-confirm-winner';
    var reject_winner = 'div#atwi-winner-'+postID+'-'+winnerNumber+' a.atwi-reject-winner';
    var header = 'div#atwi-winner-'+postID+'-'+winnerNumber+' h3';
    var thinking = 'tr#atwi-post-'+postID+' div.atwi-thinking';

		// hide functionality to prevent further user interaction
    $(confirm_winner).hide();
    $(reject_winner).hide();

		// we're busy
    $(thinking).slideDown();

    // make the AJAX call
    $.post(ajaxurl,
      {
        action: 'atwi_confirm_winner',
        'cookie': encodeURIComponent(document.cookie),
        'post_id': postID,
        'comment_id': commentID
      },
      function(data) {
        $(thinking).slideUp('slow', function() {
					if (data.error) {
						// restore functionality
						$(confirm_winner).show();
						$(reject_winner).show();

						// show the error
						alert(data.message);
					} else {
						// mark the winner as confirmed
						$(header).after('<span>(confirmed)</span>');
						$(header).parents('div.atwi-winner').addClass('confirmed');
					}
				});
      },
      'json'
    );
  } // ajaxConfirmWinner

	/*
	 * Makes the AJAX call to reject the winner for a contest.
	 */
  function ajaxRejectWinner(postID, commentID, winnerNumber) {

		var number_of_winners = 'span#atwi-winner-'+postID+'-number-of-winners';
		var number_of_possible_winners = 'td#atwi-winner-'+postID+'-number-of-possible-winners';
    var confirm_winner = 'div#atwi-winner-'+postID+'-'+winnerNumber+' a.atwi-confirm-winner';
    var reject_winner = 'div#atwi-winner-'+postID+'-'+winnerNumber+' a.atwi-reject-winner';
    var open_comments = 'tr#atwi-post-'+postID+' a.atwi-open-comments';
    var get_winner = 'tr#atwi-post-'+postID+' a.atwi-get-winner';
    var winner = 'div#atwi-winner-'+postID+'-'+winnerNumber;
    var winner_comment = 'div#atwi-winner-'+postID+'-'+winnerNumber+' div.atwi-winner';
    var thinking = 'tr#atwi-post-'+postID+' div.atwi-thinking';

		// hide functionality to prevent further user interaction
    $(confirm_winner).hide();
    $(reject_winner).hide();

		// we're busy
    $(thinking).slideDown();

    // make the AJAX call
    $.post(ajaxurl,
      {
        action: 'atwi_reject_winner',
        'cookie': encodeURIComponent(document.cookie),
        'post_id': postID,
				'comment_id': commentID
      },
      function(data) {
        $(thinking).slideUp('slow', function() {
					if (data.error) {
						// restore functionality
						$(confirm_winner).show();
						$(reject_winner).show();

						// show the error
						alert(data.message);
					} else {
						// decrement the current number of winners
						$(number_of_winners).html(parseInt($(number_of_winners).html()) - 1);

						// remove the winning comment
						$(winner).slideUp('slow', function() {
							$(winner_comment).remove();

              $(winner).show();
              
              // move all remaining winners up
              for (var i = parseInt(winnerNumber) + 1; i <= parseInt($(number_of_possible_winners).html()); i++) {
                $('div#atwi-winner-'+postID+'-'+i+' div.atwi-winner').remove().insertAfter('div#atwi-winner-'+postID+'-'+String(i-1)+' span.atwi-winner-number');
              }

              // show new functionality
              if (0 == parseInt($(number_of_winners).html())) {
                $(open_comments).show();
              }
              $(get_winner).show();

              bindConfirmRejectEvents();
						});

					}
				});
      },
      'json'
    );
  } // ajaxRejectWinner

	/*
	 * Makes the AJAX call to uninstall all data for And The Winner Is...
	 */
	function ajaxUninstall() {

		// Make sure the user wants to uninstall
		if (!confirm($('span#atwi-confirm-text').html())) {
			return;
		}

		var thinking = 'div#atwi-thinking';
		var complete = 'div#atwi-uninstall-complete';
		var button = 'a.atwi-uninstall';

		// hide the uninstall button so they don't click it twice
		$(button).hide();

		// we're busy
		$(thinking).show();

		// make the AJAX call
    $.post(ajaxurl,
      {
        action: 'atwi_uninstall',
        'cookie': encodeURIComponent(document.cookie)
      },
      function(data) {
				// we're not busy anymore
        $(thinking).hide();

        if (data.error) {
					// restore functionality
          $(button).show();

					// show the error
          alert(data.message);
        } else {
					// Let the user know we finished
					alert($(complete).html());
        }
      },
      'json'
    );
	} // ajaxUninstall

	/**
	 * Event handlers...
	 */
  function bindConfirmRejectEvents() {

    $("a.atwi-confirm-winner").unbind('click').bind('click', function(event) {
      event.preventDefault();

      var id = $(this).parents('tr.atwi-post-row').find('td.atwi-post-id').html();
      var comment_id = $(this).parents('div.atwi-winner').find('span.atwi-winner-comment-id').html();
      var winner_number = $(this).parents('div.atwi-winner-container').find('span.atwi-winner-number').html();
      ajaxConfirmWinner(id, comment_id, winner_number);
    });

    $("a.atwi-reject-winner").unbind('click').bind('click', function(event) {
      event.preventDefault();

      var id = $(this).parents('tr.atwi-post-row').find('td.atwi-post-id').html();
      var comment_id = $(this).parents('div.atwi-winner').find('span.atwi-winner-comment-id').html();
      var winner_number = $(this).parents('div.atwi-winner-container').find('span.atwi-winner-number').html();
      ajaxRejectWinner(id, comment_id, winner_number);
    });
    
  }

  bindConfirmRejectEvents();

	$("a.atwi-uninstall").click(function(event) {
		event.preventDefault();

		ajaxUninstall();
	});

  $("a.atwi-get-winner").click(function(event) {
    event.preventDefault();

    var id = $(this).parents('tr.atwi-post-row').find('td.atwi-post-id').html();
    ajaxGetAWinner(id);
  });

  $("a.atwi-close-comments").click(function(event) {
    event.preventDefault();

    var id = $(this).parents('tr.atwi-post-row').find('td.atwi-post-id').html();
    ajaxSetComments(id, 'closed');
  });

  $("a.atwi-open-comments").click(function(event) {
    event.preventDefault();

    var id = $(this).parents('tr.atwi-post-row').find('td.atwi-post-id').html();
    ajaxSetComments(id, 'open');
  });

});
