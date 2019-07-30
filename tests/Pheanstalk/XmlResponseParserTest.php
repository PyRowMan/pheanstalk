<?php

namespace Pheanstalk;

use Pheanstalk\Response\ArrayResponse;
use PHPUnit\Framework\TestCase;

class XmlResponseParserTest extends TestCase
{

    public function testParseResponse()
    {
        $parser = new XmlResponseParser();
        $xmlString = '<response error="Unknown command or action" error-code="UNKNOWN_COMMAND" node="localhost" status="OK"/>';
        $xml = new \SimpleXMLElement($xmlString);
        $json = json_encode($xml);
        $responseLine = json_decode($json, true);
        $responseName = preg_replace('#^(\S+).*$#s', '$1', $responseLine["@attributes"]['status']);
        $response = $parser->parseResponse($responseName, $responseLine);
        $this->assertSame('OK', $response->getResponseName());
    }

    /**
     * @expectedException \Pheanstalk\Exception\ServerException
     */
    public function testShouldThrowNotFoundException()
    {
        $parser = new XmlResponseParser();
        $xmlString = '<response error="Unknown command or action" error-code="UNKNOWN_COMMAND" node="localhost" status="NOT_FOUND"/>';
        $xml = new \SimpleXMLElement($xmlString);
        $json = json_encode($xml);
        $responseLine = json_decode($json, true);
        $responseName = preg_replace('#^(\S+).*$#s', '$1', $responseLine["@attributes"]['status']);
        $response = $parser->parseResponse($responseName, $responseLine);
    }
}
