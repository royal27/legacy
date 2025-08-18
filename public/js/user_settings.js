$(document).ready(function() {
    // --- Reusable function to update all CSRF tokens on the page ---
    function updateCsrfTokens(newToken) {
        $('input[name="csrf_token"]').val(newToken);
    }

    // --- Generic AJAX Form Submission Handler ---
    function handleAjaxFormSubmit(form, successCallback) {
        var url = form.attr('action');
        // Add a flag to indicate an AJAX request
        var formData = form.serialize() + '&ajax=1';
        var submitButton = form.find('button[type="submit"]');
        var originalButtonText = submitButton.text();

        submitButton.prop('disabled', true).text('Saving...');

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    if (response.new_csrf_token) {
                        updateCsrfTokens(response.new_csrf_token);
                    }
                    if (successCallback) {
                        successCallback(form, response);
                    }
                } else {
                    if (response.errors && Array.isArray(response.errors)) {
                        response.errors.forEach(function(error) {
                            toastr.error(error);
                        });
                    } else {
                        toastr.error(response.message || 'An unknown error occurred.');
                    }
                }
            },
            error: function() {
                toastr.error('A server communication error occurred.');
            },
            complete: function() {
                submitButton.prop('disabled', false).text(originalButtonText);
            }
        });
    }

    // --- Profile Info Form Handler ---
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        handleAjaxFormSubmit($(this));
    });

    // --- Password Change Form Handler ---
    $('#password-form').on('submit', function(e) {
        e.preventDefault();
        handleAjaxFormSubmit($(this), function(form) {
            form.trigger('reset'); // Clear password fields on success
        });
    });
});
