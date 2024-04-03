define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  require('components/field/actors/list')

  return new Class(
  {
    Extends: Plugin,

    onAfterFormData: function(data)
    {
      let obj = this.obj
      
      data.ordering = obj.node.find('.ordering').val()
    },

    onAfterObjsRender: function()
    {
      let obj = this.obj,
          self = this

      obj.node.find('.forminput.Type').change(function()
      {
        let type = $(this).val()

        if (type)
        {
          self.ajax({
            method: 'getComponentParams',
            data: {
              ftype: type
            },
            success: function(data)
            {
              let html = ''

              if (data.params)
              {
                html = App.render(self.getActor(data.params, {
                  data: {Params: JSON.parse(get(obj.opts.rows[0], '[]', 'Params'))}
                }))
              }
              else
              {
                html = 'Not params'
              }

              obj.node.find('.params-container').html(html)
            }
          })
        }
      }).change()
    },

    onElementGetValue: function()
    {
      let obj = this.obj, sobj = this.sobj

      // Ordering
      if (sobj.opts.name == 'Ordering')
      {
        let list = this.getActor({
              group: 'field',
              name: 'list',
              opts: {
                isedit: true,
                name: 'ordering',
                isps: false,
                options: obj.opts.rows[0].Ordering
              },
              value: obj.opts.rowId
            })

        sobj.value = App.render(list)
      }
    }
  })
})