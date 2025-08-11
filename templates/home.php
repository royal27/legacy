<?php
// Prevent direct file access
if (!defined('APP_LOADED')) {
    http_response_code(403);
    die('Forbidden');
}
?>
<h1><?php echo trans('welcome_message', 'Welcome to the Homepage!'); ?></h1>
<p><?php echo trans('homepage_content', 'This is the main content area. We will build exciting things here soon.'); ?></p>
<p>Here are some sample buttons with the requested color scheme:</p>
<p>
    <a href="#" class="btn btn-primary">Primary Action (Blue)</a>
    <a href="#" class="btn btn-secondary">Secondary Action (Violet)</a>
    <a href="#" class="btn btn-accent">Accent Action (Red)</a>
</p>

<?php include __DIR__ . '/partials/plugin_links.php'; ?>

<h2>Sample Form</h2>
<form action="#" method="post">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" placeholder="Enter your name">
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email">
    </div>
    <div class="form-group">
        <label for="message">Message</label>
        <textarea id="message" name="message" rows="5" placeholder="Your message here..."></textarea>
    </div>
    <div class="form-group">
        <label for="options">Choose an option</label>
        <select id="options" name="options">
            <option value="1">Option 1 (Blue)</option>
            <option value="2">Option 2 (Violet)</option>
            <option value="3">Option 3 (Red)</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
