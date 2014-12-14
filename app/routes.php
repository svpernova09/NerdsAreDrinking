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
	return View::make('hello');
//	$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
//	$getfield = '?screen_name=joepferguson';
//	$requestMethod = 'GET';
//	$settings = array(
//		'oauth_access_token' => $_ENV['oauth_access_token'],
//		'oauth_access_token_secret' => $_ENV['oauth_access_token_secret'],
//		'consumer_key' => $_ENV['consumer_key'],
//		'consumer_secret' => $_ENV['consumer_secret'],
//	);
//
//	$since = DB::table('last_tweets')->orderBy('created_at', 'desc')->first();
//	if (!is_null($since))
//	{
//		$getfield .= '&since_id=' . $since->since_id;
//	}
//
//	$twitter = new TwitterAPIExchange($settings);
//	$response = $twitter->setGetfield($getfield)
//	                    ->buildOauth($url, $requestMethod)
//	                    ->performRequest();
//	// Decode the response
//	$response = json_decode($response);
//
//	if (count($response) > 0)
//	{
//		// update the since_id with the lastest tweet in $response
//		$lastTweet = new LastTweet;
//		$lastTweet->since_id = $response[0]->id;
//		$lastTweet->save();
//
//		foreach ($response as $tweet)
//		{
//			if (strpos($tweet->text,'Drinking a') !== false &&
//				strpos($tweet->source, 'untappd') !== false)
//			{
//
//				echo $tweet->text . '<br>';
//				// Retweet this $tweet
//				$user = '@' . $tweet->user->screen_name;
//				$status = '#NerdsDrinking RT: ' . $user . ' ' . $tweet->text;
//				$regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?).*$)@";
//				$status =  preg_replace($regex, ' ', $status);
//				$status = str_replace(' —  ', '', $status);
//				$inReplyTo = $tweet->id;
//				$url = 'https://api.twitter.com/1.1/statuses/update.json';
//				$postFields['status'] = json_encode($status);
//				$postFields['in_reply_to_status_id'] = $inReplyTo;
//				$requestMethod = 'POST';
//				var_dump($status);
//				var_dump(strlen($status));
//
//				$tweet = new TwitterAPIExchange($settings);
//				$response = $tweet->setPostfields($postFields)
//				                    ->buildOauth($url, $requestMethod)
//				                    ->performRequest();
//			}
//		}
//	}
//
//	var_dump($response);
});
