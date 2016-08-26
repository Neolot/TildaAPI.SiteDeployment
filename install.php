<?php
include_once( __DIR__ . '/config.php' );
include_once( __DIR__ . '/inc/functions.php' );

/*
 * Создаем файлы проекта
 */

$url_data['publickey'] = PUBLICKEY;
$url_data['secretkey'] = SECRETKEY;
$url_data['projectid'] = PROJECTID;
$url_params = http_build_query($url_data);

// Получаем данные о проекте

$method = 'getprojectexport';
$url = APIURL . $method . '/?' . $url_params;
$query_project = file_get_contents($url);
$response_project = json_decode($query_project, true);

if ( $response_project['status'] == 'FOUND' ) {
	echo 'Project files created<br>';
    tilda_createProjectFiles($response_project['result']);
} else {
	echo 'Error creating project files';
	exit;
}

/*
 * Создаем страницы
 */

$method = 'getpageslist';
$url = APIURL . $method . '/?' . $url_params;
$query_pages = file_get_contents($url);
$response_pages = json_decode($query_pages, true);

if ( $response_pages['status'] == 'FOUND' ) {
	echo 'Pages created<br>';
    tilda_createPages($response_pages['result']);
} else {
	echo 'Error creating pages';
	exit;
}
