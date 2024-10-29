<div class="wrap">
<h2><?php _e('Uninstall "And The Winner Is"', ATWI_DOMAIN); ?>&hellip;</h2>

<span class="hidden" id="atwi-ajax-url"><?= get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php</span>
<span id="atwi-confirm-text" class="hidden"><?php _e('This operation cannot be undone. Are you sure you want to remove all data associated with '.ATWI.'?', ATWI_DOMAIN); ?></span>

<p>
	<?php _e('Executing the uninstall will remove all data related to this plugin.', ATWI_DOMAIN); ?>
	<?php _e('Any posts marked as contests will no longer be contests.', ATWI_DOMAIN); ?>
	<?php _e('All winner data (confirmed or unconfirmed) for all contests will be removed.', ATWI_DOMAIN); ?>
</p>

<p>
	<strong><?php _e('This operation cannot be undone.', ATWI_DOMAIN); ?></strong>
</p>

<div id="atwi-thinking" class="hidden">
	<?php _e('Uninstalling', ATWI_DOMAIN).' '.ATWI; ?>&hellip;
	<img src="<?= ATWI_URL; ?>/images/loading.gif" alt="<?php _e('Uninstalling', ATWI_DOMAIN).' '.ATWI; ?>&hellip;" />
</div>

<p>
  <a href="#" title="<?php _e('Uninstall', ATWI_DOMAIN); ?>" class="button-secondary atwi-uninstall"><?= __('Delete all data related to', ATWI_DOMAIN).' '.ATWI ?>&hellip;</a>
</p>

<div id="atwi-uninstall-complete" class="hidden"><?php _e('Uninstall complete.', ATWI_DOMAIN); ?></div>

</div><!--wrap-->