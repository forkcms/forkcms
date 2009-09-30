<?php

$navigation['dashboard'] = array('url' => 'dashboard/index', 'label' => 'Dashboard');

$navigation['pages'] = array('url' => 'pages/index', 'label' => 'Pages');

$navigation['modules'] = array('url' => null, 'label' => 'Modules');
$navigation['modules']['children']['blog'] = array('url' => null, 'label' => 'Blog');
$navigation['modules']['children']['blog']['children'][] = array('url' => 'blog/index', 'label' => 'Posts');
$navigation['modules']['children']['blog']['children'][] = array('url' => 'blog/categories', 'label' => 'Categories');
$navigation['modules']['children']['blog']['children'][] = array('url' => 'blog/comments', 'label' => 'Comments');
$navigation['modules']['children']['events'] = array('url' => 'events/index', 'label' => 'Events');
$navigation['modules']['children']['faq'] = array('url' => null, 'label' => 'FAQ');
$navigation['modules']['children']['faq']['children'][] = array('url' => 'faq/index', 'label' => 'Questions');
$navigation['modules']['children']['faq']['children'][] = array('url' => 'faq/categories', 'label' => 'Categories');
$navigation['modules']['children']['gallery'] = array('url' => 'gallery/index', 'label' => 'Gallery');
$navigation['modules']['children']['guestbook'] = array('url' => 'guestbook/index', 'label' => 'Guestbook');
$navigation['modules']['children']['links'] = array('url' => null, 'label' => 'Links');
$navigation['modules']['children']['links']['children'][] = array('url' => 'links/index', 'label' => 'Links');
$navigation['modules']['children']['links']['children'][] = array('url' => 'links/categories', 'label' => 'Categories');
$navigation['modules']['children']['news'] = array('url' => 'news/index', 'label' => 'News');
$navigation['modules']['children']['slideshow'] = array('url' => 'slideshow/index', 'label' => 'Slideshow');
$navigation['modules']['children']['location'] = array('url' => 'location/index', 'label' => 'Location');
$navigation['modules']['children']['spotlight'] = array('url' => 'spotlight/index', 'label' => 'Spotlight');
$navigation['modules']['children']['formbuilder'] = array('url' => 'formbuilder/index', 'label' => 'FormBuilder');
$navigation['modules']['children']['extranet'] = array('url' => null, 'label' => 'Extranet');
$navigation['modules']['children']['extranet']['children'][] = array('url' => 'extranet/index', 'label' => 'Groups');
$navigation['modules']['children']['extranet']['children'][] = array('url' => 'extranet/users', 'label' => 'Users');

$navigation['mailmotor'] = array('url' => null, 'label' => 'Mailmotor');
$navigation['mailmotor']['children'][] = array('url' => 'mailmotor/index', 'label' => 'Newsletters');
$navigation['mailmotor']['children'][] = array('url' => 'mailmotor/groups', 'label' => 'Groups');
$navigation['mailmotor']['children'][] = array('url' => 'mailmotor/addresses', 'label' => 'Addresses');
$navigation['mailmotor']['children'][] = array('url' => 'mailmotor/settings', 'label' => 'Settings');

$navigation['statistics'] = array('url' => null, 'label' => 'Statistics');
$navigation['statistics']['children']['this_month'] = array('url' => null, 'label' => 'ThisMonth');
$navigation['statistics']['children']['this_month']['children'][] = array('url' => 'statistics/referrers', 'label' => 'Referrers');
$navigation['statistics']['children']['this_month']['children'][] = array('url' => 'statistics/search', 'label' => 'SearchTerms');
$navigation['statistics']['children']['this_month']['children'][] = array('url' => 'statistics/visitors', 'label' => 'Visitors');
$navigation['statistics']['children']['archive'] = array('url' => null, 'label' => 'Archive');

$navigation['settings'] = array('url' => null, 'label' => 'Settings');
$navigation['settings']['children']['general_settings'] = array('url' => 'settings/general', 'label' => 'GeneralSettings');
$navigation['settings']['children']['module_settings'] = array('url' => 'settings/index', 'label' => 'ModuleSettings');
$navigation['settings']['children']['users'] = array('url' => 'users/index', 'label' => 'Users');
$navigation['settings']['children']['labels'] = array('url' => 'admin/labels', 'label' => 'Labels');

?>