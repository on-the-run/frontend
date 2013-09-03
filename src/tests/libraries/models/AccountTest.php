<?php
class AccountTest extends PHPUnit_Framework_TestCase
{
  protected $account;

  public function setUp()
  {
    // to test the write methods
    $this->account = new Account;
    $this->account->inject('config', new FauxObject);
  }

  public function testCreateSuccess()
  {
    $db = $this->getMock('Db', array('putUser'));
    $db->expects($this->any())
      ->method('putUser')
      ->will($this->returnValue(true));
    $this->account->inject('db', $db);
    $res = $this->account->create('foo@bar.com');
    $this->assertTrue($res);
  }

  public function testCreateFailure()
  {
    $db = $this->getMock('Db', array('putUser'));
    $db->expects($this->any())
      ->method('putUser')
      ->will($this->returnValue(false));
    $this->account->inject('db', $db);
    $res = $this->account->create('foo@bar.com');
    $this->assertFalse($res);
  }

  public function testEmailExistsYes()
  {
    $db = $this->getMock('Db', array('getUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->returnValue(array('id' => 'foo@bar.com')));
    $this->account->inject('db', $db);
    $res = $this->account->emailExists('foo@bar.com');
    $this->assertTrue($res);
  }

  public function testEmailExistsNo()
  {
    $db = $this->getMock('Db', array('getUser'));
    $db->expects($this->any())
      ->method('getUser')
      ->will($this->returnValue(null));
    $this->account->inject('db', $db);
    $res = $this->account->emailExists('foo@bar.com');
    $this->assertFalse($res);
  }
}
