<?php

class GoogleTimeStamp extends \Eloquent {
    protected $table = 'last_google_api_timestamp';
	protected $fillable = [
		'timestamp'
	];
}