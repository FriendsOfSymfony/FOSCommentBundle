/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * To use this reference javascript, you must also have jQuery and jquery.form.js
 * installed.
 *
 * jquery.form.js is available from: http://malsup.com/jquery/form/
 */

$('form.fos_comment_comment_form').live('submit', function() {
    var $form = $(this).addClass('processing').ajaxSubmit({
        success: function(html) {
            $form.closest('div.fos_comment_thread_show').replaceWith(html);
        },
        error: function(xhr, status, error) {
            $form.addClass('error').removeClass('processing');
        }
    });
    return false;
});

$('button.fos_comment_comment_reply_show_form').live('click', function() {
    var $button = $(this);
    var $container = $button.parent().addClass('replying');
    var $reply = $('div.fos_comment_reply_prototype').clone()
        .removeClass('fos_comment_reply_prototype')
        .find('.fos_comment_reply_name_placeholder').text($button.attr('data-name')).end()
        .find('.fos_comment_comment_form').attr('action', $button.attr('data-url')).end()
        .find('.fos_comment_reply_cancel').click(function() {
            $reply.remove();
            $container.removeClass('replying');
        }).end()
        .appendTo($container)
        .find('textarea').focus().end();
});

$('button.fos_comment_comment_loadmore_load').live('click', function() {
    var $button = $(this);
    var $container = $button.parent();

    $container.load($button.attr('data-url'));
});

$('button.fos_comment_comment_vote').live('click', function () {
    var $button = $(this);
    var $container = $button.parent();
    var $score = $container.find('.fos_comment_comment_score');

    $.getJSON($button.attr('data-url'), function (data) {
        $score.text(data.score);
    });
});