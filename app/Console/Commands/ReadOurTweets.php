<?php

namespace App\Console\Commands;

use App\LastTweet;
use App\Nerds;
use Illuminate\Console\Command;
use TwitterAPIExchange;

class ReadOurTweets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nerds:read {test=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse tweets to @NerdsDrinking';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $tweets = $this->getTweets();

        foreach ($tweets as $tweet) {
            if ($this->checkTweetForAdd($tweet)) {
                // Does Nerd Already Exist?
                $nerd = Nerds::where('twitter', '=', $tweet->user->screen_name)->get();

                if (count($nerd) === 0) {
                    $this->info('We should add ' . $tweet->user->screen_name);
                    // Add user
                    if ($this->argument('test') === 'false') {
                        $this->addUser($tweet);
                    }
                }
            }
            if ($this->checkTweetForRemove($tweet)) {
                $this->info('We should remove ' . $tweet->user->screen_name);
                // Remove user
                if ($this->argument('test') === 'false') {
                    $this->removeUser($tweet);
                }
            }
        }

        // update the since_id with the latest tweet in $tweets
        if ($this->argument('test') === 'false') {
            if (count($tweets) > 0) {
                $this->updateSince($tweets['0']->id, 'nerdsdrinking');
            }
        }
    }

    public function getSince($screen_name)
    {
        return $since = LastTweet::where('name', $screen_name)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function updateSince($tweet_id, $screen_name)
    {
        $this->info("Updating last tweet for " . $screen_name);
        $lastTweet = LastTweet::where('name', $screen_name)->first();
        $lastTweet->since_id = $tweet_id;
        $lastTweet->save();
    }

    /**
     * @param $status string
     * @throws \Exception
     */
    private function postTweet($status)
    {
        $url = 'https://api.twitter.com/1.1/statuses/update.json';
        $postFields['status'] = $status;

        $tweet = new TwitterAPIExchange($this->getSettings());

        if ($this->argument('test') === 'false') {
            $response = $tweet->setPostfields($postFields)
                ->buildOauth($url, 'POST')
                ->performRequest();
        }

        if ($this->argument('test') === 'test') {
            $this->info('We should have tweeted: ' . $status);
        }
    }

    /**
     * @param $tweet object
     * @throws \Exception
     */
    private function addUser($tweet)
    {
        Nerds::create([
            'name' => $tweet->user->name,
            'twitter' => $tweet->user->screen_name,
        ]);

        $this->notifyUserAdded($tweet->user->screen_name);
    }

    /**
     * @param $screen_name string
     * @throws \Exception
     */
    private function notifyUserAdded($screen_name)
    {
        $this->postTweet('@' . $screen_name . ', You have been added, tweet \'remove\' at us to stop.');
    }

    /**
     * @param $tweet object
     * @throws \Exception
     */
    private function removeUser($tweet)
    {
        $nerd = Nerds::where('twitter', $tweet->user->screen_name)->first();

        if ($nerd) {
            $nerd->delete();
            $this->notifyUserRemoved($tweet->user->screen_name);
        }
    }

    /**
     * @param $screen_name string
     * @throws \Exception
     */
    private function notifyUserRemoved($screen_name)
    {
        $this->postTweet('@' . $screen_name . ', You have been removed.');
    }

    /**
     * @return array
     */
    private function getSettings()
    {
        $settings = [
            'oauth_access_token' => env('oauth_access_token', ''),
            'oauth_access_token_secret' => env('oauth_access_token_secret', ''),
            'consumer_key' => env('consumer_key', ''),
            'consumer_secret' => env('consumer_secret', ''),
        ];

        return $settings;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function getTweets()
    {
        $url = 'https://api.twitter.com/1.1/statuses/mentions_timeline.json';
        $getField = '?screen_name=nerdsdrinking';

        $since = $this->getSince('nerdsdrinking');
        if (!is_null($since) && !empty($since->since_id)) {
            $getField .= '&since_id=' . $since->since_id;
        }

        $twitter = new TwitterAPIExchange($this->getSettings());

        $response = $twitter->setGetfield($getField)
            ->buildOauth($url, 'GET')
            ->performRequest();

        return json_decode($response);
    }

    /**
     * @param $tweet object
     * @return bool
     */
    private function checkTweetForAdd($tweet)
    {
        if (strpos($tweet->text, 'add me #nerdsdrinking') !== false) {
            return true;
        }

        return false;
    }

    /**
     * @param $tweet object
     * @return bool
     */
    private function checkTweetForRemove($tweet)
    {
        if (strpos($tweet->text, 'remove') !== false) {
            return true;
        }

        return false;
    }
}
