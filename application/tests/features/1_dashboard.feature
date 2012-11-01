
Feature: Login
	In order to be able to administer courses
	As a user
	I want to be able to login to the system

	Scenario: User goes to login page and uses a bad password.
		Given I am on "the login page"
		Then I should see "Login To Your Dashboard"
		When I fill in the following:
	 	 | username         | cs462 |
	  	 | password 		| badpass |
		And I press "Login To Dashboard"
		Then I should see "Autentication failed"


	Scenario: User goes to login page and uses no password
		Given I am on "the login page"
		Then I should see "Login To Your Dashboard"
		When I fill in the following:
	 	 | username         | cs462 |
	  	 | password 		|  |
		And I press "Login To Dashboard"
		Then I should see "Autentication failed"

	Scenario: User goes to login page and uses correct password
		Given I am on "the login page"
		Then I should see "Login To Your Dashboard"
		When I fill in the following:
	 	 | username         | cs462 |
	  	 | password 		|  this_will_fail |
		And I press "Login To Dashboard"
		Then I should see "You are logged in as: cs462"







