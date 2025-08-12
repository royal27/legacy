<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1 class="gradient-text"><?php echo htmlspecialchars($data['title']); ?></h1>
    <a href="/admin/links/add" class="btn">Add New Link</a>
</div>

<p>Here you can manage the main navigation menu. Drag and drop to reorder is not yet implemented.</p>

<div class="links-list">
    <?php
    function display_links($links, $level = 0) {
        echo '<ul style="list-style: none; padding-left: ' . ($level * 20) . 'px;">';
        foreach ($links as $link) {
            echo '<li style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 10px; margin-bottom: 5px; display: flex; justify-content: space-between; align-items: center;">';
            echo '<span>';
            echo '<strong>' . htmlspecialchars($link['translations']['en'] ?? '[No Title]') . '</strong>';
            echo ' (' . htmlspecialchars($link['url']) . ')';
            echo '</span>';
            echo '<span>';
            echo '<a href="/admin/links/edit/' . $link['id'] . '" class="btn">Edit</a> ';
            echo '<a href="/admin/links/delete/' . $link['id'] . '" class="btn" onclick="return confirm(\'Are you sure?\');" style="background: var(--color-accent);">Delete</a>';
            echo '</span>';
            echo '</li>';

            if (!empty($link['children'])) {
                display_links($link['children'], $level + 1);
            }
        }
        echo '</ul>';
    }

    display_links($data['links']);
    ?>
</div>
