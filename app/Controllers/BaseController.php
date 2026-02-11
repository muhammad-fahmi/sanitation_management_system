<?php

namespace App\Controllers;

use App\Libraries\JwtService;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;
    protected $jwt;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->jwt = new JwtService();

        // Initialize revision room count for sidebar badge
        $jwt = session()->get('jwt');
        if ($jwt) {
            try {
                $user = $this->jwt->decode($jwt);
                $role = $user['user_role'] ?? $user['slug'] ?? '';
                if ($user && $role === 'operator') {
                    $db = \Config\Database::connect();
                    $revisedSubmissions = $db->table('task_submissions')
                        ->select('room_id')
                        ->distinct()
                        ->where('status', 'revision_requested')
                        ->get()
                        ->getResultArray();
                    $revisionRoomCount = count($revisedSubmissions);
                    // Store in session for use in all views
                    session()->set('revision_room_count', $revisionRoomCount);
                }
            } catch (\Exception $e) {
                // Silent fail if JWT decode fails
            }
        }

        // Preload any models, libraries, etc, here.
        // E.g.: $this->session = service('session');
    }
}
