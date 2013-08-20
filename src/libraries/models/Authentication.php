<?php
/**
  * Authentication model
  *
  * This is the model handles authentication and abstracts between HTTP sessions and OAuth.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class Authentication
{
  /*
   * Constructor
   */
  public function __construct($params = null)
  {
    if(isset($params['credential']))
      $this->credential = $params['credential'];
    else
      $this->credential = getCredential();

    if(isset($params['session']))
      $this->session = $params['session'];
    else
      $this->session = getSession();

    if(isset($params['user']))
      $this->user = $params['user'];
    else
      $this->user = new User;

    $this->permission = new Permission;
  } 

  /**
    * Checks to see if there are any authentication credentials present in this request
    *
    * @return boolean
    */
  public function isRequestAuthenticated()
  {
    if($this->user->isLoggedIn())
      return true;
    elseif($this->credential->isOAuthRequest())
      return true;

    return false;
  }

  /**
    * Requires authentication as a viewer or owner.
    * Throws exception on failure.
    *
    * @return boolean
    */
  public function requireAuthentication($requested = array('C','R','U','D'))
  {
    // TODO !group enforce group permissions for oauth requests
    
    // first check if it's an oauth request
    // else the user has to be logged in
    // if they're logged in and not an admin we still have to verify permissions.
    if($this->credential->isOAuthRequest())
    {
      if(!$this->credential->checkRequest())
      {
        OPException::raise(new OPAuthorizationOAuthException($this->credential->getErrorAsString()));
      }
    }
    elseif(!$this->user->isLoggedIn())
    {
      OPException::raise(new OPAuthorizationSessionException());
    }
    elseif(!$this->user->isAdmin())
    {
      $granted = $this->permission->getCollapsed();
      // sort arrays for comparison
      sort($requested);
      sort($granted);
      if(array_intersect($requested, $granted) !== $requested)
        OPException::raise(new OPAuthorizationPermissionException());
    }
  }

   /**
    * Check that the crumb is valid
    *
    * @param $crumb the crumb posted to validate
    */
  public function requireCrumb($crumb = null)
  {
    if($this->credential->isOAuthRequest())
      return;
    elseif($crumb === null && isset($_REQUEST['crumb']))
      $crumb = $_REQUEST['crumb'];

    if($this->session->get('crumb') != $crumb)
      OPException::raise(new OPAuthorizationException('Crumb does not match'));
  }
}
