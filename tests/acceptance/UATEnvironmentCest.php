<?php


class UATEnvironmentCest
{

    public function checkLoginToLDAPFromLdapMyAdmin(AcceptanceTester $I){
        $I->wantTo("");
        $I->amOnPage('http://0.0.0.0:8080/index.php?server_id=1&redirect=true');
//        $I->fillField('Username:', 'root');
//        $I->fillField('Password:', '1234');
//        $I->click('Go');
//        $I->see('Server: db');
        $I->see("openldap");
    }

}
