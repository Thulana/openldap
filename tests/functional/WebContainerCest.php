<?php


class WebContainerCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }


    public function ldapadminconnectionTest(FunctionalTester $I){
        $I->wantTo("verify ldapadmin container is linked with ldap server properly");
        $I->runShellCommand("docker exec openldap ping phpldapadmin -c 2");
        $I->seeInShellOutput('2 packets transmitted, 2 received');
    }



}
