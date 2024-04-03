define(function(require) 
{
	let Popup = require('components/builder/actors/popup')

  return new Class(
  {
  	Extends: Popup,

		initialize: function(popupData)
		{
      let self = this

      popupData.opts.ajax = {
        data: {
          option: 'fabrik',
          task: 'form',
          id: popupData.opts.entityid,
          rowId: get(popupData.opts.rowId)
        },
        success: function(data)
        {
          let object = new App.dep[App.clientPath](data),
              html = 
                '<div class="fabrik-modal">'+
                  App.render(object)+
                '</div>'

          if (popupData.opts.afterOpen)
            popupData.opts.afterOpen(object)

          object.popup = self
          return html
        }
      }

      this.parent(popupData)
		}
  })
})
