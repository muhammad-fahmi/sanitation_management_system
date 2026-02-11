<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', function () {
    $data = [
        'page_title' => 'Portal Pengguna'
    ];
    return view('portal', $data);
});

// Authentication Routes
$routes->group("auth", static function (RouteCollection $routes) {
    // GET
    $routes->match(['get', 'post'], 'login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
});


// Admin Routes
$routes->group('admin', [
    'namespace' => 'App\Controllers\Admin'
], static function (RouteCollection $routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('get_stats', 'Admin::get_stats');
    $routes->get('get_room_visits', 'Admin::get_room_visits');


    $routes->group('manage', static function (RouteCollection $routes) {

        $routes->post('user', 'User::index');
        $routes->presenter('user');
        $routes->presenter('room');
        $routes->presenter('item');
        $routes->presenter('action');
        $routes->presenter('role');
        $routes->presenter('permission');
        $routes->presenter('user_role');
        $routes->presenter('role_permission');

        // Management Task
        // $routes->group('task', static function (RouteCollection $routes) {

        //     $routes->get('/', 'Admin\Task::index');
        //     $routes->post('get_datatable/location', 'Admin\Task::get_datatable_location');
        //     $routes->post('modal/location', 'Admin\Task::modal_location');
        //     $routes->post('add/location', 'Admin\Task::add_location');
        //     $routes->put('edit/location', 'Admin\Task::update_location');
        //     $routes->delete('delete/location', 'Admin\Task::delete_location');

        //     $routes->get('(:num)', 'Admin\Task::index/$1');
        //     $routes->post('get_datatable/item', 'Admin\Task::get_datatable_item');
        //     $routes->post('modal/item', 'Admin\Task::modal_item');
        //     $routes->post('add/item', 'Admin\Task::add_item');
        //     $routes->put('edit/item', 'Admin\Task::update_item');
        //     $routes->delete('delete/item', 'Admin\Task::delete_item');

        //     $routes->get('(:num)/(:num)', 'Admin\Task::index/$1/$2');
        //     $routes->post('get_datatable/action', 'Admin\Task::get_datatable_action');
        //     $routes->post('modal/action', 'Admin\Task::modal_action');
        //     $routes->post('add/action', 'Admin\Task::add_action');
        //     $routes->put('edit/action', 'Admin\Task::update_action');
        //     $routes->delete('delete/action', 'Admin\Task::delete_action');

        // });

    });
});

$routes->group("operator", static function (RouteCollection $routes) {
    $routes->get('/', 'Operator::index');
    $routes->get('scan/(:num)', 'Operator::scan/$1');
    $routes->get('revisi', 'Operator::revisi');
    $routes->post('modal', 'Operator::modal');
    $routes->post('add', 'Operator::add_submission');
    $routes->post('increment_visit/(:num)', 'Operator::increment_visit/$1');
    $routes->delete('cancel/(:num)', 'Operator::cancel_submission/$1');
});

$routes->group("verifikator", static function (RouteCollection $routes) {
    // GET
    $routes->get('/', 'Verifikator::index');
    // POST
    $routes->post('get_datatable', 'Verifikator::get_datatable');
    $routes->post('modal', 'Verifikator::modal');
    $routes->post('update', 'Verifikator::update');
    // PUT
    $routes->put('edit', 'Verifikator::update');
});


$routes->set404Override(function () {
    echo view('errors/vw_custom_error');
});