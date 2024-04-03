define(function(require) 
{
	let Actor = require('components/field/actor')

  return new Class(
  {
  	Extends: Actor,

  	formData: {
  		data: null,
  		name: null
  	},

		render: function()
		{
			let method = this.opts.isedit ? 'renderEdit' : 'renderRO'

			return this[method]()
		},

		renderEdit: function()
		{
			return '<input id="'+this.key+'" class="file forminput form-control '+this.getClasses()+'" type="file" placeholder="'+get(this.opts, '', 'placeholder')+'">'
		},

		onAfterRender: function()
		{
			let self = this

			this.node.change(function(e)
			{
		    let file = e.target.files,
		        f = file[0],
		        reader = new FileReader();

		    // if (!f.type.match('image.*'))
		    //  alert("Image only please....");

		    reader.onload = (function(theFile) 
		    {
		      return function(e) 
		      {
		        let data = e.target.result,
		            name = theFile.name /*escape(theFile.name).replace(/\s/ig, '_'),*/

		        self.formData = {
		        	data: data,
		        	name: name
		        }
		      }
		    })(f)

		    reader.readAsDataURL(f)
			})
		}
  })
})