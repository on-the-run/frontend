(function($){
  op.ns('data.view').GroupListItem = op.data.view.Editable.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    className: 'group-list-item-meta',
    template    :_.template($('#group-list-item-meta').html()),
    editable    : {
      '.name.edit' : {
        name: 'name',
        placement: 'top',
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
    events: {
      'click .delete': 'delete_',
      'click .undelete': 'undelete'
    },
    modelChanged: function() {
      this.render();
    },
    delete_: function(ev) {
      ev.preventDefault();
      var $el = $(ev.target), id = $el.attr('data-id'), model = this.model;
      model.destroy({wait: true, success: this.modelDeleted.bind(model), error: TBX.notification.display.generic.error});
    },
    undelete: function(ev) {
      ev.preventDefault();
      var model = this.model;
      model.save({deleted: false}, {wait:true, success: this.modelUndeleted.bind(model), error: TBX.notification.display.generic.error});
    },

    modelDeleted: function() {
      var id = this.get('id'), $el = $('.group-'+id);
      TBX.notification.show('Your request to delete <em>' + this.get('name') + '</em> was successful.', 'flash', 'confirm');
      this.set('deleted', true);
    },
    modelUndeleted: function() {
      TBX.notification.show('Your request to restore <em>' + this.get('name') + '</em> was successful.', 'flash', 'confirm');
    }
  });
})(jQuery);
