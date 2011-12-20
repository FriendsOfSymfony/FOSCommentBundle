/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * To use this reference javascript, you must also have jQuery and easyXdm
 * installed.
 *
 * @todo: expand this explanation (also in the docs)
 *
 * Then a comment thread can be embedded on any page:
 *
 * <div id="fos_comment_thread">#comments</div>
 * <script type="text/javascript">
 *     var fos_comment_thread_id = 'a_unique_identifier_for_the_thread';
 *     var fos_comment_remote_cors_url = 'http://example.org/cors/index.html';
 *
 * (function() {
 *     var fos_comment_script = document.createElement('script');
 *     fos_comment_script.async = true;
 *     fos_comment_script.src = 'http://example.org/path/to/this/file.js';
 *     fos_comment_script.type = 'text/javascript';
 *
 *     (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(fos_comment_script);
 * })();
 * </script>
 */
var FOS_COMMENT = {
    /**
     * easyXDM instance to use
     */
    easyXDM: easyXDM.noConflict('FOS_COMMENT'),

    /**
     * Shorcut post method.
     *
     * @param string url The url of the page to post.
     * @param object data The data to be posted.
     * @param function callback Optional callback function to use.
     */
    post: function(url, data, callback) {
        // todo: is there a better way to do this?
        if('undefined' === typeof callback) {
            callback = function(r){};
        }
        FOS_COMMENT.xhr.request({
                url: url,
                method: 'POST',
                data: data,
        }, callback);
    },

    /**
     * Gets the comments of a thread and places them in the thread holder.
     *
     * @param string identifier Unique identifier for the thread.
     * @param string url Optional url for the thread. Defaults to current location.
     */
    getThreadComments: function(identifier, permalink) {
        if('undefined' == typeof permalink) {
            permalink = window.location.href;
        }

        FOS_COMMENT.xhr.request({
                // todo: fix hardcoded links
                url: '/app_dev.php/api/threads/'+encodeURIComponent(identifier)+'/comments',
                method: 'GET',
                data: {permalink: encodeURIComponent(permalink)},

        }, function(response) {
                $('#fos_comment_thread').html(response.data);
        });
    },

    /**
     * Initialize the event listeners.
     */
    init: function() {
        $('form.fos_comment_comment_form').live('submit',
            function(e) {
                FOS_COMMENT.post(
                    $(this).data('action'),
                    FOS_COMMENT.serializeObject(this),
                    function(response) {
                        $('#fos_comment_box').prepend(response.data);
                        $('form.fos_comment_comment_form')[0].reset();
                    }
                );

                e.preventDefault();
            }
        );
    },

    /**
     * easyXdm doesn't seem to pick up 'normal' serialized forms yet in the
     * data property, so use this for now.
     * http://stackoverflow.com/questions/1184624/serialize-form-to-json-with-jquery#1186309
     */
    serializeObject: function(obj)
    {
        var o = {};
        var a = $(obj).serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    }
};

/* Initialize xhr object to do cross-domain requests. */
FOS_COMMENT.xhr = new FOS_COMMENT.easyXDM.Rpc({
        remote: fos_comment_remote_cors_url
}, {
    remote: {
        request: {} // request is exposed by /cors/
    }
});


// get the thread comments and init listeners
FOS_COMMENT.getThreadComments(fos_comment_thread_id);
FOS_COMMENT.init();
