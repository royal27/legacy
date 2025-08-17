<div class="card">
    <h1>Create New Support Ticket</h1>
    <p>Please describe your issue in detail. Our team will get back to you as soon as possible.</p>

    <form action="<?= url('tickets/create') ?>" method="POST">
        <div class="form-group">
            <label for="title">Subject</label>
            <input type="text" name="title" id="title" required>
        </div>

        <div class="form-group">
            <label for="priority">Priority</label>
            <select name="priority" id="priority">
                <option value="Normal">Normal</option>
                <option value="High">High</option>
                <option value="Low">Low</option>
            </select>
        </div>

        <div class="form-group">
            <label for="content">Describe your issue</label>
            <textarea name="content" id="content" rows="10" required></textarea>
        </div>

        <button type="submit" class="btn">Submit Ticket</button>
        <a href="<?= url('tickets') ?>" class="btn-link">Cancel</a>
    </form>
</div>

<style>
/* Reusing styles for consistency */
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
input[type="text"], textarea, select {
    width: 100%;
    padding: 12px;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    color: #fff;
    font-size: 16px;
    box-sizing: border-box;
}
.btn { display: inline-block; padding: 12px 20px; background: var(--primary-color); border: none; border-radius: 8px; color: #fff; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; }
.btn-link { display: inline-block; margin-left: 10px; color: var(--accent-color); }
</style>
