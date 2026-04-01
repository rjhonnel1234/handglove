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

    $routes->group('agencies', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('', 'Agencies::index');
        $routes->get('create', 'Agencies::create');
        $routes->post('store', 'Agencies::store');
        $routes->get('edit/(:num)', 'Agencies::edit/$1');
        $routes->post('update/(:num)', 'Agencies::update/$1');
        $routes->get('delete/(:num)', 'Agencies::delete/$1');
        $routes->post('list', 'Agencies::list');
    });

    $routes->group('clinician-types', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('', 'ClinicianTypes::index');
        $routes->get('create', 'ClinicianTypes::create');
        $routes->post('store', 'ClinicianTypes::store');
        $routes->get('edit/(:num)', 'ClinicianTypes::edit/$1');
        $routes->post('update/(:num)', 'ClinicianTypes::update/$1');
        $routes->get('delete/(:num)', 'ClinicianTypes::delete/$1');
        $routes->post('list', 'ClinicianTypes::list');
    });

    $routes->group('institutions', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('', 'Institutions::index');
        $routes->get('create', 'Institutions::create');
        $routes->post('store', 'Institutions::store');
        $routes->get('edit/(:num)', 'Institutions::edit/$1');
        $routes->post('update/(:num)', 'Institutions::update/$1');
        $routes->get('delete/(:num)', 'Institutions::delete/$1');
        $routes->post('list', 'Institutions::list');
    });

    $routes->group('credential-types', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('', 'CredentialTypes::index');
        $routes->get('create', 'CredentialTypes::create');
        $routes->post('store', 'CredentialTypes::store');
        $routes->get('edit/(:num)', 'CredentialTypes::edit/$1');
        $routes->post('update/(:num)', 'CredentialTypes::update/$1');
        $routes->get('delete/(:num)', 'CredentialTypes::delete/$1');
        $routes->post('list', 'CredentialTypes::list');
    });

    $routes->group('clinicians', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('', 'Clinicians::index');
        $routes->get('create', 'Clinicians::create');
        $routes->post('store', 'Clinicians::store');
        $routes->get('edit/(:num)', 'Clinicians::edit/$1');
        $routes->post('update/(:num)', 'Clinicians::update/$1');
        $routes->get('delete/(:num)', 'Clinicians::delete/$1');
        $routes->get('send-reset-password/(:num)', 'Clinicians::sendResetPassword/$1');
        $routes->post('upload', 'Clinicians::upload');
        $routes->get('get-pcc-credentials', 'Clinicians::get_pcc_credentials');
        $routes->post('toggle-user-status', 'Clinicians::toggle_user_status');
        $routes->get('get-credentials', 'Clinicians::get_credentials');
        $routes->post('upload-credential', 'Clinicians::upload_credential');
        $routes->post('list', 'Clinicians::list');
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
        $routes->post('get-available-clinicians', 'Schedules::get_available_clinicians');
        $routes->post('assign-clinician', 'Schedules::assign_clinician');
    });

    $routes->group('shifts', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('/', 'Shifts::index');
        $routes->post('list', 'Shifts::list');
        $routes->get('resources', 'Shifts::resources');
    });

    $routes->group('leads', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('', 'Leads::index');
        $routes->get('create', 'Leads::create');
        $routes->post('store', 'Leads::store');
        $routes->get('view/(:num)', 'Leads::view/$1');
        $routes->get('edit/(:num)', 'Leads::edit/$1');
        $routes->post('update/(:num)', 'Leads::update/$1');
        $routes->get('delete/(:num)', 'Leads::delete/$1');
        $routes->post('list', 'Leads::list');
        $routes->post('updateStatus', 'Leads::updateStatus');
    });

    $routes->group('facilities', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('', 'Facilities::index');
        $routes->get('create', 'Facilities::create');
        $routes->post('store', 'Facilities::store');
        $routes->get('edit/(:num)', 'Facilities::edit/$1');
        $routes->post('update/(:num)', 'Facilities::update/$1');
        $routes->get('delete/(:num)', 'Facilities::delete/$1');
        $routes->post('list', 'Facilities::list');
        $routes->post('upload', 'Facilities::upload');

        // New Routes for Details & Sub-Management
        $routes->get('details/(:num)', 'Facilities::details/$1');
        
        // Units
        $routes->post('units/list/(:num)', 'Facilities::units_list/$1');
        $routes->get('units/create/(:num)', 'Facilities::units_create/$1');
        $routes->post('units/store', 'Facilities::units_store');
        $routes->get('units/edit/(:num)', 'Facilities::units_edit/$1');
        $routes->post('units/update/(:num)', 'Facilities::units_update/$1');
        $routes->get('units/delete/(:num)', 'Facilities::units_delete/$1');

        // Personnel
        $routes->post('personnel/list/(:num)', 'Facilities::personnel_list/$1');
        $routes->get('personnel/create/(:num)', 'Facilities::personnel_create/$1');
        $routes->post('personnel/store', 'Facilities::personnel_store');
        $routes->get('personnel/edit/(:num)', 'Facilities::personnel_edit/$1');
        $routes->post('personnel/update/(:num)', 'Facilities::personnel_update/$1');
        $routes->get('personnel/delete/(:num)', 'Facilities::personnel_delete/$1');

        // Lists for Shifts, Invoices, Receipts
        $routes->post('shifts/list/(:num)', 'Facilities::shifts_list/$1');
        $routes->post('invoices/list/(:num)', 'Facilities::invoices_list/$1');
    });
});
