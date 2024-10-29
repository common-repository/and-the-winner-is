<?php if (!class_exists('AndTheWinnerIs')) {

/**
 * Main plugin class containing basic functionality
 *
 * @package AndTheWinnerIs
 */

class AndTheWinnerIs {

  private static $installed_version;
  private static $AJAX;

  public function __construct($file) {

    register_activation_hook( $file, array(&$this, 'Activate') );
    register_deactivation_hook( $file, array(&$this, 'Deactivate') );

    $this->AJAX = new AndTheWinnerIs_AJAX();

    add_action('admin_menu', array(&$this, 'AdminMenus'));
    add_action('admin_menu', array(&$this, 'AddMetaBox'));
    add_action('admin_init', array(&$this, 'AdminInit'));
    add_action('admin_enqueue_scripts', array(&$this, 'AdminEnqueueScripts'));
    add_action('wp_insert_post', array(&$this, 'InsertPostMeta'), 10, 2);

    add_action('wp_ajax_atwi_set_comments', array(&$this->AJAX, 'SetComments'));
    add_action('wp_ajax_atwi_get_winner', array(&$this->AJAX, 'GetRandomWinner'));
    add_action('wp_ajax_atwi_confirm_winner', array(&$this->AJAX, 'ConfirmWinner'));
    add_action('wp_ajax_atwi_reject_winner', array(&$this->AJAX, 'RejectWinner'));
    add_action('wp_ajax_atwi_uninstall', array(&$this->AJAX, 'Uninstall'));

    load_plugin_textdomain( ATWI_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ).'/translations' );
  }

  /**
   * Actives the plugin.
   */
  public function Activate() {
    if (!current_user_can('activate_plugins'))
      return;

    if (AndTheWinnerIs::GetInstalledVersion() < 1.0) {
      $admin = get_role('administrator');
      $admin->add_cap(ATWI_CAPABILITY);
      update_option(ATWI_VERSION_TXT, ATWI_VERSION);
    } // version < 1.0
  }

  /**
   * Deactivates the plugin
   */
  public function Deactivate() {
    if (!current_user_can('activate_plugins'))
      return;

    $roles = new WP_Roles();
    foreach ($roles->role_objects as $role) {
      $role->remove_cap(ATWI_CAPABILITY);
    }
    delete_option(ATWI_VERSION_TXT);
  }

  /**
   * Uninstalls all post meta data for the plugin. Requires an Admistrator.
   *
   * @global wpdb $wpdb
   * @return <boolean> true if the uninstall succeeded
   */
  public static function Uninstall() {
    if (!current_user_can('administrator'))
      return false;

    global $wpdb;
    $sql = "DELETE FROM `".$wpdb->postmeta."` WHERE `meta_key` IN ('".
						ATWI_POST_META_WINNERS_UNCONFIRMED."','".ATWI_POST_META_WINNERS_CONFIRMED."','".ATWI_POST_META_WINNERS_REJECTED."','".
						ATWI_POST_META_IS_CONTEST."','".ATWI_POST_META_NUMBER_OF_WINNERS."')";
    $result = $wpdb->query($wpdb->prepare($sql));
    return (false !== $result);
  }

  /**
   * Gets the currently installed version of the plugin
   *
   * @return <float>
   */
  public static function GetInstalledVersion() {
    if (empty(self::$installed_version)) {
      $v = get_option(ATWI_VERSION_TXT);
      self::$installed_version = (empty($v)) ? 0 : ((float)$v);
    }
    return (self::$installed_version);
  }

  /**
   * Adds a meta box to the side of the edit post screen.
   */
  public function AddMetaBox() {
    add_meta_box(ATWI_DOMAIN, ATWI, array(&$this, 'ContestMetaBox'), 'post', 'side');
  }

  /**
   * Outputs a small form for a post meta box
   *
   * @global <type> $post
   */
  public function ContestMetaBox() {
    global $post;

    $is_contest = get_post_meta($post->ID, ATWI_POST_META_IS_CONTEST, true) ? true : false;

    $number_of_winners = get_post_meta($post->ID, ATWI_POST_META_NUMBER_OF_WINNERS, true);
    $number_of_winners = (empty($number_of_winners)) ? 1 : intval($number_of_winners);

    echo '<input type="checkbox" id="'.ATWI_FORM_POST_IS_CONTEST.'" name="'.ATWI_FORM_POST_IS_CONTEST.'" '.checked($is_contest, true, false).'/>&nbsp;';
    echo '<label for="'.ATWI_FORM_POST_IS_CONTEST.'">'.__('Is this post a contest?', ATWI_DOMAIN).'</label><p/>';
    echo '<label for="'.ATWI_FORM_POST_NUMBER_OF_WINNERS.'">'.__('Number of winners possible:', ATWI_DOMAIN).'</label>&nbsp;';
    echo '<input size="2" maxlength="2" type="text" id="'.ATWI_FORM_POST_NUMBER_OF_WINNERS.'" name="'.ATWI_FORM_POST_NUMBER_OF_WINNERS.'" value="'.$number_of_winners.'" />';
  }

  /**
   * Inserts posts meta for this plugin
   *
   * @param <int> $post_id
   * @uses $_POST[ATWI_FORM_POST_IS_CONTEST]
   * @uses $_POST[ATWI_FORM_POST_NUMBER_OF_WINNERS]
   */
  public function InsertPostMeta($id) {
    global $id;

    if ( !isset($id) )
        $id = (int)$_REQUEST['post_ID'];
    if ( is_preview() && !isset($id) )
        $id = (int)$_GET['preview_id'];

    if ( !current_user_can('edit_post') )
        return;

    if (isset($_POST[ATWI_FORM_POST_IS_CONTEST])) {
      update_post_meta($id, ATWI_POST_META_IS_CONTEST, true);
      $number = intval($_POST[ATWI_FORM_POST_NUMBER_OF_WINNERS]);
      $number = ($number < 1) ? 1 : $number;
      update_post_meta($id, ATWI_POST_META_NUMBER_OF_WINNERS, $number);
    } else {
      delete_post_meta($id, ATWI_POST_META_IS_CONTEST);
      delete_post_meta($id, ATWI_POST_META_NUMBER_OF_WINNERS);
    }

  }

  /**
   * Builds a displayable block of information for a comment
   *
   * @param <int> $comment A valid comment object
   * @param <bool> $confirmed Whether or not this comment is the confirmed winner.
   * @return <string> Comment block
   */
  public static function FormatWinner($comment, $confirmed) {

    $result = '';

    if (!empty($comment->comment_ID)) {

      $result .= '<div class="atwi-winner clearfix '.($confirmed ? 'confirmed' : '').'">';
			$result .= '<span class="atwi-winner-comment-id hidden">'.$comment->comment_ID.'</span>';
      $result .= '<div class="alignleft" style="width: 150px;">';
      $result .= '<h3>Your Winner</h3>';
      if ($confirmed) {
        $result .= '<span>('.__('confirmed', ATWI_DOMAIN).')</span>';
      }
      $result .= '<br/>';
      $result .= '<a href="#" class="button-secondary atwi-confirm-winner '.(($confirmed) ? 'hidden' : '').'" title="'.__('Confirm Winner', ATWI_DOMAIN).'">'.__('Confirm&nbsp;Winner', ATWI_DOMAIN).'</a><br/>';
      $result .= '<br/>';
      $result .= '<a href="#" class="button-secondary atwi-reject-winner '.(($confirmed) ? 'hidden' : '').'" title="'.__('Reject Winner', ATWI_DOMAIN).'">&nbsp;'.__('Reject Winner', ATWI_DOMAIN).'&nbsp;</a>';
      $result .= '</div>';
      $result .= '<div class="alignleft" style="width: 500px;">';
      $result .= '<strong>'.__('Author:', ATWI_DOMAIN).'</strong> '.$comment->comment_author.' ';
      $result .= '(<a href="mailto:'.$comment->comment_author_email.'?subject='.get_the_title($comment->comment_post_ID).' :: '.ATWI.' '.__('You!', ATWI_DOMAIN).'" title="'.$comment->comment_author.'">'.$comment->comment_author_email.'</a>)';
      if (!empty($comment->comment_author_url)) {
        $result .= '<br/><strong>'.__('Website:', ATWI_DOMAIN).'</strong> <a href="'.$comment->comment_author_url.'" title="'.$comment->comment_author.' website">'.$comment->comment_author_url.'</a>';
      }
      $result .= '<br style="margin-bottom: 10px;" />';
      $result .= '<strong>'.__('Comment', ATWI_DOMAIN).': <a href="'.get_permalink($comment->comment_post_ID).'#comment-'.$comment->comment_ID.'" title="'.__('permalink', ATWI_DOMAIN).'">'.__('permalink', ATWI_DOMAIN).'</a></strong><br/><p class="comment">'.$comment->comment_content.'</p>';
      $result .= '</div></div>';
    }

    return ($result);
  }

  /**
   * Adds the admin area menu options.
   */
  public function AdminMenus() {
    add_menu_page(ATWI, ATWI, ATWI_CAPABILITY, ATWI_DOMAIN, array(&$this, 'ContestsPage'));
    add_submenu_page(ATWI_DOMAIN, ATWI, 'Contests', ATWI_CAPABILITY, ATWI_DOMAIN, array(&$this, 'ContestsPage'));
    add_submenu_page(ATWI_DOMAIN, ATWI, 'Uninstall', 'administrator', ATWI_DOMAIN . '-uninstall', array(&$this, 'UninstallPage'));
  }

  public function AdminInit() {
    
  }
  
  /**
   * Queues up various scripts and styles for the admin area.
   */
  public function AdminEnqueueScripts( $hook ) {

    switch ( $hook ) {
      case "toplevel_page_and-the-winner-is":
      case "and-the-winner-is_page_and-the-winner-is-uninstall":
        wp_enqueue_script('jquery');
        wp_enqueue_script(ATWI_DOMAIN, ATWI_URL . '/js/atwi.js', array('jquery'));
        wp_enqueue_style(ATWI_DOMAIN, ATWI_URL . '/css/atwi.css');
        break;
    }
  }

  /**
   * Displays the 'Your Contests' page.
   */
  public function ContestsPage() {
    include('admin-pages/contests.php');
  }

  /**
   * Displays the 'Uninstall' page.
   */
  public function UninstallPage() {
    include('admin-pages/uninstall.php');
  }
    
} // class

} /* class_exists */
