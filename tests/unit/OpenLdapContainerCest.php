<?php


class OpenLdapContainerCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function checkForOpenLdapServer(UnitTester $I){
        $I->wantTo("verify ldap server is running");
        $I->runShellCommand("docker ps");
        $I->seeInShellOutput("phpldapadmin");
        $I->seeInShellOutput("openldap");
    }


    public function checkSlapdService(UnitTester $I){
        $I->wantTo("verify slapd is configured in the container");
        $I->runShellCommand("docker exec openldap service slapd restart");
        $I->runShellCommand("docker exec openldap service slapd status");
        $I->seeInShellOutput("slapd is running.");
    }

}
