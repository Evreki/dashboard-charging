<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Dashboard::index');
$routes->get('portal', 'Dashboard::index');
$routes->get('dashboard', 'Dashboard::index');

// Routes untuk masing-masing Line Charging (A / F)
$routes->get('charging/(:segment)', 'Dashboard::line/$1');
$routes->get('dashboard/(:segment)', 'Dashboard::line/$1');

// JSON API
$routes->get('api/charging', 'Dashboard::api');
$routes->get('api.php', 'Dashboard::api');
