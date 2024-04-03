define(function(require) 
{
  let View = require('view'),
      HObject = require('lib/bs/helper/object')

  require('components/builder/actors/params')
  require('components/builder/actors/popup')

  return new Class(
  {
    Extends: View,

    plg: null,

    currentType: null,
    currentBlock: null,
    currentPopup: null,

    initialize: function(options, plg) 
    {
      this.options = options
      this.plg = plg
    },

    onAfterRender: function()
    {
      this.node = $('.builder')
      this.editAddon()
    },

    editAddon: function()
    {
      let self = this

      this.node.on('click', '.edit-addon', function()
      {
        let html = '',
            addon = $(this).parents('.addon:first'),
            params = self.getActor({
              group: 'builder',
              name: 'params',
              opts: {
                scheme: self.options.popupparams.addon,
                data: self.getParams(addon)
              }
            }),
            popup = self.getActor({
              group: 'builder',
              name: 'popup',
              opts: {
                label: 'Addon',
                width: 'litle',
                afterRender: function(popup) {popup.plugin.afterPopap()}
              }
            })

        html = 
          '<div class="params-modal">'+
            App.render(params)+
            '<div class="addon-params" style="margin-top:20px"></div>'+
            '<button type="submit" class="b b-s b-success apply">Apply</button>'+
          '</div>'

        self.currentType = 'addon'
        self.currentBlock = addon
        self.currentPopup = popup

        popup.plugin = self
        popup.opts.content = html
        popup.open()
      })
    },

    afterPopap: function()
    {
      let self = this

      this.selectAddon()

      this.currentPopup.node.find('.apply').click(function()
      {
        let params = HObject.inputsToObject(self.currentPopup.node.find('input, select, textarea'))
        
        params.type = self.currentType
        self.setParams(self.currentBlock, params)
        self.currentBlock.find('.adminlabel').html(self.getAdminLabel())
        self.currentPopup.close()
      })
    },

    getAdminLabel: function()
    {
      let adminlabel

      adminlabel  = '<span>'+this.currentPopup.node.find('.form-control.name option:selected').text()+'</span>'
      adminlabel += ' '+this.currentPopup.node.find('.form-control.adminlabel').val()

      return adminlabel
    },

    selectAddon: function()
    {
      let self = this

      this.currentPopup.node.find('.params-modal > .params select.name').change(function()
      {
        let addon = $(this).val(),
            parent = $(this).parents('.params-modal'),
            pcont = parent.find('.addon-params'),
            addonparams = self.getParams(self.currentBlock)

        if (addon)
        {
          self.plg.ajax({
            method: 'getAddonParams',
            data: {
              addon: addon,
              addongroup: self.options.group,
              extra: self.getExtraData()
            },
            success: function(data)
            {
              let params,
                  html = ''

              if (data.params)
              {
                params = self.getActor({
                  group: 'builder',
                  name: 'params',
                  opts: {
                    scheme: data.params,
                    data: {params: addonparams.params},
                    names:['params']
                  }
                })

                html = App.render(params)
              }

              pcont.html(html)
            }
          }) 
        }
      }).change()
    },

    getParams: function(node)
    {
      return JSON.parse($(node).find('> .params').text())
    },

    setParams: function(node, params)
    {
      $(node).find('> .params').text(JSON.stringify(params))
    },

    stringParams: function(data, deltype)
    {
      let string,
          typedata = data[deltype]

      delete data[deltype]
      string = JSON.stringify(data)
      data[deltype] = typedata;

      return string
    },

    getDefParams: function(type)
    {
      let def = {
            type: type,
            params: {}
          }

      if (type == 'row')
      {
        def.columns = [{type:'column', size:24, data:[]}]
      }
      else if (type == 'col')
      {
        def.size = 24
        def.data = []
      }
      else if (type == 'addon')
      {
        this.options.popupparams.addon.items[0].data.items.map(field =>
        {
          def[field.name] = get(field, '', 'default')
        })
      }

      return def
    },

    formDataAddon: function(addonNode)
    {
      return JSON.parse($(addonNode).find('> .params').text())
    },

    getExtraData: function()
    {
      return {}
    }
  })
})