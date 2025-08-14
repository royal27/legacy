<div class="card">
    <h1>Upload a New File</h1>
    <p>Select a file to upload. It will be reviewed by an administrator before it becomes public.</p>

    <form action="/downloads/save" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">File Title</label>
            <input type="text" name="title" id="title" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="download_file">File</label>
            <input type="file" name="download_file" id="download_file" required>
        </div>

        <button type="submit" class="btn">Upload File</button>
        <a href="/downloads" class="btn-link">Cancel</a>
    </form>
</div>

<style>
/* Reusing styles for consistency */
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
input[type="text"], input[type="file"], textarea, select {
    width: 100%;
    padding: 12px;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    color: #fff;
    font-size: 16px;
    box-sizing: border-box;
}
input[type="file"] { padding: 10px; }
.btn { display: inline-block; padding: 12px 20px; background: var(--primary-color); border: none; border-radius: 8px; color: #fff; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; }
.btn-link { display: inline-block; margin-left: 10px; color: var(--accent-color); }
</style>
