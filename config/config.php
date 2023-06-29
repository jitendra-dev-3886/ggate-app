<?php
session_start();

//Local Server => 0
//Production Server => 1
//Development Server => 2

// Set Environment (Production or Development or local)
$isProduction = 0;

if ($isProduction == 0) {
	//Localhost
	error_reporting(E_ALL);

	$installDir = '/ggate-comm-dashboard';
	define('FILEUSER', 'localhost');

	define('DB_SERVER', 'localhost');
	define('DB_SERVER_HOST', '127.0.0.1');
	define('DB_SERVER_USERNAME', 'root');
	define('DB_SERVER_PASSWORD', '');
	//define('DB_SERVER_PASSWORD', 'ripl@2x20');
	define('DB_SERVER_PORT', '3307');
	define('DB_DATABASE', 'ggate-commercial-dev');
	define('DB_DATABASE_SOCKET', '');
	define('HTTP_SERVER', 'http://' . $_SERVER['SERVER_NAME']);
	define('WS_ROOT', $installDir . '/');
	define('WS_ADMIN_ROOT', '/ggate-comm-dashboard/');
	//define('WS_ADMIN_ROOT', '/GGate/ADMIN/dashboard.ggatecomm/');

	define("GGATE_APP_DASHBORD_COMMUNITY_URL", "http://localhost/GGate/APIs/ggate-commercial-api/v1/utils/index.php/dashboard/");
} else {
	error_reporting(~E_NOTICE || ~E_WARNING);
	$installDir = '';

	$CLOUDSQL_HOST = "34.93.112.233";
	$CLOUDSQL_CONNECTION_NAME = "ggate";
	$CLOUDSQL_PASSWORD = "a4lylKNeKcy94hmz";
	$CLOUDSQL_PORT = "3306";

	if ($isProduction == 1) {
		//Production
		$CLOUDSQL_USER = "root";
		$CLOUDSQL_DATABASE_NAME = "ggate-commercial";
		$CLOUDSQL_DATABASE_SOCKET = "/cloudsql/ggate-mob-cloud:asia-south1:ggate-test-db";
		$CLOUDSQL_DSN = "mysql:dbname=ggate-commercial;unix_socket=/cloudsql/ggate-mob-cloud:asia-south1:ggate-test-db";
		$APP_DASHBORD_COMMUNITY_URL = "https://api.ggatecomm.app/v1/utils/dashboard/";
	} else {
		//Development
		$CLOUDSQL_USER = "root";
		$CLOUDSQL_DATABASE_NAME = "ggate-commercial-dev";
		$CLOUDSQL_DATABASE_SOCKET = "/cloudsql/ggate-mob-cloud:asia-south1:ggate-test-db";
		$CLOUDSQL_DSN = "mysql:dbname=ggate-commercial-dev;unix_socket=/cloudsql/ggate-mob-cloud:asia-south1:ggate-test-db";
		$APP_DASHBORD_COMMUNITY_URL = "https://devapi.ggatecomm.app/v1/utils/index.php/dashboard/";
	}

	//Config Params
	date_default_timezone_set('Asia/Kolkata');
	if (!$CLOUDSQL_DSN || !$CLOUDSQL_USER || false === $CLOUDSQL_PASSWORD) {
		die('Set CLOUDSQL_DSN, CLOUDSQL_USER, and CLOUDSQL_PASSWORD environment variables');
	}
	$dsn = $CLOUDSQL_DSN;
	define('DB_SERVER', $CLOUDSQL_HOST);
	define('DB_SERVER_USERNAME', $CLOUDSQL_USER);
	define('DB_SERVER_PASSWORD', $CLOUDSQL_PASSWORD);
	define('DB_SERVER_PORT', $CLOUDSQL_PORT);
	define('DB_DATABASE', $CLOUDSQL_DATABASE_NAME);
	define('DB_DATABASE_SOCKET', $CLOUDSQL_DATABASE_SOCKET);

	define('HTTP_SERVER', 'https://' . $_SERVER['SERVER_NAME']);
	define('WS_ROOT', $installDir . '/');
	define('WS_ADMIN_ROOT', WS_ROOT . '');

	//API Access Point
	define("GGATE_APP_DASHBORD_COMMUNITY_URL", $APP_DASHBORD_COMMUNITY_URL);
}

define('IS_PRODUCTION', $isProduction);

$gen_root = $_SERVER['DOCUMENT_ROOT'] . $installDir;

define('WS_ADMIN_CONFIG', 'config/');
define('WS_ADMIN_LIB', 'lib/');

