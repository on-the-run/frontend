(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};
  
  function Players() {
    var players = [], $scriptEl = $('.flow-player-script'), swf = $scriptEl.attr('data-swf'), html5 = $scriptEl.attr('data-html5');

    flowplayer.conf = {
      splash: true,
      adaptiveRatio: true
    };

    this.load = function(id, params) {
      var player, elementId = 'video-element-'+id, $metaEl = $('#'+elementId).closest('.imageContainer').find('.photo-meta');
      var $el = $('#'+elementId);

      $el.flowplayer({
        engine:'html5',
        src:swf,
        // one video: a one-member playlist
        playlist: [
          [
             { mp4:  "http://content.bitsontherun.com/videos/lWMJeVvV-364767.mp4" }
          ]
        ],
        // TODO check if this ever works
        width:params.width,
        height:params.height,
        canvas: {backgroundColor:'#000000'},
        plugins: {
          controls: {
            progressColor: '#000000',
            bufferColor: '#000000'
          }
        }
      })
    };
  }
  
  TBX.players = new Players;
})(jQuery);

