<?php

/**
 * @package AndTheWinnerIs
 */
if (!class_exists('AndTheWinnerIs_ContestWinner')) {

class AndTheWinnerIs_ContestWinner {

	private $post_id = 0;
	private $comment_id = 0;
	private $confirmed = false;
	private $rejected = false;

	public function __construct($post_id, $comment_id) {
		if (null === get_post($post_id)) {
			throw new Exception(ATWI_ERROR_INVALID_POST_ID);
		}

		if (null === get_comment($comment_id)) {
			throw new Exception(ATWI_ERROR_INVALID_COMMENT_ID);
		}

		$this->post_id = $post_id;
		$this->comment_id = $comment_id;

		$confirmed_ids = get_post_meta($this->post_id, ATWI_POST_META_WINNERS_CONFIRMED);
		$unconfirmed_ids = get_post_meta($this->post_id, ATWI_POST_META_WINNERS_UNCONFIRMED);
		$rejected_ids = get_post_meta($this->post_id, ATWI_POST_META_WINNERS_REJECTED);

		$this->confirmed = in_array($this->comment_id, $confirmed_ids);
		$this->rejected = in_array($this->comment_id, $rejected_ids);

		$unconfirmed = in_array($this->comment_id, $unconfirmed_ids);

		if ((false === $this->rejected) && (false === $this->confirmed) && (false === $unconfirmed)) {
			throw new Exception(__('The given comment is not related to this post.', ATWI_DOMAIN));
		}

		if ((true === $this->rejected) && (true === $this->confirmed)) {
			throw new Exception(__('Winner is both "rejected" and "confirmed".', ATWI_DOMAIN));
		}
	}

	/**
   * Marks a given winner as confirmed
   *
   * @todo Verify comment is connected to post
	 */
	public function Confirm() {
		if ($this->confirmed) {
			throw new Exception(ATWI_ERROR_WINNER_ALREADY_CONFIRMED);
		}

		if ($this->rejected) {
			throw new Exception(ATWI_ERROR_WINNER_ALREADY_REJECTED);
		}

		if (comments_open($this->post_id)) {
			throw new Exception(ATWI_ERROR_COMMENTS_ARE_OPEN);
		}

		delete_post_meta($this->post_id, ATWI_POST_META_WINNERS_UNCONFIRMED, $this->comment_id);
		add_post_meta($this->post_id, ATWI_POST_META_WINNERS_CONFIRMED, $this->comment_id);
		
		$this->confirmed = true;
	}

  /**
   * Get the post ID for the contest this winner is associated to
   *
   * @return <int> post ID
   */
	public function GetPostID() {
		return $this->post_id;
	}

  /**
   * Get the comment ID for this winner
   *
   * @return <int> comment ID
   */
	public function GetCommentID() {
		return $this->comment_id;
	}

  /**
   * Gets the comment for this winner
   *
   * @return <object> comment data table row
   */
	public function GetComment() {
    return (get_comment($this->comment_id));
	}

  /**
   * Gets an XHTML formatted comment
   *
   * @return <string>
   */
	public function GetCommentBlock() {
		return AndTheWinnerIs::FormatWinner($this->GetComment(), $this->confirmed);
	}

  /**
   * Whether or not this winner has been confirmed
   *
   * @return <boolean>
   */
	public function IsConfirmed() {
		return $this->confirmed;
	}

  /**
   * Whether or not this winner has been rejected
   *
   * @return <boolean>
   */
	public function IsRejected() {
		return $this->rejected;
	}

  /**
   * Marks this winner as rejected
   */
	public function Reject() {
		if ($this->confirmed) {
			throw new Exception(ATWI_ERROR_WINNER_ALREADY_CONFIRMED);
		}

		if ($this->rejected) {
			throw new Exception(ATWI_ERROR_WINNER_ALREADY_REJECTED);
		}

		if (comments_open($this->post_id)) {
			throw new Exception(ATWI_ERROR_COMMENTS_ARE_OPEN);
		}

		delete_post_meta($this->post_id, ATWI_POST_META_WINNERS_UNCONFIRMED, $this->comment_id);
		add_post_meta($this->post_id, ATWI_POST_META_WINNERS_REJECTED, $this->comment_id);
		
		$this->rejected = true;
	}
	
} // class

} /* class_exists */

?>
