(function($){
  op.ns('data.model').Group = Backbone.Model.extend({
    defaults: {
      deleted: false
    },
    sync: function(method, model, options) {
      options.data = {};
      options.data.crumb = TBX.crumb();
      options.data.httpCodes='*';
      switch(method) {
        case 'read':
          options.url = '/group/'+model.get('id')+'/view.json';
          break;
        case 'update':
          var changedParams = model.changedAttributes();
          console.log(changedParams);
          if(typeof(changedParams['deleted']) === 'undefined') {
            options.url = '/group/'+model.get('id')+'/update.json';
            for(i in changedParams) {
              if(changedParams.hasOwnProperty(i) && i !== 'deleted') {
                options.data[i] = changedParams[i];
              }
            }
          } else if(changedParams['deleted'] === false) {
            options.url = '/group/'+model.get('id')+'/undelete.json';
          }
          break;
        case 'delete':
          options.url = '/group/'+model.get('id')+'/delete.json';
          break;
      }
      return Backbone.sync(method, model, options);
    },
    parse: function(response) {
      return response.result;
    }
  });
})(jQuery);

