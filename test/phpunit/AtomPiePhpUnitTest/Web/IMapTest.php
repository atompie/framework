<?php

namespace AtomPiePhpUnitTest\Web;

use PHPUnit_Framework_TestCase;
use AtomPie\Web\Connection\Imap\Client;

set_time_limit(180);

/**
 * test case.
 */
class IMapTest extends PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function testIMapConnect()
    {
        $oClient = new Client('ramtamtam73@gmail.com', 'sxxx', 'imap.gmail.com', 993, 'ssl');
        $this->assertTrue('{imap.gmail.com:993/imap/ssl/novalidate-cert}' == $oClient->getConnectionString());
        $oClient->open('INBOX');

        $oEmails = $oClient->fetchAllMailOverview();

        $oProcessedEmails = array();
        $iEmailNumber = 0;
        if ($oEmails != false) {
            foreach ($oEmails as $oOverview) {

                $oProcessedEmails[$iEmailNumber]['Date'] = $oClient->decodeMimeString($oOverview->date);
                $oProcessedEmails[$iEmailNumber]['From'] = $oClient->decodeMimeString($oOverview->from);
                if (isset($oOverview->subject)) {
                    $oProcessedEmails[$iEmailNumber]['Subject'] = $oClient->decodeMimeString($oOverview->subject);
                } else {
                    $oProcessedEmails[$iEmailNumber]['Subject'] = '';
                }

                $bIsFailed = $oClient->isDeliveryFailed($oOverview);
                if ($bIsFailed != false) {
                    var_dump($bIsFailed);
                }

                $iMessageNumber = $oClient->getMessageNumber($oOverview->uid);
                $oProcessedEmails[$iEmailNumber]['Body'] = $oClient->getBody($oOverview->uid);
                $sSand = $oProcessedEmails[$iEmailNumber]['Subject'] . $oProcessedEmails[$iEmailNumber]['From'] . $oProcessedEmails[$iEmailNumber]['Date'];
                $oProcessedEmails[$iEmailNumber]['Attachements'] = $oClient->saveAttachments($iMessageNumber,
                    '/tmp/attach', $sSand);

//				$oClient->deleteMessage($iMessageNumber);
                $iEmailNumber++;

            }
        }

        var_dump($oProcessedEmails);

    }

}
