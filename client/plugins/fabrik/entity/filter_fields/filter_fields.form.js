define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  return new Class(
  {
    Extends: Plugin,

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
            method: 'getTypeParams',
            data: {
              ftype: type
            },
            success: function(data)
            {
              let html = ''

              if (data.params)
              {
                html = App.render(self.getActor(data.params, {
                  data: {Params: JSON.parse(get(obj.opts.rows[0], '{}', 'Params'))},
                  getExtraData: function()
                  {
                    return {
                      entityid: self.obj.node.find('select.entityid').val(),
                      filterid: self.obj.opts.rows[0].FilterId
                    }
                  }
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
    }
  })
})