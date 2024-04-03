define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  return new Class(
  {
    Extends: Plugin,

    onBeforeRenderAddon: function(page, addon)
    {
      this.renderFilter(page, addon)
      this.renderActions(page, addon)
    },

    renderFilter: function(page, addon)
    {
      if (addon.name == 'module')
      {
        if (addon.opts.branch == 'fabrik.filter')
        {
          let module = addon.getAddonActor()

          module.getField('from').placeholder = 'С'
          module.getField('to').placeholder = 'По'

          module.html = 
            '<div class="date-from-to">'+
              '<div class="lab">Дата:</div>'+
              '<div class="fields">'+
                module.renderField(module.getField('from'), true)+
                module.renderField(module.getField('to'), true)+
              '</div>'+
            '</div>'
        }
      }
    },

    renderActions: function(page, addon)
    {
      if (addon.name == 'title')
      {
        addon.opts.title += 
          '<div class="header-actions">'+
            '<button class="b b-s b-primary open-shift">'+
              '<i class="far fa-door-open"></i>'+
              (!App.ismobile ? 'Открыть Смену' : '')+
            '</button>'+
            '<button class="b b-s b-primary close-shift">'+
              '<i class="far fa-door-closed"></i>'+
              (!App.ismobile ? 'Закрыть Смену' : '')+
            '</button>'+
          '<div>'

        console.log();
      }
    },

    onAfterObjsRender: function(resData, reqData)
    {
      if (reqData.isWindow)
      {
        this.opencloseShift()
      }
    },

    opencloseShift: function()
    {
      let self = this,
          filter = App.modules[37]

      function ajax(action, btn)
      {
        let title = $(btn).html(),
            metjod = action+'Shift'

        $(btn).html('<i class="far fa-spinner-third spin status" style="margin-right:0"></i>')

        self.ajax({
          group: 'fabrik',
          type: 'entity',
          name: 'shifts',
          format: 'form',
          method: metjod,
          data: {},
          success: function(data)
          {
            if (!data.error) 
            {
              filter.apply(filter.getField('from'))
            }
            else
            {
              alert(data.error)
            }

            $(btn).html(title)
          }
        })
      }

      $('.open-shift').click(function()
      {
        ajax('open', this)
      })

      $('.close-shift').click(function()
      {
        ajax('close', this)
      })
    }
  })
})