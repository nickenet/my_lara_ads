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
    
    'countries' => 'pays',
    
    'login' => 'connexion',
    'logout' => 'deconnexion',
    'signup' => 'inscription',
    'create' => 'creer-annonce',
    
    'about' => 'apropos.html',
    'contact' => 'contact.html',
    'faq' => 'faq.html',
    'phishing' => 'usurpation-d-identite.html',
    'anti-scam' => 'anti-arnaque.html',
    'terms' => 'conditions.html',
    'privacy' => 'vie-privee.html',

];

if (config('larapen.core.multi_countries_website'))
{
    // Sitemap
    $lcRoutes['sitemap'] = '{countryCode}/plan-du-site.html';
    $lcRoutes['v-sitemap'] = ':countryCode/plan-du-site.html';

    // Latest Ads
    $lcRoutes['search'] = '{countryCode}/recherche';
    $lcRoutes['t-search'] = 'recherche';
    $lcRoutes['v-search'] = ':countryCode/recherche';

    // Search by Sub-Category
    $lcRoutes['search-subCat'] = '{countryCode}/categorie/{catSlug}/{subCatSlug}';
    $lcRoutes['t-search-subCat'] = 'categorie';
    $lcRoutes['v-search-subCat'] = ':countryCode/categorie/:catSlug/:subCatSlug';

    // Search by Category
    $lcRoutes['search-cat'] = '{countryCode}/categorie/{catSlug}';
    $lcRoutes['t-search-cat'] = 'categorie';
    $lcRoutes['v-search-cat'] = ':countryCode/categorie/:catSlug';

    // Search by Location
    $lcRoutes['search-location'] = '{countryCode}/petites-annonces/{city}/{id}';
    $lcRoutes['t-search-location'] = 'petites-annonces';
    $lcRoutes['v-search-location'] = ':countryCode/petites-annonces/:city/:id';

    // Search by User
    $lcRoutes['search-user'] = '{countryCode}/recherche/vendeur/{id}';
    $lcRoutes['t-search-user'] = 'recherche/vendeur';
    $lcRoutes['v-search-user'] = ':countryCode/recherche/vendeur/:id';
}
else
{
    // Sitemap
    $lcRoutes['sitemap'] = 'plan-du-site.html';
    $lcRoutes['v-sitemap'] = 'plan-du-site.html';

    // Latest Ads
    $lcRoutes['search'] = 'recherche';
    $lcRoutes['t-search'] = 'recherche';
    $lcRoutes['v-search'] = 'recherche';

    // Search by Sub-Category
    $lcRoutes['search-subCat'] = 'categorie/{catSlug}/{subCatSlug}';
    $lcRoutes['t-search-subCat'] = 'categorie';
    $lcRoutes['v-search-subCat'] = 'categorie/:catSlug/:subCatSlug';

    // Search by Category
    $lcRoutes['search-cat'] = 'categorie/{catSlug}';
    $lcRoutes['t-search-cat'] = 'categorie';
    $lcRoutes['v-search-cat'] = 'categorie/:catSlug';

    // Search by Location
    $lcRoutes['search-location'] = 'petites-annonces/{city}/{id}';
    $lcRoutes['t-search-location'] = 'petites-annonces';
    $lcRoutes['v-search-location'] = 'petites-annonces/:city/:id';

    // Search by User
    $lcRoutes['search-user'] = 'recherche/vendeur/{id}';
    $lcRoutes['t-search-user'] = 'recherche/vendeur';
    $lcRoutes['v-search-user'] = 'recherche/vendeur/:id';
}

return $lcRoutes;
