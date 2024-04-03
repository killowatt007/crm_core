define(function(require) 
{
  /**
   * $version 1.1
   * $adv invalid_debt
   *      mailing
   * --------
   * $version 1.2
   * $info добавил в форму чекбокс "Отправить квитанции"
   */

	let Actor = require('components/field/actor')

  require('components/builder/actors/table')
  require('components/builder/actors/popup')
  require('components/builder/actors/addons/general/html')
  require('components/builder/actors/addons/general/text')

  require('components/field/actors/list')
  require('components/field/actors/yesno')

  require('components/system/actors/parts_progress')

  return new Class(
  {
  	Extends: Actor,

		render: function()
		{
			let html = ''

			html +=
        '<button class="b b-s b-primary quarterInvoice">'+
          '<i class="far fa-file-invoice"></i>'+
          (!App.ismobile ? 'Квартальные начисления' : '')+
        '</button>'

			return html
		},

    onAfterRender: function(resData, reqData)
    {
      let self = this

      $('.quarterInvoice').click(function()
      {
        let popup = self.getActor({
              group: 'builder',
              name: 'popup',
              opts: {
                content: 
                  '<div class="fabrik form" style="padding:20px;">'+
                    '<div class="data">'+
                      App.render(self.getform())+
                    '</div>'+
                    '<div class="actions">'+
                      '<div class="left">'+
                        '<button class="b b-s bs-r b-success submit">Начислить</button>'+
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
      })
    },

    getform: function()
    {
      let fbase = this.getActor({
            group: 'field',
            name: 'list',
            // value: this.opts.display,
            opts: {
              options: this.opts.baseOpts,
              name: 'BaseId',
              isedit: true,
              isps: true,
              minimumResultsForSearch: -1
            }
          }),
          table,
          tdata = []

      tdata.push({
        type:'row', params:[],
        columns: 
        [
          {
            type:'column', size:6, params:[],
            data:[{
              type:'addon', group:'builder', branch:'addons.general', name:'text',
              opts:{text:'База', classes:'label'}
            }]
          },
          {
            type:'column', size:18, params:[],
            data: [{
              type:'addon', group:'builder', branch:'addons.general', name:'html',
              opts:{html: App.render(fbase)}
            }]
          }
        ]
      })

      // mailing
      if (this.opts.advanced.mailing)
      {
        this.fismailing = this.getActor({
          group: 'field',
          name: 'yesno',
          value: 0,
          opts: {
            options: this.opts.baseOpts,
            name: 'ismailing',
            isedit: true
          }
        })

        tdata.push({
          type:'row', params:[],
          columns: 
          [
            {
              type:'column', size:6, params:[],
              data:[{
                type:'addon', group:'builder', branch:'addons.general', name:'text',
                opts:{text:'Отправить квитанции', classes:'label'}
              }]
            },
            {
              type:'column', size:18, params:[],
              data: [{
                type:'addon', group:'builder', branch:'addons.general', name:'html',
                opts:{html: App.render(this.fismailing)}
              }]
            }
          ]
        })
      }

      table = this.getActor({
        group: 'builder',
        name: 'table',
        opts:
        {
          tmpls: [
            {
              id:48, childId:0, parentId:0, render:true,
              data:tdata
            }
          ],
          group: 'fabrikform'
        }
      })

      return table
    },

    submit: function(popup)
    {
      let self = this

      popup.node.find('.submit').click(function()
      {
        let parts_progress = self.getActor({
              group: 'system',
              name: 'parts_progress',
              opts: {
                subname: 'quarter_process',
                popup: popup
              }
            })

        parts_progress.start()
      })
    },

    quarter_process: 
    {
      beforeAjax: function()
      {
        // invoice
        if (this.prog.partname == 'invoice')
        {
          if (this.prog.opts.popup.node)
            this.prog.opts.popup.close()
        }
      },

      step: function()
      {
        // invalid_debt
        if (this.prog.partname == 'invalid_debt')
        {
          this.prog.addInfo([{
            label: 'Отключенные абоненты  ('+this.prog.ajaxData.debts.length+')',
            list: this.prog.ajaxData.debts
          }])
        }

        // invoice
        else if (this.prog.partname == 'invoice')
        {
          this.prog.addInfo([{
            label: 'Начислений ('+this.prog.ajaxData.length+')'
          }])
        }

        // mailing
        else if (this.prog.partname == 'mailing')
        {
          let info = {
                label: 'Абоненты получившие ел.квитанцию ('+this.prog.ajaxData.length+')',
                list: []
              }

          this.prog.ajaxData.clients.map(client =>
          {
            let label = client.label

            if (client.error)
              label += ' <b>'+client.error+'</b>'

            info.list.push({
              label: label
            })
          })

          this.prog.addInfo([info])
        }
      },

      props: function()
      {
        let props = {
          pp: {},
          parts: {}
        }

        props.pp = {
          label: 'Квартальные начисления'
        }

        // invalid_debt
        if (this.po.opts.advanced.invalid_debt)
        {
          props.parts.invalid_debt = {
            type: 'ajax',
            pp: {
              label: 'Отключение должников'
            },
            ajax: {
              data: {
                option: 'domofon',
                task: 'invalid_debt.invalid',
                branch: 'clients',
                baseid: this.prog.opts.popup.node.find('.BaseId').val()
              }
            }
          }
        }

        props.parts.invoice = {
          type: 'ajax',
          pp: {
            label: 'Квартальные начисления'
          },
          ajax: {
            data: {
              option: 'domofon',
              task: 'quarter.invoice',
              branch: 'invoice',
              baseid: this.prog.opts.popup.node.find('.BaseId').val()
            }
          }
        }

        // mailing
        if (this.po.opts.advanced.mailing)
        {
          let ismailing = this.po.fismailing.getCurrentValue()

          if (ismailing)
          {
            props.parts.mailing = {
              type: 'ajax',
              pp: {
                label: 'Рассылка квитанций'
              },
              ajax: {
                data: {
                  option: 'domofon',
                  task: 'mailing.send',
                  branch: 'clients'
                }
              }
            }
          }
        }

        return props
      }
    }
  })
})