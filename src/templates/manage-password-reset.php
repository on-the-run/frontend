<div class="manage password">
  <form method="post" action="/user/password/reset" class="passwordReset">
    <h2>Password Reset</h2>
    <p>
      No problem. Fill out the form below to reset it.
    </p>
    <label>New password</label>
    <input type="password" name="password" class="input-password">

    <label>Confirm new password</label>
    <input type="password" name="password-confirm" class="input-password-confirm">

    <br>
    <button type="submit" class="btn btn-brand addSpinner">Update my password</button>
    
    <input type="hidden" name="token" value="<?php $this->utility->safe($passwordToken); ?>">
  </form>
</div>
