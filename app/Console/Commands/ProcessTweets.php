<?php namespace App\Console\Commands;

use App\Nerds;
use App\LastTweet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use TwitterAPIExchange;

class ProcessTweets extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'nerds:process {test=false}';

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
    public function handle()
    {
        $nerds = $this->getNerds();

        foreach ($nerds as $nerd) {
            $this->info('Processing ' . $nerd->twitter);
            $tweets = $this->getTweets($nerd);

            if (is_array($tweets)) {
                if (count($tweets) > 0) {
                    $this->info('Found ' . count($tweets) . ' for ' . $nerd->twitter);
                    foreach ($tweets as $tweet) {
                        $this->parseTweets($tweet);
                    }

                    // update the since_id with the latest tweet in $tweets
                    if ($this->argument('test') === 'false') {
                        $this->updateSince($tweets['0']->id, $nerd->twitter);
                    }
                }
            }
        }
    }

    public function getNerds()
    {
        return Nerds::all();
    }

    public function getSettings()
    {
        $settings = [
            'oauth_access_token' => env('oauth_access_token', ''),
            'oauth_access_token_secret' => env('oauth_access_token_secret', ''),
            'consumer_key' => env('consumer_key', ''),
            'consumer_secret' => env('consumer_secret', ''),
        ];

        return $settings;
    }

    public function getSince($screen_name)
    {
        return $since = DB::table('last_tweets')
                          ->where('name', $screen_name)
                          ->orderBy('created_at', 'desc')
                          ->first();
    }

    public function getTweets($user)
    {
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $getField = '?screen_name=' . $user->twitter;

        $since = $this->getSince($user->twitter);
        if (!is_null($since)) {
            $getField .= '&since_id=' . $since->since_id;
        }

        $twitter = new TwitterAPIExchange($this->getSettings());

        $response = $twitter->setGetfield($getField)
                            ->buildOauth($url, 'GET')
                            ->performRequest();
        $result = json_decode($response);

        if (isset($result->error)) {
            return [];
        }

        return $result;
    }

    public function updateSince($tweet_id, $screen_name)
    {
        $this->info("Updating last tweet for " . $screen_name);
        $lastTweet = LastTweet::where('name', $screen_name)->first();
        $lastTweet->since_id = $tweet_id;
        $lastTweet->save();
    }

    public function parseTweets($tweet)
    {
        $this->info('Tweet Text: ' . $tweet->text);
        if (strpos($tweet->text, 'Drinking') !== false ||
                strpos($tweet->text, 'Enjoying a') !== false) {
            if (strpos($tweet->source, 'untappd') !== false) {
                $user = '@' . $tweet->user->screen_name;
                $status = '#NerdsDrinking RT ' . $user . ' ' . $tweet->text;
                $regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?).*$)@";
                $status = preg_replace($regex, ' ', $status);
                $status = str_replace('-', '', $status);
                $status = str_replace('—', '', $status);

                $this->postTweet($status, $tweet->id);
            } else {
                $this->info('Found Tweets but they didn\'t look like untapped checkins');
            }
        }
    }

    public function postTweet($status, $tweet_id)
    {
        $url = 'https://api.twitter.com/1.1/statuses/update.json';
        $postFields['status'] = $status;
        $postFields['in_reply_to_status_id'] = $tweet_id;

        $this->info("Should tweet: " . $status);

        $tweet = new TwitterAPIExchange($this->getSettings());

//        if ($this->argument('test') === 'false') {
//            $response = $tweet->setPostfields($postFields)
//                              ->buildOauth($url, 'POST')
//                              ->performRequest();
//        }

        if ($this->argument('test') === 'test') {
            $this->info('We should have tweeted: ' . $status);
        }
    }
}
