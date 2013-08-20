<div class="row">
  <div class="span2">
    <ul class="nav nav-tabs nav-stacked affix sub-navigation">
      <li class="active settingsSubNav"><a href="#settings" class="settingsSubNav"><i class="icon-sitemap"></i> Your Site</a></li>
      <li><a href="#applications" class="settingsSubNav"><i class="icon-briefcase"></i> Applications</a></li>
      <li><a href="#plugins" class="settingsSubNav"><i class="icon-circle-blank"></i> Plugins</a></li>
      <li><a href="#tokens" class="settingsSubNav"><i class="icon-share-alt"></i> Sharing Tokens</a></li>
    </ul>
  </div>
  <div class="span10 sections">
    <div class="row settings section">
      <form method="post" action="/manage/settings">
        <div class="span10">
          <h2>Your site</h2>
          <div class="controls">
            <label class="checkbox inline">
              <input type="checkbox" name="enableBetaFeatures" value="1" <?php if($enableBetaFeatures) { ?>checked="checked"<?php } ?>>
              Enable beta features on this site and on the mobile apps
              <small><i class="icon-info-sign"></i> Fun and experimental features which haven't been fully tested.</small>
            </label>
          </div>
          <div class="controls">
            <label class="checkbox inline">
              <input type="checkbox" name="allowDuplicate" value="1" <?php if($allowDuplicate) { ?>checked="checked"<?php } ?>>
              Allow duplicate photos to be uploaded to your account
            </label>
          </div>
          <div class="controls">
            <label class="checkbox inline">
              <input type="checkbox" name="downloadOriginal" value="1" <?php if($downloadOriginal) { ?>checked="checked"<?php } ?>>
              Let visitors download my original hi-res photos
              <small><i class="icon-info-sign"></i> Only applies to users who have access to view the photo.</small>
            </label>
          </div>
          <div class="controls">
            <label class="checkbox inline">
              <input type="checkbox" name="hideFromSearchEngines" value="1" <?php if($hideFromSearchEngines) { ?>checked="checked"<?php } ?>>
              Hide my site from search engines
            </label>
          </div>
          <div class="controls">
            <label class="checkbox inline">
              <input type="checkbox" name="decreaseLocationPrecision" value="1" <?php if($decreaseLocationPrecision) { ?>checked="checked"<?php } ?>>
              Decrease the accuracy when displaying my photos on a map for others
            </label>
          </div>
          <div class="btn-toolbar"><button class="btn btn-brand addSpinner">Save</button></div>
          <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
        </div>
      </form>
    </div>

    <div class="row applications section hide">
      <div class="span10">
        <h2>Your Applications</h2>
        <p class="blurb">
          <i class="icon-info-sign"></i> Applications are devices and services you've granted access to your account. Manage them below or <a href="/v1/oauth/authorize?oauth_callback=<?php $this->utility->safe(sprintf('%s://%s%s', $this->utility->getProtocol(false), $_SERVER['HTTP_HOST'], '/manage/apps/callback')); ?>&name=<?php $this->utility->safe(urlencode('Self Generated App')); ?>&tokenType=access">create a new one</a>.
        </p>
        <?php if(!empty($credentials)) { ?>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Application Name</th>
                <th></th>
              </tr>
            </thead>
            <?php foreach($credentials as $credential) { ?>
              <tr>
                <td>
                  <?php $this->utility->safe($credential['name']); ?>
                  <?php if(!empty($credential['dateCreated'])) { ?>
                    <small><em class="credential-date">(<?php $this->utility->safe(ucwords($credential['type'])); ?> token created on <?php $this->utility->dateLong($credential['dateCreated']); ?>)</em></small>
                  <?php } ?>
                </td>
                <td>
                  <div class="pull-right">
                  <a href="/v1/oauth/<?php $this->utility->safe($credential['id']); ?>/markup" class="credentialView"><i class="icon-eye-open icon-large"></i> View</a>
                    &nbsp; &nbsp; &nbsp;
                    <a href="/oauth/<?php $this->utility->safe($credential['id']); ?>/delete" class="credentialDelete"><i class="icon-trash icon-large"></i> Revoke</a>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </table>
        <?php } ?>
      </div>
    </div>
      
    <div class="row plugins section hide">
      <div class="span10">
        <h2>Your plugins</h2>
        <p class="blurb">
          <i class="icon-info-sign"></i> Plugins help you add more features to your Trovebox site. Below is a list of all the available plugins you can activate and configure.
        </p>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Plugin Name</th>
              <th></th>
            </tr>
          </thead>
          <?php foreach($plugins as $plugin) { ?>
            <tr>
              <td><?php $this->utility->safe($plugin['name']); ?></td>
              <td>
                <div class="pull-right">
                  <div class="<?php if($plugin['status'] === 'inactive') { ?>hide <?php } ?>active"><i class="icon-check icon-large"></i> Active (<a href="/plugin/<?php $this->utility->safe($plugin['name']); ?>/view" class="pluginView">Configure</a> or <a href="/plugin/<?php $this->utility->safe($plugin['name']); ?>/deactivate" class="pluginStatusToggle">Deactivate</a>)</div>
                  <div class="<?php if($plugin['status'] === 'active') { ?>hide <?php } ?>inactive"><i class="icon-check-empty icon-large"></i> Inactive (<a href="/plugin/<?php $this->utility->safe($plugin['name']); ?>/activate" class="pluginStatusToggle">Activate</a>)</div>
                </div>
              </td>
            </tr>
          <?php } ?>
        </table>
      </div>
    </div>

    <div class="row tokens section hide">
      <div class="span10">
        <h2>Your sharing tokens</h2>
        <p class="blurb">
          <i class="icon-info-sign"></i> Anytime you share a photo we generate a sharing token. Revoke access to any previously shared links below.
        </p>
        <table class="table table-striped">
          <thead>
            <tr>
              <th colspan="2">Photos</th>
            </tr>
          </thead>
          <?php if(count($tokens['photos']) === 0) { ?>
            <tr><td colspan="2">You don't have any sharing tokens for your photos.</td></tr>
          <?php } else { ?>
            <?php foreach($tokens['photos'] as $photo) { ?>
              <tr>
                <td>
                  Photo <?php $this->utility->safe($photo['data']); ?>
                  <small><em>(<?php if(empty($photo['dateExpires'])) { ?>Sharing token never expires<?php } else { ?>Sharing token expires on <?php $this->utility->dateLong($photo['dateExpires']); ?><?php } ?>)</em></small>
                </td>
                <td>
                  <div class="pull-right">
                    <a href="<?php $this->url->photoView($photo['data']); ?>"><i class="icon-eye-open icon-large"></i> View</a>
                    &nbsp; &nbsp; &nbsp;
                    <a href="/token/<?php $this->utility->safe($photo['id']); ?>/delete" class="tokenDelete"><i class="icon-trash icon-large"></i> Revoke</a>
                  </div>
                </td>
              </tr>
            <?php } ?>
          <?php } ?>
        </table>
        <table class="table table-striped">
          <thead>
            <tr>
              <th colspan="2">Albums</th>
            </tr>
          </thead>
          <?php if(count($tokens['albums']) === 0) { ?>
            <tr><td colspan="2">You don't have any sharing tokens for your albums.</td></tr>
          <?php } else { ?>
            <?php foreach($tokens['albums'] as $album) { ?>
              <tr>
                <td>
                  Album
                  <small><em>(<?php if(empty($album['dateExpires'])) { ?>Sharing token never expires<?php } else { ?>Sharing token expires on <?php $this->utility->dateLong($album['dateExpires']); ?><?php } ?>)</em></small>
                </td>
                <td>
                  <div class="pull-right">
                    <a href="<?php $this->url->albumView($album['data']); ?>"><i class="icon-eye-open icon-large"></i> View</a>
                    &nbsp; &nbsp; &nbsp;
                    <a href="/token/<?php $this->utility->safe($album['id']); ?>/delete" class="tokenDelete"><i class="icon-trash icon-large"></i> Revoke</a>
                  </div>
                </td>
              </tr>
            <?php } ?>
          <?php } ?>
        </table>
      </div>
    </div>
  </div>
</div>

