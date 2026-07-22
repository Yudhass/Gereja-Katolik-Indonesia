<?php

$router->get('/', 'HomeController@index');

$router->get('/gereja/{slug}', 'GerejaController@detail');
$router->get('/cari', 'CariController@index');
$router->get('/jadwal', 'JadwalController@index');
$router->get('/maps', 'MapsController@index');

$router->get('/saran/{slug}', 'SaranController@form');
$router->post('/saran/kirim', 'SaranController@kirim');

$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@login_process');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@register_process');
$router->get('/logout', 'AuthController@logout');

$router->get('/admin/dashboard', 'AdminController@index', array('auth'));

$router->get('/admin/gereja', 'AdminGerejaController@index', array('auth'));
$router->get('/admin/gereja/{id}', 'AdminGerejaController@get', array('auth'));
$router->post('/admin/gereja/add', 'AdminGerejaController@add', array('auth'));
$router->post('/admin/gereja/update/{id}', 'AdminGerejaController@update', array('auth'));
$router->post('/admin/gereja/delete/{id}', 'AdminGerejaController@delete', array('auth'));
$router->get('/admin/gereja/getRegencies/{provinceId}', 'AdminGerejaController@getRegencies', array('auth'));
$router->get('/admin/gereja/getDistricts/{regencyId}', 'AdminGerejaController@getDistricts', array('auth'));
$router->get('/admin/gereja/getVillages/{districtId}', 'AdminGerejaController@getVillages', array('auth'));

$router->get('/admin/jadwal', 'AdminJadwalController@index', array('auth'));
$router->get('/admin/jadwal/{id}', 'AdminJadwalController@get', array('auth'));
$router->post('/admin/jadwal/add', 'AdminJadwalController@add', array('auth'));
$router->post('/admin/jadwal/update/{id}', 'AdminJadwalController@update', array('auth'));
$router->post('/admin/jadwal/delete/{id}', 'AdminJadwalController@delete', array('auth'));

$router->get('/admin/saran', 'AdminSaranController@index', array('auth'));
$router->post('/admin/saran/approve/{id}', 'AdminSaranController@approve', array('auth'));
$router->post('/admin/saran/reject/{id}', 'AdminSaranController@reject', array('auth'));
