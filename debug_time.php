<?php
require 'vendor/autoload.php';
// Boot CI4
$app = \Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();
$latest = $db->table('sensors')->orderBy('created_at', 'DESC')->limit(5)->get()->getResultArray();

echo "Current server time: " . date('Y-m-d H:i:s') . "\n";
echo "Application Timezone: " . config('App')->appTimezone . "\n";
echo "Latest data from sensors table:\n";
foreach ($latest as $row) {
    echo "ID: {$row['id']} | Created At: {$row['created_at']}\n";
}
