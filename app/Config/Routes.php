<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Dashboard::index');
$routes->get('dashboard', 'Dashboard::index');
$routes->get('api/charging', 'Dashboard::api');
$routes->get('api.php', 'Dashboard::api'); // Kompatibilitas jika dipanggil script lama
