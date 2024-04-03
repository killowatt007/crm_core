define(function(require) 
{
	let View = require('view')

  return new Class(
  {
  	Extends: View,

  	block: null,
  	container: false,

		render: function()
		{
			let block = new App.dep['components/fabrik/actors/form'](this.opts.view),
					html = App.render(block)

			this.block = block
			this.block.fabrikMed = this.fabrikMed

			return html
		},

		updateRender: function(data)
		{
			this.block.updateRender(data.opts.view)
		}
  })
})
