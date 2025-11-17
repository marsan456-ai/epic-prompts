/**
 * Epic Prompts Main JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        /**
         * Copy Prompt Functionality
         */
        if (typeof ClipboardJS !== 'undefined') {
            var clipboard = new ClipboardJS('#copy-prompt-btn');

            clipboard.on('success', function(e) {
                $('#copy-feedback').fadeIn().delay(2000).fadeOut();
                e.clearSelection();
            });

            clipboard.on('error', function(e) {
                console.error('Copy failed:', e);
            });
        }

        /**
         * Reactions System
         */
        $('.reaction-btn').on('click', function(e) {
            e.preventDefault();

            if (!epicPromptsTheme.is_logged_in) {
                alert('Please login to react to prompts.');
                return;
            }

            var $btn = $(this);
            var promptId = $btn.data('prompt-id');
            var reactionType = $btn.data('reaction-type');

            $.ajax({
                url: epicPromptsTheme.ajaxurl,
                type: 'POST',
                data: {
                    action: 'add_reaction',
                    nonce: epicPromptsTheme.nonce,
                    prompt_id: promptId,
                    reaction_type: reactionType
                },
                beforeSend: function() {
                    $btn.prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        // Update all reaction counts
                        $.each(response.data.reactions, function(type, count) {
                            $('.reaction-count[data-reaction="' + type + '"]').text(count);
                        });

                        // Update active state
                        $('.reaction-btn').removeClass('active');
                        $btn.addClass('active');

                        // Show XP notification if awarded
                        if (response.data.xp_awarded > 0) {
                            showXPNotification(response.data.xp_awarded);
                            updateHeaderXP(response.data.xp_awarded);
                        }

                        // Show success feedback
                        showNotification('Reaction added! 🎉', 'success');
                    } else {
                        showNotification(response.data.message || 'Error adding reaction', 'error');
                    }
                },
                error: function() {
                    showNotification('Network error. Please try again.', 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });

        /**
         * Show XP Notification
         */
        function showXPNotification(xp) {
            var $notification = $('<div class="xp-notification">+' + xp + ' XP</div>');
            $('body').append($notification);

            setTimeout(function() {
                $notification.fadeOut(function() {
                    $(this).remove();
                });
            }, 2000);

            // Confetti animation if available
            if (typeof confetti !== 'undefined') {
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 }
                });
            }
        }

        /**
         * Update Header XP Display
         */
        function updateHeaderXP(addedXP) {
            var $xpDisplay = $('#header-xp-display');
            if ($xpDisplay.length) {
                var currentText = $xpDisplay.text();
                var currentXP = parseInt(currentText.replace(/[^0-9]/g, ''));
                var newXP = currentXP + addedXP;
                $xpDisplay.text(formatNumber(newXP) + ' XP');
            }
        }

        /**
         * Format Number (1.2k, 5M, etc)
         */
        function formatNumber(num) {
            if (num >= 1000000) {
                return (num / 1000000).toFixed(1) + 'M';
            }
            if (num >= 1000) {
                return (num / 1000).toFixed(1) + 'k';
            }
            return num.toString();
        }

        /**
         * Show General Notification
         */
        function showNotification(message, type) {
            type = type || 'info';
            var bgColor = type === 'success' ? '#10B981' : (type === 'error' ? '#EF4444' : '#6366F1');

            var $notification = $('<div>')
                .css({
                    'position': 'fixed',
                    'top': '20px',
                    'right': '20px',
                    'background': bgColor,
                    'color': 'white',
                    'padding': '1rem 1.5rem',
                    'border-radius': '0.5rem',
                    'box-shadow': '0 10px 15px -3px rgba(0, 0, 0, 0.1)',
                    'z-index': '9999',
                    'font-weight': '600'
                })
                .text(message);

            $('body').append($notification);

            setTimeout(function() {
                $notification.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        }

        /**
         * Filter Form Submit Handler
         */
        $('#prompts-filter-form').on('submit', function(e) {
            // Let it submit normally - no AJAX for now
        });

        /**
         * Increment Views (on single prompt page)
         */
        if ($('.single-prompt').length && epicPromptsTheme.is_logged_in) {
            var promptId = $('.single-prompt').attr('id').replace('prompt-', '');

            $.ajax({
                url: epicPromptsTheme.ajaxurl,
                type: 'POST',
                data: {
                    action: 'increment_views',
                    nonce: epicPromptsTheme.nonce,
                    prompt_id: promptId
                }
            });
        }

        /**
         * Mobile Menu Toggle (if needed in future)
         */
        $('.mobile-menu-toggle').on('click', function() {
            $('.main-nav').toggleClass('active');
        });

        /**
         * Smooth Scroll to Anchor Links
         */
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.hash);
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        });

        /**
         * Lazy Load Images (simple implementation)
         */
        if ('IntersectionObserver' in window) {
            var imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img.lazy').forEach(function(img) {
                imageObserver.observe(img);
            });
        }

        /**
         * Form Validation Helper
         */
        function validateEmail(email) {
            var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        /**
         * Character Counter for Textareas
         */
        $('textarea[data-maxlength]').each(function() {
            var $textarea = $(this);
            var maxLength = $textarea.data('maxlength');
            var $counter = $('<div class="char-counter"></div>');

            $textarea.after($counter);

            $textarea.on('input', function() {
                var currentLength = $(this).val().length;
                $counter.text(currentLength + ' / ' + maxLength);

                if (currentLength > maxLength) {
                    $counter.css('color', '#EF4444');
                } else {
                    $counter.css('color', '#6B7280');
                }
            }).trigger('input');
        });

    }); // document.ready

})(jQuery);
