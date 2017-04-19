<?php

if (version_compare($this->container['my_cms_version'], $this->container['theme']->getStyleInfo("cms_version"), '<')) {
    $message = $this->container['theme']->getStyleInfo("name") . ' template requires at least MyCMS ' . $this->container['theme']->getStyleInfo("cms_version") . ', Please upgrade!';
    throw new \MyCMS\App\Utils\Exceptions\MyCMSException($message, "template_001");
}

//NECESSARY PAGE DON'T REMOVE
//PAGE
$styleInfo = $this->container['theme']->styleInfo(MY_THEME);
$this->container['router']->map('GET', '/', 'pages/index');
$this->container['router']->map('GET', '/index', 'pages/index');
$this->container['router']->map('GET', '/404', '404');
$this->container['router']->map('GET', '/maintenance', 'maintenance');
$this->container['router']->map('GET|POST', '/login', 'pages/login');
$this->container['router']->map('GET|POST', '/registration', 'pages/registration');
$this->container['router']->map('GET|POST', '/logout', 'pages/logout');
$this->container['router']->map('GET', '/tests', 'pages/test');

$this->container['router']->map('GET', '/blog', 'pages/blog');
$this->container['router']->map('GET', '/blog/m/[i:maxPosts]', 'pages/blog');
$this->container['router']->map('GET', '/blog/max/[i:maxPosts]', 'pages/blog');

$this->container['router']->map('GET|POST', '/blog/[i:year]/[i:month]/[*:title]', 'pages/blog');
$this->container['router']->map('GET|POST', '/blog/id/[i:id]', 'pages/blog');
$this->container['router']->map('GET', '/blog/category/[:category]', 'pages/blog');
$this->container['router']->map('GET', '/blog/category/[:category]/max/[i:maxPosts]', 'pages/blog');
$this->container['router']->map('GET', '/blog/author/[i:author]', 'pages/blog');
$this->container['router']->map('GET', '/blog/author/[i:author]/max/[i:maxPosts]', 'pages/blog');
$this->container['router']->map('GET', '/blog/search/[:search]', 'pages/blog');
$this->container['router']->map('GET', '/blog/search/[:search]/max/[i:maxPosts]', 'pages/blog');

$this->container['theme']->addStyleScript('css', '{@siteURL@}/src/App/Content/Theme/{@siteTEMPLATE@}/assets/css/bootstrap.min.css');
$this->container['theme']->addStyleScript('css', '{@siteURL@}/src/App/Content/Theme/{@siteTEMPLATE@}/assets/css/style.css');
$this->container['theme']->addStyleScript('script', '{@siteURL@}/src/App/Content/Theme/{@siteTEMPLATE@}/assets/js/jquery-3.1.0.min.js');
$this->container['theme']->addStyleScript('script', '{@siteURL@}/src/App/Content/Theme/{@siteTEMPLATE@}/assets/js/bootstrap.min.js');

$this->container['theme']->indexErrorStyle("<br><div class='container'><div class='danger'>", "</div></div>");

include "customizer.php";