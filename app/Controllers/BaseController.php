<?php

namespace App\Controllers;

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

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Track activity and perform lazy cleanup
        $session = session();
        if ($session->get('isLoggedIn')) {
            $userId = $session->get('id');
            $userModel = new \App\Models\UserModel();
            $now = date('Y-m-d H:i:s');
            
            // Update last active
            $userModel->update($userId, ['last_active_at' => $now]);
            $session->set('last_active_at', $now);

            // Lazy cleanup: 5% chance to mark inactive users as offline
            if (mt_rand(1, 100) <= 5) {
                $thirtyMinutesAgo = date('Y-m-d H:i:s', strtotime('-30 minutes'));
                $userModel->where('last_active_at <', $thirtyMinutesAgo)
                          ->where('online_status', 1)
                          ->set(['online_status' => 0])
                          ->update();
            }
        }
    }
}
