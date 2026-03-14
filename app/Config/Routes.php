<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Load Public and Common Routes
require APPPATH . 'Config/Routes/PublicRoutes.php';

// Load Clinician Routes
require APPPATH . 'Config/Routes/ClinicianRoutes.php';

// Load Facility Routes
require APPPATH . 'Config/Routes/FacilityRoutes.php';

// Load Admin Routes
require APPPATH . 'Config/Routes/AdminRoutes.php';