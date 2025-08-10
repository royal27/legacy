document.addEventListener('DOMContentLoaded', () => {
    // --- Toastr Configuration ---
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        // Display a welcome toast for testing
        toastr.success('Page loaded successfully!', 'Welcome');
    }

    const body = document.body;

    // Start with a fade-in effect on page load
    body.classList.add('fade-in');

    // Add fade-out effect when navigating away
    const links = document.querySelectorAll('a');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');

            // Check if it's a regular, internal link and not a link to open in a new tab
            if (href && href.startsWith('/') && !this.target) {
                e.preventDefault(); // Stop the browser from navigating instantly
                body.classList.add('fade-out');

                // Wait for the animation to finish, then navigate
                setTimeout(() => {
                    window.location.href = href;
                }, 500); // This duration should match the CSS transition duration
            }
        });
    });

    // Handle back/forward button navigation
    window.addEventListener('pageshow', function(event) {
        // The pageshow event is fired when a session history entry is being traversed.
        // If the page was loaded from the cache, it might have the fade-out class still.
        if (event.persisted) {
            body.classList.remove('fade-out');
        }
    });

    // --- AJAX Test Handler ---
    const ajaxButton = document.getElementById('ajax-test-button');
    if (ajaxButton) {
        ajaxButton.addEventListener('click', function() {
            $.ajax({
                url: 'includes/ajax_handler.php?action=get_server_time',
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    toastr.info('Fetching data from server...');
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Server time: ' + response.data.server_time, 'AJAX Success!');
                    } else {
                        toastr.error(response.message || 'An unknown error occurred.', 'AJAX Error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    toastr.error('Request failed: ' + textStatus, 'AJAX Error');
                }
            });
        });
    }
});
