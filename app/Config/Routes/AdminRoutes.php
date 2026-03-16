<?php

/**
 * Admin Routes
 */
$routes->group("admin", ["namespace" => "App\Controllers\Admin"], function ($routes) {
    $routes->get('', 'Admin::index');
    $routes->get('login', 'Login::index');
    $routes->post('login', 'Login::index');
    $routes->get('logout', 'Login::logout');
    $routes->get('dashboard', 'Dashboard::index', ['filter' => 'adminAuth']);

    $routes->group('board-of-directors', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('', 'BoardOfDirectors::index');
        $routes->get('create', 'BoardOfDirectors::create');
        $routes->post('store', 'BoardOfDirectors::store');
        $routes->get('edit/(:num)', 'BoardOfDirectors::edit/$1');
        $routes->post('update/(:num)', 'BoardOfDirectors::update/$1');
        $routes->get('delete/(:num)', 'BoardOfDirectors::delete/$1');
        $routes->post('upload', 'BoardOfDirectors::upload');
        $routes->post('list', 'BoardOfDirectors::list');
    });

    $routes->group('donors', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('', 'Donors::index');
        $routes->get('create', 'Donors::create');
        $routes->post('store', 'Donors::store');
        $routes->get('edit/(:num)', 'Donors::edit/$1');
        $routes->post('update/(:num)', 'Donors::update/$1');
        $routes->get('delete/(:num)', 'Donors::delete/$1');
        $routes->post('upload', 'Donors::upload');
        $routes->post('list', 'Donors::list');
    });

    $routes->group('tax-types', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('', 'TaxTypes::index');
        $routes->get('create', 'TaxTypes::create');
        $routes->post('store', 'TaxTypes::store');
        $routes->get('edit/(:num)', 'TaxTypes::edit/$1');
        $routes->post('update/(:num)', 'TaxTypes::update/$1');
        $routes->get('delete/(:num)', 'TaxTypes::delete/$1');
        $routes->post('list', 'TaxTypes::list');
    });

    $routes->group('settings', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('', 'Settings::index');
        $routes->post('store', 'Settings::store');
    });

    $routes->group('roles', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('', 'Roles::index');
        $routes->get('create', 'Roles::create');
        $routes->post('store', 'Roles::store');
        $routes->get('edit/(:num)', 'Roles::edit/$1');
        $routes->post('update/(:num)', 'Roles::update/$1');
        $routes->get('delete/(:num)', 'Roles::delete/$1');
        $routes->post('list', 'Roles::list');
    });

    $routes->group('users', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('', 'Users::index');
        $routes->get('create', 'Users::create');
        $routes->post('store', 'Users::store');
        $routes->get('edit/(:num)', 'Users::edit/$1');
        $routes->post('update/(:num)', 'Users::update/$1');
        $routes->get('delete/(:num)', 'Users::delete/$1');
        $routes->post('list', 'Users::list');
    });

    $routes->group('invoices', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('/', 'Invoices::index');
        $routes->get('view/(:num)', 'Invoices::view/$1');
        $routes->post('list', 'Invoices::list');
    });

    $routes->group('payroll', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('setup', 'Payroll::setup');
        $routes->post('save-settings', 'Payroll::saveSettings');
        $routes->post('generate-period', 'Payroll::generatePeriod');
        $routes->get('delete-period/(:num)', 'Payroll::deletePeriod/$1');
        $routes->get('view-period/(:num)', 'Payroll::viewPeriod/$1');
        $routes->post('generate-stubs/(:num)', 'Payroll::generateStubs/$1');
        $routes->get('view-stub/(:num)', 'Payroll::viewStub/$1');
        $routes->get('mark-as-paid/(:num)', 'Payroll::markAsPaid/$1');
    });

    $routes->group('schedules', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('/', 'Schedules::index');
        $routes->post('list', 'Schedules::list');
        $routes->post('details', 'Schedules::details');
    });

    $routes->group('shifts', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('/', 'Shifts::index');
        $routes->post('list', 'Shifts::list');
        $routes->get('resources', 'Shifts::resources');
    });
});
