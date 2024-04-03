define(function(require) 
{
  /**
   * $version 1.1
   */

  let Actor = require('components/field/actor')

  require('components/field/actors/yesno')
  require('components/field/actors/date')

  return new Class(
  {
    Extends: Actor,

    init: function()
    {
      this.fdebt_date = this.getActor({
        group: 'field',
        name: 'date',
        value: this.opts.debt_date,
        opts: {
          name: 'debt_date',
          isedit: true
        }
      })
      this.fisplus_balance = this.getActor({
        group: 'field',
        name: 'yesno',
        value: '',
        opts: {
          name: 'isplus_balance',
          isedit: true
        }
      })
    },

    getTFields: function()
    {
      let rows = []

      rows = [
        {
          type:'row', params:[],
          columns: [
            {
              type:'column', params:[], size:6, 
              data: [{
                type:'addon', group:'builder', branch:'addons.general', name:'text',
                opts: {text:'Нет проплаты с', classes:'label'}
              }]
            },
            {
              type:'column', params:[], size:18, 
              data: [{
                type: 'addon', group:'builder', branch:'addons.general', name:'html',
                opts: {html: App.render(this.fdebt_date)}
              }]
            }
          ]
        },
        {
          type:'row', params:[],
          columns: [
            {
              type:'column', params:[], size:6, 
              data: [{
                type:'addon', group:'builder', branch:'addons.general', name:'text',
                opts: {text:'Положительный баланс', classes:'label'}
              }]
            },
            {
              type:'column', params:[], size:18, 
              data: [{
                type: 'addon', group:'builder', branch:'addons.general', name:'html',
                opts: {html: App.render(this.fisplus_balance)}
              }]
            }
          ]
        },
        {
          type:'row', params:[],
          columns: [
            {
              type:'column', params:[], size:24, 
              data: [{
                type:'addon', group:'builder', branch:'addons.general', name:'text',
                opts: {text:'', classes:'pi-clients-container'}
              }]
            }
          ]
        }
      ]

      return rows
    },

    getAction: function()
    {
      return '<button class="b b-s bs-r b-primary find">Поиск</button>'
    },

    afterPopup: function(popup)
    {
      let self = this

      popup.node.find('.find').click(function()
      {
        self.opts.debt_date = self.fdebt_date.getCurrentValue()

        self.ajax({
          data: {
            task: 'for_receipt.find',
            branch: 'clients',
            contractids: self.po.contractids,
            isplus_balance: self.fisplus_balance.getCurrentValue(),
            debt_date: self.opts.debt_date,
            date_from: self.po.ffate_from.getCurrentValue(),
            date_to: self.po.ffate_to.getCurrentValue()
          },
          success: function(data)
          {
            let html = ''

            html +=
              '<div class="pi-layer">'+
                '<table>'+
                  '<thead>'+
                    '<tr>'+
                      '<td class="check"></td>'+
                      '<td>ФИО</td>'+
                      '<td>Счет</td>'+
                      '<td>Баланс</td>'+
                      '<td>Последняя дата оплаты</td>'+
                    '<tr>'+
                  '</thead>'+
                  data.clientsData.map(clients =>
                  {
                    let html =
                          '<tbody>'+
                            '<tr>'+
                              '<td class="pi-title" colspan="5"><div class="l">#'+
                                clients.contractid+
                                '<div class="address">'+clients.address+'</div>'+
                              '</td>'+
                            '</tr>'+
                          '</tbody>'+
                          '<tbody>'+
                            clients.valid.map(client =>
                            {
                              let html = ''

                              html =
                                '<tr>'+
                                  '<td><input class="clientid" type="checkbox" value="'+client.id+'" checked></td>'+
                                  '<td>'+client.FIO+'</td>'+
                                  '<td>'+client.id+'</td>'+
                                  '<td>'+client.balance+'</td>'+
                                  '<td>'+client.last_pay_date+'</td>'+
                                '</tr>'

                              return html
                            }).join('')+
                          '</tbody>'+
                          '<tbody>'+
                            '<tr>'+
                              '<td class="pi-title2" colspan="5"></td>'+
                            '</tr>'+
                          '</tbody>'+
                          '<tbody cla>'+
                            clients.notvalid.map(client =>
                            {
                              let html = ''

                              html =
                                '<tr>'+
                                  '<td><input class="clientid" type="checkbox" value="'+client.id+'"></td>'+
                                  '<td>'+client.FIO+'</td>'+
                                  '<td>'+client.id+'</td>'+
                                  '<td>'+client.balance+'</td>'+
                                  '<td>'+client.last_pay_date+'</td>'+
                                '</tr>'

                              return html
                            }).join('')+
                          '</tbody>'

                    return html
                  }).join('')+
                '</table>'+
              '</div>'

            $('.pi-clients-container').html(html)
            $('.pi-modal .submit').show(0)
          }
        })
      })
    }
  })
})