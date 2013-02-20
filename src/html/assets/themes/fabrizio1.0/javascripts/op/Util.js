(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};

  function Util() {
    this.fetchAndCache = function(src) {
      $('<img />').attr('src', src).appendTo('body').css('display', 'none').on('load', function(ev) { $(ev.target).remove(); });
    };

    // http://stackoverflow.com/questions/12662824/twitter-bootstrap-typeahead-multiple-values
    // used by typeahead plugin
    this.tagExtractor = function(query) {
      var result = /([^,]+)$/.exec(query);
      if(result && result[1])
          return result[1].trim();
      return '';
    };
  }

  TBX.util = new Util;
})(jQuery);
