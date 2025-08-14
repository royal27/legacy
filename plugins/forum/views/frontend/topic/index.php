<a href="/forum"> &laquo; Back to Forum Index</a>
<h1><?= htmlspecialchars($forum['name']) ?></h1>
<p><?= htmlspecialchars($forum['description']) ?></p>

<div class="topic-actions">
    <a href="/forum/topic/new/<?= $forum['id'] ?>" class="btn">New Topic</a>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Topic</th>
            <th>Author</th>
            <th>Replies</th>
            <th>Last Post</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($topics)): ?>
            <?php foreach ($topics as $topic): ?>
                <tr>
                    <td>
                        <?php if ($topic['is_sticky']): ?><strong>Sticky:</strong><?php endif; ?>
                        <a href="/topic/<?= $topic['id'] ?>"><?= htmlspecialchars($topic['title']) ?></a>
                    </td>
                    <td><?= htmlspecialchars($topic['author_name']) ?></td>
                    <td>0</td> <!-- Placeholder -->
                    <td>N/A</td> <!-- Placeholder -->
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No topics in this forum yet.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<style>
/* Reusing styles from admin for consistency */
.data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.data-table th, .data-table td { padding: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); text-align: left; }
.data-table th { background-color: rgba(0, 0, 0, 0.2); }
.topic-actions { margin: 20px 0; }
.btn { display: inline-block; padding: 12px 20px; background: var(--primary-color); border: none; border-radius: 8px; color: #fff; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; }
</style>
