<div class="card">
    <div class="page-header">
        <h1>Photo Gallery</h1>
        <a href="<?= url('my-gallery/upload') ?>" class="btn">Upload Photo</a>
    </div>
    <p>Browse public photo albums.</p>

    <div class="album-grid">
        <?php if (!empty($albums)): ?>
            <?php foreach ($albums as $album): ?>
                <a href="<?= url('gallery/album/' . $album['id']) ?>" class="album-item">
                    <div class="album-thumbnail">
                        <!-- A placeholder thumbnail -->
                        <div class="thumbnail-placeholder"></div>
                    </div>
                    <div class="album-info">
                        <h3><?= htmlspecialchars($album['name']) ?></h3>
                        <span>by <?= htmlspecialchars($album['username']) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No public albums have been created yet.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.page-header h1 { margin: 0; }
.album-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-top: 20px; }
.album-item { display: block; text-decoration: none; color: #fff; background: rgba(0,0,0,0.2); border-radius: 8px; overflow: hidden; transition: transform 0.2s; }
.album-item:hover { transform: translateY(-5px); }
.album-thumbnail .thumbnail-placeholder { height: 150px; background-color: rgba(255,255,255,0.1); }
.album-info { padding: 15px; }
.album-info h3 { margin: 0 0 5px 0; }
.album-info span { font-size: 0.9em; color: rgba(255,255,255,0.7); }
</style>
