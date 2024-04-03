define(function(require) 
{
	let Addon = require('components/builder/addon')

  return new Class(
  {
  	Extends: Addon,

		render_front: function()
		{
			let html = 
						'<h1 class="page">'+this.opts.title+'</h1>'

			return html
		}
  })
})