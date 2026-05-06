<?php

namespace App\Controllers;

use App\Libraries\JwtService;
use CodeIgniter\Controller;
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
    * @var \CodeIgniter\HTTP\CLIRequest|\CodeIgniter\HTTP\IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['cookie'];

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

        // Restore auth session from cookies when PHP session rotates/expires.
        if (!session()->has('jwt')) {
            $cookieJwt = $this->request->getCookie('auth_jwt');
            $cookieKey = $this->request->getCookie('auth_key');

            if (!empty($cookieJwt) && !empty($cookieKey)) {
                session()->set('jwt', $cookieJwt);
                session()->set('key', $cookieKey);
            }
        }

        if (session()->has('jwt')) {
            try {
                $decoded = $this->jwt->decode(session()->get('jwt'));
                if (time() > ((int) ($decoded['expire_time'] ?? 0))) {
                    session()->remove(['jwt', 'key', 'revision_room_count']);
                    $this->response->deleteCookie('auth_jwt');
                    $this->response->deleteCookie('auth_key');
                }
            } catch (\Throwable $e) {
                session()->remove(['jwt', 'key', 'revision_room_count']);
                $this->response->deleteCookie('auth_jwt');
                $this->response->deleteCookie('auth_key');
            }
        }

        // Initialize revision room count for sidebar badge
        $jwt = session()->get('jwt');
        if ($jwt) {
            try {
                $user = $this->jwt->decode($jwt);
                if ($user && $user['user_role'] === 'operator') {
                    $db = \Config\Database::connect();
                    $revisedSubmissions = $db->table('r_task_submission')
                        ->select('location_id')
                        ->distinct()
                        ->where('status', 'revisi')
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
