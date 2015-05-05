<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('process tweets looking for beers');
$I->runShellCommand('php artisan nerds:cal test');
$I->seeInShellOutput('Not time to update');
