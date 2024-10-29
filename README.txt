=== And The Winner Is... ===
Contributors: spencersokol
Donate link: http://spencersokol.com/donations/
Tags: giveaway, give-away, contest, random comment, winner,
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 1.1.1

Manage your product giveaways by marking posts as "contests" and
selecting a random comment from a contest post as the winner.

== Description ==

And The Winner Is... is a WordPress plugin that helps you manage product giveaways by allowing you to mark posts as "contests". Each contest has a user-specified number of winners that can be selected at random from comments for that giveaway post.

The problem is a simple one. Many site owners run contests that allow entries as comments on a blog post, but managing entries and randomly selecting winners is a chore. And The Winner Is... helps to solve this problem by managing the comment entries and winners by selecting those winners at the click of a button.

== Installation ==

1. Upload the contents of the plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How do I setup a contest? =

Simply edit a post and check the box in the "And The Winner Is..." meta box to indicate that you want 
that specific post to be considered a contest and the number of winners that are possible for this contest.

= How do I find a winner? =

In the WordPress admin area, you'll find a menu option, "And The Winner Is...", which can be expanded.
Select the "Contests" option and a list of posts you've marked as contests will appear.  You can only 
have the plugin find a winner if comment on that contest are closed. You can, however, close the comments 
for a post from this screen.  Once comments are closed, click the "And The Winner Is..." button for your 
corresponding contest.  The plugin will randomly select a comment from that contest and mark it as an 
unconfirmed winner.  Unconfirmed winners appear under your contest description with a background color 
of yellow.

= How do I confirm a winner for a contest? =

If the comment meets your criteria for winning the contest, you can click the "Confirm Winner" button
on a contest where a winner has been selected, but not confirmed. Once you've confirmed the winner, they
cannot be rejected, nor can another winner be selected.  Confirmed winners appear under your contest
description with a background color of yellow and with the text "(confirmed)".

= How do I reject a winner, or select a different winner for a contest =

If the contest winner has not been marked as confirmed by the plugin (i.e. it appears in yellow below your
contest description), you can select "Reject Winner" and that winner will be forgotten and you can find a
new random winner by clicking the "And The Winner Is..." button again.

A comment that has been rejected as a winner will not be selected again as a possible winner, so be
careful when rejecting winners.

= How do I completely uninstall the plugin? =

In the WordPress admin area, you'll find a menu option, "And The Winner Is...", which can be expanded.
Select the Uninstall option and click the button that indicates all contest data will be removed.

= If I deactivate the plugin, will I lose all of my contest data? =

No, deactivating the plugin will not remove your contest data.  You will have to run the uninstall 
procedure to remove all of your contest data.

== Changelog ==

= 1.1.1 =
* Fixed the script enqueue problem

= 1.1.0 =
* Added pagination & search to contests page
* Changed contests page query to custom query, instead of using query_posts, which should help with multilingual sites
* Other minor things

= 1.0.2 =
* Bug fix for plugin not activating properly in 3.0.1
* Added comment permalink to confirmation box
* Added subject to comment winner mailto link

= 1.0.1 =
* Bug fix for opening/closing comments from the Contests page

= 1.0 =
* Initial public release

= beta 2 2010.05.22 =
* Added the functionality for a contest to have multiple winners

= beta 1 2010.05.18 =
* Initial release

== Future Releases ==

* Contest pagination
* Contest filtering (AJAX) & sorting
* Automatic notification to a confirmed winner
* Alternate randomization options
* Ignore certain email addresses