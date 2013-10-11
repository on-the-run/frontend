<?php
class PermissionTest extends PHPUnit_Framework_TestCase
{
  private $user, $permission, $db;
  public function setUp()
  {
    $this->permission = new Permission;

    $this->db = $this->getMock('Db', array('getGroupsByUser'));
    $this->user = $this->getMock('User', array('isOwner','getEmailAddress'));
    $this->user->expects($this->any())
      ->method('getEmailAddress')
      ->will($this->returnValue('test@example.com'));
    //$this->permission = new Group(array('user' => new FauxObject));
    //$this->permission->config = json_decode(json_encode(array('application' => array('appId' => 'foo'), 'user' => array('email' => 'bar'))));
    $this->ownerPermission = array(
      'C' => true,
      'R' => true,
      'U' => true,
      'D' => true
    );
    $this->visitorPermission = array(
      'C' => false,
      'R' => false,
      'U' => false,
      'D' => false
    );
  }

  public function testGetOwnerPermissionSuccess()
  {
    $ownerPermission = array(
      'C' => true,
      'R' => true,
      'U' => true,
      'D' => true
    );

    $user = $this->getMock('User', array('isOwner','getEmailAddress'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(true));
    $user->expects($this->any())
      ->method('getEmailAddress')
      ->will($this->returnValue('test@example.com'));
    $this->permission->inject('user', $user);
    /* Leave this out to make sure we don't query db for owner
    $db = $this->getMock('db', array('getGroupsByUser'));
    $db->expects($this->any())
      ->method('getGroupsByUser')
      ->will($this->returnValue(false));
    $this->permission->inject('db', $db);*/

    $res = $this->permission->get();
    $this->assertEquals($res, $ownerPermission);
  }

  public function testGetVisitorNoPermissionSuccess()
  {
    $visitorPermission = array(
      'C' => false,
      'R' => false,
      'U' => false,
      'D' => false
    );

    $user = $this->getMock('User', array('isOwner','getEmailAddress'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(false));
    $user->expects($this->any())
      ->method('getEmailAddress')
      ->will($this->returnValue('test@example.com'));
    $this->permission->inject('user', $user);
    $db = $this->getMock('db', array('getGroupsByUser'));
    $db->expects($this->any())
      ->method('getGroupsByUser')
      ->will($this->returnValue(false));
    $this->permission->inject('db', $db);

    $res = $this->permission->get();
    $this->assertEquals($res, $visitorPermission);
  }

  public function testGetVisitorYesPermissionSuccess()
  {
    $visitorPermission = array(
      'C' => array('z','y'),
      'R' => array(),
      'U' => array('y'),
      'D' => array()
    );

    $userGroups = array(
      array(
        'id' => 'a',
        'owner' => 'text@example.com',
        'actor' => 'test@example.com',
        'name' => 'Group 1',
        'description' => 'Group 1 detail',
        'album' => array(
          'z' => array('C'=>true,'R'=>false,'U'=>false,'D'=>null),
          'y' => array('C'=>true,'R'=>null,'U'=>true,'D'=>null),
          'w' => array('C'=>null,'R'=>null,'U'=>null,'D'=>null),

        ),
        'user' => array('C'=>true,'R'=>true,'U'=>true,'D'=>true),
        'timestamp' => '2013-08-15 00:33:46'
      )
    );

    $user = $this->getMock('User', array('isOwner','getEmailAddress'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(false));
    $user->expects($this->any())
      ->method('getEmailAddress')
      ->will($this->returnValue('test@example.com'));
    $this->permission->inject('user', $user);
    $db = $this->getMock('db', array('getGroupsByUser'));
    $db->expects($this->any())
      ->method('getGroupsByUser')
      ->will($this->returnValue($userGroups));
    $this->permission->inject('db', $db);

    $res = $this->permission->get();
    $this->assertEquals($res, $visitorPermission);
  }

  public function testCanUploadOwnerYes()
  {
    $user = $this->getMock('User', array('isOwner'));
    $user->expects($this->any())
      ->method('isOwner')
      ->will($this->returnValue(true));
    $this->permission->inject('user', $user);
    $res = $this->permission->canUpload();
    $this->assertTrue($res);
  }

  public function testCanUploadAdminYes()
  {
    $user = $this->getMock('User', array('isAdmin'));
    $user->expects($this->any())
      ->method('isAdmin')
      ->will($this->returnValue(true));
    $this->permission->inject('user', $user);
    $res = $this->permission->canUpload();
    $this->assertTrue($res);
  }

  public function testCanUploadNo()
  {
    $this->permission->inject('stored', array('C'=>false));
    $res = $this->permission->canUpload();
    $this->assertFalse($res);
  }

  public function testCanUploadYes()
  {
    $this->permission->inject('stored', array('C'=>array('a')));
    $res = $this->permission->canUpload();
    $this->assertTrue($res);
  }

  public function testCanUploadToAlbumNo()
  {
    $this->permission->inject('stored', array('C'=>array('a')));
    $res = $this->permission->canUpload('b');
    $this->assertFalse($res);
  }

  public function testCanUploadToAlbumYes()
  {
    $this->permission->inject('stored', array('C'=>array('a')));
    $res = $this->permission->canUpload('a');
    $this->assertTrue($res);
  }

  public function testGetCollapsed()
  {
    $this->permission->inject('stored', array('C'=>array('a'),'R'=>false,'U'=>false,'D'=>false));
    $res = $this->permission->getCollapsed();
    $this->assertEquals($res, array('C'));

    $this->permission->inject('stored', array('C'=>array('a'),'R'=>false,'U'=>false,'D'=>true));
    $res = $this->permission->getCollapsed();
    $this->assertEquals($res, array('C','D'));
  }

  public function testAllowedAlbumsRead()
  {
    $albums = array('a','b','c');
    sort($albums);
    $this->permission->inject('stored', array('C'=>false,'R'=>array('a','b','c'),'U'=>false,'D'=>false));
    $res = $this->permission->allowedAlbums('R');
    sort($res);
    $this->assertEquals($res, $albums);
  }

  public function testAllowedAlbumsCreate()
  {
    $albums = array('a','b','c');
    sort($albums);
    $this->permission->inject('stored', array('C'=>array('a','b','c'),'R'=>array('a','b','c'),'U'=>false,'D'=>false));
    $res = $this->permission->allowedAlbums('C');
    sort($res);
    $this->assertEquals($res, $albums);
  }

  public function testAllowedAlbumsCreateEmpty()
  {
    $this->permission->inject('stored', array('C'=>false,'R'=>array('a','b','c'),'U'=>false,'D'=>false));
    $res = $this->permission->allowedAlbums('C');
    $this->assertEquals($res, array());
  }
}
