<div class="row">
  <div class="span2">
    <ul class="nav nav-tabs nav-stacked affix sub-navigation">
      <li class="<?php if($page === 'group-view') { ?>active <?php } ?>"><a href="/manage/users/list"><i class="icon-user"></i> Manage Users</a></li>
      <li class="<?php if($page === 'group-list') { ?>active <?php } ?>"><a href="/manage/groups/list"><i class="icon-group"></i> Manage Groups</a></li>
    </ul>
  </div>
  <div class="span10 sections">
    <?php if($page === 'group-list') { ?>
      <div class="row grouplist">
        <div class="span10">
          <h2>Groups <small><a href="#" class="toggle" data-target="form.groupCreate">Create a group</a></small></h2>
          <form class="groupCreate hide">
            <label>What's the name of your group?</label>
            <input type="text" name="name">

            <label>Describe this group (optional)</label>
            <textarea name="description"></textarea>

            <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
            <input type="hidden" name="httpCodes" value="*">
            <div class="btn-toolbar"><button class="btn btn-brand addSpinner">Create</button></div>
            <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
          </form>
          <table class="table groups hide"></table>
          <div class="no-groups hide">You haven't created any groups yet.</div>
        </div>
      </div>
      <script> var __initData = <?php echo json_encode($groups); ?>; </script>
    <?php } elseif($page === 'group-view') { ?>
      <div class="row groupview">
        <div class="span10">
          <h2><?php $this->utility->safe($group['name']); ?></h2>
          <p><?php $this->utility->safe($group['description']); ?></p>
          <hr>
          <h3>Members</h3>
          <p class="blurb">
            <i class="icon-info-sign"></i> View and manage which users that belong to this group.
          </p>
          <!--<form action="/group/<?php $this->utility->safe($group['id']); ?>/update" class="groupUpdateHash hide groupPermissionUser">
            <div class="controls">
              <label class="checkbox inline">
                <input type="checkbox" name="R" value="1" <?php if($group['user'] === true || (isset($group['user']['R']) && $group['user']['R'] === true)) { ?>checked="checked"<?php } ?>>
                View users in this group
              </label>
            </div>
            <div class="controls">
              <label class="checkbox inline">
                <input type="checkbox" name="C" value="1" <?php if($group['user'] === true || (isset($group['user']['C']) && $group['user']['C'] === true)) { ?>checked="checked"<?php } ?>>
                Add users to this group
              </label>
            </div>
            <div class="controls">
              <label class="checkbox inline">
                <input type="checkbox" name="D" value="1" <?php if($group['user'] === true || (isset($group['user']['D']) && $group['user']['D'] === true)) { ?>checked="checked"<?php } ?>>
                Remove users from this group
              </label>
            </div>
            <div class="btn-toolbar"><button class="btn btn-brand addSpinner">Save</button></div>
            <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
            <input type="hidden" name="key" value="user">
          </form>-->
          <div class="row userList">
            <div class="span10">
              <form action="/group/<?php $this->utility->safe($group['id']); ?>/member/add" class="form-inline groupMemberAdd">
                <input type="text" placeholder="Email address" name="emails"> <button class="btn btn-brand addSpinner">Add member</button>
                <input type="hidden" name="httpCodes" value="*">
                <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
              </form>
              <ul class="unstyled">
                <?php foreach($group['members'] as $member) { ?>
                  <li data-email="li-<?php $this->utility->safe($member); ?>"><?php $this->utility->safe($member); ?> <a href="#/group/<?php $this->utility->safe($group['id']); ?>/member/remove" class="groupMemberRemove" data-email="<?php $this->utility->safe($member); ?>"><i class="icon-trash"></i> delete</a></li>
                <?php } ?>
              </ul>
            </div>
          </div>
 
          <h3>Albums</h3>
          <p class="blurb">
            <i class="icon-info-sign"></i> Below is a list of albums along with the permissions granted by this group.
          </p>
          <?php if(empty($group['album'])) { ?>
            <div class="no-albums">You haven't created any albums yet.</div>
          <?php } else { ?>
            <div class="row albums">
              <?php foreach($group['album'] as $albumId => $permissions) { ?>
                <div class="span3">
                  <div class="cover">
                    <span class="stack stack1"></span>
                    <span class="stack stack2"></span>
                    <?php if(!empty($albums[$albumId]['cover'])) { ?>
                      <img class="img-polaroid" src="<?php $this->utility->safe($albums[$albumId]['cover']['path200x200xCR']); ?>">
                    <?php } else { ?>
                      <img class="img-polaroid" src="data:image/gif;base64,R0lGODlhAQABAPAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==">
                    <?php } ?>
                  </div>
                </div>
                <div class="span2">
                  <h4><?php $this->utility->safe($albums[$albumId]['name']); ?></h4>
                  <form action="/group/<?php $this->utility->safe($group['id']); ?>/update" class="groupUpdateHash">
                    <div class="controls">
                      <label class="checkbox inline">
                        <input type="checkbox" name="R" value="1" <?php if($group['user'] === true || (isset($group['user']['R']) && $group['user']['R'] === true)) { ?>checked="checked"<?php } ?>>
                        View this album
                      </label>
                    </div>
                    <div class="controls">
                      <label class="checkbox inline">
                        <input type="checkbox" name="C" value="1" <?php if($group['user'] === true || (isset($group['user']['C']) && $group['user']['C'] === true)) { ?>checked="checked"<?php } ?>>
                        Add photos
                      </label>
                    </div>
                    <div class="controls">
                      <label class="checkbox inline">
                        <input type="checkbox" name="D" value="1" <?php if($group['user'] === true || (isset($group['user']['D']) && $group['user']['D'] === true)) { ?>checked="checked"<?php } ?>>
                        Remove photos
                      </label>
                    </div>
                    <div class="btn-toolbar"><button class="btn btn-brand addSpinner album-<?php $this->utility->safe($albumId); ?>">Save</button></div>
                    <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
                    <input type="hidden" name="key" value="album">
                    <input type="hidden" name="albumId" value="<?php $this->utility->safe($albumId); ?>">
                  </form>
                </div>
              <?php } ?>
            </div>
          <?php } ?>
        </div>
      </div>
      <script>
        var __initData = <?php echo json_encode($group); ?>;
        var __initDataAlbums = <?php echo json_encode($albums); ?>;
      </script>
    <?php } elseif($page === 'user-list') { ?>
      <div class="row userlist">
        <div class="span10">
          <form class="userCreate">
            <h2>Create a new group</h2>
            <label>What's the name of your group?</label>
            <input type="text" name="name">

            <label>Describe this group (optional)</label>
            <textarea name="description"></textarea>

            <input type="hidden" name="crumb" value="<?php $this->utility->safe($crumb); ?>">
            <div class="btn-toolbar"><button class="btn btn-brand">Create</button></div>
          </form>

          <h2>View and edit your users <small>(<a href="#" class="toggle" data-target="form.groupCreate"><i class="icon-plus-sign-alt"></i> New group</a>)</small></h2>
          <table class="table groups hide"></table>
          <div class="no-groups hide">You haven't created any groups yet.</div>
        </div>
      </div>
    <?php } ?>
    <!--<div class="row settings groupform">
      <div class="span10">
      </div>
    </div>-->
  </div>
</div>
