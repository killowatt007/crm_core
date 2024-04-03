define(function(require) 
{
  /**
   * $version 1.1
   */

  let Actor = require('components/field/actor')

  require('components/builder/actors/table')
  require('components/builder/actors/popup')
  require('components/builder/actors/addons/general/html')
  require('components/builder/actors/addons/general/text')

  require('components/field/actors/field')

  return new Class(
  {
    Extends: Actor,

    render: function()
    {
      let html =
        '<button class="b b-s b-primary single_inv">'+
          '<i class="fas fa-envelope"></i>'+
          (!App.ismobile ? 'Квитанция' : '')+
        '</button>'

      return html
    },

    getClient: function()
    {
      let data = null

      if (this.opts.onGetClient)
        data = this.opts.onGetClient()

      return data
    },

    onAfterRender: function()
    {
      let self = this

      $('.single_inv').click(function()
      {
        let cid = self.getClient()

        self.ajax({
          data: {
            option: 'domofon',
            task: 'single_inv_mail.get_client_data',
            branch: 'clients',
            clientid: cid
          },
          success: function(data)
          {
            let popup = self.getActor({
                  group: 'builder',
                  name: 'popup',
                  opts: {
                    content: 
                      '<div class="fabrik form" style="padding:20px;">'+
                        '<div class="data">'+
                          App.render(self.getform(data.client))+
                        '</div>'+
                        '<div class="actions">'+
                          '<div class="left">'+
                            '<button class="b b-s bs-r b-success submit">Отправить</button>'+
                          '</div>'+
                          '<div class="right">'+
                            '<button class="b b-s bs-r b-primary gb">Закрыть</button>'+
                          '</div>'+
                        '</div>'+
                      '</div>',
                    label: 'Квартальные начисления',
                  }
                })

            popup.open()
            $('.actions .gb').click(()=>popup.close())
            self.submit(popup)
          }
        })
      })
    },

    submit: function(popup)
    {
      let self = this

      popup.node.find('.submit').click(function()
      {
        let cid = self.getClient()

        self.ajax({
          data: {
            option: 'domofon',
            task: 'single_inv_mail.send',
            branch: 'clients',
            clientid: cid,
            mail: popup.node.find('.forminput.mail').val()
          },
          success: function(data)
          {
            if (data.error)
            {
              alert(data.error)
            }
            else
            {
              popup.close()
              alert('Письмо успешно отправлено')
            }
          }
        })
      })
    },

    getform: function(data)
    {
      let fmail = this.getActor({
            group: 'field',
            name: 'field',
            value: (data.Mail ? data.Mail : ''),
            opts: {
              name: 'mail',
              isedit: true
            }
          }),
          table = this.getActor({
            group: 'builder',
            name: 'table',
            opts:
            {
              tmpls: [
                {
                  id:48, childId:0, parentId:0, render:true,
                  data: [
                    {
                      type:'row', params:[],
                      columns: 
                      [
                        {
                          type:'column', size:6, params:[],
                          data:[{
                            type:'addon', group:'builder', branch:'addons.general', name:'text',
                            opts:{text:'ФИО', classes:'label'}
                          }]
                        },
                        {
                          type:'column', size:18, params:[],
                          data: [{
                            type:'addon', group:'builder', branch:'addons.general', name:'text',
                            opts:{text:data.FIO, classes:'input'}
                          }]
                        }
                      ]
                    },
                    {
                      type:'row', params:[],
                      columns: 
                      [
                        {
                          type:'column', size:6, params:[],
                          data:[{
                            type:'addon', group:'builder', branch:'addons.general', name:'text',
                            opts:{text:'Mail', classes:'label'}
                          }]
                        },
                        {
                          type:'column', size:18, params:[],
                          data: [{
                            type:'addon', group:'builder', branch:'addons.general', name:'html',
                            opts:{html: App.render(fmail)}
                          }]
                        }
                      ]
                    }
                  ]
                }
              ],
              group: 'fabrikform'
            }
          })

      return table
    },
  })
})