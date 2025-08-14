<div class="card">
    <div class="page-header">
        <h1>Downloads</h1>
        <a href="/downloads/upload" class="btn">Upload File</a>
    </div>
    <p>Browse through our download categories.</p>

    <?php $success = \App\Core\Session::getFlash('success'); ?>
    <?php if ($success): ?>
        <div class="notice success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="category-list">
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <a href="/downloads/category/<?= $category['id'] ?>" class="category-item">
                    <h3><?= htmlspecialchars($category['name']) ?></h3>
                    <p><?= htmlspecialchars($category['description']) ?></p>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No download categories have been created yet.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.page-header h1 { margin: 0; }
.category-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
.category-item {
    display: block;
    padding: 20px;
    background: rgba(0,0,0,0.2);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    text-decoration: none;
    color: #fff;
    transition: background-color 0.2s;
}
.category-item:hover { background-color: rgba(255,255,255,0.1); }
.category-item h3 { margin: 0 0 10px 0; color: var(--accent-color); }
.category-item p { margin: 0; font-size: 0.9em; color: rgba(255,255,255,0.7); }
</style>
