define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  return new Class(
  {
    Extends: Plugin,

    // onElementGetValue: function()
    // {
    //   let obj = this.obj, sobj = this.sobj

    //   // Mobile
    //   if (sobj.opts.name == 'ChatId')
    //   {
    //     sobj.value = '0123'
    //   }
    // },

    onBeforeRender: function()
    {
      if (this.obj.opts.isEditable)
      {
        let obj = this.obj,
            chatid = obj.getElement(303)

        if (obj.opts.isNewRecord)
        {
          chatid.opts.placeholder = 'Введите проверочный ключ'
          chatid.opts.button = {
            label: 'Найти ID',
            classname: 'findid'
          }
        }
        else
        {
          chatid.opts.readonly = true
        } 
      }
    },

    onAfterObjsRender: function(data)
    {
      let self = this

      $('.findid').click(function()
      {
        let key = $('.ChatId').val()

        if (key)
        {
          self.ajax({
            method: 'findChatId',
            data: {
              key: key
            },
            success: function(data)
            {
              let text = ''

              if (data.status == 'notfound')
              {
                text = 'Чат ID не найден'
              }
              else
              {
                text = "Чат ID: "+data.id+"\nИмя: "+data.name

                if (data.status == 'ok')
                {
                  text += "\n\n Ok!"

                  $('.ChatId').val(data.id)
                  $('.ChatId').attr('readonly', 'readonly') 
                }
                else if (data.status == 'exist')
                {
                  text += "\n\nДанный Чат ID уже зарегестрирован в системе"
                }
              }

              alert(text)
            }
          })
        }
        else
        {
          alert('Введите проверочный ключ')
        }
      }) 
    }
  })
})