<?php


class OpenLdapContainerCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }


    public function ldapadminconnectionTest(FunctionalTester $I){
        $I->wantTo("verify ldapadmin container is linked with ldap server properly");
        $I->runShellCommand("docker exec openldap curl phpldapadmin");
        $I->seeInShellOutput('phpLDAPadmin (1.2.3)');
    }



}
