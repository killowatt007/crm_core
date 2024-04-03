define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  return new Class(
  {
    Extends: Plugin,

    onAfterProcess: function(data)
    {
      let filter = $('.fabrik.filter')
      filter.find('.value.id select').html('<option value="'+data.clientid+'" selected>'+data.clientid+'</option')
    },

    onAfterObjsRender: function()
    {
      let self = this

      this.subconst('checkid')
    },

    checkid: 
    {
      timeout: null,
      fieldid: null,

      init: function()
      {
        let self = this
          
        this.fieldid = this.po.obj.fields[138]
        this.po.obj.node.find('.forminput.id').keyup(function()
        {
          self.ajax($(this).val())
        })
      },

      ajax: function(value)
      {
        let self = this

        if (this.timeout)
          clearTimeout(this.timeout)

        this.fieldid.icon('loader')

        this.timeout = setTimeout(function()
        {
          self.po.ajax({
            method: 'checkId',
            data: {
              value: value,
              selfid: self.po.obj.opts.rowId
            },
            success: function(data)
            {
              let iconName = data.isset ? 'error' : 'ok'

              self.fieldid.icon(iconName)
            }
          })
        }, 1000)
      }
    }
  })
})