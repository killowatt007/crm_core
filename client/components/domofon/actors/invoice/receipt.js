define(function(require) 
{
  /**
   * $version 1.1
   * $adv domofon.clients.for_receipt v1.1
   */

  let Actor = require('components/field/actor')

  require('components/builder/actors/table')
  require('components/builder/actors/popup')

  require('components/builder/actors/addons/general/html')
  require('components/builder/actors/addons/general/text')

  require('components/field/actors/list')

  require('components/system/actors/parts_progress')

  return new Class(
  {
    Extends: Actor,

    contractids: [],
    forReceipt: null,

    render: function()
    {
      let html = ''

      html +=
        '<button class="b b-s b-primary downloadInvoice">'+
          '<i class="fas fa-file-archive"></i>'+
          (!App.ismobile ? 'Скачать квитанции' : '')+
        '</button>'+
        '<button class="b b-s b-primary printInvoice">'+
          '<i class="far fa-file-invoice"></i>'+
          (!App.ismobile ? 'Печать квитанций' : '')+
        '</button>'

      return html
    },

    getForReceipt: function()
    {
      if (this.opts.for_receipt && !this.forReceipt)
          this.forReceipt = this.getActor(this.opts.for_receipt)

      return this.forReceipt
    },

    onAfterRender: function()
    {
      let self = this

      this.downloadInvoice()
      this.printInvoice()
    },

    downloadInvoice: function()
    {
      let self = this

      $('.downloadInvoice').click(function()
      {
        let popup = self.getActor({
              group: 'builder',
              name: 'popup',
              opts: {
                content: 
                  '<div class="fabrik form pi-modal" style="padding:20px;">'+
                    '<div class="data">'+
                      App.render(self.getform('download'))+
                    '</div>'+
                    '<div class="actions">'+
                      '<div class="left">'+
                        '<button class="b b-s bs-r b-success submit">Скачать</button>'+
                      '</div>'+
                      '<div class="right">'+
                        '<button class="b b-s bs-r b-primary gb">Закрыть</button>'+
                      '</div>'+
                    '</div>'+
                  '</div>',
                label: 'Скачать квитанции',
              }
            })

        popup.open()

        $('.actions .gb').click(()=>popup.close())
        self.submitDownload(popup)
      })
    },

    printInvoice: function()
    {
      let self = this

      $('.printInvoice').click(function()
      {
        self.contractids = []

        $('.fabrik.list').find('.selectItem').each(function()
        {
          if ($(this).is(':checked'))
          {
            let id = $(this).parents('tr:first').attr('rowid')
            self.contractids.push(id)
          }
        })

        if (self.contractids.length)
        {
          let popup = self.getActor({
                group: 'builder',
                name: 'popup',
                opts: {
                  content: 
                    '<div class="fabrik form pi-modal" style="padding:20px;">'+
                      '<div class="data">'+
                        App.render(self.getform())+
                      '</div>'+
                      '<div class="actions">'+
                        '<div class="left">'+
                          (self.opts.for_receipt ? self.getForReceipt().getAction() : '')+
                          '<button class="b b-s bs-r b-success submit">Сформировать</button>'+
                        '</div>'+
                        '<div class="right">'+
                          '<button class="b b-s bs-r b-primary gb">Закрыть</button>'+
                        '</div>'+
                      '</div>'+
                    '</div>',
                  label: 'Сформировать квитанцию',
                }
              })

          popup.open()

          $('.actions .gb').click(()=>popup.close())
          self.submit(popup)

          if (self.opts.for_receipt)
            self.getForReceipt().afterPopup(popup)
        }
        else
        {
          alert('Выберите договор')
        }
      })
    },

    getform: function(type)
    {
      let table,
          tableData = []

      this.ffate_from = this.getActor({
        group: 'field',
        name: 'date',
        value: this.opts.date_from,
        opts: {
          placeholder: 'С',
          name: 'date_from',
          isedit: true
        }
      })
      this.ffate_to = this.getActor({
        group: 'field',
        name: 'date',
        value: this.opts.date_to,
        opts: {
          placeholder: 'По',
          name: 'date_to',
          isedit: true
        }
      })

      tableData = [
        {
          type:'row', params:[],
          columns: [
            {
              type:'column', size:6, params:[], 
              data: [{
                type:'addon', group:'builder', branch:'addons.general', name:'text',
                opts: {text:'Дата', classes:'label'}
              }]
            },
            {
              type:'column', size:9, params:[], 
              data: [{
                type:'addon', group:'builder', branch:'addons.general', name:'html',
                opts: {html: App.render(this.ffate_from)},
              }]
            },
            {
              type:'column', size:9, params:[], 
              data: [{
                type:'addon', group:'builder', branch:'addons.general', name:'html',
                opts: {html: App.render(this.ffate_to)}
              }]
            }
          ]
        }
      ]

      // for download
      if (type == 'download') {
        this.district = this.po.getActor({
          group: 'field',
          name: 'list',
          opts: {
            options: this.opts.districts_options,
            name: 'district',
            isedit: true,
            isps: true,
            minimumResultsForSearch: -1
          }
        })

        tableData[0].columns.push({
          type:'column', size:6, params:[], 
          data: [{
            type:'addon', group:'builder', branch:'addons.general', name:'text',
            opts: {text:'Район', classes:'label'}
          }]
        })
        tableData[0].columns.push({
          type:'column', size:18, params:[], 
          data: [{
            type:'addon', group:'builder', branch:'addons.general', name:'html',
            opts: {html: App.render(this.district)}
          }]
        })
      }

      table = this.getActor({
        group: 'builder',
        name: 'table',
        opts:
        {
          tmpls: [
            {
              id: '48',
              childId: 0,
              parentId: 0,
              render: true,
              data: tableData
            }
          ],
          group: 'fabrikform'
        }
      })

      return table
    },

    submitDownload: function(popup)
    {
      let self = this

      popup.node.find('.submit').click(function()
      {
        let error,
            district = popup.node.find('.district').val()

        self.opts.date_from = self.ffate_from.getCurrentValue()
        self.opts.date_to = self.ffate_to.getCurrentValue()

        if (!self.opts.date_from || !self.opts.date_to)
          error = 'Укажите дату'
        else if (!district)
          error = 'Выберите район'

        if (!error) {
          self.subconst('create_arch', {
            district: district,
            date_from: self.opts.date_from,
            date_to: self.opts.date_to
          })
        }
        else {
          alert(error)
        }
      })
    },

    create_arch: 
    {
      pp: null,
      contractIds: null,
      contractIdIndex: null,
      size: 0,

      init: function() {
        const self = this

        this.getpp()

        this.ajax('getContracts', {}, (data) => {
          if (data.contractIds.length) {
            self.contractIds = data.contractIds
            self.contractIdIndex = 0

            self.size = 0
            self.pp.open()
            self.createInvoice() 
          }
          else {
            alert('В данном районе нет договоров')
          }
        })
      },

      ajax: function(flag, data, callback) {
        const self = this

        data = data ? data : {}

        this.po.ajax({
          data: Object.assign({
            task: 'receipt.createArch',
            branch: 'invoice',
            date_from: self.opts.date_from,
            date_to: self.opts.date_to,
            district: self.opts.district,
            flag: flag
          }, data),
          success: function(data)
          {
            if (flag != 'createInvoice') {
              if (callback) {
                callback(data)
              }
            }
            else {
              self.size += Number(data.size)
              
              if (self.contractIds.length != (self.contractIdIndex+1)) {
                self.contractIdIndex++
                self.createInvoice()
              }
              else {
                self.pp.addItem({label: 'Формирование архива'})
                self.ajax('createArch', {size: self.size}, (data) => {
                  let zip_name = data.zip_name,
                      path

                  path = 
                    '/bootstrap.php?option=domofon&task=receipt.createArch&branch=invoice&'+
                    'group=fabrik&type=entity&name=contracts&format=form&'+
                    'itemId=76'+
                    '&flag=download'+
                    '&zip_name='+zip_name

                  self.pp.close()
                  window.open(path);   
                })
              }
            }
          }
        })
      },
      
      createInvoice: function() {
        const self = this
        const contractId = self.contractIds[self.contractIdIndex]

        self.pp.setInfo('Договор '+(self.contractIdIndex+1)+' из '+self.contractIds.length)
        self.pp.addItem({label: 'Номер договора '+self.contractIds[self.contractIdIndex]})
        self.ajax('createInvoice', {contractId: contractId})
      },

      getpp: function()
      {
        if (!this.pp)
        {
          this.pp = this.po.getActor({
            group: 'builder',
            name: 'popup_progress',
            opts: {
              label: 'Создание архива с квитанциями'
            }
          })
        }

        return this.pp;
      }
    },






    submit: function(popup)
    {
      let self = this

      popup.node.find('.submit').click(function()
      {
        let path,
            clientids = '',
            contractids = ''

        if (self.opts.isfind)
        {
          $('.pi-clients-container input.clientid').each(function()
          {
            if ($(this).is(':checked'))
            {
              clientids += '&clientids[]='+$(this).val()
            }
          })
        }
        else
        {
          self.contractids.map(cid =>
          {
            contractids += '&contractids[]='+cid
          })
        }

        self.opts.date_from = self.ffate_from.getCurrentValue()
        self.opts.date_to = self.ffate_to.getCurrentValue()

        path = 
          '/bootstrap.php?option=domofon&task=receipt.printInvoice&branch=invoice&'+
          'group=fabrik&type=entity&name=contracts&format=form&'+
          'itemId=76'+
          '&date_from='+self.opts.date_from+
          '&date_to='+self.opts.date_to
        path += self.opts.isfind ? clientids : contractids

        self.print(path)
        popup.close()
      })
    },

    print: function(path)
    {
      let iframe = $('<iframe>')                          
            .hide()
            .attr('id', 'iframe_print')
            .attr('src', path)

      if ($('#iframe_print')[0])
        $('#iframe_print').remove()

      iframe
        .appendTo('body')
        // .on('load', function() {})

      iframe[0].contentWindow.focus()
      iframe[0].contentWindow.print()
    }
  })
})