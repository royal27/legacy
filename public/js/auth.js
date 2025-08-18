$(document).ready(function() {
    // --- Registration Form Handler ---
    $('#register-form').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        var form = $(this);
        var url = form.attr('action');
        var formData = form.serialize();
        var submitButton = form.find('button[type="submit"]');
        var originalButtonText = submitButton.text();

        submitButton.prop('disabled', true).text('Processing...');

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    // Redirect to login page after a short delay
                    setTimeout(function() {
                        window.location.href = '/login.php';
                    }, 2000);
                } else {
                    // Display errors
                    if (response.errors && Array.isArray(response.errors)) {
                        response.errors.forEach(function(error) {
                            toastr.error(error);
                        });
                    } else {
                        toastr.error('An unknown error occurred.');
                    }
                    // Re-enable the button on error
                    submitButton.prop('disabled', false).text(originalButtonText);
                }
            },
            error: function(jqXHR) {
                toastr.error('An error occurred: ' + jqXHR.statusText);
                // Re-enable the button on error
                submitButton.prop('disabled', false).text(originalButtonText);
            }
        });
    });

    // --- Login Form Handler ---
    $('#login-form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var url = form.attr('action');
        var formData = form.serialize();
        var submitButton = form.find('button[type="submit"]');
        var originalButtonText = submitButton.text();

        submitButton.prop('disabled', true).text('Logging in...');

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    setTimeout(function() {
                        window.location.href = response.redirect_url;
                    }, 1000);
                } else {
                    toastr.error(response.message);
                    submitButton.prop('disabled', false).text(originalButtonText);
                }
            },
            error: function(jqXHR) {
                toastr.error('An error occurred: ' + jqXHR.statusText);
                submitButton.prop('disabled', false).text(originalButtonText);
            }
        });
    });
});
