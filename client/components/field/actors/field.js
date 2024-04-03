define(function(require) 
{
	let Actor = require('components/field/actor')

  return new Class(
  {
  	Extends: Actor,

  	opts: {
  		button: null
  	},

  	icons: {
  		clear: 'far fa-backspace',
  		error: 'far fa-times-circle',
  		ok: 'far fa-check-circle',
  		loader: 'fad fa-spinner-third spin'
  	},

		render: function()
		{
			let method = this.opts.isedit ? 'renderEdit' : 'renderRO'

			return this[method]()
		},

		renderEdit: function()
		{
			let readonly = get(this.opts, false, 'readonly')
					html =
						'<div id="'+this.key+'" class="field-field">'+
							'<input name="'+this.getName()+'" class="forminput form-control '+this.getClasses()+'" type="text" value="'+get(this.getValue(), '')+'" placeholder="'+get(this.opts, '', 'placeholder')+'" '+(readonly ? 'readonly="readonly"': '')+'>'+
							this.getButton()+
						'</div>'

			return html
		},

		getButton: function()
		{
			let html = '',
					data = get(this.opts, null, 'button'),
					classname = data && data.classname ? data.classname : ''

			html += '<div class="button '+classname+'">'

			if (data)
			{
				html += data.label
			}

			html += '</div>'

			return html
		},

		icon: function(name, isshow)
		{
			let classes = this.icons[name]

			if (get(isshow, true))
			{
				this.node.find('.button').html('<i class="'+classes+' '+name+'"></i>')
			}
			else
			{
				this.node.find('.button').html('')
			}
		}
  })
})