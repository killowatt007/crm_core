define(function(require) 
{
	let Actor = require('components/field/actor')

  return new Class(
  {
  	Extends: Actor,

  	render: function()
  	{
  		let self = this,
  				def = this.getValue('moduleid'),
  				html = 
  					'<div id="'+this.key+'" class="filterfields">'+
		          '<select class="'+this.getClasses()+' moduleid forminput form-control" name="'+this.getName('moduleid')+'">'+
		          	'<option value="">Please Select</option>'+
		          	this.opts.options.map(function(option)
		          	{
						    	let html,
						    			selected = option.value == def ? 'selected' : ''
						    				
						    	return '<option value="'+option.value+'" '+selected+'>'+option.label+'</option>'
		          	}).join('')+
		          '</select>'+
		          '<select class="'+this.getClasses()+' fieldid forminput form-control" name="'+this.getName('fieldid')+'">'+
		          	'<option>- Empty -</option>'+
		          '</select>'+
	          '<div>'

	    return html
  	},

  	getName: function(name)
  	{
  		return this.parent()+'['+name+']'
  	},

  	getValue: function(name)
  	{
  		return get(this.parent(), '', name)
  	},

		onAfterRender: function()
		{
			let self = this

			this.node.find('.moduleid').change(function()
			{
				let moduleid = $(this).val(),
						container = self.node.find('.fieldid'),
						def = self.getValue('fieldid')

				if (moduleid)
				{
					self.ajax({
						data: {
		          option: 'field',
		          branch: 'fabrik',
		          task: 'filterfields.getFields',

		          moduleid: moduleid
						},
						success: function(data)
						{
							let options = '<option value="">Please Select</option>'

	          	data.options.map(function(option)
	          	{
					    	let selected = option.value == def ? 'selected' : ''
					    	options += '<option value="'+option.value+'" '+selected+'>'+option.label+'</option>'
	          	})

	          	container.html(options)
						}
					})
				}
			}).change()
		}
  })
})