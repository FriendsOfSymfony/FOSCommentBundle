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

(function(window, $, easyXDM){
    var FOS_COMMENT = {
        /**
         * easyXDM instance to use
         */
        easyXDM: easyXDM.noConflict('FOS_COMMENT'),

        /**
         * Shorcut request method.
         *
         * @param string method The request method to use.
         * @param string url The url of the page to request.
         * @param object data The data parameters.
         * @param function callback Optional callback function to use.
         */
        request: function(method, url, data, callback) {
            // todo: is there a better way to do this?
            if('undefined' === typeof callback) {
                callback = function(r){};
            }
            FOS_COMMENT.xhr.request({
                    url: url,
                    method: method,
                    data: data,
            }, callback);
        },
        /**
         * Shorcut post method.
         *
         * @param string url The url of the page to post.
         * @param object data The data to be posted.
         * @param function callback Optional callback function to use.
         */
        post: function(url, data, callback) {
            this.request('POST', url, data, callback);
        },

        /**
         * Shorcut get method.
         *
         * @param string url The url of the page to get.
         * @param object data The query data.
         * @param function callback Optional callback function to use.
         */
        get: function(url, data, callback) {
            this.request('GET', url, data, callback);
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

            FOS_COMMENT.get(
                '/app_dev.php/api/threads/'+encodeURIComponent(identifier)+'/comments',
                {permalink: encodeURIComponent(permalink)},
                function(response) {
                    $('#fos_comment_thread').html(response.data);
                }
            );
        },

        /**
         * Initialize the event listeners.
         */
        init: function() {
            $('form.fos_comment_comment_form').live('submit',
                function(e) {
                    var that = $(this);
                    var data = that.data();

                    FOS_COMMENT.post(
                        data.action,
                        FOS_COMMENT.serializeObject(this),
                        function(response) {
                            FOS_COMMENT.appendComment(response.data, that);
                        }
                    );

                    e.preventDefault();
                }
            );

            $('.fos_comment_comment_reply_show_form').live('click',
                function(e) {
                    var data = $(this).data();
                    var that = this;

                    FOS_COMMENT.get(
                        data.url,
                        {parentId: data.parentId},
                        function(response) {
                            $(that).after(response.data);
                        }
                    );
                }
            );

            $('.fos_comment_comment_vote').live('click',
                function(e) {
                    var data = $(this).data();
                    var that = this;

                    // Get the form
                    FOS_COMMENT.get(
                        data.url,
                        {},
                        function(response) {
                            // Post it
                            var form = $(response.data).children('form');
                            var data = $(form).data();

                            FOS_COMMENT.post(
                                data.action,
                                FOS_COMMENT.serializeObject(form),
                                function(response) {
                                    $('#' + data.scoreHolder).html(response.data);
                                }
                            );
                        }
                    );
                }
            );
        },

        appendComment: function(commentHtml, form) {
            var data = form.data();

            if('' != data.parent) {
                form.after(commentHtml);

                // one up for form holder, then again one up
                form.parent().parent().after(commentHtml);

                // Remove the form
                form.parent().remove();
            }
            else {
                form.after(commentHtml);
                form[0].reset();
            }
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

    window.fos = window.fos || {};
    window.fos.Comment = FOS_COMMENT;
})(window, window.jQuery, window.easyXDM)
