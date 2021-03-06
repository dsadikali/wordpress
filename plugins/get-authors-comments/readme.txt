=== Get Author's Comments ===
Contributors: piouPiouM
Tags: comment, comments, search, author
Requires at least: 2.7-beta1
Tested up to: 2.9
Stable tag: 1.1.0

Get Author's Comments displays or retrieves a list of comments posted by a user.

== Description ==

This plugin allows to display or retrieve comments posted by a user. In order to avoid homonyms, a user is identified by his name and email(s).

**Note:** Get Author's Comments uses the function [wp\_list\_comments](http://codex.wordpress.org/Template_Tags/wp_list_comments) introduced by WordPress 2.7 for the (x)HTML output.

== Installation ==

Get Author's Comments is installed in 3 easy steps:

1. Upload `get_authors_comments.php` to the `/wp-content/plugins/` directory.
2. Activate `Get Author's Comments` through the _Plugins_ menu in WordPress.
3. Add in your theme:

        <?php if (function_exist('ppm_author_comments')): ?>
            <ol>
                <?php ppm_author_comments('name', 'email', 'post_ID', 'args') ?>
            </ol>
        <?php endif ?>

== Usage ==

= Display all comments of a specific user in the current post =

    <?php
        <ol>
            <?php ppm_author_comments('piouPiouM', 'foo@example.com'); ?>
        </ol>
    ?>

or, if *piouPiouM* wrote with two different emails:

    <?php
        <ol>
            <?php ppm_author_comments('piouPiouM', array('foo@example.com', 'bar@example.org')); ?>
        </ol>
    ?>

= Display all comments posted by a user =

    <?php
        <ol>
            <?php ppm_author_comments('piouPiouM', 'foo@example.com', null, 'all=1'); ?>
        </ol>
    ?>

= Display comments wrote by a user in the post of ID number 9 =

    <?php
        <ol>
            <?php ppm_author_comments('piouPiouM', 'foo@example.com', 9); ?>
        </ol>
    ?>

**Note:** If you used the tags `ppm_author_comments` or `ppm_get_author_comments` whithin [The Loop](http://codex.wordpress.org/The_Loop "The Loop &laquo; WordPress Codex"), the parameter `$postID` will be replaced automatically by the numeric ID of the current post.

= Lastest comments ordered by post_ID =

To show the last ten piouPiouM's comments sorted by post_ID in ascending order, the following will display their comment date and excerpt:

    <?php
        $comments = ppm_get_author_comments('piouPiouM', 'foo@example.com', null, 'number=10&order=ASC&orderby=post_id');
        foreach ($comments as $comment):
    ?>
    <p><cite><?php comment_author_link() ?></cite> says:</p>
    <ol>
        <li>
            <p>Comment posted on <?php comment_date('n-j-Y'); ?>:<br/></p>
            <p><?php comment_excerpt(); ?></p>
        </li>
    </ol>
    <?php endforeach; ?>

= Comments with a custom comment display =

    <?php
        <ol>
            <?php ppm_author_comments('piouPiouM', 'info@example.com', null, 'callback=mytheme_comment'); ?>
        </ol>
    ?>

See [Comments Only With A Custom Comment Display](http://codex.wordpress.org/Template_Tags/wp_list_comments#Comments_Only_With_A_Custom_Comment_Display "Template Tags/wp list comments &laquo; WordPress Codex") for an example of a custom callback function.

= Show the total number of comments posted by a user on the site =

    <?php
        get_currentuserinfo();
        $comments = ppm_get_author_comments($current_user->display_name, $current_user->user_email, null, 'all=1');
        printf('Hello ! <a href="/author/%s/">%s</a>! '
             . '[ <a href="%s" class="logout">Log Out</a> ]<br/>'
             . '%d posts and %d comments',
            $current_user->user_login,
            $current_user->display_name,
            wp_logout_url(),
            get_usernumposts($current_user->ID),
            count($comments));
    ?>

== Parameters ==

**all**  
*(boolean)* *(optional)* Retrieve all comments. Default to *FALSE*.

**number**  
*(integer)* *(optional)* Number of comments to return. Default to *None*, returns all comments.

**offset**  
*(integer)* *(optional)* Offset from latest comment. Default to 0.

**orderby**  
*(string)* *(optional)* Sort posts by one of various values (separated by space), including:

* `'comment_ID'` - Sort by numeric comment ID.
* `'content'` - Sort by content.
* `'date'` - Sort by creation date. (Default)
* `'post_ID'` - Sort by post ID.
* `'rand'` - Sort in random order.
* `'status'` - Sort by status.
* `'type'` - Sort by type.

**order**  
*(string)* *(optional)* Sort order, ascending or descending for the orderby parameter. Valid values:

* `'ASC'` - Ascending (lowest to highest).
* `'DESC'` - Descending (highest to lowest). (Default)

**output**  
*(string)* *(optional)* How you'd like the result. Only for `ppm_get_author_comments`.

* `OBJECT` - Returns an object. (Default)
* `ARRAY_A` - Returns an associative array of field names to values.
* `ARRAY_N` - Returns a numeric array of field values.
* `HTML` - Returns a (x)HTML version generated by [wp\_list\_comments](http://codex.wordpress.org/Template_Tags/wp_list_comments).

**status**  
*(string)* *(optional)* The comments status. Default to hold and approve. Valid values:

* `'hold'` - Unapproved.
* `'approve'` - Approved.
* `'spam'` - Spam.

== Changelog ==

= 1.1.0 =

* Support a new `all` argument for retrieves all comments posted by a user.
* Add changelog and additional examples.

= 1.0.1 =

* The arguments are case insensitive.
* Support of two new `orderby` arguments: `comment_ID` and `post_ID`.
* Set default `orderby` argument to `date`.

= 1.0.0 =

* Initial release.