define('DIR_WS_JS_PATH', 'assets/js/');
define('DIR_WS_CSS_PATH', 'assets/css/');
define('DIR_WS_IMAGE_PATH', 'assets/images/');

define('DIR_WS_CONTROLLER_PATH', 'controller/');

define('DIR_FS_INCLUDES', $gen_root . '/includes/');
define('DIR_FS_TEMPLATE', $gen_root . '/template/');
define('DIR_FS_CONTROLLER', $gen_root . '/controller');
define('DIR_FS_PDF_TMP_PATH', $gen_root . '/temp');

define('DIR_FS_QRCODES_TMP_PATH', $gen_root . '/qrcodes');
define('DIR_WS_QRCODES_TMP_PATH', HTTP_SERVER . WS_ROOT . 'qrcodes/');

define('DIR_FS_UPLOAD_PATH', $gen_root . '/upload/');
define('DIR_WS_UPLOAD_PATH', HTTP_SERVER . WS_ROOT . 'upload/');

define('DIR_FS_EXCEL_PATH', $gen_root . '/upload/tmpreport/');
define('DIR_WS_EXCEL_PATH', HTTP_SERVER . WS_ROOT . 'upload/tmpreport/');

define('DIR_FS_PAGE_IMAGE_PATH', $gen_root . '/upload/pages/');
define('DIR_WS_PAGE_IMAGE_PATH', HTTP_SERVER . WS_ROOT . 'upload/pages/');

define('DIR_FS_SLIDER_PATH', $gen_root . '/upload/slider/');
define('DIR_WS_SLIDER_PATH', HTTP_SERVER . WS_ROOT . 'upload/slider/');

define('DIR_FS_PRESS_PATH', $gen_root . '/upload/press/');
define('DIR_WS_PRESS_PATH', HTTP_SERVER . WS_ROOT . 'upload/press/');

define('DIR_FS_DOWNLOAD_PATH', $gen_root . '/upload/download/');
define('DIR_WS_DOWNLOAD_PATH', HTTP_SERVER . WS_ROOT . 'upload/download/');

define('DIR_FS_OURTEAM_PATH', $gen_root . '/upload/team/');
define('DIR_WS_OURTEAM_PATH', HTTP_SERVER . WS_ROOT . 'upload/team/');

define('DIR_FS_SERVICES_PATH', $gen_root . '/upload/services/');
define('DIR_WS_SERVICES_PATH', HTTP_SERVER . WS_ROOT . 'upload/services/');

define('DIR_FS_CASESTUDIES_PATH', $gen_root . '/upload/casestudies/');
define('DIR_WS_CASESTUDIES_PATH', HTTP_SERVER . WS_ROOT . 'upload/casestudies/');

define('DIR_FS_POPUP_PATH', $gen_root . '/upload/popup/');
define('DIR_WS_POPUP_PATH', HTTP_SERVER . WS_ROOT . 'upload/popup/');

define('DIR_FS_INFOGRAPHICS_PATH', $gen_root . '/upload/infographics/');
define('DIR_WS_INFOGRAPHICS_PATH', HTTP_SERVER . WS_ROOT . 'upload/infographics/');

define('DIR_FS_BLOG_PATH', $gen_root . '/upload/blog/');
define('DIR_WS_BLOG_PATH', HTTP_SERVER . WS_ROOT . 'upload/blog/');

define('DIR_FS_BIODATA_PATH', $gen_root . '/upload/biodata/');
define('DIR_WS_BIODATA_PATH', HTTP_SERVER . WS_ROOT . 'upload/biodata/');

define('DIR_WS_PROFILE_PATH', HTTP_SERVER . WS_ROOT . 'upload/profile/');

define('DIR_FS_IMPORT_PATH', $gen_root . '/upload/files/import/');

define('DIR_FS_SITE_PATH', $gen_root . '/');

define('ENCRYPTKEY', '0104proencrypt17');
define('BLOCKSIZE', 256);

define('ADMINTITLE', 'ggate, Admin Panel');
define('USE_PCONNECT', 'false');
define('CFG_TIME_ZONE', 'Asia/Kolkata');

define('GCLOUD_PROJECT_ID', 'ggate-mob-cloud');
define('GCLOUD_CDN_URL', 'https://cdn.ggate.app/');
define('GCLOUD_BUCKET', 'ggate-cdn-images');

date_default_timezone_set('Asia/Kolkata');

