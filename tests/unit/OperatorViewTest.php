<?php

use CodeIgniter\Test\CIUnitTestCase;

final class OperatorViewTest extends CIUnitTestCase
{
    public function testDashboardRendersWithMissingNameNoNotices(): void
    {
        // convert notices to exceptions for this test and ensure restoration in finally
        $initialObLevel = ob_get_level();
        set_error_handler(function ($severity, $message) {
            if (($severity & E_NOTICE) !== 0) {
                throw new \ErrorException($message, 0, $severity);
            }
            return false;
        });

        // Provide username & slug so master layout has required fields, while omitting 'name' to test the fallback
        $userInfo = ['user_id' => 42, 'user_role' => 'Operator', 'username' => 'user_42', 'slug' => 'operator'];
        $rooms = [];

        try {
            $html = view('operator/vw_dashboard', ['page_title' => 'Test', 'user_info' => $userInfo, 'rooms' => $rooms]);
        } catch (\Throwable $e) {
            // ensure we restore the handler and clean only buffers created during this test
            restore_error_handler();
            while (ob_get_level() > $initialObLevel) {
                @ob_end_clean();
            }

            $this->fail('Rendering produced a notice/exception: ' . $e->getMessage());
            return;
        } finally {
            // always restore handler and clean any output buffers created by the view rendering
            restore_error_handler();
            while (ob_get_level() > $initialObLevel) {
                @ob_end_clean();
            }
        }

        // fallback display should include the username (since 'name' is missing)
        $this->assertStringContainsString('user_42', $html);
        $this->assertStringContainsString('Operator', $html);
    }

    public function testDashboardRendersWithUsernameFallback(): void
    {
        $userInfo = ['username' => 'john.doe', 'user_role' => 'Operator', 'slug' => 'operator'];
        $rooms = [];

        $html = view('operator/vw_dashboard', ['page_title' => 'Test', 'user_info' => $userInfo, 'rooms' => $rooms]);

        $this->assertStringContainsString('john.doe', $html);
        $this->assertStringContainsString('Operator', $html);
    }
}
