<?php
/**
 * Video model.
 *
 * Operations related to videos
 * @author James Walker <walkah@walkah.net>
 */
class Video extends Media
{
  public function __construct($params = null)
  {
    parent::__construct();
    
    if(isset($params['utility']))
      $this->utility = $params['utility'];
    else
      $this->utility = new Utility;

    if(isset($params['url']))
      $this->url = $params['url'];
    else
      $this->url = new Url;

    if(isset($params['user']))
      $this->user = $params['user'];
    else
      $this->user = new User;

    if(isset($params['config']))
      $this->config = $params['config'];
  }


  public function generatePaths($videoName, $dateTaken)
  {
    $ext = substr($videoName, (strrpos($videoName, '.')+1));
    $rootName = preg_replace('/[^a-zA-Z0-9.-_]/', '-', substr($videoName, 0, (strrpos($videoName, '.'))));
    $originalName = sprintf('%s-%s.%s', $rootName, uniqid(), $ext);
    
    return array(
      'pathOriginal' => sprintf('/video/base/%s/%s', date('Ym', $dateTaken), $originalName)
    );
  }

  /**
   * Uploads a new video to the remote file system and database.
   *
   * @param string $localFile The local file system path to the video.
   * @param string $name The file name of the video.
   * @param array $attributes The attributes to save
   * @return mixed string on success, false on failure
   */
  public function upload($localFile, $name, $attributes = array())
  {
    $tagObj = new Tag;

    $id = $this->user->getNextId('photo');
    if ($id === false)
    {
      $this->logger->crit('Could not fetch next photo ID');
      return false;
    }

    if(isset($attributes['dateTaken']) && !empty($attributes['dateTaken']))
      $dateTaken = $attributes['dateTaken'];
    else
      $dateTaken = time();

    $resp = $this->storeOriginal($name, $localFile, $dateTaken);
    $paths = $resp['paths'];

    $attributes = $this->whitelistParams($attributes);

    if ($resp['status'])
    {
      $this->logger->info("Video ({$id}) successfully stored on the file system");
      
      if(isset($attributes['dateUploaded']) && !empty($attributes['dateUploaded']))
        $dateUploaded = $attributes['dateUploaded'];
      else
        $dateUploaded = time();

      if(isset($attributes['tags']) && !empty($attributes['tags']))
        $tagObj->createBatch($attributes['tags']);

      $attributes['owner'] = $this->owner;
      $attributes['actor'] = $this->getActor();

      $stored = $this->db->putPhoto($id, $attributes, $dateTaken);
      unlink($localFile);
      if($stored)
      {
        $this->logger->info("Photo ({$id}) successfully stored to the database");
        return $id;
      }
      else
      {
        $this->logger->warn("Photo ({$id}) could NOT be stored to the database");
        return false;
      }
    }
  }


  private function storeOriginal($name, $localFile, $dateTaken)
  {
    $paths = $this->generatePaths($name, $dateTaken);

    $uploaded = $this->fs->putPhotos(
      array(
        array($localFile => array($paths['pathOriginal'], $dateTaken))
      )
    );
    
    return array('status' => $uploaded, 'paths' => $paths);
  }
}