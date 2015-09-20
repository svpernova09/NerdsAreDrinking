<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('process tweets looking for beers');
$I->runShellCommand('php artisan nerds:process test');
$I->seeInShellOutput('Processing thirstyrunner');
$I->seeInShellOutput('Processing svpernova09');
$I->seeInShellOutput('Processing markonthebluffs');
$I->seeInShellOutput('Processing Syliddar');
$I->seeInShellOutput('Processing sanseref');
$I->seeInShellOutput('Processing dan9186');
$I->seeInShellOutput('Processing vongrippen');
