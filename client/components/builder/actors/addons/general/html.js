define(function(require) 
{
	let Addon = require('components/builder/addon')

  return new Class(
  {
  	Extends: Addon,

		render_front: function()
		{
			let html = '<div class="layer">'+this.opts.html+'</div>'

			return html
		}
  })
})