$gcloudPrivateKey = '{
"type": "service_account",
"project_id": "ggate-mob-cloud",
"private_key_id": "fd6ab34640a129a49ebcdb4dba71a7dacdd0d9f0",
"private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC6RP8EogsIjwFb\nWI07oT8umtos9166PsscDP8McOYaKqQjAg4SQJxMoxkk0u6GlfCJbg2+qPUnzKLF\nq2PA1fPHLZIKXE23JwG/ygnJKJ2B+mSIObubTMg4B48zQIfRYlWY69LaBJm2YFqd\nn7wavOBFhP0GEo18PlinzVqkL/U4fbzbQPLH5J9e6eIgeEhcmy9mgN4yOUVD6pCU\nTB5p87iB2wVNORu+RnTUPe82iWsAIkewgkSjiH22o3OGJAi6jTm4FE3/SSSo4TYJ\nUrVuQEZpWBQ08nDx8P+44ZMa/u0+qNRSnYI8+kbLussRH9GkedybSf1JmkobktqZ\ns5/9MBddAgMBAAECggEACDpqBXte/2bJyY401vB9iQKAJsJapeGGOVv9nEAUeg1u\nKLm7g2TlQqFlx/GlZb6T3I5G/ygZhEOO4Sp8ApWw6hiCPgDr0e5gLmevXPp1UruL\n21Spi+etZIQ4+CuelEpCu7mXG9heLec9uX7H4Wp8gbWpM654H4flhKwWl/AiZfdc\nlTc7+CcBAIJLLAGgZuumP9qEtEz2arLOoJYpMbAZd1uUutoaOWFv6hI1n9LtMV7Z\nWTw+ok6K5WATZ9E2kXps+lgvFLimslMdffBxlXQPix0QCCSrnavND3gszTp2hoEa\n4qlArFvAbb/agAKxInbEf+9WZ24y+f57aVQkSlF8RQKBgQDuglO/ns1aBlDga/5F\nQJsCSPaU98f6aWhyxsCcUCwEzibx2xqNUAS0HgE5WgXItib88r+3WV3olkl6nmiK\n2VlZpkZN52sBhCH95wCb9ndzH0bt+aT/YIJVGZN7maNIt3/K8bzpl9qg0f1d5kTf\nXZnb3i2RrBWdcNW+xs+aB+QQzwKBgQDH7fHIIMfv4QQ1w0MP4hNZo4eVeDHqDfQo\n/502mKYV58iVZapxeM9M6guSOMjfMJlWlUu3PpENkrEX8/gelM0Uyww3XouvZJvd\nOZVCPhJ8hpY4m0YJdS/RVHlPLfJYqMu11Zcr0vr4K2llwF7eXeABj5W2zMYp8+qz\nrAWSQNWoEwKBgDZxqYxkQ5v5NR0FidVlGf07io4WwZ930E/i7rS/2EyAgoNS8Iyx\nZ2F2N/FPi2J1shCrmPfRfo5JQBytbE/FrY+5VPMTkGmYL+o1gP4ZFMtCqH5KLk/d\nR+MFI5VjTNKckJ9S7zyjXVS4mo2EstQKGcUGwoAuOADocOJLn7gZdQYhAoGBAIsw\nMtW7TLbKFx4+1J9oK0SKvsfmqAlksYkhfBgYafhyJ4krAGCDVP8dKfUgp2gK85X9\n1nq6ik3CxJwCc8kGm0hQnC6oGeN/zatUfX9iq4gaQtch8r5+4U2A2/ut5zymNzxe\neSI/fg3sWzJFnUZ+YId1qStcoNFwSQPRTQenL5gVAoGBAJJ5aXzSYijHt9bIR19e\ngnJDRJS7OEhUy0pOT4rpbPzCOaVZztBeVYE0H2SMJLaeHfr84q7/Ww/Lnh+eqS3d\nQxlxl/EqRgoMjmDtrrbiqZW55B5jUK1mjSaUbh9zW6QQDNyQYPBREJnE81nQZLff\nO+AjyJCc3PeUrLn6qZEnH++s\n-----END PRIVATE KEY-----\n",
"client_email": "ggate-storage@ggate-mob-cloud.iam.gserviceaccount.com",
"client_id": "109407703562336358148",
"auth_uri": "https://accounts.google.com/o/oauth2/auth",
"token_uri": "https://oauth2.googleapis.com/token",
"auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
"client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/ggate-storage%40ggate-mob-cloud.iam.gserviceaccount.com"
}';

if (isset($_SESSION['userID']) && is_numeric($_SESSION['userID'])) {
	define('ADMIN_ALLOWED', 1);
}
