define(function(require) 
{
	let View = require('view')

  return new Class(
  {
  	Extends: View,

  	id: null,
		// initialize: function(opts, moduleid)
		// {
		// 	this.parent(opts, moduleid)
		// },

    ajax: function(obj)
    {
    	obj.data = Object.assign(obj.data, {
        option: 'fabrik',
        id: this.id,
        moduleid: this.opts.moduleid
    	})

      this.parent(obj)
    }
  })
})
