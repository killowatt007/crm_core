define(function(require) 
{
	let Obj = require('lib/bs/object/object')

  return new Class(
  {
  	Extends: Obj,

  	type: 'actor',

  	po: null,

  	node: null,
  	i: null,
  	key: null,
  	isupdrender: false,
  	isrender: false,

  	plgmanager: null,

		initialize: function(data)
		{
			this.parent(data)
			this.i = App.objectCounter++
			this.key = 'e'+this.i
		},

  	execute: function()
  	{
      $(App.selector).html(App.render(this))
  	},

		updateRender: function(data, notstack)
		{
			if (this.node)
			{
				data = get(data, {})
				
				this.isupdrender = true
				this.isrender = false
				this.setOpts(data.opts)
				this.initProp(data)

				this.node.html($(App.render(this, true, notstack))[0].innerHTML)
				this.onAfterUpdateRender()
			}
			else
			{
				console.log('error update render, container false')
			}
		},

		onAfterRender: function() {},
		onAfterUpdateRender: function() {}
  })
})
