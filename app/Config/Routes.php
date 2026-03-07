<?php
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


$routes->get('/login', 'Login::index',['filter' => 'authenticate']);
$routes->get('/login/reset-password/(:segment)', 'Login::reset_password/$1',['filter' => 'authenticate']);
$routes->post('/login/change-password/', 'Login::change_password',['filter' => 'authenticate']);

$routes->group("notifications", ["namespace" => "App\Controllers", "filter" => "userAuth"], function ($routes) {
    $routes->get('', 'NotificationsController::index');
    $routes->get('manage', 'NotificationsController::index');
    $routes->post('get_unread', 'NotificationsController::get_unread');
    $routes->post('mark_all_read', 'NotificationsController::mark_all_read');
    $routes->post('mark_read/(:num)', 'NotificationsController::mark_read/$1');
});

$routes->match(['post'], '/login', 'Login::index',['filter' => 'authenticate']);
$routes->match(['post', 'get'], '/login/forgot-password', 'Login::forgot_password',['filter' => 'authenticate']);
$routes->get('login/logout', 'Login::logout');

$routes->get('demo-request', 'Demo::index');

$routes->group("demo", ["namespace" => "App\Controllers"], function ($routes) {
    $routes->post('generateOTP', 'Demo::generateOTP');
    $routes->post('verifyOTP', 'Demo::verifyOTP');
    $routes->post('generateSMSOTP', 'Demo::generateSMSOTP');
    $routes->post('verifySMSOTP', 'Demo::verifySMSOTP');
    $routes->post('submit', 'Demo::submit');
});

$routes->get('profile/clinician/(:num)', 'Clinician\Profile::public_profile/$1');

$routes->group("profile", ["namespace" => "App\Controllers\Clinician", "filter" => "userAuth"], function ($routes) {
    $routes->get('', 'Profile::index');
    $routes->post('edit', 'Profile::edit');
    $routes->post('update', 'Profile::update');
    $routes->post('change_password', 'Profile::change_password');
    $routes->post('update_password', 'Profile::update_password');
    $routes->post('update_status', 'Profile::update_status');
    $routes->post('upload_credentials', 'Profile::upload_credentials');
    $routes->post('test_email', 'Profile::test_email');
    $routes->post('request', 'Profile::request');
    $routes->get('view-stub/(:num)', 'Profile::view_stub/$1');
    $routes->get('shifts', 'Shifts::index');
    $routes->post('shifts/list', 'Shifts::list');
    $routes->post('shifts/clockIn', 'Shifts::clockIn');
    $routes->post('shifts/clockOut', 'Shifts::clockOut');
    $routes->post('shifts/submitFeedback', 'Shifts::submitFeedback');
});



