<?php
/**
 * DigitalKasur.com - Database Connection Test
 * Tests DB connectivity and verifies all required tables exist
 * 
 * Usage: https://digitalkasur.com/test-connection.php
 * DELETE THIS FILE AFTER DEPLOYMENT!
 */

// Start timing
$startTime = microtime(true);

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB Connection Test - DigitalKasur</title>
    <style>
        body { font-family: "Courier New", monospace; background: #1a1a2e; color: #e0e0e0; padding: 2rem; line-height: 1.6; }
        h1 { color: #00d4ff; border-bottom: 2px solid #00d4ff; padding-bottom: 0.5rem; }
        h2 { color: #f59e0b; margin-top: 2rem; }
        .success { color: #10b981; font-weight: bold; }
        .error { color: #ef4444; font-weight: bold; }
        .warning { color: #f59e0b; font-weight: bold; }
        .info { color: #3b82f6; }
        .section { background: #16213e; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem; border-left: 4px solid #00d4ff; }
        .section.error-section { border-left-color: #ef4444; }
        .section.success-section { border-left-color: #10b981; }
        table { border-collapse: collapse; width: 100%; margin-top: 0.5rem; }
        th, td { border: 1px solid #333; padding: 0.5rem 1rem; text-align: left; }
        th { background: #0f3460; color: #00d4ff; }
        .badge { padding: 2px 8px; border-radius: 4px; font-size: 0.85em; }
        .badge-ok { background: rgba(16,185,129,0.2); color: #10b981; }
        .badge-missing { background: rgba(239,68,68,0.2); color: #ef4444; }
        .badge-empty { background: rgba(245,158,11,0.2); color: #f59e0b; }
        .footer { margin-top: 2rem; color: #666; font-size: 0.85rem; border-top: 1px solid #333; padding-top: 1rem; }
        .security-warning { background: rgba(239,68,68,0.1); border: 1px solid #ef4444; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <h1>🔌 DigitalKasur - Database Connection Test</h1>
    <div class="security-warning">
        <span class="error">⚠️ SECURITY WARNING:</span> Delete this file after testing! It exposes database structure information.
    </div>';

// ============================================================
// TEST 1: CONFIG FILE
// ============================================================
echo '<div class="section">';
echo '<h2>1. Configuration File</h2>';

if (file_exists(__DIR__ . '/config.php')) {
    echo '<p class="success">✅ config.php found</p>';
    require_once __DIR__ . '/config.php';
    
    echo '<table>';
    echo '<tr><th>Setting</th><th>Value</th><th>Status</th></tr>';
    
    $configs = [
        ['DB_HOST', DB_HOST, 'non-empty'],
        ['DB_NAME', DB_NAME, 'non-empty'],
        ['DB_USER', DB_USER, 'non-empty'],
        ['SITE_URL', SITE_URL, 'non-empty'],
        ['APP_VERSION', APP_VERSION, 'non-empty'],
        ['display_errors', ini_get('display_errors'), 'should be 0 in production'],
    ];
    
    foreach ($configs as $cfg) {
        $status = !empty($cfg[1]) ? '<span class="badge badge-ok">OK</span>' : '<span class="badge badge-missing">EMPTY</span>';
        echo "<tr><td>{$cfg[0]}</td><td>" . htmlspecialchars($cfg[1]) . "</td><td>{$status}</td></tr>";
    }
    
    echo '</table>';
    
    // Check display_errors
    if (ini_get('display_errors') == '1' || ini_get('display_errors') === 'On') {
        echo '<p class="warning">⚠️ display_errors is ON. Should be OFF in production!</p>';
    } else {
        echo '<p class="success">✅ display_errors is OFF (production-safe)</p>';
    }
} else {
    echo '<p class="error">❌ config.php NOT FOUND!</p>';
    echo '</div>';
    echo '<div class="footer">Test aborted. Execution time: ' . round(microtime(true) - $startTime, 3) . 's</div>';
    echo '</body></html>';
    exit;
}
echo '</div>';

// ============================================================
// TEST 2: DATABASE CONNECTION
// ============================================================
echo '<div class="section">';
echo '<h2>2. Database Connection</h2>';

try {
    require_once __DIR__ . '/db.php';
    
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    echo '<p class="success">✅ Database connection successful!</p>';
    
    // Get MySQL version
    $version = $conn->query("SELECT VERSION()")->fetchColumn();
    echo '<p class="info">MySQL Version: ' . htmlspecialchars($version) . '</p>';
    
    // Get database size
    $dbSize = $conn->query("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "'")->fetchColumn();
    echo '<p class="info">Database Size: ' . ($dbSize ?: '0') . ' MB</p>';
    
    // Get charset
    $charset = $conn->query("SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'")->fetch();
    if ($charset) {
        echo '<p class="info">Charset: ' . $charset['DEFAULT_CHARACTER_SET_NAME'] . ' | Collation: ' . $charset['DEFAULT_COLLATION_NAME'] . '</p>';
    }
    
} catch (Exception $e) {
    echo '<p class="error">❌ Database connection FAILED!</p>';
    echo '<p class="error">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
    echo '<div class="footer">Test aborted. Execution time: ' . round(microtime(true) - $startTime, 3) . 's</div>';
    echo '</body></html>';
    exit;
}
echo '</div>';

// ============================================================
// TEST 3: TABLE EXISTENCE & ROW COUNTS
// ============================================================
echo '<div class="section">';
echo '<h2>3. Table Verification</h2>';

$requiredTables = [
    'users',
    'cities',
    'categories',
    'events',
    'businesses',
    'business_reviews',
    'jobs',
    'job_applications',
    'news',
    'blog',
    'contact_messages',
    'payments',
    'newsletter_subscribers',
    'settings',
    'event_registrations',
];

echo '<table>';
echo '<tr><th>Table</th><th>Exists?</th><th>Row Count</th><th>Engine</th><th>Collation</th></tr>';

$allTablesExist = true;
$totalRows = 0;

foreach ($requiredTables as $table) {
    try {
        $exists = $conn->query("SHOW TABLES LIKE '{$table}'")->rowCount() > 0;
        
        if ($exists) {
            $count = $conn->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
            $totalRows += $count;
            $info = $conn->query("SELECT ENGINE, TABLE_COLLATION FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DB_NAME . "' AND TABLE_NAME = '{$table}'")->fetch();
            
            $countBadge = $count > 0 ? '<span class="badge badge-ok">' . number_format($count) . '</span>' : '<span class="badge badge-empty">0 (empty)</span>';
            $engine = $info ? $info['ENGINE'] : '-';
            $collation = $info ? $info['TABLE_COLLATION'] : '-';
            
            echo "<tr><td>{$table}</td><td class='success'>✅ Yes</td><td>{$countBadge}</td><td>{$engine}</td><td>{$collation}</td></tr>";
        } else {
            $allTablesExist = false;
            echo "<tr><td>{$table}</td><td class='error'>❌ Missing!</td><td>-</td><td>-</td><td>-</td></tr>";
        }
    } catch (Exception $e) {
        $allTablesExist = false;
        echo "<tr><td>{$table}</td><td class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</td><td>-</td><td>-</td><td>-</td></tr>";
    }
}

echo '</table>';

if ($allTablesExist) {
    echo '<p class="success">✅ All ' . count($requiredTables) . ' required tables exist!</p>';
    echo '<p class="info">Total rows across all tables: ' . number_format($totalRows) . '</p>';
    echo '</div>';
} else {
    echo '<p class="error">❌ Some tables are missing! Import database.sql to create them.</p>';
    echo '</div>';
}

// ============================================================
// TEST 4: CRITICAL DATA CHECKS
// ============================================================
echo '<div class="section">';
echo '<h2>4. Critical Data Checks</h2>';

echo '<table>';
echo '<tr><th>Check</th><th>Result</th></tr>';

// Admin user exists
$adminCount = DB::count('users', "role = ?", ['admin']);
echo '<tr><td>Admin user exists</td><td>' . ($adminCount > 0 ? '<span class="success">✅ Yes (' . $adminCount . ')</span>' : '<span class="error">❌ No admin user!</span>') . '</td></tr>';

// Cities exist
$cityCount = DB::count('cities', "is_active = 1");
echo '<tr><td>Active cities</td><td>' . ($cityCount > 0 ? '<span class="success">✅ ' . $cityCount . ' cities</span>' : '<span class="warning">⚠️ No active cities</span>') . '</td></tr>';

// Categories exist
$catCount = DB::count('categories', "is_active = 1");
echo '<tr><td>Active categories</td><td>' . ($catCount > 0 ? '<span class="success">✅ ' . $catCount . ' categories</span>' : '<span class="warning">⚠️ No active categories</span>') . '</td></tr>';

// Settings exist
$settingsCount = DB::count('settings');
echo '<tr><td>Settings configured</td><td>' . ($settingsCount > 0 ? '<span class="success">✅ ' . $settingsCount . ' settings</span>' : '<span class="warning">⚠️ No settings</span>') . '</td></tr>';

// Upload directories
$uploadDirs = ['uploads/blog', 'uploads/businesses', 'uploads/cities', 'uploads/events', 'uploads/news', 'uploads/users'];
$missingDirs = [];
foreach ($uploadDirs as $dir) {
    if (!is_dir(__DIR__ . '/' . $dir)) {
        $missingDirs[] = $dir;
    }
}
echo '<tr><td>Upload directories</td><td>' . (empty($missingDirs) ? '<span class="success">✅ All exist</span>' : '<span class="warning">⚠️ Missing: ' . implode(', ', $missingDirs) . '</span>') . '</td></tr>';

// .htaccess files
$rootHtaccess = file_exists(__DIR__ . '/.htaccess');
$uploadHtaccess = file_exists(__DIR__ . '/uploads/.htaccess');
echo '<tr><td>Root .htaccess</td><td>' . ($rootHtaccess ? '<span class="success">✅ Present</span>' : '<span class="warning">⚠️ Missing</span>') . '</td></tr>';
echo '<tr><td>uploads/.htaccess</td><td>' . ($uploadHtaccess ? '<span class="success">✅ Present</span>' : '<span class="error">❌ Missing - PHP execution not blocked!</span>') . '</td></tr>';

echo '</table>';
echo '</div>';

// ============================================================
// TEST 5: PHP EXTENSIONS
// ============================================================
echo '<div class="section">';
echo '<h2>5. PHP Environment</h2>';

echo '<table>';
echo '<tr><th>Setting</th><th>Value</th></tr>';

$phpChecks = [
    ['PHP Version', phpversion()],
    ['PDO MySQL', extension_loaded('pdo_mysql') ? '✅ Loaded' : '❌ Missing'],
    ['mbstring', extension_loaded('mbstring') ? '✅ Loaded' : '❌ Missing'],
    ['json', extension_loaded('json') ? '✅ Loaded' : '❌ Missing'],
    ['gd', extension_loaded('gd') ? '✅ Loaded' : '⚠️ Missing (image processing)'],
    ['curl', extension_loaded('curl') ? '✅ Loaded' : '⚠️ Missing (API calls)'],
    ['openssl', extension_loaded('openssl') ? '✅ Loaded' : '⚠️ Missing (HTTPS)'],
    ['fileinfo', extension_loaded('fileinfo') ? '✅ Loaded' : '⚠️ Missing (file uploads)'],
    ['session', extension_loaded('session') ? '✅ Loaded' : '❌ Missing'],
    ['upload_max_filesize', ini_get('upload_max_filesize')],
    ['post_max_size', ini_get('post_max_size')],
    ['max_execution_time', ini_get('max_execution_time')],
    ['memory_limit', ini_get('memory_limit')],
    ['display_errors', ini_get('display_errors')],
];

foreach ($phpChecks as $check) {
    echo "<tr><td>{$check[0]}</td><td>{$check[1]}</td></tr>";
}

echo '</table>';
echo '</div>';

// ============================================================
// SUMMARY
// ============================================================
$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 3);

echo '<div class="section success-section">';
echo '<h2>📋 Summary</h2>';
echo '<p>Execution time: <strong>' . $executionTime . 's</strong></p>';
echo '<p>Database: <strong>' . DB_NAME . '</strong> on <strong>' . DB_HOST . '</strong></p>';

if ($allTablesExist && $adminCount > 0) {
    echo '<p class="success">✅ System appears to be properly configured!</p>';
} else {
    echo '<p class="warning">⚠️ Some issues detected. Review the results above.</p>';
}

echo '</div>';

echo '<div class="footer">
    <p>DigitalKasur v' . APP_VERSION . ' | Test generated at ' . date('Y-m-d H:i:s') . ' | ⚠️ DELETE THIS FILE after testing!</p>
</div>';

echo '</body></html>';
?>
