<?php

use CodeIgniter\Test\CIUnitTestCase;

final class ViewHelpersTest extends CIUnitTestCase
{
    public function testDisplayNameFallbacks()
    {
        $this->assertEquals('User', display_name(null));
        $this->assertEquals('User 123', display_name(['user_id' => 123]));
        $this->assertEquals('jdoe', display_name(['username' => 'jdoe']));
        $this->assertEquals('John Doe', display_name(['name' => 'John Doe']));
    }

    public function testDisplayRoleFallbacks()
    {
        $this->assertEquals('Operator', display_role(null));
        $this->assertEquals('Verifikator', display_role(['role' => 'Verifikator']));
        $this->assertEquals('Admin', display_role(['user_role' => 'Admin']));
    }

    public function testRoomAndItemHelpers()
    {
        $this->assertEquals('Room X', room_name(['location_name' => 'Room X']));
        $this->assertEquals('Room Y', room_name(['name' => 'Room Y']));
        $this->assertEquals('Item A', item_name(['item_name' => 'Item A']));
        $this->assertEquals('Item B', item_name(['name' => 'Item B']));
        $this->assertEquals(11, room_id(['location_id' => 11]));
        $this->assertEquals(22, item_id(['item_id' => 22]));
    }
}
