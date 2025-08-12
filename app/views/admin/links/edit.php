<h1 class="gradient-text"><?php echo htmlspecialchars($data['title']); ?></h1>
<p>Edit the navigation link.</p>

<form action="/admin/links/edit/<?php echo $data['link']['id']; ?>" method="post" style="max-width: 600px;">
    <label for="url">URL (e.g., /pages/about): <sup>*</sup></label>
    <input type="text" name="url" id="url" value="<?php echo htmlspecialchars($data['link']['url']); ?>" required>

    <label for="parent_id">Parent Link:</label>
    <select name="parent_id" id="parent_id">
        <option value="0">-- No Parent --</option>
        <?php
        function display_link_options($links, $level = 0, $current_link) {
            foreach ($links as $link) {
                // Prevent a link from being its own parent
                if ($link['id'] == $current_link['id']) continue;

                $selected = ($link['id'] == $current_link['parent_id']) ? 'selected' : '';
                echo '<option value="' . $link['id'] . '" ' . $selected . '>' . str_repeat('&nbsp;&nbsp;', $level) . htmlspecialchars($link['translations']['en'] ?? '[No Title]') . '</option>';
                if (!empty($link['children'])) {
                    display_link_options($link['children'], $level + 1, $current_link);
                }
            }
        }
        display_link_options($data['links'], 0, $data['link']);
        ?>
    </select>

    <label for="display_order">Display Order:</label>
    <input type="number" name="display_order" id="display_order" value="<?php echo htmlspecialchars($data['link']['display_order']); ?>" required>

    <fieldset style="margin-top: 1rem; border: 1px solid var(--border-color); padding: 1rem;">
        <legend>Link Titles (by language)</legend>
        <?php foreach ($data['languages'] as $language): ?>
            <?php $title = $data['link']['translations'][$language['code']] ?? ''; ?>
            <label for="title_<?php echo $language['code']; ?>">Title in <?php echo htmlspecialchars($language['name']); ?>:</label>
            <input type="text" name="translations[<?php echo $language['code']; ?>]" id="title_<?php echo $language['code']; ?>" value="<?php echo htmlspecialchars($title); ?>">
        <?php endforeach; ?>
    </fieldset>

    <button type="submit" class="btn" style="margin-top: 1rem;">Update Link</button>
    <a href="/admin/links" style="margin-left: 1rem;">Cancel</a>
</form>
