<?php

/**
 * @package AndTheWinnerIs
 */

global $wpdb;

$search = ( isset( $_GET[ "s" ] ) ) ? $_GET[ "s" ]: "";

$link = get_admin_url() . "admin.php?page=" . ATWI_DOMAIN;
$search_link = $link;

$query = $wpdb->prepare(
          "SELECT  p.`ID`, p.`post_title`, p.`post_content`, p.`post_date`,
          mcw.`meta_value` as possible_winners, p.`comment_count`
          FROM  `{$wpdb->posts}` p
          INNER JOIN  `{$wpdb->postmeta}` mic ON mic.`meta_key` = %s AND mic.`post_ID` = p.`ID`
          INNER JOIN `{$wpdb->postmeta}` mcw ON mcw.`meta_key` = %s AND mcw.`post_ID` = p.`ID`
          WHERE p.`comment_count` > 0 ", ATWI_POST_META_IS_CONTEST, ATWI_POST_META_NUMBER_OF_WINNERS );

if ( !empty( $search ) ) {
  $query = $wpdb->prepare( $query . "AND p.`post_title` LIKE %s ", "%$search%" );
  $search_link = $link . "&s=$search";
}

$query .= "ORDER BY p.`post_date` DESC;";

$posts = $wpdb->get_results( $query );

$posts_per_page = 3;
$total = count( $posts );
$total_pages = intval( ceil( $total / $posts_per_page ) );

$page = ( isset( $_GET[ "paged" ] ) ) ? intval( $_GET[ "paged" ] ) : 1;
$paged = ( isset( $_GET[ "paged" ] ) ) ? "&paged=" . $_GET[ "paged" ] : "";

$start = ( ( $page - 1 ) * $posts_per_page ) + 1;
$end = $start + $posts_per_page - 1;

if ( $end > $total ) { $end = $total; }

?>

<div class="wrap">

<h2>
  <?php _e('Your Contests', ATWI_DOMAIN); ?>
  <?php if ( !empty( $search ) ) : ?>
    <span class="subtitle">
      <?php _e( "Search results for", ATWI_DOMAIN ); ?>&nbsp;&ldquo;<?php echo htmlentities( $search ); ?>&rdquo;
      <a href="<?php echo $link; ?>" title="<?php _e( "Clear", ATWI_DOMAIN ); ?>" class="button"><?php _e( "Clear", ATWI_DOMAIN ); ?></a>
    </span>
  <?php endif; ?>
</h2>

<form action="<?php echo get_admin_url(); ?>" method="get">
  <input type="hidden" name="page" value="<?php echo ATWI_DOMAIN; ?>" />
  <p class="search-box">
    <label class="screen-reader-text" for="post-search-input"><?php _e( "Search Contests", ATWI_DOMAIN ); ?></label>
    <input type="text" id="post-search-input" name="s" value="<?php echo htmlentities( $search ); ?>" />
    <input type="submit" value="<?php _e( "Search Contests", ATWI_DOMAIN ); ?>" class="button">
  </p>
</form>

<div class="tablenav">

  <?php if ( $total > $posts_per_page ) : ?>

  <div class="tablenav-pages">
    <span class="displaying-num">
      <?php _e( "Displaying " . $start . "-" . $end . " of " . $total, ATWI_DOMAIN ); ?>
    </span>

      <?php

      if ( $total_pages > 1 ) {

        if ( 1 !== $page ) {
          $prev = $page - 1;
          // show the previous page link
          echo "<a class=\"prev page-numbers\" href=\"$search_link&paged=$prev\">&laquo;</a>";
        }

        // show up to 5 pages in the middle
        if ( $total_pages < 5 ) {
          $to_show = $total_pages;
          $start_page = 1;
        } else {

          $start_page = ( ( $page - 2 ) < 1) ? 1 : $page - 2;
          $to_show = 5;

          if ( ( $start_page + $to_show ) > $total_pages ) {
            $start_page = $total_pages - 4;
          }
        }

        if ( $start_page > 1 ) {
          echo "<a class=\"page-numbers\" href=\"$search_link&paged=1\">1</a>";
          if ( $start_page > 2 ) {
            echo "&hellip;";
          }
        }

        for ( $i = $start_page; $i < ( $start_page + $to_show ); $i++ ) {
          // show page links
          if ( $i === $page ) {
            echo "<span class=\"page-numbers current\">$i</span>";
          } else {
            echo "<a class=\"page-numbers\" href=\"$search_link&paged=$i\">$i</a>";
          }
        }

        if ( $total_pages >= ( $start_page + $to_show ) ) {
          if ( $total_pages > ( $start_page + $to_show ) ) {
            echo "&hellip;";
          }
          echo "<a class=\"page-numbers\" href=\"$search_link&paged={$total_pages}\">{$total_pages}</a>";
        }
        
        if ( $page !== $total_pages ) {
          // show the next page link
          $next = $page + 1;
          echo "<a class=\"next page-numbers\" href=\"$search_link&paged={$next}\">&raquo;</a>";
        }

      }

      ?>
  </div>

  <?php endif; ?>

  <?php include(ATWI_PATH.'/admin-pages/donate.php'); ?>

</div>

<span class="hidden" id="atwi-ajax-url"><?= get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php</span>

<table class="widefat" cellspacing="0" id="atwi-posts-with-comments">

	<thead>
		<tr>
			<th scope="col" class="manage-column hidden"><?php _e('ID', ATWI_DOMAIN); ?></th>
			<th scope="col" class="manage-column" style="width: 100px;"><?php _e('Contest Post', ATWI_DOMAIN); ?></th>
			<th scope="col" class="manage-column" style="width: 700px;"><?php _e('Excerpt', ATWI_DOMAIN); ?></th>
			<th scope="col" class="manage-column"><?php _e('Number of Comments', ATWI_DOMAIN); ?></th>
      <th scope="col" class="manage-column"><?php _e('Possible Winners', ATWI_DOMAIN); ?></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th scope="col" class="manage-column hidden"><?php _e('ID', ATWI_DOMAIN); ?></th>
			<th scope="col" class="manage-column" style="width: 100px;"><?php _e('Contest Post', ATWI_DOMAIN); ?></th>
			<th scope="col" class="manage-column" style="width: 700px;"><?php _e('Excerpt', ATWI_DOMAIN); ?></th>
			<th scope="col" class="manage-column"><?php _e('Number of Comments', ATWI_DOMAIN); ?></th>
      <th scope="col" class="manage-column"><?php _e('Possible Winners', ATWI_DOMAIN); ?></th>
		</tr>
	</tfoot>

	<tbody>

<?php

  $count = 0;
  
  if ( count( $posts ) > 0 ) : for ( $index = ( $start - 1 ); $index < $end; $index++ ) {

    $post = $posts[ $index ];
		$contest = new AndTheWinnerIs_Contest( $post->ID );

		if ( $contest->GetNumberOfEntries() > 0 ) :
      
			$count++;
			$winners = $contest->GetAllWinners();

      $date = new DateTime( $post->post_date );
      
?>

		<tr id="atwi-post-<?php echo $post->ID; ?>" class="atwi-post-row">
			<td class="atwi-post-id hidden"><?php echo $post->ID; ?></td>
			<td>
        <?php edit_post_link( $post->post_title,  null, null, $post->ID ); ?>
        <br/>
        <?php echo $date->format( "m/d/Y" ); ?>
      </td>
			<td>
				<p><?php echo substr( strip_tags( $post->post_content ), 0, 400 ); ?>&hellip;</p>

        <span id="atwi-winner-<?php echo $post->ID; ?>-number-of-winners" class="hidden"><?php echo count( $winners ); ?></span>

        <div class="atwi-thinking hidden">
          <?php echo ATWI_MESSAGE_PLEASE_WAIT ?>&hellip;
          <img src="<?php echo ATWI_URL; ?>/images/loading.gif" alt="<?php echo ATWI_MESSAGE_PLEASE_WAIT ?>&hellip;" />
        </div>

        <?php for ($i = 1; $i <= $contest->GetNumberOfWinnersPossible(); $i++) { ?>
        <div id="atwi-winner-<?php echo $post->ID; ?>-<?php echo $i; ?>" class="atwi-winner-container <?php	if ( $i > count( $winners ) ) { echo 'hidden'; } ?>">
          <span class="atwi-winner-number hidden"><?php echo $i; ?></span>
          <?php	if ( $i <= count( $winners ) ) { echo $winners[ $i - 1 ]->GetCommentBlock(); } ?>
        </div>
        <div class="clear"></div>
        <?php } /* for */ ?>

        <div class="atwi-actions">
          <a href="#" class="button-secondary atwi-get-winner <?php echo ( ( count( $winners ) >= $contest->GetNumberOfWinnersPossible() ) || comments_open( $post->ID )) ? 'hidden' : ''; ?>" title="<?php echo ATWI; ?>&hellip;"><?php echo ATWI; ?>&hellip;</a>
          <a href="#" class="submitdelete atwi-open-comments <?php echo ( ( 0 === ( count( $winners ) ) ) && !comments_open( $post->ID ) ) ? '' : 'hidden'; ?>" title="<?php _e( 'Re-open comments', ATWI_DOMAIN ); ?>"><?php _e( 'Re-open&nbsp;comments', ATWI_DOMAIN ); ?></a>
          <a href="#" class="atwi-close-comments <?= comments_open( $post->ID ) ? '' : 'hidden'; ?>" title="<?php _e( 'Close Comments', ATWI_DOMAIN ); ?>"><?php _e( 'Close&nbsp;Comments', ATWI_DOMAIN ); ?></a>
        </div>

      </td>
			<td><?php echo $contest->GetNumberOfEntries(); ?></td>
      <td id="atwi-winner-<?php echo $post->ID; ?>-number-of-possible-winners"><?php echo $contest->GetNumberOfWinnersPossible(); ?></td>
		</tr>

		<?php endif; ?>

<?php } endif; ?>

    <?php if (0 === $count) : ?>
		<tr>
			<td colspan="4" id="atwi-no-posts">
				<strong><?php _e( 'There are no posts available to choose a winning comment for.', ATWI_DOMAIN ); ?></strong>
				<p><?php _e( 'Perhaps you have not marked any posts as contests or there are no contest posts with comments.', ATWI_DOMAIN ); ?></p>
			</td>
		</tr>
    <?php endif; ?>
    
	</tbody>
</table>

<?php include(ATWI_PATH.'/admin-pages/donate.php'); ?>

</div><!--wrap-->