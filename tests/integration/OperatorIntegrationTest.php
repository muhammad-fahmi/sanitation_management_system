<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

final class OperatorIntegrationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    /**
     * Run migrations before tests to ensure tables exist in the test DB
     * @var bool
     */
    protected $migrate = true;

    protected function ensureSchemaExists()
    {
        $db = \Config\Database::connect();
        $prefix = $db->DBPrefix ?? '';

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

        // Audits table schema matching Tatter Audits expectations
        $db->query("CREATE TABLE IF NOT EXISTS " . $prefix . "audits (id INTEGER PRIMARY KEY AUTOINCREMENT, source TEXT, source_id INTEGER, user_id INTEGER, event TEXT, summary TEXT, created_at DATETIME)");
        $db->query("CREATE TABLE IF NOT EXISTS audits (id INTEGER PRIMARY KEY AUTOINCREMENT, source TEXT, source_id INTEGER, user_id INTEGER, event TEXT, summary TEXT, created_at DATETIME)");
    }

    public function testAddSubmissionCreatesRecords(): void
    {
        $this->ensureSchemaExists();

        $db = \Config\Database::connect();
        $prefix = $db->DBPrefix ?? '';

        // create room
        $db->table($prefix . 'rooms')->insert(['name' => 'Integration Room']);
        $roomId = $db->insertID();

        // create item
        $db->table($prefix . 'items')->insert(['room_id' => $roomId, 'name' => 'Integration Item']);
        $itemId = $db->insertID();

        // create action
        $db->table($prefix . 'actions')->insert(['item_id' => $itemId, 'action_name' => 'check']);
        $actionId = $db->insertID();

        $date = date('Y-m-d');
        $submissions = [
            [
                'location_id' => $roomId,
                'item_id' => $itemId,
                'date' => $date,
                'action_id' => $actionId
            ]
        ];

        // create a valid operator JWT and attach to session so the operator filter allows the request
        $keyPair = sodium_crypto_sign_keypair();
        $privateKey = base64_encode(sodium_crypto_sign_secretkey($keyPair));
        $publicKey = base64_encode(sodium_crypto_sign_publickey($keyPair));
        $payload = ['user_id' => 999, 'slug' => 'operator', 'expire_time' => time() + 3600];
        $jwt = \Firebase\JWT\JWT::encode($payload, $privateKey, 'EdDSA');

        $response = $this->withSession(['jwt' => $jwt, 'jwt_public_key' => $publicKey])
            ->post('operator/add', [
                'user_id' => 999,
                'submissions' => json_encode($submissions)
            ]);

        $response->assertStatus(200);
        $response->assertJSONFragment(['status' => 200]);

        // check that a task submission exists for the room
        $ts = $db->table($prefix . 'task_submissions')->where('room_id', $roomId)->orderBy('id', 'DESC')->get()->getRowArray();
        $this->assertNotEmpty($ts, 'task_submissions row not found for room');

        $tsi = $db->table($prefix . 'task_submission_items')->where('task_submission_id', $ts['id'])->where('item_id', $itemId)->get()->getRowArray();
        $this->assertNotEmpty($tsi, 'task_submission_items row not found');

        $tsa = $db->table($prefix . 'task_submission_actions')->where('task_submission_item_id', $tsi['id'])->where('action_id', $actionId)->get()->getRowArray();
        $this->assertNotEmpty($tsa, 'task_submission_actions row not found');
    }

    public function testResubmissionIncrementsRepetitions(): void
    {
        $this->ensureSchemaExists();
        $db = \Config\Database::connect();
        $prefix = $db->DBPrefix ?? '';

        // Create room/item/action
        $db->table($prefix . 'rooms')->insert(['name' => 'ReSub Room']);
        $roomId = $db->insertID();
        $db->table($prefix . 'items')->insert(['room_id' => $roomId, 'name' => 'ReSub Item']);
        $itemId = $db->insertID();
        $db->table($prefix . 'actions')->insert(['item_id' => $itemId, 'action_name' => 'recheck']);
        $actionId = $db->insertID();

        $date = date('Y-m-d');
        // First submission via API
        $payload = [
            [
                'location_id' => $roomId,
                'item_id' => $itemId,
                'date' => $date,
                'action_id' => $actionId
            ]
        ];

        $keyPair = sodium_crypto_sign_keypair();
        $privateKey = base64_encode(sodium_crypto_sign_secretkey($keyPair));
        $publicKey = base64_encode(sodium_crypto_sign_publickey($keyPair));
        $jwtPayload = ['user_id' => 888, 'slug' => 'operator', 'expire_time' => time() + 3600];
        $jwt = \Firebase\JWT\JWT::encode($jwtPayload, $privateKey, 'EdDSA');

        $this->withSession(['jwt' => $jwt, 'jwt_public_key' => $publicKey])
            ->post('operator/add', ['user_id' => 888, 'submissions' => json_encode($payload)])
            ->assertStatus(200);

        $ts = $db->table($prefix . 'task_submissions')->where('room_id', $roomId)->orderBy('id', 'DESC')->get()->getRowArray();
        $this->assertNotEmpty($ts, 'Initial submission not created');
        $tsi = $db->table($prefix . 'task_submission_items')->where('task_submission_id', $ts['id'])->where('item_id', $itemId)->get()->getRowArray();
        $this->assertNotEmpty($tsi, 'Initial task_submission_items missing');
        $tsa = $db->table($prefix . 'task_submission_actions')->where('task_submission_item_id', $tsi['id'])->where('action_id', $actionId)->get()->getRowArray();
        $this->assertNotEmpty($tsa, 'Initial task_submission_actions missing');
        $initialReps = (int) ($tsa['repetitions'] ?? 0);

        // Re-submit same action via API
        $this->withSession(['jwt' => $jwt, 'jwt_public_key' => $publicKey])
            ->post('operator/add', ['user_id' => 888, 'submissions' => json_encode($payload)])
            ->assertStatus(200);

        $tsaAfter = $db->table($prefix . 'task_submission_actions')->where('task_submission_item_id', $tsi['id'])->where('action_id', $actionId)->get()->getRowArray();
        $this->assertNotEmpty($tsaAfter, 'Action row missing after resubmission');
        $this->assertEquals($initialReps + 1, (int) ($tsaAfter['repetitions'] ?? 0), 'Repetitions did not increment on resubmission');
    }

    public function testResubmissionFromRevisionRequestedToPendingReview(): void
    {
        $this->ensureSchemaExists();
        $db = \Config\Database::connect();
        $prefix = $db->DBPrefix ?? '';

        // Create base data
        $db->table($prefix . 'rooms')->insert(['name' => 'Rev Room']);
        $roomId = $db->insertID();
        $db->table($prefix . 'items')->insert(['room_id' => $roomId, 'name' => 'Rev Item']);
        $itemId = $db->insertID();
        $db->table($prefix . 'actions')->insert(['item_id' => $itemId, 'action_name' => 'revaction']);
        $actionId = $db->insertID();

        $dateTime = date('Y-m-d H:i:s');
        // Insert a task_submission with status 'revision_requested'
        $db->table($prefix . 'task_submissions')->insert(['submission_code' => 'REV-1', 'date' => $dateTime, 'room_id' => $roomId, 'status' => 'revision_requested', 'submitted_by' => 777]);
        $tsId = $db->insertID();
        $db->table($prefix . 'task_submission_items')->insert(['task_submission_id' => $tsId, 'item_id' => $itemId, 'cleaning_frequency' => 1]);
        $tsiId = $db->insertID();
        $db->table($prefix . 'task_submission_actions')->insert(['task_submission_item_id' => $tsiId, 'action_id' => $actionId, 'repetitions' => 1]);

        // sanity check
        $rowBefore = $db->table($prefix . 'task_submissions')->where('id', $tsId)->get()->getRowArray();
        $this->assertEquals('revision_requested', $rowBefore['status']);

        // perform resubmission via API (should flip status to pending_review and increment reps)
        $date = date('Y-m-d');
        $payload = [['location_id' => $roomId, 'item_id' => $itemId, 'date' => $date, 'action_id' => $actionId]];

        $keyPair = sodium_crypto_sign_keypair();
        $privateKey = base64_encode(sodium_crypto_sign_secretkey($keyPair));
        $publicKey = base64_encode(sodium_crypto_sign_publickey($keyPair));
        $jwtPayload = ['user_id' => 555, 'slug' => 'operator', 'expire_time' => time() + 3600];
        $jwt = \Firebase\JWT\JWT::encode($jwtPayload, $privateKey, 'EdDSA');

        $this->withSession(['jwt' => $jwt, 'jwt_public_key' => $publicKey])
            ->post('operator/add', ['user_id' => 555, 'submissions' => json_encode($payload)])
            ->assertStatus(200);

        $rowAfter = $db->table($prefix . 'task_submissions')->where('id', $tsId)->get()->getRowArray();
        $this->assertEquals('pending_review', $rowAfter['status'], 'Status not moved to pending_review on resubmission from revision_requested');

        $tsaAfter = $db->table($prefix . 'task_submission_actions')->where('task_submission_item_id', $tsiId)->where('action_id', $actionId)->get()->getRowArray();
        $this->assertNotEmpty($tsaAfter);
        $this->assertEquals(2, (int) ($tsaAfter['repetitions'] ?? 0), 'Expected repetitions to be incremented after resubmission');
    }

    public function testCancelActionDeletesActionButKeepsItemAndSubmissionWhenOtherActionsExist(): void
    {
        $this->ensureSchemaExists();
        $db = \Config\Database::connect();
        $prefix = $db->DBPrefix ?? '';

        // Setup room/item/master action
        $db->table($prefix . 'rooms')->insert(['name' => 'Cancel Room']);
        $roomId = $db->insertID();
        $db->table($prefix . 'items')->insert(['room_id' => $roomId, 'name' => 'Cancel Item']);
        $itemId = $db->insertID();
        $db->table($prefix . 'actions')->insert(['item_id' => $itemId, 'action_name' => 'a1']);
        $masterActionA = $db->insertID();
        $db->table($prefix . 'actions')->insert(['item_id' => $itemId, 'action_name' => 'a2']);
        $masterActionB = $db->insertID();

        // Create submission with two action records for same item
        $db->table($prefix . 'task_submissions')->insert(['submission_code' => 'C-1', 'date' => date('Y-m-d H:i:s'), 'room_id' => $roomId, 'status' => 'pending_review']);
        $tsId = $db->insertID();
        $db->table($prefix . 'task_submission_items')->insert(['task_submission_id' => $tsId, 'item_id' => $itemId, 'cleaning_frequency' => 1]);
        $tsiId = $db->insertID();
        $db->table($prefix . 'task_submission_actions')->insert(['task_submission_item_id' => $tsiId, 'action_id' => $masterActionA, 'repetitions' => 1]);
        $actionRecordA = $db->insertID();
        $db->table($prefix . 'task_submission_actions')->insert(['task_submission_item_id' => $tsiId, 'action_id' => $masterActionB, 'repetitions' => 1]);
        $actionRecordB = $db->insertID();

        // auth
        $keyPair = sodium_crypto_sign_keypair();
        $privateKey = base64_encode(sodium_crypto_sign_secretkey($keyPair));
        $publicKey = base64_encode(sodium_crypto_sign_publickey($keyPair));
        $jwtPayload = ['user_id' => 101, 'slug' => 'operator', 'expire_time' => time() + 3600];
        $jwt = \Firebase\JWT\JWT::encode($jwtPayload, $privateKey, 'EdDSA');

        // Delete first action
        $this->withSession(['jwt' => $jwt, 'jwt_public_key' => $publicKey])
            ->delete('operator/cancel/' . $actionRecordA)
            ->assertStatus(200);

        // Ensure action A removed, action B remains, item and submission remain
        $this->assertEmpty($db->table($prefix . 'task_submission_actions')->where('id', $actionRecordA)->get()->getRowArray(), 'Action A should be deleted');
        $this->assertNotEmpty($db->table($prefix . 'task_submission_actions')->where('id', $actionRecordB)->get()->getRowArray(), 'Action B should remain');
        $this->assertNotEmpty($db->table($prefix . 'task_submission_items')->where('id', $tsiId)->get()->getRowArray(), 'Task submission item should remain');
        $this->assertNotEmpty($db->table($prefix . 'task_submissions')->where('id', $tsId)->get()->getRowArray(), 'Task submission should remain');
    }

    public function testCancelLastActionDeletesItemAndSubmission(): void
    {
        $this->ensureSchemaExists();
        $db = \Config\Database::connect();
        $prefix = $db->DBPrefix ?? '';

        // Setup single-action submission
        $db->table($prefix . 'rooms')->insert(['name' => 'Cancel Last Room']);
        $roomId = $db->insertID();
        $db->table($prefix . 'items')->insert(['room_id' => $roomId, 'name' => 'Cancel Last Item']);
        $itemId = $db->insertID();
        $db->table($prefix . 'actions')->insert(['item_id' => $itemId, 'action_name' => 'alone']);
        $masterAction = $db->insertID();

        $db->table($prefix . 'task_submissions')->insert(['submission_code' => 'C-2', 'date' => date('Y-m-d H:i:s'), 'room_id' => $roomId, 'status' => 'pending_review']);
        $tsId = $db->insertID();
        $db->table($prefix . 'task_submission_items')->insert(['task_submission_id' => $tsId, 'item_id' => $itemId, 'cleaning_frequency' => 1]);
        $tsiId = $db->insertID();
        $db->table($prefix . 'task_submission_actions')->insert(['task_submission_item_id' => $tsiId, 'action_id' => $masterAction, 'repetitions' => 1]);
        $actionRecord = $db->insertID();

        // auth
        $keyPair = sodium_crypto_sign_keypair();
        $privateKey = base64_encode(sodium_crypto_sign_secretkey($keyPair));
        $publicKey = base64_encode(sodium_crypto_sign_publickey($keyPair));
        $jwtPayload = ['user_id' => 202, 'slug' => 'operator', 'expire_time' => time() + 3600];
        $jwt = \Firebase\JWT\JWT::encode($jwtPayload, $privateKey, 'EdDSA');

        $this->withSession(['jwt' => $jwt, 'jwt_public_key' => $publicKey])
            ->delete('operator/cancel/' . $actionRecord)
            ->assertStatus(200);

        // Expect action removed, item removed, submission removed
        $this->assertEmpty($db->table($prefix . 'task_submission_actions')->where('id', $actionRecord)->get()->getRowArray(), 'Action row should be deleted');
        $this->assertEmpty($db->table($prefix . 'task_submission_items')->where('id', $tsiId)->get()->getRowArray(), 'Task submission item should be deleted');
        $this->assertEmpty($db->table($prefix . 'task_submissions')->where('id', $tsId)->get()->getRowArray(), 'Task submission should be deleted');
    }
}
