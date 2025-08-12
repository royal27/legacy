<h1 class="gradient-text"><?php echo htmlspecialchars($data['title']); ?></h1>
<p>Create a new navigation link.</p>

<form action="/admin/links/add" method="post" style="max-width: 600px;">
    <label for="url">URL (e.g., /pages/about): <sup>*</sup></label>
    <input type="text" name="url" id="url" required>

    <label for="parent_id">Parent Link:</label>
    <select name="parent_id" id="parent_id">
        <option value="0">-- No Parent --</option>
        <?php
        function display_link_options($links, $level = 0) {
            foreach ($links as $link) {
                echo '<option value="' . $link['id'] . '">' . str_repeat('&nbsp;&nbsp;', $level) . htmlspecialchars($link['translations']['en'] ?? '[No Title]') . '</option>';
                if (!empty($link['children'])) {
                    display_link_options($link['children'], $level + 1);
                }
            }
        }
        display_link_options($data['links']);
        ?>
    </select>

    <label for="display_order">Display Order:</label>
    <input type="number" name="display_order" id="display_order" value="0" required>

    <fieldset style="margin-top: 1rem; border: 1px solid var(--border-color); padding: 1rem;">
        <legend>Link Titles (by language)</legend>
        <?php foreach ($data['languages'] as $language): ?>
            <label for="title_<?php echo $language['code']; ?>">Title in <?php echo htmlspecialchars($language['name']); ?>:</label>
            <input type="text" name="translations[<?php echo $language['code']; ?>]" id="title_<?php echo $language['code']; ?>">
        <?php endforeach; ?>
    </fieldset>

    <button type="submit" class="btn" style="margin-top: 1rem;">Create Link</button>
    <a href="/admin/links" style="margin-left: 1rem;">Cancel</a>
</form>
