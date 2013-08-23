<?php
/**
 * AwsTranscoder Plugin
 *
 * @author James Walker - walkah@walkah.net
 */

// iPhone - 1351620000001-100020

use Aws\ElasticTranscoder\ElasticTranscoderClient;
use Aws\Sns\SnsClient;


class AwsTranscoderPlugin extends PluginBase
{

  public function __construct()
  {
    parent::__construct();

    $utilityObj = new Utility;
    $this->sns = SnsClient::factory(
      array(
        'key'    => $utilityObj->decrypt($this->config->credentials->awsKey),
        'secret' => $utilityObj->decrypt($this->config->credentials->awsSecret),
        'region' => 'us-east-1'
      )
    );
    $this->transcoder = ElasticTranscoderClient::factory(
      array(
        'key'    => $utilityObj->decrypt($this->config->credentials->awsKey),
        'secret' => $utilityObj->decrypt($this->config->credentials->awsSecret),
        'region' => 'us-east-1'
      )
    );
    
  }

  public function defineConf()
  {
    return array(
      'pipelineId' => '',
      'presets' => '',
    );
  }
  
  public function onVideoUploaded()
  {
    parent::onVideoUploaded();

    $conf = $this->getConf();
    
    $video = $this->plugin->getData('video');
    $videoId = $this->plugin->getData('videoId');
    
    $args = array(
      'PipelineId' => $conf->pipelineId,
      'Input' => array(
        'Key' => substr($video['pathOriginal'], 1),
        'FrameRate' => 'auto',
        'Resolution' => 'auto',
        'AspectRatio' => 'auto',
        'Interlaced' => 'auto',
        'Container' => 'auto',
      ),
    );

    $presets = explode(',', $conf->presets);

    foreach($presets as $presetId) {
      $this->logger->warn('preset: '. $presetId);
      $paths = $this->generatePaths($presetId, $video);
      
      $args['Outputs'][] = array(
        'Key' => $paths['video'],
        'ThumbnailPattern' => $paths['thumbnail'],
        'Rotate' => 'auto',
        'PresetId' => $presetId,
      );
    }
    
    // Create ET job
    try {
      $result = $this->transcoder->createJob($args);
      $job = $result->get('Job');

      $video['key'] = $job['Id'];
      $video['extraVideo']['status'] = 'pending';
      $video['extraVideo']['jobId'] = $job['Id'];
      $this->db->postPhoto($videoId, $video);
    }
    catch (Exception $e) {
      $this->logger->crit("Unable to create Job: ". $e->getMessage());
    }

    
  }

  public function defineApis()
  {
    return array(
      'sns' => array('POST', '/sns.json', EpiApi::external)
    );
  }

  public function routeHandler($route)
  {
    switch($route) {
      case '/sns.json':
        $data = file_get_contents('php://input');
        $json = json_decode($data);

        switch ($json->Type) {
          case 'SubscriptionConfirmation':
            return $this->confirmSubscription($json);
          case 'Notification':
            return $this->handleNotification($json);
        }
        
        break;
    }
  }

  private function confirmSubscription($values)
  {
    $params = array(
      'TopicArn' => $values->TopicArn,
      'Token' => $values->Token
    );

    try {
      $this->sns->confirmSubscription($params);
    } catch (Exception $e) {
      $this->logger->crit("Unable to confirm SNS subscription: " . $e->getMessage());
    }

    return array(
      'code' => 200
    );
  }

  private function handleNotification($values)
  {
    $message = json_decode($values->Message);

    switch ($message->state) {
      case 'ERROR':
        $this->logger->error('Transcoding failed for jobId: '. $message->jobId);
        break;
      case 'COMPLETED':
        $photo = $this->db->getPhotoByKey($message->jobId);

        // TODO(walkah): This isn't working ...
        $thumbnailFile = $this->fs->getPhoto(str_replace('{count}', '00001.png', $message->outputs[0]->thumbnailPattern));
        
        $params = array(
          'photo' => $thumbnailFile
        );
        foreach ($message->outputs as $output) {
          $params['extraVideo']['output'] = $output->key;
        }

        $photoId = $photo['id'];
        $apiVersion = Request::getApiVersion();
        $this->api->invoke('/{$apiVersion}/photo/{$photoId}/replace.json', EpiRoute::httpPost, array('_POST' => $params));
        break;
        
    }
    return array(
      'code' => 200
    );
  }
  
  private function generatePaths($presetId, $video)
  {
    try {
      $result = $this->transcoder->readPreset(array('Id' => $presetId));
      $preset = $result->get('Preset');
    } catch (Exception $e) {
      $this->logger->crit('Unable to read preset: '. $e->getMessage());
    }
      
    $parts = pathinfo($video['pathOriginal']);

    return array(
      'video' => sprintf('video/custom/%s/%s-%s.%s', date('Ym', $video['dateTaken']), $parts['filename'], $present['Id'], $preset['Container']),
      'thumbnail' => sprintf('video/custom/%s/%s-{count}', date('Ym', $video['dateTaken']), $parts['filename'])
    );
  }  
}