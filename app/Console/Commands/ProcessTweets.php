<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ProcessTweets extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nerds:process';

	/**4
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Process Tweets Looking for beers.';

    /**
     *
     */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $this->info('Test: ' . $this->argument('test'));
        
		$nerds = $this->getNerds();

		foreach ($nerds as $nerd)
		{
			$tweets = $this->getTweets($nerd);

			if (count($tweets) > 0)
			{
				// update the since_id with the latest tweet in $tweets
				if (!$this->argument('test')) {
					$this->updateSince($tweets['0']->id, $nerd->name);
				}
			}

			foreach ($tweets as $tweet)
			{
				$this->parseTweets($tweet);
			}
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('test', InputArgument::OPTIONAL, 'Run in test mode. Does not update database. Does not tweet'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
//			array('test', 't', InputOption::VALUE_OPTIONAL, 'If present, run test mode', 'false'),
		);
	}

	public function getNerds()
	{
		return Nerds::all();
	}

	public function getSettings()
	{
		$settings = array(
			'oauth_access_token' => env('oauth_access_token', ''),
			'oauth_access_token_secret' => env('oauth_access_token_secret', ''),
			'consumer_key' => env('consumer_key', ''),
			'consumer_secret' => env('consumer_secret', ''),
		);

		return $settings;
	}

	public function getSince($name)
	{
		return $since = DB::table('last_tweets')
			->where('name', $name)
			->orderBy('created_at', 'desc')
			->first();
	}

	public function getTweets($user)
	{
		$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
		$getField = '?screen_name=' . $user->twitter;

		$since = $this->getSince($user->name);
		if (!is_null($since))
		{
			$getField .= '&since_id=' . $since->since_id;
		}

		$twitter = new TwitterAPIExchange($this->getSettings());

		$response = $twitter->setGetfield($getField)
		                    ->buildOauth($url, 'GET')
		                    ->performRequest();

		return json_decode($response);
	}

	public function updateSince($tweet_id, $name)
	{
		$lastTweet = new LastTweet;
		$lastTweet->since_id = $tweet_id;
		$lastTweet->name = $name;
		$lastTweet->save();
	}

	public function parseTweets($tweet)
	{
		if (strpos($tweet->text, 'Drinking a') !== false &&
				strpos($tweet->source, 'untappd') !== false)
			{
				$user = '@' . $tweet->user->screen_name;
				$status = '#NerdsDrinking RT ' . $user . ' ' . $tweet->text;
				$regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?).*$)@";
				$status =  preg_replace($regex, ' ', $status);
				$status = str_replace('-', '', $status);
				$status = str_replace('—', '', $status);

				$this->postTweet($status, $tweet->id);
			} else {
                $this->info('We should have tweeted: But there were no tweets');
            }
	}

	public function postTweet($status, $tweet_id)
	{
		$url = 'https://api.twitter.com/1.1/statuses/update.json';
		$postFields['status'] = $status;
		$postFields['in_reply_to_status_id'] = $tweet_id;

		$tweet = new TwitterAPIExchange($this->getSettings());

		if (!$this->argument('test'))
		{
			$response = $tweet->setPostfields($postFields)
			                  ->buildOauth($url, 'POST')
			                  ->performRequest();
		}

		if ($this->argument('test'))
		{
			$this->info('We should have tweeted: ' . $status);
		}
	}
}
