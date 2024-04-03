define(function(require) 
{
	let List = require('components/field/actors/list')

  return new Class(
  {
  	Extends: List,

		onAfterRender: function()
		{
			let self = this

			this.node.change(function()
			{
				let entityid = $(this).val(),
						tmplid = get(self.getPplg().obj, '', 'opts.rows.0.TmplId')

				if (entityid)
				{
					self.ajax({
						data: {
		          option: 'field',
		          branch: 'fabrik',
		          task: 'entity.getParams',

		          eview: self.opts.view,
		          entityid: entityid,
		          tmplid: tmplid
						},
						success: function(data)
						{
							let entity_params = $('.entity-params'),
		              params = self.getActor(data.params, {
		                data: {Params: JSON.parse(get(self.getPplg().obj.opts.rows[0], '[]', 'Params'))},
		                activeparams: true,
		                names: self.opts.names,
		                getExtraData: function()
		                {
		                	return {
		                		tmplid: tmplid,
		                		entityid: entityid
		                	}
		                }
		              }),
		              html = App.render(params)

              if (entity_params[0])
              	entity_params.html(html)
              else
              	$('.params-container').append('<div class="entity-params" style="margin-top:20px">'+html+'</div>')
						}
					})
				}
			}).change()
		}
  })
})