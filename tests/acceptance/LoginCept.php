<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('sign in');
$I->amOnPage('/login');
$I->see('Log in');
$I->fillField('Email', 'admin');
$I->fillField('Password', 'admin');
$I->click('_submit');
$I->see('lecture');
