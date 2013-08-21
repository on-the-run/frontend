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
         engine:'flash',
         src:swf,
         // one video: a one-member playlist
         playlist: [
            [
               { mp4:  "http://content.bitsontherun.com/videos/lWMJeVvV-364767.mp4" }
            ]
         ]
      });
//    console.log(id);
//    console.log(params);
//    params.flashplayer = swf;
//    params.html5player = html5;
//    params.modes = [
//      {type:'html5'},{type:'flash'}
//    ];

//    player = jwplayer(elementId).setup(params);
//    player.onPlay(TBX.handlers.custom.videoHideMeta.bind($metaEl));
//    player.onPause(TBX.handlers.custom.videoHideShow.bind($metaEl));
//    player.onComplete(TBX.handlers.custom.videoHideShow.bind($metaEl));

//    players[id] = player;
    };
  }
  
  TBX.players = new Players;
})(jQuery);

