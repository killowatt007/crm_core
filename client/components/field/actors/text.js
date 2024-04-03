define(function(require) 
{
	let Actor = require('components/field/actor')

  return new Class(
  {
  	Extends: Actor,

		render: function()
		{
			let method = this.opts.isedit ? 'renderEdit' : 'renderRO'

			return this[method]()
		},

		renderEdit: function()
		{
			return '<textarea id="'+this.key+'" name="'+this.getName()+'" class="'+this.getClasses()+' forminput form-control">'+this.getValue()+'</textarea>'
		}
  })
})