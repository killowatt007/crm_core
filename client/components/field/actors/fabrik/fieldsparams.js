define(function(require) 
{
	let List = require('components/field/actors/list')

  return new Class(
  {
  	Extends: List,

  	render: function()
  	{
  		let html = 
  					'<div id="'+this.key+'" class="fieldsparams">'+
							'<select class="form-control fieldlist">'+this.getOptions()+'</select>'+
							'<a href="#" class="b b-c button add-field"><i class="fa fa-plus"></i></a>'+
					  	'<div class="fields-data"></div>'+
  					'</div>'

  		return html
  	},

  	// data.field.label
  	getField: function(id, label, params, _data)
  	{
  		let self = this,
  				data = {}

  		if (_data)
  		{
  			data = {Params: {fields: {}}}
  			data.Params.fields[id] = _data
  		}

      let html =
						'<div class="field">'+
							'<div class="boxheader">'+
								'<div class="llabel">'+label+'</div>'+
								'<div class="remove"><i class="far fa-trash-alt"></i></div>'+
							'</div>'+
							'<div class="elparams">'+
								App.render(this.getActor(params, {
								  data: data,
								  names: this.opts.names.concat([this.opts.name, id]),
								  activeparams: true,
	                getExtraData: function()
	                {
	                	return {
	                		fieldid: id,
	                		tmplid: self.getPplg().obj.opts.rows[0].TmplId
	                	}
	                }
								}))+
							'</div>'+
						'</div>'

			return html
  	},

  	renderField: function(id, data)
  	{
  		let self = this

			this.ajax({
				data: {
          option: 'field',
          branch: 'fabrik',
          task: 'fieldsparams.getParams',

          fieldid: id,
				},
				success: function(resp)
				{
					if (resp.params)
						self.node.find('.fields-data').append(self.getField(id, resp.field.label, resp.params, data))
				}
			})
  	},

		onAfterRender: function()
		{
			let self = this,
					fields = this.value ? this.value : {}

			// each fields
  		$.each(fields, (id, data) => 
  		{
  			this.renderField(id, data)
  		})

			// add
			this.node.on('click', '.add-field', function(e)
			{
				e.preventDefault()

				let fieldid = self.node.find('.fieldlist').val()

				if (fieldid)
					self.renderField(fieldid, {})
			})

			// open/hide
			this.node.on('click', '.field .boxheader .llabel', function()
			{
				let parent = $(this).parents('.field:first')

				parent.toggleClass('open')
			})

			// remove
			this.node.on('click', '.field .boxheader .remove', function()
			{
				$(this).parents('.field:first').remove()
			})
		}
  })
})


