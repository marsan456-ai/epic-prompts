/**
 * Epic Prompts Importer - Admin JavaScript
 */

(function($) {
    'use strict';

    var EpicPromptsImporter = {

        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.updateModeDescription();
        },

        /**
         * Bind Events
         */
        bindEvents: function() {
            $('#epi-validate-btn').on('click', this.validateJSON.bind(this));
            $('#epi-import-btn').on('click', this.importJSON.bind(this));
            $('input[name="import_mode"]').on('change', this.updateModeDescription.bind(this));
        },

        /**
         * Update Mode Description
         */
        updateModeDescription: function() {
            var mode = $('input[name="import_mode"]:checked').val();
            var descriptions = {
                'skip': '<strong>Skip Duplicates:</strong> Import only new prompts. If a prompt with the same title already exists, it will be skipped.',
                'update': '<strong>Update Existing:</strong> Import all prompts. If a prompt with the same title exists, it will be updated with the new data.',
                'fresh': '<strong>Fresh Import:</strong> Delete all existing prompts and import fresh. <span style="color: #EF4444;">⚠️ This will permanently delete all current prompts!</span>'
            };

            $('.epi-mode-description').html(descriptions[mode] || '');
        },

        /**
         * Validate JSON
         */
        validateJSON: function(e) {
            e.preventDefault();

            var $btn = $(e.currentTarget);
            var jsonUrl = $('#json-url').val().trim();

            if (!jsonUrl) {
                this.showAlert('Please enter a JSON URL', 'error');
                return;
            }

            // Disable button
            $btn.prop('disabled', true).html('<span class="epi-spinner"></span> Validating...');

            // Hide previous results
            $('.epi-results').removeClass('active');

            // AJAX call to validate
            $.ajax({
                url: epiAdminData.ajaxurl,
                type: 'POST',
                data: {
                    action: 'epi_validate_json',
                    nonce: epiAdminData.nonce,
                    json_url: jsonUrl
                },
                success: function(response) {
                    $btn.prop('disabled', false).html('🔍 Validate JSON');

                    if (response.success) {
                        this.showValidationSuccess(response.data);
                        $('#epi-import-btn').prop('disabled', false);
                    } else {
                        this.showValidationError(response.data);
                        $('#epi-import-btn').prop('disabled', true);
                    }
                }.bind(this),
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false).html('🔍 Validate JSON');
                    this.showAlert('Network error: ' + error, 'error');
                    $('#epi-import-btn').prop('disabled', true);
                }.bind(this)
            });
        },

        /**
         * Show Validation Success
         */
        showValidationSuccess: function(data) {
            var html = '<div class="epi-alert epi-alert-success">';
            html += '<strong>✅ Validation Successful!</strong><br>';
            html += 'JSON structure is valid and ready for import.';
            html += '</div>';

            html += '<div class="epi-stats">';
            html += '<div class="epi-stat-box">';
            html += '  <div class="epi-stat-number">' + data.total_prompts + '</div>';
            html += '  <div class="epi-stat-label">Total Prompts</div>';
            html += '</div>';
            html += '<div class="epi-stat-box">';
            html += '  <div class="epi-stat-number">' + (data.platforms_count || 0) + '</div>';
            html += '  <div class="epi-stat-label">Platforms</div>';
            html += '</div>';
            html += '<div class="epi-stat-box">';
            html += '  <div class="epi-stat-number">' + (data.types_count || 0) + '</div>';
            html += '  <div class="epi-stat-label">Prompt Types</div>';
            html += '</div>';
            html += '<div class="epi-stat-box">';
            html += '  <div class="epi-stat-number">' + (data.categories_count || 0) + '</div>';
            html += '  <div class="epi-stat-label">Categories</div>';
            html += '</div>';
            html += '</div>';

            if (data.warnings && data.warnings.length > 0) {
                html += '<div class="epi-alert epi-alert-warning" style="margin-top: 1rem;">';
                html += '<strong>⚠️ Warnings:</strong><ul style="margin: 0.5rem 0 0 1.5rem;">';
                data.warnings.forEach(function(warning) {
                    html += '<li>' + warning + '</li>';
                });
                html += '</ul></div>';
            }

            $('.epi-results').html(html).addClass('active');
        },

        /**
         * Show Validation Error
         */
        showValidationError: function(data) {
            var html = '<div class="epi-alert epi-alert-error">';
            html += '<strong>❌ Validation Failed!</strong><br>';
            html += data.message || 'Invalid JSON structure.';
            html += '</div>';

            if (data.errors && data.errors.length > 0) {
                html += '<div class="epi-validation-results">';
                data.errors.forEach(function(error) {
                    html += '<div class="epi-validation-item error">';
                    html += '  <div class="epi-validation-icon">❌</div>';
                    html += '  <div>' + error + '</div>';
                    html += '</div>';
                });
                html += '</div>';
            }

            $('.epi-results').html(html).addClass('active');
        },

        /**
         * Import JSON
         */
        importJSON: function(e) {
            e.preventDefault();

            var $btn = $(e.currentTarget);
            var jsonUrl = $('#json-url').val().trim();
            var importMode = $('input[name="import_mode"]:checked').val();

            if (!jsonUrl) {
                this.showAlert('Please enter a JSON URL', 'error');
                return;
            }

            // Confirm fresh import
            if (importMode === 'fresh') {
                if (!confirm('⚠️ WARNING: This will DELETE all existing prompts and import fresh data.\n\nThis action cannot be undone. Are you sure?')) {
                    return;
                }
            }

            // Disable buttons
            $btn.prop('disabled', true).html('<span class="epi-spinner"></span> Importing...');
            $('#epi-validate-btn').prop('disabled', true);

            // Show progress
            $('.epi-results').removeClass('active');
            $('.epi-progress-container').addClass('active');
            this.updateProgress(0, 'Starting import...');

            // AJAX call to import
            $.ajax({
                url: epiAdminData.ajaxurl,
                type: 'POST',
                data: {
                    action: 'epi_import_json',
                    nonce: epiAdminData.nonce,
                    json_url: jsonUrl,
                    import_mode: importMode
                },
                success: function(response) {
                    $btn.prop('disabled', false).html('🚀 Start Import');
                    $('#epi-validate-btn').prop('disabled', false);

                    if (response.success) {
                        this.showImportSuccess(response.data);
                    } else {
                        this.showImportError(response.data);
                    }

                    $('.epi-progress-container').removeClass('active');
                }.bind(this),
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false).html('🚀 Start Import');
                    $('#epi-validate-btn').prop('disabled', false);
                    this.showAlert('Network error: ' + error, 'error');
                    $('.epi-progress-container').removeClass('active');
                }.bind(this),
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    // Track progress if available
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = (evt.loaded / evt.total) * 100;
                            this.updateProgress(percentComplete, 'Uploading data...');
                        }
                    }.bind(this), false);
                    return xhr;
                }.bind(this)
            });

            // Simulate progress updates (since we can't track server-side progress easily)
            this.simulateProgress();
        },

        /**
         * Simulate Progress
         */
        simulateProgress: function() {
            var progress = 0;
            var interval = setInterval(function() {
                progress += Math.random() * 15;
                if (progress > 90) {
                    progress = 90;
                    clearInterval(interval);
                }
                this.updateProgress(progress, 'Importing prompts...');
            }.bind(this), 500);
        },

        /**
         * Update Progress
         */
        updateProgress: function(percent, message) {
            percent = Math.min(Math.round(percent), 100);
            $('.epi-progress-fill').css('width', percent + '%').text(percent + '%');
            $('.epi-progress-info').text(message || 'Processing...');
        },

        /**
         * Show Import Success
         */
        showImportSuccess: function(data) {
            this.updateProgress(100, 'Import complete!');

            var html = '<div class="epi-alert epi-alert-success">';
            html += '<strong>🎉 Import Successful!</strong><br>';
            html += data.message || 'All prompts imported successfully.';
            html += '</div>';

            html += '<div class="epi-stats">';
            html += '<div class="epi-stat-box">';
            html += '  <div class="epi-stat-number">' + (data.imported || 0) + '</div>';
            html += '  <div class="epi-stat-label">Imported</div>';
            html += '</div>';
            html += '<div class="epi-stat-box">';
            html += '  <div class="epi-stat-number">' + (data.updated || 0) + '</div>';
            html += '  <div class="epi-stat-label">Updated</div>';
            html += '</div>';
            html += '<div class="epi-stat-box">';
            html += '  <div class="epi-stat-number">' + (data.skipped || 0) + '</div>';
            html += '  <div class="epi-stat-label">Skipped</div>';
            html += '</div>';
            html += '<div class="epi-stat-box">';
            html += '  <div class="epi-stat-number">' + (data.errors || 0) + '</div>';
            html += '  <div class="epi-stat-label">Errors</div>';
            html += '</div>';
            html += '</div>';

            if (data.error_messages && data.error_messages.length > 0) {
                html += '<div class="epi-alert epi-alert-warning" style="margin-top: 1rem;">';
                html += '<strong>⚠️ Some errors occurred:</strong><ul style="margin: 0.5rem 0 0 1.5rem;">';
                data.error_messages.forEach(function(error) {
                    html += '<li>' + error + '</li>';
                });
                html += '</ul></div>';
            }

            html += '<div style="margin-top: 1.5rem; text-align: center;">';
            html += '<a href="edit.php?post_type=ai_prompt" class="epi-btn epi-btn-primary">View All Prompts</a>';
            html += '</div>';

            $('.epi-results').html(html).addClass('active');

            // Confetti if available
            if (typeof confetti !== 'undefined') {
                confetti({
                    particleCount: 150,
                    spread: 70,
                    origin: { y: 0.6 }
                });
            }
        },

        /**
         * Show Import Error
         */
        showImportError: function(data) {
            var html = '<div class="epi-alert epi-alert-error">';
            html += '<strong>❌ Import Failed!</strong><br>';
            html += data.message || 'An error occurred during import.';
            html += '</div>';

            if (data.errors && data.errors.length > 0) {
                html += '<div class="epi-validation-results">';
                data.errors.forEach(function(error) {
                    html += '<div class="epi-validation-item error">';
                    html += '  <div class="epi-validation-icon">❌</div>';
                    html += '  <div>' + error + '</div>';
                    html += '</div>';
                });
                html += '</div>';
            }

            $('.epi-results').html(html).addClass('active');
        },

        /**
         * Show Alert
         */
        showAlert: function(message, type) {
            type = type || 'info';
            var alertClass = 'epi-alert-' + type;

            var $alert = $('<div class="epi-alert ' + alertClass + '">' + message + '</div>');
            $('.epi-results').html($alert).addClass('active');

            setTimeout(function() {
                $alert.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        if ($('#epic-prompts-importer-page').length) {
            EpicPromptsImporter.init();
        }
    });

})(jQuery);
