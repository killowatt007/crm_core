define(function(require) 
{
	let Addon = require('components/builder/addon')

  return new Class(
  {
  	Extends: Addon,

  	addonactor: null,

		render_front: function()
		{
			let actor = this.getAddonActor(),
					html = App.render(actor)

			return html
		},

		getAddonActor: function()
		{
			if (!this.addonactor)
				this.addonactor = this.getActor(this.opts)
			
			return this.addonactor
		}
  })
})