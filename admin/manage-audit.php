<?php include('partials/menu.php'); ?>
<?php include('partials/admin-check.php'); ?>

<div class="main-content">
    <div class="wrapper">
        <h1>Audit Logs</h1>

        <p>Showing latest 200 audit entries.</p>

        <?php
        $rows = Audit::fetchRecent(200);

        // Helper: safely format audit values (handles nulls and pretty-prints JSON)
        if (!function_exists('fmt_audit')) {
            function fmt_audit($v) {
                if ($v === null || $v === '') return '';
                // Ensure we have a string
                if (!is_string($v)) $v = (string)$v;
                // Try to decode JSON and pretty-print if valid
                $decoded = json_decode($v, true);
                if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                    return htmlspecialchars(json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                }
                return htmlspecialchars($v);
            }
        }

        // Export CSV
        if (isset($_GET['export']) && $_GET['export'] == '1') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="audit_export.csv"');
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id','created_at','user_type','user_id','username','action','ip','user_agent','url']);
            foreach ($rows as $r) {
                fputcsv($out, [$r['id'],$r['created_at'],$r['user_type'],$r['user_id'],$r['username'],$r['action'],$r['ip'],$r['user_agent'],$r['url']]);
            }
            fclose($out);
            exit();
        }
        ?>

        <div style="margin-bottom:10px;">
            <a href="?export=1" class="btn-secondary">Export CSV</a>
        </div>

        <div class="table-responsive">
            <table class="tbl-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars((($row['user_type'] ?? '') ? $row['user_type'] : '') . ' ' . ($row['username'] ?? $row['user_id'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($row['action'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['ip'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('partials/footer.php'); ?>