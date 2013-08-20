<?php
/**
 * Media controller for API endpoints
 *
 * @author James Walker <walkah@walkah.net>
 */
class ApiMediaController extends ApiBaseController
{

  public function __construct()
  {
    parent::__construct();
    $this->photo = new Photo;
    $this->tag = new Tag;
    $this->user = new User;
  }

  /**
   * Upload new media.
   *
   * @return string standard json envelope
   */
  public function upload()
  {
    $this->logger->warn("STARTING MEDIA");

    getAuthentication()->requireAuthentication();
    getAuthentication()->requireCrumb();
    $httpObj = new Http;
    $attributes = $_REQUEST;

    // determine localFile
    extract($this->parseMediaFromRequest());
    
    // Get file mimetype
    $utility = new Utility;
    $media_type = $utility->getMediaType($localFile);

    $this->logger->warn(sprintf("GOT MEDIA TYPE: %s", $media_type));
    // Invoke type-specific
    switch ($media_type) {
      case 'photo':
        return $this->api->invoke("/{$this->apiVersion}/photo/upload.json", EpiRoute::httpPost);
      case 'video':
        return $this->api->invoke("/{$this->apiVersion}/video/upload.json", EpiRoute::httpPost);
    }
    
    return $this->error('Unsupported media type', false);
  }

  /**
   *
   */
  private function parseMediaFromRequest()
  {
    $name = '';
    if(isset($_FILES) && isset($_FILES['photo']))
    {
      $localFile = $_FILES['photo']['tmp_name'];
      $name = $_FILES['photo']['name'];
    }
    elseif(isset($_POST['photo']))
    {
      // if a filename is passed in we use it else it's the random temp name
      $localFile = tempnam($this->config->paths->temp, 'opme');
      $name = basename($localFile).'.jpg';

      // if we have a path to a photo we download it
      // else we base64_decode it
      if(preg_match('#https?://#', $_POST['photo']))
      {
        $fp = fopen($localFile, 'w');
        $ch = curl_init($_POST['photo']);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        // TODO configurable timeout
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
      }
      else
      {
        file_put_contents($localFile, base64_decode($_POST['photo']));
      }
    }

    if(isset($_POST['filename']))
      $name = $_POST['filename'];

    return array('localFile' => $localFile, 'name' => $name);    
  }
}