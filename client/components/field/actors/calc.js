define(function(require) 
{
	let Actor = require('components/field/actor')

  return new Class(
  {
  	Extends: Actor,

		render: function()
		{
			return '<span class="'+this.getClasses()+'">'+this.getValue()+'</span>'
		}
  })
})
