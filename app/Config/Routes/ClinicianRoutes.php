<?php

/**
 * Clinician (Profile) Routes
 */
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
    $routes->post('shifts/get_offers', 'Shifts::get_offers');
    $routes->post('shifts/respond_to_offer', 'Shifts::respond_to_offer');
    $routes->post('shifts/send_update', 'Shifts::send_update');
});
