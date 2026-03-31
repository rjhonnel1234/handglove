<?php

/**
 * Facility Routes
 */
$routes->group("facility", ["namespace" => "App\Controllers"], function ($routes) {
    $routes->get('/', 'Facility::index');
    $routes->get('profile/(:num)', 'Facility::profile/$1');
    $routes->get('profile/(:num)/onboarding/(:num)/pdf', 'Facility::onboarding_pdf/$1/$2');
    $routes->get('profile/(:num)/onboarding/(:num)', 'Facility::onboarding/$1/$2');
    $routes->post('vote', 'Facility::vote');
    $routes->post('get_reviews', 'Facility::get_reviews');

    $routes->group("schedules", ['namespace' => 'App\Controllers\Facility'], function ($routes) {
        $routes->post('upload', 'Schedules::upload');
        $routes->post('list', 'Schedules::list');
        $routes->get('parse/(:num)', 'Schedules::parse/$1');
        $routes->get('preview/(:num)', 'Schedules::preview/$1');
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
            $routes->post('list', 'Clinicians::list');
            $routes->get('online_list', 'Clinicians::online_list');
            $routes->post('request_shift', 'Clinicians::request_shift');
            $routes->post('cancel_request', 'Clinicians::cancel_request');
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
            $routes->post('get_clinicians_by_week', 'Votes::get_clinicians_by_week');
            $routes->post('generate_certificate', 'Votes::generate_certificate');
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
            $routes->get('get_clinician_timeline/(:num)', 'Shifts::get_clinician_timeline/$1');
            $routes->get('get_clinician_details/(:num)', 'Shifts::get_clinician_details/$1');
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

});

$routes->post('webhook/stripe', 'Invoices::webhook', ['namespace' => 'App\Controllers\Facility']);
