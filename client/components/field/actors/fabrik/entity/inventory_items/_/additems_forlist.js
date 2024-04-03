define(function(require) 
{
  let View = require('view'),
      Popup = require('components/builder/actors/popup')

  require('components/fabrik/actors/list')
  require('components/builder/actors/table')

  require('components/field/actors/number')
  require('components/field/actors/list')

  require('components/builder/actors/addons/general/html')
  require('components/builder/actors/addons/general/text')

  return new Class(
  {
    Extends: View,

    popup: null,
    itemsgroup: [],

    keyg: null,
    keyi: null,
    ismount: null,

    addeditems: {
      m: {},
      d: {}
    },

    render: function()
    {
      let html = ''

      if (this.opts.requestInventory)
        this.itemsgroup =  this.opts.requestInventory

      html =
        '<div class="inventory-items" id="'+this.key+'">'+
          '<div class="ii-actions">'+
            '<button type="submit" class="b b-s bs-r b-success mount">'+
              '<i class="far fa-plus"></i>'+
              'Монтаж'+
            '</button>'+
            '<button type="submit" class="b b-s bs-r b-success demount">'+
              '<i class="far fa-plus"></i>'+
              'Демонтаж'+
            '</button>'+
          '</div>'+
          '<div class="items">'+
            '<div class="cnt mount"></div>'+
            '<div class="cnt demount"></div>'+
          '</div>'+
        '</div>'

      return html
    },

    addItems: function(q, ismount)
    {
      let itemkey = ismount ? 'm' : 'd',
          key = itemkey+'-'+this.keyg+'-'+this.keyi,
          selector = ismount ? '.mount' : '.demount',
          group = this.getCurrentGroupData()[this.keyg],
          item = group.items[this.keyi],
          container = this.node.find('.items > '+selector),
          itemnode = $('#'+key)

      if (!container.find('table')[0])
      {
        container.append(
          '<div class="lr">'+
            '<div class="lb">'+(ismount ? 'Монтаж' : 'Демонтаж')+'</div>'+
            '<table class="table">'+
              '<thead>'+
                '<tr>'+
                  '<th>#</th>'+
                  '<th>Позиция</th>'+
                  '<th class="q">Кол-во</th>'+
                  '<th class="action"></th>'+
                '</tr>'+
              '</thead>'+
              '<tbody>'+
              '</tbody>'+
            '</table>'+
          '</div>'
        )
      }

      if (itemnode[0])
      {
        let qnode = itemnode.find('.q')

        qnode.text(parseInt(qnode.text()) + q)
      }
      else
      {
        let itemlabel = group.name+' '+item.name,
            i = 1

        container.find('tbody').append(
          '<tr id="'+key+'" key-g="'+this.keyg+'" key-i="'+this.keyi+'">'+
            '<td class="n"></td>'+
            '<td>'+itemlabel+'</td>'+
            '<td class="q">'+q+'</td>'+
            '<td class="action"><i class="far fa-trash-alt trash-item clr-d"></i></td>'+
          '</tr>'
        )

        this.node.find('.items '+selector+' tbody tr .n').each(function()
        {
          $(this).text(i++)
        })
      }

      if (ismount)
        this.getCurrentGroupData()[this.keyg].items[this.keyi].q -= q

      if (!this.addeditems[itemkey][item.id])
        this.addeditems[itemkey][item.id] = q
      else
        this.addeditems[itemkey][item.id] += q
    },

    onAfterRender: function()
    {
      this.mountBtns()
      this.remove()
    },

    remove: function()
    {
      let self = this

      this.node.on('click', '.trash-item', function()
      {
        let tr = $(this).parents('tr:first'),
            cnt = $(this).parents('.cnt:first'),
            lr = cnt.find('.lr'),
            typegroup = cnt.hasClass('mount') ? 'mount' : 'demount',
            itemkey = typegroup == 'mount' ? 'm' : 'd',
            keyg = tr.attr('key-g'),
            keyi = tr.attr('key-i'),
            item = self.itemsgroup[typegroup][keyg].items[keyi]
              
        delete self.addeditems[itemkey][item.id]   

        if (typegroup == 'mount')
        {
          let q = parseInt(tr.find('.q').text())
          item.q += q
        }

        tr.remove()

        if (!lr.find('tbody tr')[0])
          lr.remove()
      })
    },

    mountBtns: function()
    {
      let self = this

      this.node.find('.ii-actions button').click(function()
      {
        self.ismount = $(this).hasClass('mount')

        if (!self.popup)
        {
          self.ajax({
            data: {
              method: 'test',
              staffid: self.po.obj.opts.rows[0].AssignedToStaffId
            },
            success: function(data)
            {
              self.itemsgroup = data.itemsgroup
              self.initPopup()
            }
          })
        }
        else
        {
          self.popup.open()
        }
      })
    },

    initPopup: function()
    {
      let self = this,
          content

      content =
        '<div class="inventory-items-madal-add">'+
          App.render(this.getForm())+
          '<button type="submit" class="b b-s bs-r b-success add">Добавить</button>'+
        '</div>'

      this.popup = this.getActor({
        group: 'builder',
        name: 'popup',
        opts: {
          label: (this.ismount ? 'Монтаж' : 'Демонтаж'),
          width: 'litle',
          labelAlign: 'center',
          content: content,
          hide: true,
          beforeOpen: function()
          {
            let label = self.ismount ? 'Монтаж' : 'Демонтаж'

            if (self.popup.node)
            {
              self.co.items.opts.options = self.getItemOptions()
              self.co.items.updateRender()
              self.popup.node.find('.header .title').html(label)

              $('.forminput.Quantity').val(0)
            }
          }
        }
      })
      this.popup.open()

      $('.inventory-items-madal-add .CatalogItemId').change(function()
      {
        let opt = $(this).find('option:selected')

        self.keyg = opt.attr('key-g')
        self.keyi = opt.attr('key-i')

        if (self.ismount)
        {
          let quantity = $('.forminput.Quantity'),
              current = parseInt(quantity.val()),
              item = self.itemsgroup.mount[self.keyg].items[self.keyi]

          if (current > item.q)
            quantity.val(item.q)
        }
      })

      $('.inventory-items-madal-add button.add').click(function()
      {
        let q = parseInt($('.inventory-items-madal-add .Quantity').val())

        self.addItems(q, this.ismount)
        self.popup.close()
      })
    },

    getCurrentGroupData: function()
    {
      let key = this.ismount ? 'mount' : 'demount'
      return this.itemsgroup[key]
    },

    getItemOptions: function()
    {
      let options = []

      this.getCurrentGroupData().map((group, g) => 
      {
        let data = {
              label: group.name,
              options: []
            }

        group.items.map((item, i) => 
        {
          data.options.push({
            value: item.id,
            label: item.name+(item.q!==null ? ' ('+item.q+')' : ''),
            attrs: 'key-g="'+g+'" key-i="'+i+'"' + (item.q ? ' q="'+item.q+'"' : '')
          })
        })

        options.push(data)
      })

      return options
    },

    getForm: function()
    {
      let self = this,
          table

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
                            text: 'Позиция',
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
                            html: App.render(this.getActor({
                              alias: 'items',
                              group: 'field',
                              name: 'list',
                              opts: {
                                options: self.getItemOptions(),
                                name: 'CatalogItemId',
                                isedit: true,
                                isps: true,
                                minimumResultsForSearch: 1
                              }
                            }))
                          },
                        },
                      ],
                    }
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
                            text: 'Кол-во',
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
                            html: App.render(this.getActor({
                              group: 'field',
                              name: 'number',
                              value: 0,
                              opts: {
                                name: 'Quantity',
                                isedit: true,
                                onBeforeSetVal: function(args)
                                {
                                  if (self.ismount)
                                  {
                                    let item = self.itemsgroup.mount[self.keyg].items[self.keyi]

                                    if (args.newval > item.q)
                                      args.newval = item.q

                                    if (args.newval < 0)
                                      args.newval = 0
                                  }
                                }
                              }
                            }))
                          },
                        },
                      ],
                    }
                  ],
                }
              ],
            },
          ],
          group: 'fabrikform'
        }
      })

      return table
    }
  })
})