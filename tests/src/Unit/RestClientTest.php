<?php

namespace Drupal\Tests\salesforce\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\salesforce\SFID;
use Drupal\salesforce\Exception;
use Drupal\salesforce\Rest\RestClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Drupal\salesforce\Rest\RestResponse as RestResponse;

/**
 * @coversDefaultClass \Drupal\salesforce\Rest\RestClient
 * @group salesforce
 */

class RestClientTest extends UnitTestCase {

  static $modules = ['salesforce'];

  public function setUp() {
    parent::setUp();
    $methods = [
      'getConsumerKey',
      'getConsumerSecret',
      'getRefreshToken',
      'getAccessToken',
      'refreshToken',
      'getApiEndPoint',
      'httpRequest',
    ];
    
    $this->httpClient = $this->getMock('\GuzzleHttp\Client');
    $this->configFactory = 
      $this->getMockBuilder('\Drupal\Core\Config\ConfigFactory')
        ->disableOriginalConstructor()
        ->getMock();
    $this->state =
      $this->getMockBuilder('\Drupal\Core\State\State')
        ->disableOriginalConstructor()
        ->getMock();
    $this->cache = $this->getMock('\Drupal\Core\Cache\CacheBackendInterface');
    $args = [$this->httpClient, $this->configFactory, $this->state, $this->cache];

    $this->client = $this->getMock(RestClient::CLASS, $methods, $args);
    $this->client->expects($this->any())
      ->method('getApiEndPoint')
      ->willReturn('https://example.com');
  }

  /**
   * @covers ::isAuthorized
   */
  public function testAuthorized() {
    $this->client->expects($this->at(0))
      ->method('getConsumerKey')
      ->willReturn($this->randomMachineName());
    $this->client->expects($this->at(1))
      ->method('getConsumerSecret')
      ->willReturn($this->randomMachineName());
    $this->client->expects($this->at(2))
      ->method('getRefreshToken')
      ->willReturn($this->randomMachineName());
    
    $this->assertTrue($this->client->isAuthorized());

    // Next one will fail because mocks only return for specific invocations.
    $this->assertFalse($this->client->isAuthorized());
  }

  /**
   * @covers ::apiCall
   */
  public function testSimpleApiCall() {
    // Test that an apiCall returns a json-decoded value.
    $body = array('foo' => 'bar');
    $response = new GuzzleResponse(200, [], json_encode($body));

    $this->client->expects($this->any())
      ->method('getAccessToken')
      ->willReturn(TRUE);

    $this->client->expects($this->any())
      ->method('httpRequest')
      ->willReturn($response);

    $result = $this->client->apiCall('');
    $this->assertEquals($result, $body);
  }

  /**
   * @covers ::apiCall
   * @expectedException Exception
   */
  public function testExceptionApiCall() {
    // Test that SF client throws an exception for non-200 response 
    $response = new GuzzleResponse(456);

    $this->client->expects($this->any())
      ->method('getAccessToken')
      ->willReturn(TRUE);
    $this->client->expects($this->any())
      ->method('httpRequest')
      ->willReturn($response);

    $result = $this->client->apiCall('');
  }

  /**
   * @covers ::apiCall
   */
  public function testReauthApiCall() {
    // Test that apiCall does auto-re-auth after 401 response
    $response_401 = new GuzzleResponse(401);
    $response_200 = new GuzzleResponse(200);

    $this->client->expects($this->any())
      ->method('getAccessToken')
      ->willReturn(TRUE);
    // First httpRequest() is position 4.
    // @TODO this is extremely brittle, exposes complexity in underlying client. Refactor this.
    $this->client->expects($this->at(3))
      ->method('httpRequest')
      ->willReturn($response_401);
    $this->client->expects($this->at(4))
      ->method('httpRequest')
      ->willReturn($response_200);

    $result = $this->client->apiCall('');
  }

  
  /**
   * @covers ::objects
   */
  public function testObjects() {

  }

  /**
   * @covers ::query
   */
  public function testQuery() {

  }

  /**
   * @covers ::objectDescribe
   */
  public function testObjectDescribe() {

  }

  /**
   * @covers ::objectCreate
   */
  public function testObjectCreate() {

  }

  /**
   * @covers ::objectUpsert
   */
  public function testObjectUpsert() {

  }

  /**
   * @covers ::objectUpdate
   */
  public function testObjectUpdate() {

  }

  /**
   * @covers ::objectRead
   */
  public function testObjectRead() {

  }

  /**
   * @covers ::objectReadbyExternalId
   *
   * @return void
   * @author Aaron Bauman
   */
  public function testObjectReadbyExternalId() {
    
  }

  /**
   * @covers ::objectDelete
   */
  public function testObjectDelete() {

  }

  /**
   * @covers ::getDeleted
   */
  public function getDeleted() {

  }

  /**
   * @covers ::listResources
   */
  public function testListResources() {
    
  }

  /**
   * @covers ::getUpdated
   */
  public function testGetUpdated() {

  }

  /**
   * @covers ::getRecordTypes
   */
  public function testGetRecordTypes() {

  }

  /**
   * @covers ::getRecordTypeIdByDeveloperName
   */
  public function testGetRecordTypeIdByDeveloperName() {

  }

  /**
   * @covers ::getObjectTypeName
   */
  public static function testGetObjectTypeName() {

  }

}
