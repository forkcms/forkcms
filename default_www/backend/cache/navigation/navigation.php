<?php

$aNavigation['dashboard'] = array('url' => 'dashboard/index', 'label' => 'Dashboard');

$aNavigation['pages'] = array('url' => 'pages/index', 'label' => 'Pages');

$aNavigation['modules'] = array('url' => null, 'label' => 'Modules');
$aNavigation['modules']['children']['blog'] = array('url' => null, 'label' => 'Blog');
$aNavigation['modules']['children']['blog']['children'][] = array('url' => 'blog/index', 'label' => 'Posts');
$aNavigation['modules']['children']['blog']['children'][] = array('url' => 'blog/categories', 'label' => 'Categories');
$aNavigation['modules']['children']['blog']['children'][] = array('url' => 'blog/comments', 'label' => 'Comments');
$aNavigation['modules']['children']['events'] = array('url' => 'events/index', 'label' => 'Events');
$aNavigation['modules']['children']['faq'] = array('url' => null, 'label' => 'FAQ');
$aNavigation['modules']['children']['faq']['children'][] = array('url' => 'faq/index', 'label' => 'Questions');
$aNavigation['modules']['children']['faq']['children'][] = array('url' => 'faq/categories', 'label' => 'Categories');
$aNavigation['modules']['children']['gallery'] = array('url' => 'gallery/index', 'label' => 'Gallery');
$aNavigation['modules']['children']['guestbook'] = array('url' => 'guestbook/index', 'label' => 'Guestbook');
$aNavigation['modules']['children']['links'] = array('url' => null, 'label' => 'Links');
$aNavigation['modules']['children']['links']['children'][] = array('url' => 'links/index', 'label' => 'Links');
$aNavigation['modules']['children']['links']['children'][] = array('url' => 'links/categories', 'label' => 'Categories');
$aNavigation['modules']['children']['news'] = array('url' => 'news/index', 'label' => 'News');
$aNavigation['modules']['children']['slideshow'] = array('url' => 'slideshow/index', 'label' => 'Slideshow');
$aNavigation['modules']['children']['location'] = array('url' => 'location/index', 'label' => 'Location');
$aNavigation['modules']['children']['spotlight'] = array('url' => 'spotlight/index', 'label' => 'Spotlight');
$aNavigation['modules']['children']['formbuilder'] = array('url' => 'formbuilder/index', 'label' => 'FormBuilder');
$aNavigation['modules']['children']['extranet'] = array('url' => null, 'label' => 'Extranet');
$aNavigation['modules']['children']['extranet']['children'][] = array('url' => 'extranet/index', 'label' => 'Groups');
$aNavigation['modules']['children']['extranet']['children'][] = array('url' => 'extranet/users', 'label' => 'Users');

$aNavigation['mailmotor'] = array('url' => null, 'label' => 'Mailmotor');
$aNavigation['mailmotor']['children'][] = array('url' => 'mailmotor/index', 'label' => 'Newsletters');
$aNavigation['mailmotor']['children'][] = array('url' => 'mailmotor/groups', 'label' => 'Groups');
$aNavigation['mailmotor']['children'][] = array('url' => 'mailmotor/addresses', 'label' => 'Addresses');
$aNavigation['mailmotor']['children'][] = array('url' => 'mailmotor/settings', 'label' => 'Settings');

$aNavigation['statistics'] = array('url' => null, 'label' => 'Statistics');
$aNavigation['statistics']['children']['this_month'] = array('url' => null, 'label' => 'ThisMonth');
$aNavigation['statistics']['children']['this_month']['children'][] = array('url' => 'statistics/referrers', 'label' => 'Referrers');
$aNavigation['statistics']['children']['this_month']['children'][] = array('url' => 'statistics/search', 'label' => 'SearchTerms');
$aNavigation['statistics']['children']['this_month']['children'][] = array('url' => 'statistics/visitors', 'label' => 'Visitors');
$aNavigation['statistics']['children']['archive'] = array('url' => null, 'label' => 'Archive');

$aNavigation['setting'] = array('url' => null, 'label' => 'Settings');
$aNavigation['setting']['children']['general_settings'] = array('url' => 'settings/general', 'label' => 'GeneralSettings');
$aNavigation['setting']['children']['module_settings'] = array('url' => 'settings/index', 'label' => 'ModuleSettings');
$aNavigation['setting']['children']['users'] = array('url' => 'users/index', 'label' => 'Users');
$aNavigation['setting']['children']['labels'] = array('url' => 'admin/labels', 'label' => 'Labels');

?>