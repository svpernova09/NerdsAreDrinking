<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Carbon\Carbon;
use \GoogleTimeStamp;
use \GoogleClient;
use \Google_Service_Calendar;
use \Events;

class CalendarUpdate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nerds:cal';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fetches calendar events from Google.';

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
        $lastChecked = Carbon::createFromTimestampUTC(GoogleTimeStamp::first()->timestamp);
        $future = Carbon::now()->addHours(4);

        // Only update the events if it's been > 4 hours since last update
        if ($lastChecked->diffInHours($future, false) > 4)
        {
            echo "Need to update";
            //Update our last time stamp
            $lastApiCall = GoogleTimeStamp::first();
            $lastApiCall->timestamp = Carbon::now()->timestamp;
            $lastApiCall->save();

            $client = new Google_Client();
            $client->setApplicationName("NerdsAreDrinking");
            $client->setDeveloperKey(env('google_api', ''));
            $service = new Google_Service_Calendar($client);

            $calendarId = env('calendar_id', '');
            $optParams = array(
                'maxResults' => 2,
                'orderBy' => 'startTime',
                'singleEvents' => TRUE,
                'timeMin' => date('c'),
            );
            $results = $service->events->listEvents($calendarId, $optParams);

            if (count($results->getItems()) > 0) {
                print "Upcoming events:\n";
                foreach ($results->getItems() as $event) {

                    $item = [
                        'cal_id' => $event->id,
                        'summary' => $event->summary,
                        'start_date' => $event->start->dateTime
                    ];

                    $existing = Events::where('cal_id', $item['cal_id'])->first();

                    if ($this->argument('test') == 'false') {
                        // Upsert events
                        if ( count( $existing ) == 1 ) {
                            $existing->summary = $item['summary'];
                            $existing->start_date = $item['start_date'];
                            $existing->save();
                        } else {
                            Events::create( $item );
                        }
                    }

                    if ($this->argument('test'))
                    {
                        $this->info('We should have updated Events');
                    }
                }
            }
        } else {
            echo "Not time to update";
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
		return [
//			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