$routes->group("facility", ["namespace" => "App\Controllers"], function ($routes) {
    $routes->get('/', 'Facility::index');
    $routes->get('profile/(:num)', 'Facility::profile/$1');
    $routes->get('profile/(:num)/onboarding/(:num)/pdf', 'Facility::onboarding_pdf/$1/$2');
    $routes->get('profile/(:num)/onboarding/(:num)', 'Facility::onboarding/$1/$2');
    $routes->post('get_reviews', 'Facility::get_reviews');



    $routes->group("schedules", ['namespace' => 'App\Controllers\Facility'], function ($routes) {
        $routes->post('upload', 'Schedules::upload');
        $routes->post('list', 'Schedules::list');
        $routes->get('parse/(:num)', 'Schedules::parse/$1');
        $routes->get('add', 'Schedules::add');
        $routes->get('view', 'Schedules::view');
        $routes->get('download/(:num)', 'Schedules::download/$1');
        $routes->post('save_manual', 'Schedules::save_manual');
        $routes->post('get_personnel', 'Schedules::get_personnel');
        $routes->post('delete_personnel', 'Schedules::delete_personnel');
        $routes->post('get_all_personnel', 'Schedules::get_all_personnel');
        $routes->post('save_as_shifts', 'Schedules::save_as_shifts');
    });
    
    $routes->group("manage", ["namespace" => "App\Controllers\Facility", "filter" => "userAuth"], function ($routes) {
        $routes->get('', 'Dashboard::index');
        $routes->get('profile', 'Profile::index');
        $routes->get('users', 'Users::index');

        $routes->group("clinicians", function ($routes) {
        //     $routes->get('', 'Clinicians::index');
            $routes->post('list', 'Clinicians::list');
        //     $routes->post('get', 'Clinicians::get');
        //     $routes->post('add', 'Clinicians::insert');
        //     $routes->post('update', 'Clinicians::update');
        });

        $routes->group("units", function ($routes) {
            $routes->get('', 'Units::index');
            $routes->post('list', 'Units::list');
            $routes->post('get', 'Units::get');
            $routes->post('add', 'Units::insert');
            $routes->post('update', 'Units::update');
        });

        $routes->group("votes", function ($routes) {
            $routes->get('', 'Votes::index');
            $routes->post('list', 'Votes::list');
            $routes->post('get', 'Votes::get');
            $routes->post('add', 'Votes::insert');
            $routes->post('update', 'Votes::update');
            $routes->post('delete', 'Votes::delete');
        });

        $routes->group("jobs", function ($routes) {
            $routes->get('', 'Jobs::index');
            $routes->post('list', 'Jobs::list');
            $routes->post('get', 'Jobs::get');
            $routes->post('add', 'Jobs::insert');
            $routes->post('update', 'Jobs::update');
            $routes->post('request', 'Jobs::request');
            $routes->post('requests_list', 'Jobs::requests_list');
            $routes->post('respond', 'Jobs::respond_to_application');
            $routes->get('view/(:num)', 'Jobs::view/$1');
        });

        $routes->group("onboarding", function ($routes) {
            $routes->get('', 'Onboarding::index');
            $routes->post('list', 'Onboarding::list');
            $routes->post('get', 'Jobs::get');
            $routes->post('update', 'Onboarding::update');
            $routes->post('edit', 'Onboarding::edit');
            $routes->post('insert', 'Onboarding::insert');
            $routes->post('update_settings', 'Onboarding::update_settings');
        });

        $routes->group('personnel', function ($routes) {
            $routes->get('', 'Personnel::index');
            $routes->post('list', 'Personnel::list');
            $routes->post('get', 'Personnel::get');
            $routes->post('add', 'Personnel::insert');
            $routes->post('update', 'Personnel::update');
        });

        $routes->group('timekeeping', function ($routes) {
            $routes->get('', 'Timekeeping::index');
            $routes->post('list', 'Timekeeping::list');
            $routes->get('view/(:num)', 'Timekeeping::view/$1');
        });

        $routes->group("shifts", function ($routes) {
            $routes->get('', 'Shifts::index');
            $routes->post('list', 'Shifts::list');
            $routes->post('transfer', 'Shifts::transfer');
        });

        $routes->group("invoices", function ($routes) {
            $routes->post('list', 'Invoices::list');
            $routes->post('pay', 'Invoices::pay');
            $routes->get('view/(:any)', 'Invoices::view/$1');
            $routes->post('checkout', 'Invoices::checkout');
            $routes->get('success', 'Invoices::success');
            $routes->get('cancel', 'Invoices::cancel');
        });
    });

    $routes->post('webhook/stripe', 'Invoices::webhook', ['namespace' => 'App\Controllers\Facility']);
});

$routes->get('/employee-award', 'EmployeeAward::index');
$routes->post('/employee-award/submit', 'EmployeeAward::submit');
$routes->get('/jobs', 'Jobs::index');
$routes->post('/jobs/apply', 'Jobs::apply');
$routes->post('/jobs/apply_register', 'Jobs::apply_register_clinician');
$routes->get('/apply', 'Apply::index');
$routes->get('/board-of-directors', 'BoardOfDirectors::index');
$routes->get('/donors', 'Donors::index');
$routes->get('/claim-facility', 'ClaimFacility::index');

$routes->group('claim', function($routes){
    $routes->get('/', 'Claim::index');
    $routes->post('generateOTP', 'Claim::generateOTP');
    $routes->post('verifyOTP', 'Claim::verifyOTP');
    $routes->post('generateSMSOTP', 'Claim::generateSMSOTP');
    $routes->post('verifySMSOTP', 'Claim::verifySMSOTP');
    $routes->post('submit', 'Claim::submit');
});



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
});