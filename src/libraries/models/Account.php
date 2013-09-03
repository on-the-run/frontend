<?php
/**
 * Account model.
 *
 * Manage accounts
 * This class is used to create new accounts or query for existing accounts
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class Account extends BaseModel
{
  public function __construct($params = null)
  {
    parent::__construct();
  }

  public function create($email, $sendEmail = false)
  {
    $res = $this->db->putUser(array('id' => $email));
    if($sendEmail)
      $this->sendEmailCreated($email);
    return $res;
  }

  public function emailExists($email)
  {
    $account = $this->db->getUser($email);
    return $account !== null;
  }

  private function sendEmailCreated($email)
  {
    $emailer = new Emailer;
    $user = new User;

    $by = $user->getEmailAddress();
    $template = sprintf('%s/email/account-created.php', $this->config->paths->templates);
    $body = getTemplate()->get($template, array('passwordLink' => $user->generatePasswordRequestUrl(), 'email' => $email, 'by' => $by));

    $emailer->setRecipients(array($email));
    $emailer->setSubject(sprintf('Your Trovebox account has been created by %s', $by));
    $emailer->setBody($body);
    $result = $emailer->send();
    return $result;
  }
}
