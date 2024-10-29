<?php

/**
 * @package AndTheWinnerIs
 */
if (!class_exists('AndTheWinnerIs_Contest')) {

class AndTheWinnerIs_Contest {

  private $post_id = 0;
  private $is_contest = false;
  private $number_of_winners_possible;
	private $number_of_entries;

  public function __construct($post_id) {

		if (null === get_post($post_id)) {
			throw new Exception(ATWI_ERROR_INVALID_POST_ID);
		}

    $this->post_id = $post_id;
    $possible_winners = intval(get_post_meta($this->post_id, ATWI_POST_META_NUMBER_OF_WINNERS, true));
    $this->number_of_winners_possible = ($possible_winners < 1) ? 1 : $possible_winners;

    $this->is_contest = get_post_meta($this->post_id, ATWI_POST_META_IS_CONTEST, true) ? true : false;

		$comments_by_type = &separate_comments(get_comments('post_id=' . $this->post_id));
		$this->number_of_entries = count($comments_by_type['comment']);
  }

	/**
	 * Selects a random comment from this contest post as a possible winner.
   *
	 * @global <WPDB> $wpdb
   * @return <AndTheWinnerIs_ContestWinner> possible winner
	 */
	public function FindAWinner() {
		if (!$this->is_contest) {
			throw new Exception(ATWI_ERROR_POST_NOT_A_CONTEST);
		}

		if (comments_open($this->post_id)) {
			throw new Exception(ATWI_ERROR_COMMENTS_ARE_OPEN);
		}

		global $wpdb;

		// create a filter list for all rejected and un/confirmed winners
		$filter_list = '';
		$filter = array_merge($this->GetRejects(), $this->GetAllWinners());
		foreach ($filter as $filtered) {
			if (!empty($filter_list)) {
				$filter_list .= ',';
			}
			$filter_list .= $filtered->GetCommentID();
		}

		$q = "SELECT comment_ID FROM `".$wpdb->comments."` WHERE `comment_type` <> 'pingback' AND `comment_post_ID` = ".$this->post_id." AND `comment_approved` = 1 ";
		if (!empty($filter_list)) {
			$q .= "AND `comment_ID` NOT IN (".$filter_list.") ";
		}
		$q .= "ORDER BY RAND() LIMIT 1;";
		$result = $wpdb->get_var($q);

		if (null === $result) {
			throw new Exception(__('Unable to determine winner: perhaps you rejected or confirmed every comment.', ATWI_DOMAIN));
		}

		add_post_meta($this->post_id, ATWI_POST_META_WINNERS_UNCONFIRMED, $result);
		$winner = new AndTheWinnerIs_ContestWinner($this->post_id, $result);

		return ($winner);
	}

  /**
   * Get the number of entries (comments) for this contest post
   *
   * @return <int>
   */
	public function GetNumberOfEntries() {
		if (!$this->is_contest) {
			throw new Exception(ATWI_ERROR_POST_NOT_A_CONTEST);
		}

		return ($this->number_of_entries);
	}

  /**
   * Get the maximum number of winners for this contest post
   * @return <int>
   */
  public function GetNumberOfWinnersPossible() {
		if (!$this->is_contest) {
			throw new Exception(ATWI_ERROR_POST_NOT_A_CONTEST);
		}

    return ($this->number_of_winners_possible);
  }

  /**
   * Get all rejected winners for this contest post
   *
   * @return <array> of AndTheWinnerIs_ContestWinner objects
   */
	public function GetRejects() {
		if (!$this->is_contest) {
			throw new Exception(ATWI_ERROR_POST_NOT_A_CONTEST);
		}

		$rejects = array();

		$comment_ids = get_post_meta($this->post_id, ATWI_POST_META_WINNERS_REJECTED);

		foreach($comment_ids as $comment_id) {
			$rejects[] = new AndTheWinnerIs_ContestWinner($this->post_id, $comment_id);
		}

		return ($rejects);
	}

  /**
   * Get all confirmed and unconfirmed winners for this contest post
   *
   * @return <array> of AndTheWinnerIs_ContestWinner objects
   */
  public function GetAllWinners() {
		if (!$this->is_contest) {
			throw new Exception(ATWI_ERROR_POST_NOT_A_CONTEST);
		}

		return (array_merge($this->GetUnconfirmedWinners(), $this->GetConfirmedWinners()));
  }

  /**
   * Get all confirmed winners for this contest post
   *
   * @return <array> of AndTheWinnerIs_ContestWinner objects
   */
	public function GetConfirmedWinners() {
		if (!$this->is_contest) {
			throw new Exception(ATWI_ERROR_POST_NOT_A_CONTEST);
		}

		$winners = array();

		$comment_ids = get_post_meta($this->post_id, ATWI_POST_META_WINNERS_CONFIRMED);

		foreach ($comment_ids as $comment_id) {
			$winners[] = new AndTheWinnerIs_ContestWinner($this->post_id, $comment_id);
		}

		return ($winners);
	}

  /**
   * Get all unconfirmed winners for this contest post
   *
   * @return <array> of AndTheWinnerIs_ContestWinner objects
   */
	public function GetUnconfirmedWinners() {
		if (!$this->is_contest) {
			throw new Exception(ATWI_ERROR_POST_NOT_A_CONTEST);
		}

		$winners = array();

		$comment_ids = get_post_meta($this->post_id, ATWI_POST_META_WINNERS_UNCONFIRMED);

		foreach ($comment_ids as $comment_id) {
			$winners[] = new AndTheWinnerIs_ContestWinner($this->post_id, $comment_id);
		}

		return ($winners);
	}

  /**
   * Whether or not this post is a contest
   * 
   * @return <boolean>
   */
  public function IsContest() {
    return ($this->is_contest);
  }

} // class

} /* class_exists */

?>
