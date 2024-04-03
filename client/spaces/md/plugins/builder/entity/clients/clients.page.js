define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  require('components/domofon/actors/clients/single_inv_mail')

  return new Class(
  {
		Extends: Plugin,

		onBeforeRenderAddon: function(page, addon)
		{
			this.renderActions(page, addon)
		},

		renderActions: function(page, addon)
		{
      if (addon.name == 'title')
      {
        let self = this,
        		div = $('<div>').append(addon.opts.title),
        		single_inv_mail = this.getActor({
        			group: 'domofon',
        			name: 'single_inv_mail',
        			branch: 'clients',
        			opts: {
        				onGetClient: () => $('.forminput.id').val()
        			}
        		})

        div.find('.header-actions').prepend(App.render(single_inv_mail))

        addon.opts.title = div[0].innerHTML
      }
		},
  })
})