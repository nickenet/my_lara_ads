<?php
/**
 * Created by PhpStorm.
 * User: mayeul
 * Date: 24/02/2016
 * Time: 21:41
 */
$lcRoutes = [
    /*
    |--------------------------------------------------------------------------
    | Routes Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the global website.
    |
    */
    
    'countries' => 'countries',
    
    'login' => 'login',
    'logout' => 'logout',
    'signup' => 'signup',
    'create' => 'create',
    
    'about' => 'about.html',
    'contact' => 'contact.html',
    'faq' => 'faq.html',
    'phishing' => 'phishing.html',
    'anti-scam' => 'anti-scam.html',
    'terms' => 'terms.html',
    'privacy' => 'privacy.html',

];

if (config('larapen.core.multi_countries_website'))
{
    // Sitemap
    $lcRoutes['sitemap'] = '{countryCode}/sitemap.html';
    $lcRoutes['v-sitemap'] = ':countryCode/sitemap.html';

    // Latest Ads
    $lcRoutes['search'] = '{countryCode}/search';
    $lcRoutes['t-search'] = 'search';
    $lcRoutes['v-search'] = ':countryCode/search';

    // Search by Sub-Category
    $lcRoutes['search-subCat'] = '{countryCode}/category/{catSlug}/{subCatSlug}';
    $lcRoutes['t-search-subCat'] = 'category';
    $lcRoutes['v-search-subCat'] = ':countryCode/category/:catSlug/:subCatSlug';

    // Search by Category
    $lcRoutes['search-cat'] = '{countryCode}/category/{catSlug}';
    $lcRoutes['t-search-cat'] = 'category';
    $lcRoutes['v-search-cat'] = ':countryCode/category/:catSlug';

    // Search by Location
    $lcRoutes['search-location'] = '{countryCode}/free-ads/{city}/{id}';
    $lcRoutes['t-search-location'] = 'free-ads';
    $lcRoutes['v-search-location'] = ':countryCode/free-ads/:city/:id';

    // Search by User
    $lcRoutes['search-user'] = '{countryCode}/search/user/{id}';
    $lcRoutes['t-search-user'] = 'search/user';
    $lcRoutes['v-search-user'] = ':countryCode/search/user/:id';
}
else
{
    // Sitemap
    $lcRoutes['sitemap'] = 'sitemap.html';
    $lcRoutes['v-sitemap'] = 'sitemap.html';

    // Latest Ads
    $lcRoutes['search'] = 'search';
    $lcRoutes['t-search'] = 'search';
    $lcRoutes['v-search'] = 'search';

    // Search by Sub-Category
    $lcRoutes['search-subCat'] = 'category/{catSlug}/{subCatSlug}';
    $lcRoutes['t-search-subCat'] = 'category';
    $lcRoutes['v-search-subCat'] = 'category/:catSlug/:subCatSlug';

    // Search by Category
    $lcRoutes['search-cat'] = 'category/{catSlug}';
    $lcRoutes['t-search-cat'] = 'category';
    $lcRoutes['v-search-cat'] = 'category/:catSlug';

    // Search by Location
    $lcRoutes['search-location'] = 'free-ads/{city}/{id}';
    $lcRoutes['t-search-location'] = 'free-ads';
    $lcRoutes['v-search-location'] = 'free-ads/:city/:id';

    // Search by User
    $lcRoutes['search-user'] = 'search/user/{id}';
    $lcRoutes['t-search-user'] = 'search/user';
    $lcRoutes['v-search-user'] = 'search/user/:id';
}

return $lcRoutes;
