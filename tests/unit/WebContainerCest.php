<?php


class WebContainerCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }


//    public function checkVHostConfig(UnitTester $I){
//        $I->wantTo("verify vhost is configured in the container");
//        $I->runShellCommand("docker exec phantom_web httpd -S");
//        $I->seeInShellOutput("*test-phantom.orangehrm.com");
//        $I->seeInShellOutput("*uat-phantom.orangehrm.com");
//        $I->seeInShellOutput("*prod-phantom.orangehrm.com");
//        $I->seeInShellOutput("*opensource-phantom.orangehrm.com");
//        $I->seeInShellOutput("*freehost-phantom.orangehrm.com");
//    }

}
