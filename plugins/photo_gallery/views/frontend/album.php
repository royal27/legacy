<a href="<?= url('gallery') ?>"> &laquo; Back to Gallery</a>
<h1><?= htmlspecialchars($title) ?></h1>
<!-- <p><?= htmlspecialchars($album['description']) ?></p> -->

<div class="photo-grid">
    <?php if (!empty($photos)): ?>
        <?php foreach ($photos as $photo): ?>
            <a href="<?= url('gallery/photo/' . $photo['id']) ?>" class="photo-item">
                <img src="<?= htmlspecialchars($photo['filepath']) ?>" alt="<?= htmlspecialchars($photo['title']) ?>">
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <p>There are no photos in this album yet.</p>
    <?php endif; ?>
</div>

<style>
.photo-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px;
    margin-top: 20px;
}
.photo-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid transparent;
    transition: border-color 0.2s;
}
.photo-item img:hover {
    border-color: var(--accent-color);
}
</style>
