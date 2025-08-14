<h1>Forum</h1>
<p>This is the main page of the forum. Categories and forums will be listed here.</p>

<div class="forum-list">
    <?php if (isset($forums) && !empty($forums)): ?>
        <?php foreach ($forums as $category): ?>
            <div class="forum-category">
                <div class="category-header">
                    <h2><?= htmlspecialchars($category['name']) ?></h2>
                </div>
                <?php if (!empty($category['subforums'])): ?>
                    <?php foreach ($category['subforums'] as $forum): ?>
                        <div class="forum-item">
                            <div class="forum-info">
                                <h3><a href="/forum/<?= $forum['id'] ?>"><?= htmlspecialchars($forum['name']) ?></a></h3>
                                <p><?= htmlspecialchars($forum['description']) ?></p>
                            </div>
                            <div class="forum-stats">
                                <!-- Stats will be added later -->
                                Topics: 0<br>
                                Posts: 0
                            </div>
                            <div class="forum-last-post">
                                <!-- Last post info will be added later -->
                                No posts
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="forum-item">
                        <p>No forums in this category yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No forum categories have been created yet.</p>
    <?php endif; ?>
</div>

<style>
.forum-list { margin-top: 20px; }
.forum-category { margin-bottom: 20px; }
.category-header {
    background-color: rgba(0,0,0,0.3);
    padding: 10px 15px;
    border-radius: 8px 8px 0 0;
}
.category-header h2 {
    margin: 0;
    font-size: 1.2em;
}
.forum-item {
    display: flex;
    align-items: center;
    background-color: rgba(0,0,0,0.1);
    padding: 15px;
    border: 1px solid rgba(255,255,255,0.1);
    border-top: none;
}
.forum-item:last-child {
    border-radius: 0 0 8px 8px;
}
.forum-info {
    flex: 1;
}
.forum-info h3 {
    margin: 0 0 5px 0;
}
.forum-info p {
    margin: 0;
    font-size: 0.9em;
    color: rgba(255,255,255,0.7);
}
.forum-stats {
    width: 100px;
    text-align: center;
    font-size: 0.9em;
}
.forum-last-post {
    width: 150px;
    font-size: 0.9em;
    text-align: right;
}
</style>
