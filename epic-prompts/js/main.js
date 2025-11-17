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

        /**
         * Bookmark/Save Prompt
         */
        $(document).on('click', '.bookmark-btn', function(e) {
            e.preventDefault();

            if (!epicPromptsTheme.is_logged_in) {
                showNotification('Please login to bookmark prompts', 'error');
                window.location.href = epicPromptsTheme.login_url || '/wp-login.php';
                return;
            }

            var $btn = $(this);
            var promptId = $btn.data('prompt-id');
            var $icon = $btn.find('.bookmark-icon');
            var $count = $btn.find('.bookmark-count');

            $.ajax({
                url: epicPromptsTheme.ajaxurl,
                type: 'POST',
                data: {
                    action: 'bookmark_prompt',
                    nonce: epicPromptsTheme.nonce,
                    prompt_id: promptId
                },
                beforeSend: function() {
                    $btn.prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        // Toggle bookmark state
                        if (response.data.is_saved) {
                            $btn.addClass('saved');
                            $icon.text('🔖'); // Filled bookmark
                        } else {
                            $btn.removeClass('saved');
                            $icon.text('📑'); // Empty bookmark
                        }

                        // Update count
                        if ($count.length) {
                            $count.text(response.data.saves_count);
                        }

                        showNotification(response.data.message, 'success');
                    } else {
                        showNotification(response.data.message || 'Error bookmarking prompt', 'error');
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
         * Copy Prompt to Clipboard
         */
        $(document).on('click', '.copy-prompt-btn', function(e) {
            e.preventDefault();

            var $btn = $(this);
            var promptText = $btn.data('prompt-text');
            var promptId = $btn.data('prompt-id');

            // Copy to clipboard
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(promptText).then(function() {
                    // Track copy action
                    $.ajax({
                        url: epicPromptsTheme.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'copy_prompt',
                            nonce: epicPromptsTheme.nonce,
                            prompt_id: promptId
                        },
                        success: function(response) {
                            if (response.success) {
                                // Update copy count if element exists
                                var $count = $btn.find('.copy-count');
                                if ($count.length && response.data.copies) {
                                    $count.text(response.data.copies);
                                }
                            }
                        }
                    });

                    // Visual feedback
                    var originalText = $btn.html();
                    $btn.html('✅ Copied!');
                    setTimeout(function() {
                        $btn.html(originalText);
                    }, 2000);

                    showNotification('Prompt copied to clipboard! 📋', 'success');
                }).catch(function(err) {
                    console.error('Copy failed:', err);
                    showNotification('Failed to copy. Please try again.', 'error');
                });
            } else {
                // Fallback for older browsers
                var textarea = document.createElement('textarea');
                textarea.value = promptText;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();

                try {
                    document.execCommand('copy');
                    showNotification('Prompt copied to clipboard! 📋', 'success');

                    // Track copy
                    $.post(epicPromptsTheme.ajaxurl, {
                        action: 'copy_prompt',
                        nonce: epicPromptsTheme.nonce,
                        prompt_id: promptId
                    });
                } catch (err) {
                    showNotification('Failed to copy. Please try again.', 'error');
                }

                document.body.removeChild(textarea);
            }
        });

        /**
         * Share Prompt
         */
        $(document).on('click', '.share-btn', function(e) {
            e.preventDefault();

            var $btn = $(this);
            var platform = $btn.data('platform');
            var promptId = $btn.data('prompt-id');
            var shareUrl = $btn.data('share-url') || window.location.href;
            var shareTitle = $btn.data('share-title') || document.title;
            var shareText = $btn.data('share-text') || '';

            var shareLinks = {
                'twitter': 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(shareUrl) + '&text=' + encodeURIComponent(shareTitle),
                'facebook': 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(shareUrl),
                'linkedin': 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(shareUrl),
                'whatsapp': 'https://wa.me/?text=' + encodeURIComponent(shareTitle + ' ' + shareUrl),
                'telegram': 'https://t.me/share/url?url=' + encodeURIComponent(shareUrl) + '&text=' + encodeURIComponent(shareTitle),
                'email': 'mailto:?subject=' + encodeURIComponent(shareTitle) + '&body=' + encodeURIComponent(shareText + '\n\n' + shareUrl)
            };

            if (platform === 'copy') {
                // Copy link to clipboard
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(shareUrl).then(function() {
                        showNotification('Link copied to clipboard! 🔗', 'success');
                    });
                }
                return;
            }

            if (platform === 'native' && navigator.share) {
                // Use native share if available
                navigator.share({
                    title: shareTitle,
                    text: shareText,
                    url: shareUrl
                }).then(function() {
                    trackShare(promptId, 'native');
                }).catch(function(err) {
                    console.log('Share cancelled or failed:', err);
                });
                return;
            }

            // Open share window
            if (shareLinks[platform]) {
                window.open(shareLinks[platform], '_blank', 'width=600,height=400');
                trackShare(promptId, platform);
                showNotification('Shared! Thank you for spreading the word 🚀', 'success');
            }
        });

        /**
         * Track Share Action
         */
        function trackShare(promptId, platform) {
            if (!promptId) return;

            $.post(epicPromptsTheme.ajaxurl, {
                action: 'share_prompt',
                nonce: epicPromptsTheme.nonce,
                prompt_id: promptId,
                platform: platform
            });
        }

        /**
         * Advanced Search with Filters
         */
        $('#advanced-search-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $results = $('#search-results');
            var $loading = $('#search-loading');

            var formData = {
                action: 'search_prompts',
                nonce: epicPromptsTheme.nonce,
                search: $form.find('[name="search"]').val(),
                platform: $form.find('[name="platform"]').val(),
                category: $form.find('[name="category"]').val(),
                prompt_type: $form.find('[name="prompt_type"]').val(),
                sort_by: $form.find('[name="sort_by"]').val(),
                page: 1
            };

            $.ajax({
                url: epicPromptsTheme.ajaxurl,
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    $loading.show();
                    $results.empty();
                },
                success: function(response) {
                    $loading.hide();

                    if (response.success && response.data.results) {
                        displaySearchResults(response.data.results, $results);

                        // Show pagination if needed
                        if (response.data.pages > 1) {
                            displayPagination(response.data.pages, response.data.current_page);
                        }
                    } else {
                        $results.html('<p class="no-results">No prompts found. Try different filters.</p>');
                    }
                },
                error: function() {
                    $loading.hide();
                    showNotification('Search failed. Please try again.', 'error');
                }
            });
        });

        /**
         * Display Search Results
         */
        function displaySearchResults(results, $container) {
            if (!results || results.length === 0) {
                $container.html('<p class="no-results">No prompts found.</p>');
                return;
            }

            var html = '<div class="prompts-grid">';

            results.forEach(function(prompt) {
                html += '<div class="prompt-card">';
                html += '  <h3><a href="' + prompt.url + '">' + prompt.title + '</a></h3>';
                html += '  <p>' + prompt.excerpt + '</p>';
                html += '  <div class="prompt-meta">';
                html += '    <span class="platform">' + prompt.platform + '</span>';
                html += '    <span class="stats">👁️ ' + prompt.views + ' | ⭐ ' + prompt.rating.toFixed(1) + '</span>';
                html += '  </div>';
                html += '</div>';
            });

            html += '</div>';

            $container.html(html);
        }

        /**
         * Display Pagination
         */
        function displayPagination(totalPages, currentPage) {
            // Implement pagination UI
            // This is a placeholder - full implementation would go in the search page template
        }

    }); // document.ready

})(jQuery);
