<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Config/Boot/development.php';
$db = \Config\Database::connect();
$prefix = $db->DBPrefix ?? '';
// Create tables if missing
$db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "rooms (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT)");
$db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "items (id INTEGER PRIMARY KEY AUTOINCREMENT, room_id INTEGER, name TEXT)");
$db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "actions (id INTEGER PRIMARY KEY AUTOINCREMENT, item_id INTEGER, action_name TEXT)");
$db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "task_submissions (id INTEGER PRIMARY KEY AUTOINCREMENT, submission_code TEXT, date DATETIME, room_id INTEGER, visit_frequency INTEGER, revision_message TEXT, status TEXT, submitted_by INTEGER, verified_by INTEGER)");
$db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "task_submission_items (id INTEGER PRIMARY KEY AUTOINCREMENT, task_submission_id INTEGER, item_id INTEGER, cleaning_frequency INTEGER)");
$db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "task_submission_actions (id INTEGER PRIMARY KEY AUTOINCREMENT, task_submission_item_id INTEGER, action_id INTEGER, repetitions INTEGER)");

$roomId = $db->table($prefix . 'rooms')->insert(['name' => 'Debug Room']);
$rId = $db->insertID();
$itemA = $db->table($prefix . 'items')->insert(['room_id' => $rId, 'name' => 'Item A']);
$itemAId = $db->insertID();
$itemB = $db->table($prefix . 'items')->insert(['room_id' => $rId, 'name' => 'Item B']);
$itemBId = $db->insertID();
$actionA = $db->table($prefix . 'actions')->insert(['item_id' => $itemAId, 'action_name' => 'a1']);
$actionAId = $db->insertID();
$actionB = $db->table($prefix . 'actions')->insert(['item_id' => $itemBId, 'action_name' => 'b1']);
$actionBId = $db->insertID();

$tsData = ['submission_code' => uniqid('SUB-'), 'date' => date('Y-m-d H:i:s'), 'room_id' => $rId, 'visit_frequency' => 1, 'status' => 'pending_review', 'submitted_by' => null];
$db->table($prefix . 'task_submissions')->insert($tsData);
$taskSubmissionId = $db->insertID();

$db->table($prefix . 'task_submission_items')->insert(['task_submission_id' => $taskSubmissionId, 'item_id' => $itemAId, 'cleaning_frequency' => 1]);
$taskItem1 = $db->insertID();
$db->table($prefix . 'task_submission_items')->insert(['task_submission_id' => $taskSubmissionId, 'item_id' => $itemBId, 'cleaning_frequency' => 1]);
$taskItem2 = $db->insertID();

$db->table($prefix . 'task_submission_actions')->insert(['task_submission_item_id' => $taskItem1, 'action_id' => $actionAId, 'repetitions' => 1]);
$db->table($prefix . 'task_submission_actions')->insert(['task_submission_item_id' => $taskItem2, 'action_id' => $actionBId, 'repetitions' => 1]);

$date = date('Y-m-d');
$ts = $db->prefixTable('task_submissions');
$tsi = $db->prefixTable('task_submission_items');
$tsa = $db->prefixTable('task_submission_actions');

$row = $db->query("SELECT COUNT(DISTINCT tsi.item_id) AS items_cleaned_total, COUNT(DISTINCT tsa.action_id) AS actions_cleaned_total FROM {$ts} ts LEFT JOIN {$tsi} tsi ON tsi.task_submission_id = ts.id LEFT JOIN {$tsa} tsa ON tsa.task_submission_item_id = tsi.id WHERE ts.room_id = ? AND DATE(ts.date) = ?", [$rId, $date])->getRowArray();

print_r($row);
