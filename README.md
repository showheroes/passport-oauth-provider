Laravel 5, 7, 8, 9 ShowHeroes Passport OAuth Provider
Server and client setup
Server side requires use of Laravel Passport 2.0.

How to create authentication point (login and redirect), please, see IFactory project.

Create new client on relative Passport instance: php artisan passport:client

User ID: any
Name: Project name Client
Redirect URL: Project OAuth Redirect URL. Example: https://ifactory.showheroes.com/auth/passport/callback
Execute the command php artisan passport:keys for keys generating.

Store client secret and id. You will need them later for client ENV setup.

Go to database and remove user_id biding from the new created client. Table name: oauth_clients. Set field user_id for the client to NULL.

Install library in Composer:

{
...
"require": {
"showheroes/passport-oauth-provider": "dev-master"
},
...
"repositories": [
{
"type": "vcs",
"no-api": true,
"url":  "git@github.com:showheroes/passport-oauth-provider.git"
}
]
...
}

For Laravel <= 5.0 add service provider in config/app.php: \ShowHeroes\PassportOAuthProvider\PassportOAuthServiceProvider::class

Add Passport service configuration in config/services.php:


    /*
    |--------------------------------------------------------------------------
    | OAuth clients
    |--------------------------------------------------------------------------
    |
    */

    'passport' => [
        'oauth_server'  => env('PASSPORT_OAUTH_SERVER', 'https://passport.showheroes.com'),
        'client_id'     => env('PASSPORT_CLIENT_ID'),
        'client_secret' => env('PASSPORT_CLIENT_SECRET'),
        'redirect'      => env('PASSPORT_REDIRECT_URL'),
    ],

Update .env file with client config variables:
# Video Library OAuth2.0 client
PASSPORT_OAUTH_SERVER=http://passport.showheroes.dev
PASSPORT_CLIENT_ID=4
PASSPORT_CLIENT_SECRET=PlaceHereSomeSuperSecretId
PASSPORT_REDIRECT_URL=http://ifactory.showheroes.test/auth/passport/callback

AND THAT'S IT.
