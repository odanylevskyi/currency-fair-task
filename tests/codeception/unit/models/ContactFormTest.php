<?php

namespace tests\codeception\unit\models;

use app\models\ContactForm;
use Yii;
use yii\codeception\TestCase;
use Codeception\Specify;

class ContactFormTest extends TestCase
{
    use Specify;

    protected function setUp()
    {
        parent::setUp();
        Yii::$app->mailer->fileTransportCallback = function ($mailer, $message) {
            return 'testing_message.eml';
        };
    }

    protected function tearDown()
    {
        unlink($this->getMessageFile());
        parent::tearDown();
    }
}
