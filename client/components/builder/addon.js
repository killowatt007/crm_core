define(function(require) 
{
	let View = require('view')

  return new Class(
  {
  	Extends: View,

  	name: null,
  	model: null,

  	opts: {
  		side: 'front'
  	},

		render: function()
		{
			let method = 'render_'+this.opts.side

			return this[method]()
		},

  	render_back: function()
  	{
  		return this.po.addonBack(this.opts.params, '', this.key)
  	},

  	getFormParams: function()
  	{
  		let params = JSON.parse(this.node.find('> .params').text())

  		return params
  	}
  })
})