<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
//	return View::make('hello');
	$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
	$getfield = '?screen_name=joepferguson';
	$requestMethod = 'GET';
	$settings = array(
		'oauth_access_token' => $_ENV['oauth_access_token'],
		'oauth_access_token_secret' => $_ENV['oauth_access_token_secret'],
		'consumer_key' => $_ENV['consumer_key'],
		'consumer_secret' => $_ENV['consumer_secret'],
	);
	$twitter = new TwitterAPIExchange($settings);
	$response = $twitter->setGetfield($getfield)
	                    ->buildOauth($url, $requestMethod)
	                    ->performRequest();

	var_dump(json_decode($response));
});
