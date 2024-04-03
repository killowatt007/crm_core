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
			let html = '',
					checked = this.getValue() ? 'checked' : ''

			html = '<input id="'+this.key+'" value="1" name="'+this.getName()+'" class="'+this.getClasses()+' forminput form-control" type="checkbox" '+checked+'>'

			return html
		},

		renderRO: function()
		{
			let value = this.getValue(),
					html = ''

			html = value ? '<i class="far fa-check"></i>' : ''

			return html
		},

    getCurrentValue: function()
    {
      return this.node.is(':checked') ? 1 : 0
    }
  })
})