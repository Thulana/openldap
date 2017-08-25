<?php


class WebContainerCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }


    public function checkSlapdService(UnitTester $I){
        $I->wantTo("verify slapd is configured in the container");
        $I->runShellCommand("docker exec openldap service slapd status");
        $I->seeInShellOutput("slapd is running.");
    }

}
