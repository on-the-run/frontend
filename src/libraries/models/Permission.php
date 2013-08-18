<?php
class Permission extends BaseModel
{
  protected $group, $stored;

  public function __construct()
  {
    parent::__construct();
    $this->group = new Group;
  }

  public function canUpload($to = null)
  {
    $permissions = $this->get();

    // first check if there are ANY upload permissions, if not then return false
    if(!is_array($permissions['C']))
      return false;

    // if not to specific album then return true
    if($to === null)
      return true;

    // at the moment $to is a string
    return in_array($to, $permissions['C']);
  }

  public function get($cache = true)
  {
    if($cache && $this->stored)
      return $this->stored;

    $isOwner = $this->user->isOwner();
    $email = $this->user->getEmailAddress();
    $permissions = array(
      'C' => $isOwner,
      'R' => $isOwner,
      'U' => $isOwner,
      'D' => $isOwner
    );

    // we only populate if we're not the owner (owner's have full access)
    //  and if there are albums granted to this user
    if(!$isOwner)
    {
      $groups = $this->db->getGroupsByUser($email);
      if(!empty($groups))
      {
        foreach($groups as $group)
        {
          // loop over each album (the key) and it's permissions {C: , R: , U: , D: }
          foreach($group['album'] as $albumId => $perms)
          {
            if(!empty($perms))
            {
              // label is C, R, U, D and the value is null, true, false
              foreach($perms as $label => $value)
              {
                //  we only add the permission if it's true as we default to no permissions
                if($value)
                {
                  if(!is_array($permissions[$label]))
                    $permissions[$label] = array();
                  $permissions[$label][] = $albumId;
                }
              }
            }
          }
        }
      }
    }

    $this->stored = $permissions;
    return $this->stored;
  }
}
