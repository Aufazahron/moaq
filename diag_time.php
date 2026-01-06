<?php
require 'vendor/autoload.php';
$app = \Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();
$latest = $db->table('sensors')->orderBy('created_at', 'DESC')->limit(1)->get()->getRowArray();

echo "--- TIME DEBUG ---\n";
echo "1. Server date() Now     : " . date('Y-m-d H:i:s') . "\n";
echo "2. CI4 Time Jakarta Now : " . \CodeIgniter\I18n\Time::now('Asia/Jakarta')->toDateTimeString() . "\n";
echo "3. CI4 Time UTC Now     : " . \CodeIgniter\I18n\Time::now('UTC')->toDateTimeString() . "\n";
echo "4. Database Latest Data  : " . ($latest ? $latest['created_at'] : 'EMPTY') . "\n";
echo "------------------\n";

if ($latest) {
    $now = \CodeIgniter\I18n\Time::now('Asia/Jakarta');
    $startTime = $now->subHours(1)->toDateTimeString();
    echo "Query 1H Start Time: " . $startTime . "\n";
    
    $builder = $db->table('sensors')->where('created_at >=', $startTime);
    echo "SQL Query 1H Sample: " . $builder->getCompiledSelect() . "\n";
    
    $count = $db->table('sensors')->where('created_at >=', $startTime)->countAllResults();
    echo "Count 1H Results: " . $count . "\n";
}
