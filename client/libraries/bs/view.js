define(function(require) 
{
	let Actor = require('lib/bs/object/actor'),
			PlgManager = require('lib/bs/event/manager')

  return new Class(
  {
  	Extends: Actor,

  	group: null,
  	name: null,

  	plgmanager: null,

		getPluginManager: function()
		{
			if (!this.plgmanager)
				this.plgmanager = new PlgManager(App.getDispatcher('component'), this)

			return this.plgmanager
		},

    ajax: function(obj)
    {
      let task = get(obj.data, this.name + (obj.data.method ? '.'+obj.data.method : ''), 'task')

    	obj.data = Object.assign(obj.data, {
        option: this.group,
        branch: get(obj.data, this.branch, 'branch'),
        task: task
    	})

      this.parent(obj)
    }
  })
})
