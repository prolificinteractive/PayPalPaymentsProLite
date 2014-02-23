<?php
namespace PayPalPaymentsProLite;
require_once(__DIR__.'/../../src/PayFlowAPI.php');

class PayFlowAPITest extends \PHPUnit_Framework_TestCase
{
	
	public function testObjectConstruction()
	{
		$pf = new PayFlowAPI();
		//Test instance
		$this->assertTrue($pf instanceof PayFlowAPI);
		
		//Test Attributes
		$this->assertObjectHasAttribute('call_endpoint',$pf);
		$this->assertObjectHasAttribute('hosted_endpoint',$pf);
		$this->assertObjectHasAttribute('environment',$pf);
		$this->assertObjectHasAttribute('call_credentials',$pf);
		$this->assertObjectHasAttribute('call_query',$pf);
		$this->assertObjectHasAttribute('call_variables',$pf);
		$this->assertObjectHasAttribute('call_response',$pf);
		$this->assertObjectHasAttribute('call_response_decoded',$pf);
		
		
	}
	
	public function testConfiguration()
	{
		//Test config file
		$this->assertFileExists(__DIR__.'/../../config/config.php');
		
		require(__DIR__.'/../../config/config.php');
		
		//Make sure environment exists
		$this->assertNotEmpty($config['environment']);	
	}
	
	public function testCredentials()
	{
		//Test Credentials
		$this->assertFileExists(__DIR__.'/../../config/credentials.php');
		require(__DIR__.'/../../config/config.php');
		//Make sure credentials are correct.
		
		$this->assertArrayHasKey('PARTNER',$config['credentials']);
		$this->assertArrayHasKey('VENDOR',$config['credentials']);
		$this->assertArrayHasKey('USER',$config['credentials']);
		$this->assertArrayHasKey('PWD',$config['credentials']);
		
		//Make sure credentials are blank
		foreach($config['credentials'] as $key => $value)
			$this->assertEmpty($value,'Credential variable '.$key .' is not empty');
	}
	
	public function testSetCredentials()
	{
		require(__DIR__.'/../../config/config.php');
		$pf = new PayFlowAPI();
		$creds = $pf->setCredentials($config['credentials']);
		$this->assertArrayHasKey('PARTNER',$creds);
		$this->assertArrayHasKey('VENDOR',$creds);
		$this->assertArrayHasKey('USER',$creds);
		$this->assertArrayHasKey('PWD',$creds);
		
	}
	
	public function testPushVariables()
	{
		$pf = new PayFlowAPI();
		
		$variables = array(
			'TEST' => 'ME',
			'OKIE' => 'dokie'		
		);
		
		$pf->pushVariables($variables);
		
		$rvars = $pf->getCallVariables();
		$this->assertEquals($rvars['TEST'],'ME');
		$this->assertEquals($rvars['OKIE'],'dokie');
	}
	
	public function testClearVariables()
	{
		$pf = new PayFlowAPI();
		$variables = array(
				'TEST' => 'ME',
				'OKIE' => 'dokie'
		);
		$pf->pushVariables($variables);
		$pf->clearVariables();
		
		//Test clear variables
		$this->assertEmpty($pf->getCallVariables());
		
		
	}
	
	public function testClearCredentials()
	{
		require(__DIR__.'/../../config/config.php');
		$pf = new PayFlowAPI();
		$creds = $pf->setCredentials($config['credentials']);
		
		$pf->clearCredentials();
		
		//Test clear variables
		$this->assertEmpty($pf->getCredentials());
	}
	
	public function testGetApiString()
	{
		$pf = new PayFlowAPI();
		
		$variables = array(
				'TEST' => 'ME',
				'OKIE' => 'dokie'
		);
		
		$pf->pushVariables($variables);
		$string = $pf->getApiString();
		$this->assertEquals('PARTNER=&VENDOR=&USER=&PWD=&VERBOSITY=HIGH&TEST=ME&OKIE=dokie&VERBOSITY=HIGH',$string);
	}
	
	public function testDecodeReturn()
	{
		$pf = new PayFlowAPI();
		
		$string="TEST=ME&OKIE=dokie&VERBOSITY=HIGH";
		$decode = $pf->decodeReturn($string);
		
		$this->assertEquals($decode,array(
			'TEST'=>'ME',
			'OKIE'=>'dokie',
			'VERBOSITY'=>'HIGH'
		));
		
		
		
	}
	
}