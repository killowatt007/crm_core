define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  require('components/builder/actors/table')
  require('components/builder/actors/popup')
  require('components/builder/actors/popup_progress')

  require('components/builder/actors/addons/general/html')
  require('components/builder/actors/addons/general/text')

  require('components/field/actors/date')
  require('components/field/actors/list')
  require('components/field/actors/file')

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
            '<button class="b b-s b-primary loadRegistry">'+
              '<i class="far fa-file-upload"></i>'+
              (!App.ismobile ? 'Загрузить реестр платежей' : '')+
            '</button>'+
            '<button class="b b-s b-primary downloadRegistry">'+
              '<i class="far fa-download"></i>'+
              (!App.ismobile ? 'Скачать реестр начислений' : '')+
            '</button>'+
          '<div>'
      }
    },

    onAfterObjsRender: function(resData, reqData)
    {
      if (reqData.isWindow)
      {
        this.subconst('loadPayments')
        this.downloadRegistry()
      }
    },

    downloadRegistry: function() {
      $('.downloadRegistry').click(function() {
        path = 
          '/bootstrap.php?option=domofon&task=registry.downloadForRnkb&branch=clients&'+
          'itemId=76'

        window.open(path);   
      })
    },

    loadPayments: 
    {
      popupinputs: {},

      init: function()
      {
        let self = this

        $('.loadRegistry').click(function()
        {
          let popup = self.po.getActor({
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
                          '<button class="b b-s bs-r b-success submit">Загрузить</button>'+
                        '</div>'+
                        '<div class="right">'+
                          '<button class="b b-s bs-r b-primary gb">Закрыть</button>'+
                        '</div>'+
                      '</div>'+
                    '</div>',
                  label: 'Загрузка реестра платежей',
                }
              })


          popup.open()
          $('.actions .gb').click(()=>popup.close())

          popup.node.find('.submit').click(function() 
          { 
            self.process({
              storeDataLabel: 'Чтение файла',
              paymentType: 'registry',
              ajaxData: {
                storeData: {
                  formatid: popup.node.find('.formatid').val(),
                  datepay: self.popupinputs.datepay.getCurrentValue(),
                  file: self.popupinputs.file.formData
                }
              },
              onStop: function()
              {
                popup.close()
              }
            })
          })
        })

        if ($('.loadRegistryAcq')[0])
        {
          $('.loadRegistryAcq').click(function()
          {
            self.process({
              storeDataLabel: 'Загрузка платежей',
              paymentType: 'acquiring',
              ajaxData: {
                storeData: {
                }
              }
            })
          })
        }

        $('.buttons .resend').click(function()
        {
          self.process({
            storeDataLabel: 'Поиск чеков',
            paymentType: 'resend',
            ajaxData: {
              storeData: {
              }
            }
          })
        })
      },

      process: function(opts)
      {
        let self = this,
            flag = 'storeData',
            checks_i = 0,
            resdata,
            error = false

        self.step.start(
        {
          po: self,
          ajax_data: {
            group: 'fabrik',
            type: 'entity',
            name: 'checks',
            format: 'form',
            method: 'loadPayments',
            sendmethod: 'POST'
          },
          logic: function(object)
          {
            // error
            if (error)
            {
              object.stop()
              self.step.getpp().close()
              alert(error)
            }

            // storeData
            else if (flag == 'storeData')
            {
              self.step.getpp().open()
              self.step.getpp().addItem({label: opts.storeDataLabel})

              object.ajax($.extend(opts.ajaxData.storeData, {
                flag: flag,
                paymentType: opts.paymentType
              }), 
              (data) => {
                if (data.error)
                  error = data.error

                if (!error)
                {
                  resdata = data
                  self.step.getpp().setInfo('Отправленно чеков 0 из '+resdata.checks.length)
                  flag = 'kkt'
                }
              })
            }

            // kkt
            else if (flag == 'kkt')
            {
              function stop(msg)
              {
                let filter = App.modules[39]

                if (!msg)
                  msg = 'Загрузка реестра платежей завершена'

                object.stop()
                self.step.getpp().close()

                if (opts.onStop)
                  opts.onStop()
                
                filter.apply(filter.getField('from'))

                setTimeout(() => alert(msg), 100)
              }

              function msgAlert(alert)
              {
                let msg = ''

                if (alert.length)
                  alert.map(row => msg += row+"\n")

                return msg
              }

              if (resdata.checks.length)
              {
                let check = resdata.checks[checks_i]

                self.step.getpp().addItem({label: 'Ожидания платежа #'+check.clientid_l})
                object.ajax({
                  checkid: check.checkid,
                  flag: flag,
                  paymentType: opts.paymentType
                }, 
                (data) => {
                  if (resdata.checks.length == (checks_i+1))
                  {
                    stop(msgAlert(data.alert))
                  }
                  else
                  {
                    checks_i++
                    self.step.getpp().setInfo('Отправленно чеков '+checks_i+' из '+resdata.checks.length)
                  }
                })
              }
              else
              {
                stop(msgAlert(resdata.alert))
              }
            }
          }
        })
      },

      getform: function()
      {
        let table

        this.popupinputs.formatid = this.po.getActor({
          group: 'field',
          name: 'list',
          // value: this.opts.display,
          opts: {
            options: this.po.obj.opts.formatOpts,
            name: 'formatid',
            isedit: true,
            isps: true,
            minimumResultsForSearch: -1
          }
        })

        this.popupinputs.file = this.po.getActor({
          group: 'field',
          name: 'file',
          // value: this.opts.display,
          opts: {
            name: 'file',
            isedit: true
          }
        })

        this.popupinputs.datepay = this.po.getActor({
          group: 'field',
          name: 'date',
          // value: this.opts.display,
          opts: {
            name: 'datepay',
            isedit: true
          } 
        })

        table = this.po.getActor({
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
                data: [
                  {
                    type: 'row',
                    params: [],
                    columns: [
                      {
                        type: 'column', 
                        size: 6, 
                        params: [], 
                        data: [
                          {
                            type: 'addon',
                            group: 'builder',
                            branch: 'addons.general',
                            name: 'text',
                            opts: {
                              text: 'Формат реестра',
                              classes: 'label'
                            },
                          },
                        ],
                      },
                      {
                        type: 'column', 
                        size: 18, 
                        params: [], 
                        data: [
                          {
                            type: 'addon',
                            group: 'builder',
                            branch: 'addons.general',
                            name: 'html',
                            opts: {
                              html: App.render(this.popupinputs.formatid)
                            },
                          },
                        ],
                      },
                    ],
                  },
                  {
                    type: 'row',
                    params: [],
                    columns: [
                      {
                        type: 'column', 
                        size: 6, 
                        params: [], 
                        data: [
                          {
                            type: 'addon',
                            group: 'builder',
                            branch: 'addons.general',
                            name: 'text',
                            opts: {
                              text: 'Файл',
                              classes: 'label'
                            },
                          },
                        ],
                      },
                      {
                        type: 'column', 
                        size: 18, 
                        params: [], 
                        data: [
                          {
                            type: 'addon',
                            group: 'builder',
                            branch: 'addons.general',
                            name: 'html',
                            opts: {
                              html: App.render(this.popupinputs.file)
                            },
                          },
                        ],
                      },
                    ],
                  },
                  {
                    type: 'row',
                    params: [],
                    columns: [
                      {
                        type: 'column', 
                        size: 6, 
                        params: [], 
                        data: [
                          {
                            type: 'addon',
                            group: 'builder',
                            branch: 'addons.general',
                            name: 'text',
                            opts: {
                              text: 'Дата оплаты',
                              classes: 'label'
                            },
                          },
                        ],
                      },
                      {
                        type: 'column', 
                        size: 18, 
                        params: [], 
                        data: [
                          {
                            type: 'addon',
                            group: 'builder',
                            branch: 'addons.general',
                            name: 'html',
                            opts: {
                              html: App.render(this.popupinputs.datepay)
                            },
                          },
                        ],
                      },
                    ],
                  },
                ],
              },
            ],
            group: 'fabrikform'
          }
        })

        return table
      },

      step: 
      {
        options: {
          ajax_data: null,
          logic: null,
          po: null
        },

        iswork: false,
        interval: null,
        pp: null,

        ajax: function(reqdata, callback)
        {
          let self = this

          this.iswork = true

          this.options.po.po.ajax($.extend({
            data: reqdata,
            success: function(data)
            {
              if (callback)
                callback(data)

              self.iswork = false
            }
          }, this.options.ajax_data))
        },

        start: function(options)
        {
          this.options = options

          this.interval = setInterval(() => 
          {
            if (!this.iswork)
            {
              this.options.logic(this)
            }
          }, 100)
        },

        stop: function()
        {
          clearInterval(this.interval)
        },

        getpp: function()
        {
          if (!this.pp)
          {
            this.pp = this.options.po.po.getActor({
              group: 'builder',
              name: 'popup_progress',
              opts: {
                label: 'Отправка фискальных чеков',
                label_count: 'Отправленно чеков'
              }
            })
          }

          return this.pp;
        }
      }
    }
  })
})