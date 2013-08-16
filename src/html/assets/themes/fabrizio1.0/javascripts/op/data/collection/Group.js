op.ns('data.collection').Group = Backbone.Collection.extend({
  model         :op.data.model.Group,
  localStorage  :'op-groups'
});

