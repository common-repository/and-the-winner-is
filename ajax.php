<?php

/**
 * @package AndTheWinnerIs
 */
if (!class_exists('AndTheWinnerIs_AJAX')) {

class AndTheWinnerIs_AJAX {

  /**
   * Encodes a PHP array for JSON output and echo's it.
   *
   * @param <type> $data The data to be JSON encoded and echo'd
   */
  private function OutputJSONData($data) {
    header('Cache-Control: no-cache, must-revalidate');
    header('Content-type: application/json');
    echo json_encode($data);
	}

  /**
   * Selects a random comment from a post as a possible winner and
   * outputs a JSON encoded result with the following properties
   *
   * <bool> error - Whether or not an error occurred
   * <string> message - The error or a display block containing the winning comment information
   * <int> ID - The winning comment ID, if no error
   *
   * @uses $_POST['post_id']
   * @global <wpdb> $wpdb
   */
  public function GetRandomWinner() {

    $data = array();
    $data['error'] = true;
    $data['message'] = ATWI_ERROR_PERMISSION_DENIED;

    if (current_user_can(ATWI_CAPABILITY)) {
			
      $post_id = (is_numeric($_POST['post_id'])) ? intval($_POST['post_id']) : 0;

			try {
				$contest = new AndTheWinnerIs_Contest($post_id);
				$winner = $contest->FindAWinner();
				$data['error'] = false;
				$data['ID'] = $winner->GetCommentID();
				$data['message'] = $winner->GetCommentBlock();
			} catch (Exception $e) {
				$data['message'] = $e->getMessage();
			}

    } // if (current_user_can('edit_posts'))

		$this->OutputJSONData($data);
    exit;
  }

  /**
   * Confirms the given comment as the "winner" for the given post and
   * outputs a JSON encoded object with the following properties:
   *
   * <bool> error - Whether or not an error occurred
   * <string> message - The error message if there was an error
   *
   * @uses $_POST['post_id']
   * @uses $_POST['comment_id']
   */
  public function ConfirmWinner() {

    $data = array();
    $data['error'] = true;
    $data['message'] = ATWI_ERROR_PERMISSION_DENIED;

    if (current_user_can(ATWI_CAPABILITY)) {
			
      $post_id = (is_numeric($_POST['post_id'])) ? intval($_POST['post_id']) : 0;
      $comment_id = (is_numeric($_POST['comment_id'])) ? intval($_POST['comment_id']) : 0;

			try {
				$winner = new AndTheWinnerIs_ContestWinner($post_id, $comment_id);
				$winner->Confirm();
				$data['error'] = false;
        $data['message'] = '';
			} catch (Exception $e) {
				$data['message'] = $e->getMessage();
			}
			
    } // if (current_user_can('edit_posts'))

		$this->OutputJSONData($data);
    exit;
  }

  /**
   * Rejects a comment as the winner for a post and outputs a JSON
   * encoded object with the following properties:
   *
   * <bool> error - Whether or not an error occurred
   * <string> message - The error message if there was an error
   *
   * @uses $_POST['post_id']
   * @uses $_POST['comment_id']
   */
  public function RejectWinner() {

    $data = array();
    $data['error'] = true;
    $data['message'] = ATWI_ERROR_PERMISSION_DENIED;

    if (current_user_can(ATWI_CAPABILITY)) {

      $post_id = (is_numeric($_POST['post_id'])) ? intval($_POST['post_id']) : 0;
      $comment_id = (is_numeric($_POST['comment_id'])) ? intval($_POST['comment_id']) : 0;

			try {
				$winner = new AndTheWinnerIs_ContestWinner($post_id, $comment_id);
				$winner->Reject();
				$data['error'] = false;
        $data['message'] = '';
			} catch (Exception $e) {
				$data['message'] = $e->getMessage();
			}

    } // if (current_user_can('edit_posts'))

		$this->OutputJSONData($data);
    exit;
  }

  /**
   * Sets the comment status for a post and outputs a JSON
   * encoded object with the following properties:
   *
   * <bool> error - Whether or not an error occurred
   * <string> message - The error message if there was an error
   *
   * @uses $_POST['post_id']
   * @uses $_POST['comment_status']
   */
  public function SetComments() {

    $data = array();
    $data['error'] = true;
    $data['message'] = ATWI_ERROR_PERMISSION_DENIED;

    if (current_user_can(ATWI_CAPABILITY)) {
      $id = (is_numeric($_POST['post_id'])) ? intval($_POST['post_id']) : 0;
      $cs = (isset($_POST['comment_status'])) ? $_POST['comment_status'] : '';

      $data['comments_state'] = '';

      if (0 == $id) {
        $data['message'] = ATWI_ERROR_INVALID_POST_ID;
      } else if (('closed' != $cs) && ('open' != $cs)) {
        $data['message'] = __('Invalid comment status. Status must be "open" or "closed".', ATWI_DOMAIN);
      } else {
        $p = get_post($id);
        if (!empty($p)) {
          $p->comment_status = $cs;
          if (0 == wp_update_post($p)) {
            $data['message'] = __('Comments could not be closed.', ATWI_DOMAIN);
          } else {
            $data['error'] = false;
            $data['message'] = '';
          }
        } else {
          $data['message'] = ATWI_ERROR_INVALID_POST_ID;
        }
        $data['comment_state'] = (comments_open($id)) ? 'open' : 'closed';
      }

    } // if (current_user_can('edit_posts'))

		$this->OutputJSONData($data);
    exit;
  }

  /**
   * Uninstalls the data for the plugin and outputs a JSON
   * encoded object with the following properties:
   *
   * <bool> error - Whether or not an error occurred
   * <string> message - The error message if there was an error
   *
   * @uses AndTheWinnerIs::Uninstall
   */
  public function Uninstall() {

    $data = array();
    $data['error'] = true;
    $data['message'] = ATWI_ERROR_PERMISSION_DENIED;

    if (current_user_can('administrator')) {
      if (AndTheWinnerIs::Uninstall()) {
        $data['error'] = false;
        $data['message'] = '';
      } else {
        $data['message'] = __('Uninstall failed.', ATWI_DOMAIN);
      }
    } // if (current_user_can('administrator'))

		$this->OutputJSONData($data);
    exit;
  }

} // class

} /* class_exists */

?>
