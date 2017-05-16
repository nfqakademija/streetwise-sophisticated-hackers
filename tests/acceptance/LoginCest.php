<?php

class LoginCest
{
	public function signIn(AcceptanceTester $I)
	{
		$I->amOnPage('/login');
		$I->see('Log in');
		$I->fillField('Email', 'admin');
		$I->fillField('Password', 'adminas');
		$I->click('_submit');
		$I->see('News');
	}
	
	public function changeMyPassword(AcceptanceTester $I)
	{
		$this->signIn($I);
		$I->click('admin');
		$I->click('My Profile');
		$I->click('Change Password');
		$I->seeInTitle('Change Password');
		$I->fillField('Current password', 'adminas');
		$I->fillField('New password', 'adminas');
		$I->fillField('Repeat new password', 'adminas');
		$I->click('Submit');
		$I->seeInTitle('admin');
	}
	
	public function addHomework(AcceptanceTester $I)
	{
		$this->signIn($I);
		$I->amOnPage('/homework');
		$I->click('Create Homework');
		$I->fillField('Title', 'Christmas Homework');
		$I->fillField('Description', 'Do some work till Christmas');
		$I->fillField('Due date', '2017-12-31 23:59');
		$I->click('Save changes');
		$I->seeInTitle('Homework');
	}
}