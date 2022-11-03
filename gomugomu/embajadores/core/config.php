<?php
define("_SERVER","localhost");
define("_USERNAME","gomugomu");
define("_PASSWORD","osaka2011");
define("_BD","admin_feedback");
define("_LANGUAGE","es");
define("_BACKENDDIR","template/backend/");
define("_BACKENDNAME","admin2/");
define("_FRONTENDDIR","template/frontend/");
define("_FRONTENDNAME","app/");
define("_URLCORE","core/");
define("_URLMODULES","core/var/Modules.php");
define("_COIN","€");
define("_WEBSITENAME","WEBSITE NAME");
define("_HTACCESS",1);
define("_UPDATE_TIME_FILES_IN_SECONDS",10);

define("_PAGINATOR_ITEMS_PER_PAGE",10);
define("_PAGINATOR_MID_RANGE",5);

define("_VARMENU","go");
define("_TIMEFORMAT","d-m-Y H:i");
define("_MAXNEWS","10");
define("_webNAME","Grupo goaprint");
define("_webURL","http://www.socalcommunitypages.com");
define("_mailMAIL","no-reply@socalcommunitypages.com");
define("_mailHOST","localhost");
define("_mailUSER","gcurtis@socalcommunitypages.com");
define("_mailPASS","socal360");
define("_mailNAME","noresponder.fernandosalom.es");
define("_NEWS_CATEGORY","<strong>Más noticias sobre : </strong>");
define("_NEWS_CATEGORY_NONE","Sin categoria");
define("_NEWS_COMMENTS","COMENTARIOS");
define("_NEWS_COMMENTS_AVAILABLE_TAGS","<p><a><b><strong><i><em><li><ul><u>");
//INFO UPLOADS
define("_UPLOAD_DIR","../../img/upload");// The directory for the images to be saved in
define("_UPLOAD_PATH",_UPLOAD_DIR."/");// The path to where the image will be saved
define("_LARGE_IMAGE_PREFIX","large_");// The prefix name to large image
define("_RESIZE_IMAGE_PREFIX","resize_");// The prefix name to the thumb image
define("_THUMB_IMAGE_PREFIX","thumbnail_");// The prefix name to the thumb image
define("_RANDOM_KEY",strtotime(date('Y-m-d H:i:s')));
define("_LARGE_IMAGE_NAME",_LARGE_IMAGE_PREFIX._RANDOM_KEY);  
define("_RESIZE_IMAGE_NAME",_RESIZE_IMAGE_PREFIX._RANDOM_KEY);
define("_THUMB_IMAGE_NAME",_THUMB_IMAGE_PREFIX._RANDOM_KEY);
define("_MAX_FILE", "3");// Maximum file size in MB
define("_MAX_WIDTH","800");// Max width allowed for the large image
define("_THUMB_WIDTH","170");// Width of thumbnail image
define("_THUMB_HEIGHT","75");// Height of thumbnail image
define("_RESIZE_WIDTH","610");// Width of thumbnail image
define("_RESIZE_HEIGHT","270");// Height of thumbnail image
?>