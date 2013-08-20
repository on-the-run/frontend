(function($){
  op.ns('data.view').Group = op.data.view.Editable.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    className: 'group-meta',
    template    :_.template($('#group-meta').html()),
    editable    : {
      '.name.edit' : {
        name: 'name',
        placement: 'bottom',
        title: 'Edit Group Name',
        validate : function(value){
          if($.trim(value) == ''){
            return 'Please enter a name';
          }
          return null;
        }
      },
      '.description.edit' : {
        name: 'description',
        placement: 'top',
        title: 'Edit Group Description',
        type: 'textarea',
        emptytext: 'Add a description',
        validate : function(value){
          if($.trim(value) == ''){
            return 'Please enter a description';
          }
          return null;
        }
      }
    },
    modelChanged: function() {
      this.render();
    }
  });
})(jQuery);
