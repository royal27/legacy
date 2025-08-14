<div class="card">
    <div class="page-header">
        <h1>All Support Tickets</h1>
    </div>
    <p>View and manage all user support tickets.</p>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>User</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Last Updated</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($tickets)): ?>
                <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td>#<?= $ticket['id'] ?></td>
                        <td><?= htmlspecialchars($ticket['title']) ?></td>
                        <td><?= htmlspecialchars($ticket['username']) ?></td>
                        <td><span class="status-badge status-<?= strtolower($ticket['status']) ?>"><?= htmlspecialchars($ticket['status']) ?></span></td>
                        <td><?= htmlspecialchars($ticket['priority']) ?></td>
                        <td><?= date('Y-m-d H:i', strtotime($ticket['last_updated_at'])) ?></td>
                        <td>
                            <a href="/admin/tickets/<?= $ticket['id'] ?>" class="btn-action view">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No support tickets found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
/* Reusing styles for consistency */
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.page-header h1 { margin: 0; }
.data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.data-table th, .data-table td { padding: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); text-align: left; }
.data-table th { background-color: rgba(0, 0, 0, 0.2); }
.btn-action.view { background-color: var(--secondary-color); color: #fff; padding: 5px 10px; border-radius: 5px; text-decoration: none; }
.status-badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8em; color: #fff; }
.status-open { background-color: #3498db; }
.status-pending { background-color: #f1c40f; }
.status-resolved, .status-closed { background-color: #2ecc71; }
</style>
