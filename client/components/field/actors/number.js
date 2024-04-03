define(function(require) 
{
	let Actor = require('components/field/actor')

  return new Class(
  {
  	Extends: Actor,

  	opts: {
  	},

		render: function()
		{
			let method = this.opts.isedit ? 'renderEdit' : 'renderRO'

			return this[method]()
		},

		renderEdit: function()
		{
			let readonly = get(this.opts, false, 'readonly'),
					html =
						'<div id="'+this.key+'" class="field-number">'+
							'<input name="'+this.getName()+'" class="forminput form-control '+this.getClasses()+'" type="text" value="'+get(this.getValue(), '')+'" placeholder="'+get(this.opts, '', 'placeholder')+'" '+(readonly ? 'readonly="readonly"': '')+'>'+
							'<button type="button" class="b b-s bs-r nl b-primary minus"><i class="far fa-minus"></i></button>'+
							'<button type="button" class="b b-s bs-r nl b-primary plus"><i class="far fa-plus"></i></button>'+
						'</div>'

			return html
		},

    onAfterRender: function()
    {
    	let self = this,
    			input = this.node.find('.form-control')

      this.node.find('button').click(function()
      {
      	let isplus = $(this).hasClass('plus'),
      			val = parseInt(input.val()),
      			args = {
      				newval: 0
      			}
      			
      	args.newval = isplus ? val+1 : val-1

      	if (self.opts.onBeforeSetVal)
      		self.opts.onBeforeSetVal(args)

      	input.val(args.newval)
      })
    }
  })
})