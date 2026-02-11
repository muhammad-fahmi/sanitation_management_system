<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\TaskSubmissionModel;
use App\Models\TaskSubmissionItemsModel;
use App\Models\TaskSubmissionActionsModel;
use App\Models\RoomModel;
use App\Models\ItemModel;
use App\Models\ActionModel;
use App\Models\UserModel;

final class TaskSubmissionModelTest extends CIUnitTestCase
{

    /**
     * Run migrations before tests to ensure tables exist in the test DB
     * @var bool
     */
    protected $migrate = true;

    // No global seed required; tests will create their own records

    protected function ensureSchemaExists()
    {
        $db = \Config\Database::connect();
        $prefix = $db->DBPrefix ?? '';

        // Minimal schema required for tests (SQLite-friendly), respect DB prefix
        $db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "rooms (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT)");
        $db->query("CREATE TABLE IF NOT EXISTS rooms (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT)");

        $db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "items (id INTEGER PRIMARY KEY AUTOINCREMENT, room_id INTEGER, name TEXT)");
        $db->query("CREATE TABLE IF NOT EXISTS items (id INTEGER PRIMARY KEY AUTOINCREMENT, room_id INTEGER, name TEXT)");

        $db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "actions (id INTEGER PRIMARY KEY AUTOINCREMENT, item_id INTEGER, action_name TEXT)");
        $db->query("CREATE TABLE IF NOT EXISTS actions (id INTEGER PRIMARY KEY AUTOINCREMENT, item_id INTEGER, action_name TEXT)");

        $db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "task_submissions (id INTEGER PRIMARY KEY AUTOINCREMENT, submission_code TEXT, date DATETIME, room_id INTEGER, visit_frequency INTEGER, revision_message TEXT, status TEXT, submitted_by INTEGER, verified_by INTEGER, verified_at DATETIME)");
        $db->query("CREATE TABLE IF NOT EXISTS task_submissions (id INTEGER PRIMARY KEY AUTOINCREMENT, submission_code TEXT, date DATETIME, room_id INTEGER, visit_frequency INTEGER, revision_message TEXT, status TEXT, submitted_by INTEGER, verified_by INTEGER, verified_at DATETIME)");

        $db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "task_submission_items (id INTEGER PRIMARY KEY AUTOINCREMENT, task_submission_id INTEGER, item_id INTEGER, cleaning_frequency INTEGER)");
        $db->query("CREATE TABLE IF NOT EXISTS task_submission_items (id INTEGER PRIMARY KEY AUTOINCREMENT, task_submission_id INTEGER, item_id INTEGER, cleaning_frequency INTEGER)");

        $db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "task_submission_actions (id INTEGER PRIMARY KEY AUTOINCREMENT, task_submission_item_id INTEGER, action_id INTEGER, repetitions INTEGER)");
        $db->query("CREATE TABLE IF NOT EXISTS task_submission_actions (id INTEGER PRIMARY KEY AUTOINCREMENT, task_submission_item_id INTEGER, action_id INTEGER, repetitions INTEGER)");
    }

    public function testCreateSubmissionAndRetrieveDetails(): void
    {
        $this->ensureSchemaExists();

        $roomModel = new RoomModel();
        $itemModel = new ItemModel();
        $actionModel = new ActionModel();
        $userModel = new UserModel();

        // Create a room
        $db = \Config\Database::connect();
        $prefix = $db->DBPrefix ?? '';

        $roomId = $roomModel->insert(['name' => 'Test Room']);
        if (!is_numeric($roomId)) {
            $roomId = $db->insertID();
        }

        // Create an item
        $itemId = $itemModel->insert(['room_id' => $roomId, 'name' => 'Test Item']);
        if (!is_numeric($itemId)) {
            $itemId = $db->insertID();
        }

        // Create an action
        $actionId = $actionModel->insert(['item_id' => $itemId, 'action_name' => 'sudah bersih']);
        if (!is_numeric($actionId)) {
            $actionId = $db->insertID();
        }

        // Insert a submission (no user required in tests)
        $taskModel = new TaskSubmissionModel();
        $taskItemModel = new TaskSubmissionItemsModel();
        $taskActionModel = new TaskSubmissionActionsModel();

        $date = date('Y-m-d');

        $taskSubmissionId = $taskModel->insert([
            'submission_code' => uniqid('SUB-'),
            'date' => date('Y-m-d H:i:s'),
            'room_id' => $roomId,
            'visit_frequency' => 1,
            'status' => 'pending_review',
            'submitted_by' => null
        ]);

        // insert using DB builder to avoid model allowedFields restrictions
        $db = \Config\Database::connect();
        $prefix = $db->DBPrefix ?? '';
        $db->table($prefix . 'task_submission_items')->insert([
            'task_submission_id' => $taskSubmissionId,
            'item_id' => $itemId,
            'cleaning_frequency' => 1
        ]);
        $taskItemId = $db->insertID();

        $db->table($prefix . 'task_submission_actions')->insert([
            'task_submission_item_id' => $taskItemId,
            'action_id' => $actionId,
            'repetitions' => 2
        ]);

        // Fetch via model method
        $results = $taskModel->getSubmissionsWithDetails($roomId, $date);

        $this->assertIsArray($results);
        $this->assertNotEmpty($results);

        $found = false;
        foreach ($results as $row) {
            if ((int) ($row['item_id'] ?? 0) === (int) $itemId && (int) ($row['action_id'] ?? 0) === (int) $actionId) {
                $found = true;
                $this->assertEquals(2, (int) ($row['quantity'] ?? 0));
            }
        }

        $this->assertTrue($found, 'Inserted submission detail not found in getSubmissionsWithDetails result');
    }

    public function testGetTotalsByLocationDate(): void
    {
        $roomModel = new RoomModel();
        $itemModel = new ItemModel();
        $actionModel = new ActionModel();
        $userModel = new UserModel();

        $db = \Config\Database::connect();
        $prefix = $db->DBPrefix ?? '';

        $roomId = $roomModel->insert(['name' => 'Totals Room']);
        if (!is_numeric($roomId)) {
            $roomId = $db->insertID();
        }

        // Insert items directly via DB to avoid model validation/allowedFields issues in test env
        $db->table($prefix . 'items')->insert(['room_id' => $roomId, 'name' => 'Item A']);
        $itemA = $db->insertID();
        $foundA = $db->table($prefix . 'items')->where('id', $itemA)->get()->getRowArray() ?: $db->table('items')->where('id', $itemA)->get()->getRowArray();
        $this->assertNotEmpty($foundA, 'Item A not found in DB after insert (checked prefixed and unprefixed tables)');

        $db->table($prefix . 'items')->insert(['room_id' => $roomId, 'name' => 'Item B']);
        $itemB = $db->insertID();
        $foundB = $db->table($prefix . 'items')->where('id', $itemB)->get()->getRowArray() ?: $db->table('items')->where('id', $itemB)->get()->getRowArray();
        $this->assertNotEmpty($foundB, 'Item B not found in DB after insert (checked prefixed and unprefixed tables)');

        // insert actions directly via DB
        $db->table($prefix . 'actions')->insert(['item_id' => $itemA, 'action_name' => 'a1']);
        $actionA = $db->insertID();
        $db->table($prefix . 'actions')->insert(['item_id' => $itemB, 'action_name' => 'b1']);
        $actionB = $db->insertID();
        // no user required for test
        $taskModel = new TaskSubmissionModel();
        $taskItemModel = new TaskSubmissionItemsModel();
        $taskActionModel = new TaskSubmissionActionsModel();
        $date = date('Y-m-d');

        $taskSubmissionId = $taskModel->insert([
            'submission_code' => uniqid('SUB-'),
            'date' => date('Y-m-d H:i:s'),
            'room_id' => $roomId,
            'visit_frequency' => 1,
            'status' => 'pending_review',
            'submitted_by' => null
        ]);

        // insert using DB builder to avoid model allowedFields restrictions
        $db = \Config\Database::connect();
        $prefix = $db->DBPrefix ?? '';
        $db->table($prefix . 'task_submission_items')->insert(['task_submission_id' => $taskSubmissionId, 'item_id' => $itemA, 'cleaning_frequency' => 1]);
        $taskItem1 = $db->insertID();
        $db->table($prefix . 'task_submission_items')->insert(['task_submission_id' => $taskSubmissionId, 'item_id' => $itemB, 'cleaning_frequency' => 1]);
        $taskItem2 = $db->insertID();

        $db->table($prefix . 'task_submission_actions')->insert(['task_submission_item_id' => $taskItem1, 'action_id' => $actionA, 'repetitions' => 1]);
        $db->table($prefix . 'task_submission_actions')->insert(['task_submission_item_id' => $taskItem2, 'action_id' => $actionB, 'repetitions' => 1]);

        $totals = $taskModel->getTotalsByLocationDate($roomId, $date);

        // Debug: fetch joined rows to inspect what is stored
        $rows = $db->table($prefix . 'task_submissions ts')
            ->select('ts.id, tsi.item_id, tsa.action_id')
            ->join($prefix . 'task_submission_items tsi', 'tsi.task_submission_id = ts.id', 'left')
            ->join($prefix . 'task_submission_actions tsa', 'tsa.task_submission_item_id = tsi.id', 'left')
            ->where('ts.room_id', $roomId)
            ->where('DATE(ts.date)', $date)
            ->get()->getResultArray();

        $distinctItems = array_unique(array_column($rows, 'item_id'));
        $distinctActions = array_unique(array_column($rows, 'action_id'));

        $this->assertIsArray($totals);

        $itemsPrefixed = $db->table($prefix . 'items')->get()->getResultArray();
        $itemsUnprefixed = $db->table('items')->get()->getResultArray();
        $this->assertGreaterThanOrEqual(2, count($itemsPrefixed) + count($itemsUnprefixed), 'Expected items table to contain at least 2 rows (prefixed or unprefixed), prefixed: ' . json_encode($itemsPrefixed) . ' unprefixed: ' . json_encode($itemsUnprefixed));

        // Expecting two distinct items and two distinct actions
        $this->assertEquals(
            2,
            count(array_filter($distinctItems, fn($v) => !is_null($v) && $v !== '')),
            'Expected 2 distinct items in joined rows, got: ' . json_encode($distinctItems) . ' -- items table prefixed: ' . json_encode($itemsPrefixed) . ' unprefixed: ' . json_encode($itemsUnprefixed)
        );
        $this->assertEquals(
            2,
            count(array_filter($distinctActions, fn($v) => !is_null($v) && $v !== '')),
            'Expected 2 distinct actions in joined rows, got: ' . json_encode($distinctActions)
        );

        $this->assertEquals(2, (int) ($totals['items_cleaned_total'] ?? 0));
        $this->assertEquals(2, (int) ($totals['actions_cleaned_total'] ?? 0));
    }
}
