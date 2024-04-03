define(function(require) 
{
	let Actor = require('lib/bs/object/actor')

  return new Class(
  {
  	Extends: Actor,

  	id: null,
  	module: null,

		initialize: function(data)
		{
			this.parent(data)
		},

    render: function()
    {
      return this.parent()
    },

    ajax: function(obj)
    {
      obj.data = Object.assign(obj.data, {
        option: 'system',
        task: 'ajax',
        exttype: 'module',
        name: this.name
      })

      this.parent(obj)
    }
  })
})
