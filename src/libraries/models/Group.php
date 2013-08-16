<?php
/**
  * User model
  *
  * This is the model for group data.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class Group extends BaseModel
{
  /*
   * Constructor
   */
  public function __construct($params = null)
  {
    parent::__construct();
    if(isset($params['user']))
      $this->user = $params['user'];
    else
      $this->user = new User;
  }

  public function create($params)
  {
    $whitelist = $validParams = $this->getDefaultAttributes();
    foreach($params as $key => $value)
    {
      if(isset($whitelist[$key]))
        $validParams[$key] = $params[$key];
    }

    if(!$this->validate($validParams))
      return false;

    $nextGroupId = $this->user->getNextId('group');
    if($nextGroupId === false)
      return false;

    $res = $this->db->putGroup($nextGroupId, $validParams);
    if($res === false)
      return false;

    return $nextGroupId;
  }

  public function delete($id)
  {
    return $this->db->deleteGroup($id);
  }

  public function getGroup($id)
  {
    // TODO !group check permission
    $group = $this->db->getGroup($id);
    return $group;
  }

  public function getGroups($email = null)
  {
    // TODO !group check permission
    return $this->db->getGroups($email);
  }

  public function manageMembers($id, $emails, $action)
  {
    foreach($emails as $k => $v) 
    {
      if(stristr($v, '@') === false)
        unset($emails[$k]);
    }

    if(count($emails) === 0)
      return false;

    $res = false;
    switch($action)
    {
      case 'add':
        $res = $this->db->putGroupMembers($id, $emails);
        break;
      case 'remove':
        $res = $this->db->deleteGroupMembers($id, $emails);
        break;
    }
    return $res;
  }

  public function undelete($id)
  {
    return $this->db->undeleteGroup($id);
  }

  public function update($id, $params)
  {
    $defaults = $this->getDefaultAttributes();
    $validParams = array();
    foreach($defaults as $key => $value)
    {
      if(isset($params[$key]))
        $validParams[$key] = $params[$key];
    }
    if(!$this->validate($validParams, false))
      return false;

    return $this->db->postGroup($id, $validParams);
  }

  private function getDefaultAttributes()
  {
    return array(
      'name' => '',
      'description' => '',
      'album' => null,
      'user' => null,
      'group' => null
    );
  }

  private function validate($params, $create = true)
  {
    // when creating an account we require the name
    // when updateing we check to make sure if a name is passed in that it's not empty
    if(empty($params))
      return false;
    elseif($create && (!isset($params['name']) || empty($params['name'])))
      return false;
    elseif(isset($params['name']) && empty($params['name']))
      return false;

    return true;
  }
}
