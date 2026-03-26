<?php

/**
 * Public and Common Routes
 */
$routes->get('/', 'Home::index');
$routes->set404Override('App\Controllers\NotFound::index');

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

$routes->get('/employee-award', 'EmployeeAward::index');
$routes->post('/employee-award/submit', 'EmployeeAward::submit');
$routes->get('/jobs', 'Jobs::index');
$routes->post('/jobs/apply', 'Jobs::apply');
$routes->post('/jobs/apply_register', 'Jobs::apply_register_clinician');
$routes->get('/apply', 'Apply::index');
$routes->get('/board-of-directors', 'BoardOfDirectors::index');
$routes->get('/awards', 'Donors::index');
$routes->get('/awards/pdf/(:num)', 'Clinician\Profile::generate_award_pdf/$1');
$routes->get('/claim-facility', 'ClaimFacility::index');

$routes->group('claim', function($routes){
    $routes->get('/', 'Claim::index');
    $routes->post('generateOTP', 'Claim::generateOTP');
    $routes->post('verifyOTP', 'Claim::verifyOTP');
    $routes->post('generateSMSOTP', 'Claim::generateSMSOTP');
    $routes->post('verifySMSOTP', 'Claim::verifySMSOTP');
    $routes->post('submit', 'Claim::submit');
});


$routes->get('about-us', 'AboutUs::index');
$routes->get('our-solutions', 'OurSolutions::index');
$routes->get('employers', 'Employers::index');
$routes->get('job-seekers', 'JobSeekers::index');
$routes->get('leadership', 'Leadership::index');
$routes->get('resources', 'Resources::index');
$routes->get('msp', 'Msp::index');
$routes->get('testimonials', 'Testimonials::index');
$routes->get('how-it-works', 'HowItWorks::index');
$routes->get('industries', 'Industries::index');
$routes->get('contact-us', 'ContactUs::index');
$routes->post('contact-us/submit', 'ContactUs::submit');
