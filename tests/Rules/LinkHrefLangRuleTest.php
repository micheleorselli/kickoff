<?php
namespace Frickelbruder\KickOff\Tests\Rules;

use Frickelbruder\KickOff\Configuration\TargetUrl;
use Frickelbruder\KickOff\Http\HttpResponse;
use Frickelbruder\KickOff\Rules\LinkHrefLangRule;

class LinkHrefLangRuleTest extends \PHPUnit_Framework_TestCase {

    public function testValidate() {
        $targetUrl = new TargetUrl();
        $targetUrl->uri = '/somewhere-de';
        $targetUrl->host = 'test.de';
        $response = new HttpResponse();
        $response->setRequest($targetUrl);
        $response->setBody('<!DOCTYPE html><html><head>
<link rel="alternate" href="http://test.de/somewhere-de" hreflang="de">
</head></html>');

        $rule = new LinkHrefLangRule();
        $rule->setHttpResponse($response);

        $result = $rule->validate();
        $this->assertTrue($result, $rule->getErrorMessage());
    }

    public function testValidateNoSelfReferencing() {
        $targetUrl = new TargetUrl();
        $targetUrl->uri = '/somewhere-en';
        $targetUrl->host = 'test.de';
        $response = new HttpResponse();
        $response->setRequest($targetUrl);
        $response->setBody('<!DOCTYPE html><html><head>
<link rel="alternate" href="http://test.de/somewhere-de" hreflang="de">
</head></html>');

        $rule = new LinkHrefLangRule();
        $rule->setHttpResponse($response);

        $result = $rule->validate();
        $this->assertFalse($result, $rule->getErrorMessage());
    }

    public function testValidateWithMultipleItems() {
        $targetUrl = new TargetUrl();
        $targetUrl->uri = '/somewhere-de';
        $targetUrl->host = 'test.de';
        $response = new HttpResponse();
        $response->setRequest($targetUrl);
        $response->setBody('<!DOCTYPE html><html><head>
<link rel="alternate" href="http://test.de/somewhere-de" hreflang="de">
<link rel="alternate" href="http://test.de/somewhere-en" hreflang="en">
</head></html>');

        $rule = new LinkHrefLangRule();
        $rule->setHttpResponse($response);

        $result = $rule->validate();
        $this->assertTrue($result, $rule->getErrorMessage());
    }

    public function testValidateWithMultipleItemsOfSame() {
        $targetUrl = new TargetUrl();
        $targetUrl->uri = '/somewhere-de';
        $targetUrl->host = 'test.de';
        $response = new HttpResponse();
        $response->setRequest($targetUrl);
        $response->setBody('<!DOCTYPE html><html><head>
<link rel="alternate" href="http://test.de/somewhere-de" hreflang="de">
<link rel="alternate" href="http://test.de/somewhere-de/page=5" hreflang="de">
<link rel="alternate" href="http://test.de/somewhere-en" hreflang="en">
</head></html>');

        $rule = new LinkHrefLangRule();
        $rule->setHttpResponse($response);

        $result = $rule->validate();
        $this->assertFalse($result, $rule->getErrorMessage());
    }

    public function testValidateWithDefaultVersion() {
        $targetUrl = new TargetUrl();
        $targetUrl->uri = '/somewhere-de';
        $targetUrl->host = 'test.de';
        $response = new HttpResponse();
        $response->setRequest($targetUrl);
        $response->setBody('<!DOCTYPE html><html><head>
<link rel="alternate" href="http://test.de/somewhere-de" hreflang="de">
<link rel="alternate" href="http://test.de/somewhere-de/" hreflang="x-default">
<link rel="alternate" href="http://test.de/somewhere-en" hreflang="en">
</head></html>');

        $rule = new LinkHrefLangRule();
        $rule->setHttpResponse($response);

        $result = $rule->validate();
        $this->assertTrue($result, $rule->getErrorMessage());
    }

    public function testValidateHeaders() {
        $targetUrl = new TargetUrl();
        $targetUrl->uri = '/somewhere-de';
        $targetUrl->host = 'test.de';
        $response = new HttpResponse();
        $response->setRequest($targetUrl);
        $response->setHeaders(array('Link' => array('<http://test.de/somewhere-de>; rel="alternate"; hreflang="de"')));

        $rule = new LinkHrefLangRule();
        $rule->setHttpResponse($response);

        $result = $rule->validate();
        $this->assertTrue($result, $rule->getErrorMessage());
    }

    public function testValidateHeadersNoSelfReferencing() {
        $targetUrl = new TargetUrl();
        $targetUrl->uri = '/somewhere-en';
        $targetUrl->host = 'test.de';
        $response = new HttpResponse();
        $response->setRequest($targetUrl);
        $response->setHeaders(array('Link' => array('<http://test.de/somewhere-de>; rel="alternate"; hreflang="de"')));

        $rule = new LinkHrefLangRule();
        $rule->setHttpResponse($response);

        $result = $rule->validate();
        $this->assertFalse($result, $rule->getErrorMessage());
    }

    public function testValidateHeadersWithMultipleItems() {
        $targetUrl = new TargetUrl();
        $targetUrl->uri = '/somewhere-de';
        $targetUrl->host = 'test.de';
        $response = new HttpResponse();
        $response->setRequest($targetUrl);
        $response->setHeaders(array('Link' => array('<http://test.de/somewhere-de>; rel="alternate"; hreflang="de", <http://test.de/somewhere-en>; rel="alternate"; hreflang="en"')));

        $rule = new LinkHrefLangRule();
        $rule->setHttpResponse($response);

        $result = $rule->validate();
        $this->assertTrue($result, $rule->getErrorMessage());
    }

    public function testValidateHeaderWithMultipleItemsOfSame() {
        $targetUrl = new TargetUrl();
        $targetUrl->uri = '/somewhere-de';
        $targetUrl->host = 'test.de';
        $response = new HttpResponse();
        $response->setRequest($targetUrl);
        $response->setHeaders(array('Link' => array('<http://test.de/somewhere-de>; rel="alternate"; hreflang="de", <http://test.de/somewhere-de?page=5>; rel="alternate"; hreflang="de", <http://test.de/somewhere-en>; rel="alternate"; hreflang="en"')));

        $rule = new LinkHrefLangRule();
        $rule->setHttpResponse($response);

        $result = $rule->validate();
        $this->assertFalse($result, $rule->getErrorMessage());
    }

    public function testValidateHeaderWithDefaultVersion() {
        $targetUrl = new TargetUrl();
        $targetUrl->uri = '/somewhere-de';
        $targetUrl->host = 'test.de';
        $response = new HttpResponse();
        $response->setRequest($targetUrl);
        $response->setHeaders(array('Link' => array('<http://test.de/somewhere-de>; rel="alternate"; hreflang="de", <http://test.de/somewhere-de>; rel="alternate"; hreflang="x-default", <http://test.de/somewhere-en>; rel="alternate"; hreflang="en"')));

        $rule = new LinkHrefLangRule();
        $rule->setHttpResponse($response);

        $result = $rule->validate();
        $this->assertTrue($result, $rule->getErrorMessage());
    }

    public function testValidateHasNeitherHEaderNorBodyTags() {
        $targetUrl = new TargetUrl();
        $targetUrl->uri = '/somewhere-de';
        $targetUrl->host = 'test.de';
        $response = new HttpResponse();
        $response->setRequest($targetUrl);
        $response->setBody('<!DOCTYPE html><html><head></head></html>');

        $rule = new LinkHrefLangRule();
        $rule->setHttpResponse($response);

        $result = $rule->validate();
        $this->assertFalse($result, $rule->getErrorMessage());
    }
}