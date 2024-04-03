define(function(require) 
{
	let Addon = require('components/builder/addon')

  return new Class(
  {
  	Extends: Addon,

		render_front: function()
		{
			let html = null

			if (this.getElement().opts.display)
			{
				html = 
					'<div class="layer '+this.opts.view+'">'+
						this['render_'+this.opts.view]()+
					'</div>'
			}

			return html
		},

		render_input: function()
		{
			return App.render(this.getElement())
		},

		render_label: function()
		{
			return this.opts.label
		},

		getElement: function()
		{
			let formmodel = this.po.parentobject,
					element = formmodel.getElement(this.opts.elementid)

			return element
		}
  })
})