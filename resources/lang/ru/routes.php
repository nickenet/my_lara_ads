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
    
    'countries' => 'paises',
    
    'login' => 'login',
    'logout' => 'cerrar-sesion',
    'signup' => 'registrate',
    'create' => 'crear-anuncio',
    
    'about' => 'sobre-nosotros.html',
    'contact' => 'contacto.html',
    'faq' => 'faq.html',
    'phishing' => 'suplantacion-de-identidad.html',
    'anti-scam' => 'anti-estafa.html',
    'terms' => 'condiciones.html',
    'privacy' => 'vida-privada.html',

];

if (config('larapen.core.multi_countries_website'))
{
    // Sitemap
    $lcRoutes['sitemap'] = '{countryCode}/mapa-del-sitio.html';
    $lcRoutes['v-sitemap'] = ':countryCode/mapa-del-sitio.html';

    // Latest Ads
    $lcRoutes['search'] = '{countryCode}/busqueda';
    $lcRoutes['t-search'] = 'busqueda';
    $lcRoutes['v-search'] = ':countryCode/busqueda';

    // Search by Sub-Category
    $lcRoutes['search-subCat'] = '{countryCode}/categoria/{catSlug}/{subCatSlug}';
    $lcRoutes['t-search-subCat'] = 'categoria';
    $lcRoutes['v-search-subCat'] = ':countryCode/categoria/:catSlug/:subCatSlug';

    // Search by Category
    $lcRoutes['search-cat'] = '{countryCode}/categoria/{catSlug}';
    $lcRoutes['t-search-cat'] = 'categoria';
    $lcRoutes['v-search-cat'] = ':countryCode/categoria/:catSlug';

    // Search by Location
    $lcRoutes['search-location'] = '{countryCode}/anuncios-gratuitos/{city}/{id}';
    $lcRoutes['t-search-location'] = 'anuncios-gratuitos';
    $lcRoutes['v-search-location'] = ':countryCode/anuncios-gratuitos/:city/:id';

    // Search by User
    $lcRoutes['search-user'] = '{countryCode}/busqueda/vendedor/{id}';
    $lcRoutes['t-search-user'] = 'busqueda/vendedor';
    $lcRoutes['v-search-user'] = ':countryCode/busqueda/vendedor/:id';
}
else
{
    // Sitemap
    $lcRoutes['sitemap'] = 'mapa-del-sitio.html';
    $lcRoutes['v-sitemap'] = 'mapa-del-sitio.html';

    // Latest Ads
    $lcRoutes['search'] = 'busqueda';
    $lcRoutes['t-search'] = 'busqueda';
    $lcRoutes['v-search'] = 'busqueda';

    // Search by Sub-Category
    $lcRoutes['search-subCat'] = 'categoria/{catSlug}/{subCatSlug}';
    $lcRoutes['t-search-subCat'] = 'categoria';
    $lcRoutes['v-search-subCat'] = 'categoria/:catSlug/:subCatSlug';

    // Search by Category
    $lcRoutes['search-cat'] = 'categoria/{catSlug}';
    $lcRoutes['t-search-cat'] = 'categoria';
    $lcRoutes['v-search-cat'] = 'categoria/:catSlug';

    // Search by Location
    $lcRoutes['search-location'] = 'anuncios-gratuitos/{city}/{id}';
    $lcRoutes['t-search-location'] = 'anuncios-gratuitos';
    $lcRoutes['v-search-location'] = 'anuncios-gratuitos/:city/:id';

    // Search by User
    $lcRoutes['search-user'] = 'busqueda/vendedor/{id}';
    $lcRoutes['t-search-user'] = 'busqueda/vendedor';
    $lcRoutes['v-search-user'] = 'busqueda/vendedor/:id';
}

return $lcRoutes;