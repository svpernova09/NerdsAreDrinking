<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('process tweets looking for beers');
$I->runShellCommand('php artisan nerds:process test');
$I->seeInShellOutput('We should have tweeted:');
