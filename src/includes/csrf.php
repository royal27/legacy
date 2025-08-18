<?php
/**
 * CSRF (Cross-Site Request Forgery) Protection Functions
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generates a CSRF token if one doesn't already exist in the session.
 * This should be called on pages that will display a form.
 */
function csrf_generate_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

/**
 * Validates the submitted CSRF token against the one in the session.
 * Call this at the beginning of form processing logic.
 * If validation fails, the script will terminate.
 */
function csrf_validate_token() {
    // Check only for POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return; // Don't validate on GET requests
    }

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        // Clear the invalid token from the session and terminate
        unset($_SESSION['csrf_token']);
        // In a real app, you might want to log this attempt.
        die('CSRF token validation failed. The request has been blocked.');
    }

    // Once a token is used, it should be unset to prevent reuse (one-time token)
    unset($_SESSION['csrf_token']);
}

/**
 * Returns the HTML hidden input field for the CSRF token.
 *
 * @return string The HTML input field.
 */
function csrf_input(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token'] ?? '') . '">';
}
?>
