/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * To use this reference javascript, you must also have jQuery installed. If
 * you want to embed comments cross-domain, then easyXDM CORS is also required.
 *
 * @todo: expand this explanation (also in the docs)
 *
 * Then a comment thread can be embedded on any page:
 *
 * <div id="fos_comment_thread">#comments</div>
 * <script type="text/javascript">
 *     // Set the thread_id if you want comments to be loaded via ajax (url to thread comments api)
 *     var fos_comment_thread_id = 'http://example.org/api/threads/a_unique_identifier_for_the_thread/comments';
 *
 *     // Set the cors url if you want cross-domain AJAX (also needs easyXDM)
 *     var fos_comment_remote_cors_url = 'http://example.org/cors/index.html';
 *
 *     // Optionally set a different element than div#fos_comment_thread as container
 *     var fos_comment_thread_container = $('#other_element');
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
    "use strict";
    var FOS_COMMENT = {
        /**
         * Shorcut post method.
         *
         * @param string url The url of the page to post.
         * @param object data The data to be posted.
         * @param function callback Optional callback function to use.
         */
        post: function(url, data, callback) {
            $.post(url, data, callback);
        },

        /**
         * Shorcut get method.
         *
         * @param string url The url of the page to get.
         * @param object data The query data.
         * @param function callback Optional callback function to use.
         */
        get: function(url, data, callback) {
            $.get(url, data, callback);
        },

        /**
         * Gets the comments of a thread and places them in the thread holder.
         *
         * @param string identifier Unique identifier url for the thread comments.
         * @param string url Optional url for the thread. Defaults to current location.
         */
        getThreadComments: function(identifier, permalink) {
            if('undefined' == typeof permalink) {
                permalink = window.location.href;
            }

            FOS_COMMENT.get(
                identifier,
                {permalink: encodeURIComponent(permalink)},
                function(data) {
                    FOS_COMMENT.thread_container.html(data);
                }
            );
        },

        /**
         * Initialize the event listeners.
         */
        initializeListeners: function() {
            FOS_COMMENT.thread_container.on('submit',
                'form.fos_comment_comment_form',
                function(e) {
                    var that = $(this);

                    FOS_COMMENT.post(
                        this.action,
                        FOS_COMMENT.serializeObject(this),
                        function(data) {
                            FOS_COMMENT.appendComment(data, that);
                        }
                    );

                    e.preventDefault();
                }
            );

            FOS_COMMENT.thread_container.on('click',
                '.fos_comment_comment_reply_show_form',
                function(e) {
                    var form_data = $(this).data();
                    var that = this;
                    var $button = $(this);
                    var $container = $button.parent().addClass('replying');

                    FOS_COMMENT.get(
                        form_data.url,
                        {parentId: form_data.parentId},
                        function(data) {
                            $(that).after(data);
                            var $reply = $container.find('.fos_comment_comment_form_holder')
                                .find('.fos_comment_reply_cancel').click(function() {
                                    $reply.remove();
                                    $container.removeClass('replying');
                                }).end()
                                .find('textarea').focus().end();
                        }
                    );
                }
            );

            FOS_COMMENT.thread_container.on('click',
                '.fos_comment_comment_vote',
                function(e) {
                    var form_data = $(this).data();

                    // Get the form
                    FOS_COMMENT.get(
                        form_data.url,
                        {},
                        function(data) {
                            // Post it
                            var form = $(data).children('form')[0];
                            var form_data = $(form).data();

                            FOS_COMMENT.post(
                                form.action,
                                FOS_COMMENT.serializeObject(form),
                                function(data) {
                                    $('#' + form_data.scoreHolder).html(data);
                                }
                            );
                        }
                    );
                }
            );
        },

        appendComment: function(commentHtml, form) {
            var form_data = form.data();

            if('' != form_data.parent) {
                form.after(commentHtml);

                // one up for form holder, then again one up
                var $container = form.parent().parent();
                    $container.after(commentHtml);
                    $container.removeClass('replying');

                // Remove the form
                form.parent().remove();
            } else {
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

    // Check if a thread container was configured. If not, use default.
    FOS_COMMENT.thread_container = window.fos_comment_thread_container || $('#fos_comment_thread');

    // AJAX via easyXDM if this is configured
    if(typeof window.fos_comment_remote_cors_url != "undefined") {
        /**
         * easyXDM instance to use
         */
        FOS_COMMENT.easyXDM = easyXDM.noConflict('FOS_COMMENT');

        /**
         * Shorcut request method.
         *
         * @param string method The request method to use.
         * @param string url The url of the page to request.
         * @param object data The data parameters.
         * @param function callback Optional callback function to use.
         */
        FOS_COMMENT.request = function(method, url, data, callback) {
            // wrap the callback to match the callback parameters of jQuery
            var wrappedCallback = function(response){
                if('undefined' !== typeof callback) {
                    callback(response.data, response.status);
                }
            };

            // todo: is there a better way to do this?
            FOS_COMMENT.xhr.request({
                    url: url,
                    method: method,
                    data: data,
            }, wrappedCallback);
        };

        FOS_COMMENT.post = function(url, data, callback) {
            this.request('POST', url, data, callback);
        };

        FOS_COMMENT.get= function(url, data, callback) {
            this.request('GET', url, data, callback);
        };

        /* Initialize xhr object to do cross-domain requests. */
        FOS_COMMENT.xhr = new FOS_COMMENT.easyXDM.Rpc({
                remote: window.fos_comment_remote_cors_url
        }, {
            remote: {
                request: {} // request is exposed by /cors/
            }
        });
    }

    // Load the comment if there is a thread id defined.
    if(typeof window.fos_comment_thread_id != "undefined") {
        // get the thread comments and init listeners
        FOS_COMMENT.getThreadComments(window.fos_comment_thread_id);
    }

    FOS_COMMENT.initializeListeners();

    window.fos = window.fos || {};
    window.fos.Comment = FOS_COMMENT;
})(window, window.jQuery, window.easyXDM)
