define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  require('components/builder/actors/popup')
  require('components/field/actors/fabrik/entity/inventory_items/additems_forlist')

  return new Class(
  {
    Extends: Plugin,

    builder: null,

    onBeforeRender: function()
    {
      // for builder table
      this.obj.opts.tmpl.opts.nomobile = true
    },

    onBeforeSubmit: function(args)
    {
    },

    onElementGetValue: function()
    {
      let obj = this.obj, sobj = this.sobj

      // Mobile
      if (sobj.opts.name == 'Mobile')
      {
        if (App.ismobile)
        {
          let mobile = obj.opts.rows[0].Mobile

          if (mobile && !sobj.opts.isedit)
            sobj.value = '<a href="tel:'+mobile+'">'+mobile+'</a>'
        }
      }
    },

    onAfterObjsRender: function(data)
    {
      let s_pending = 15,
          s_ready = 17

      if (this.obj.opts.status == s_pending || this.obj.opts.status == s_ready)
        this.equipmentObj = this.subconst('equipment')

      this.clientInfo()
    },

    onAfterFormData: function(data)
    {
      if (this.equipmentObj)
        data.equipment = this.equipmentObj.getFormData()
    },

    equipment: 
    {
      popap: null,
      currentType: null,
      node: null,

      s_pending: 15,
      s_ready: 17,
      r_master: 4,

      init: function()
      {
        let classes = App.ismobile ? 'mobile' : ''

        this.po.obj.node.find('.equipment').append(
          '<div class="eq_data '+classes+'">'+
            '<div class="acts"></div>'+
            '<div class="data"></div>'+
          '</div>'
        )
        this.node = this.po.obj.node.find('.equipment .eq_data')

        if (this.po.obj.opts.status == this.s_pending && this.po.obj.opts.role == this.r_master)
        {
          this.render_equipment()
          this.actions()
          this.counter()
          this.remove()
        }

        if (this.po.obj.opts.role == this.r_master || this.po.obj.opts.status == this.s_ready)
          this.render_data()
      },

      getFormData: function()
      {
        let items = []

        this.po.obj.node.find('.equipment .fabrik.list').each(function()
        {
          let isdemount = $(this).hasClass('demount') ? 1 : 0

          $(this).find('.data-row').each(function()
          {
            let itemid = $(this).attr('itemid'),
                quantity = $(this).find('.quantity').text()
  
            let item = {
                  ItemId: itemid,
                  IsDemount: isdemount,
                  Quantity: quantity
                }

            items.push(item)
          })
        })

        return items
      },

      counter: function(popup)
      {
        if (popup)
        {
          popup.node.find('.counter button').click(function()
          {
            let parent = $(this).parents('.counter'),
                type = $(this).attr('d-type'),
                input = parent.find('input'),
                value = parseInt(input.val()),
                newValue

            if (type == 'plus')
              newValue = value + 1
            else
              newValue = value - 1

            if (newValue < 0)
              newValue = 0

            input.val(newValue)
          })

          popup.node.find('.counter input').keyup(function(e)
          {
            let value = parseInt($.trim($(this).val()))

            if (!value)
              value = 0

            $(this).val(value)
          })
        }
      },

      counterHover: function(popup)
      {
        popup.node.find('.counter').hover(function()
        {
          let itemNode = $(this).parents('.item:first')
          itemNode.toggleClass('hover')
        })
      },

      add: function(popup)
      {
        let self = this

        popup.node.find('button.add').click(function()
        {
          popup.node.find('.item').each(function()
          {
            let input = $(this).find('.counter input'),
                quantity = parseInt(input.val())
  
            if (quantity)
            {
              let type = self.currentType,
                  container = self.po.obj.node.find('.eq_data .data .fabrik.list.'+type+' .items'),
                  itemid = $(this).attr('itemid'),
                  itemNode = container.find('.id'+itemid)

              if (itemNode[0])
              {
                let nodeQuantity = itemNode.find('td.quantity')
                    oldQuantity = parseInt(nodeQuantity.text()),
                    newQuantity = oldQuantity + quantity

                nodeQuantity.text(newQuantity)
              }
              else
              {
                let item = self.getItemById(itemid)

                item.Quantity = quantity
                self.renderItem(type, item)
              }
            }
          })

          popup.close()
        })

        popup.node.find('button.cls').click(function() { popup.close() })
      },

      categoryShow: function(popup)
      {
        popup.node.find('.category > .lbl a').click(function(e)
        {
          e.preventDefault()

          let parent = $(this).parents('.category:first'),
              items = parent.find('>.items')

          items.toggleClass('show')
        })
      },

      remove: function()
      {
        this.po.obj.node.on('click', '.fabrik.list .data .remove', function()
        {
          let row = $(this).parents('.data-row')
          row.remove()
        })
      },

      getItemById: function(id)
      {
        let find = null

        this.po.obj.opts.equipmentGroupItems.map(group => 
        {
          group.items.map(item => 
          {
            if (item.id == id)
              find = item
          })
        })

        return find
      },

      actions: function()
      {
        let self = this

        this.po.obj.node.on('click', '.eq_data .acts button', function()
        {
          self.currentType = $(this).attr('d-type')
          self.getPopup().open()
        })
      },

      getTypeLabel: function(type)
      {
        return type == 'mount' ? 'Монтаж' : 'Демонтаж'
      },

      reN: function()
      {
        this.po.obj.node.find('.eq_data .fabrik.list').each(function()
        {
          $(this).find('.data-row').each(function()
          {
            $(this).find('.n').text($(this).index()+1)
          })
        })
      },

      render_data: function()
      {
        this.node.find('.data').html(
          this.renderTable('mount')+
          this.renderTable('demount')
        )

        if (this.po.obj.opts.status == this.s_ready)
        {
          this.po.obj.opts.requestEquipment.map(item => 
          {
            let type = item.IsDemount == '1' ? 'demount' : 'mount'

            this.renderItem(type, {
                  id: 1,
                  Name: item.CategoryName+' - '+item.Name,
                  Quantity: item.Quantity
                })
          })
        }
      },

      render_equipment: function()
      {
        let html =
              '<button class="b b-s bs-r b-primary mount" d-type="mount">'+
                '<i class="fas fa-plus-circle"></i>'+
                'Монтаж'+
              '</button>'+
              '<button class="b b-s bs-r b-primary demount" d-type="demount">'+
                '<i class="fas fa-plus-circle"></i>'+
                'Демонтаж'+
              '</button>'

        this.node.find('.acts').html(html)
      },

      renderTable: function(type)
      {
        let html = ''
        html =
          '<div class="fabrik list simple '+type+'">'+
            '<h4 class="lab">'+this.getTypeLabel(type)+'</h4>'+
            '<div class="data">'+
              '<table>'+
                '<thead>'+
                  '<tr>'+
                    '<th class="n">#</th>'+
                    '<th class="name">Позиция</th>'+
                    '<th class="q">Кол-во</th>'+
                    '<th class="a"></th>'+
                  '</tr>'+
                '</thead>'+
                '<tbody class="items">'+
                '</tbody>'+
              '</table>'+
            '</div>'+
          '</div>'

        return html
      },

      renderItem: function(type, data)
      {
        let html = '',
            container = this.po.obj.node.find('.eq_data .data .fabrik.list.'+type+' .items'),
            index = this.po.obj.node.find('.eq_data .fabrik.list .'+type+' .data-row').length

        html =
          '<tr class="rw data-row odd id'+data.id+'" itemid="'+data.id+'">'+
            '<td class="n">'+(index+1)+'</td>'+
            '<td class="name">'+data.Name+'</td>'+
            '<td class="quantity">'+data.Quantity+'</td>'+
            '<td>'

        if (this.po.obj.opts.status == this.s_pending)
        {
          html +=
            '<i class="far fa-trash remove"></i>'
        }

        html +=
            '</td>'+
          '</tr>'

        container.append(html)
      },

      renderEq: function()
      {
        let html = '',
            classes = App.ismobile ? 'mobile' : ''

        html =
          '<div class="eq_popup '+classes+'">'+
            '<div class="group">'+
              this.po.obj.opts.equipmentGroupItems.map(group => 
              {
                let html = '',
                    mleft = 0,
                    itemClasses = !group.items.length ? 'empty' : ''

                if (group.lvl != 1)
                  mleft = 20*(group.lvl-1)

                html =
                  '<div class="category" style="margin-left:'+mleft+'px">'+
                    '<div class="lbl">'+
                      '<a href="#">'+group.Name+
                        '<i class="fal fa-chevron-down arrow"></i>'+
                      '</a>'+
                    '</div>'+
                    '<div class="items '+itemClasses+'">'+
                      group.items.map(item => 
                      {
                        let html = ''

                        html =
                          '<div class="item" itemid="'+item.id+'">'+
                            '<div class="lbl">'+
                              item.Name+
                            '</div>'+
                            '<div class="counter">'+
                              '<button type="button" class="b b-s bs-f nl b-primary minus" d-type="minus"><i class="far fa-minus"></i></button>'+
                              '<input type="text" class="form-control" value="0">'+
                              '<button type="button" class="b b-s bs-f nl b-primary plus" d-type="plus"><i class="far fa-plus"></i></button>'+
                            '</div>'+
                          '</div>'

                        return html
                      }).join('')+
                    '</div>'+
                  '</div>'

                  return html
              }).join('')+
            '</div>'+
            '<div class="footer">'+
              '<button type="button" class="b b-s bs-r b-primary add">Добавить</button>'+
              '<button type="button" class="b b-s bs-r b-default cls">Закрыть</button>'+
            '</div>'+
          '</div>'

        return html
      },

      getPopup: function()
      {
        let self = this
  
        if (!this.popap)
        {
          this.popap = self.po.getActor({
            group: 'builder',
            name: 'popup',
            opts: {
              contStyle: !App.ismobile ? 'width:calc(100% - 200px);margin:50px auto' : '',
              content: self.renderEq(),
              afterRender: (popup) => {
                self.counter(popup)
                self.counterHover(popup)
                self.add(popup)
                self.categoryShow(popup)
              }
            }
          })
        }

        this.popap.opts.label = this.getTypeLabel(this.currentType)

        return this.popap
      }
    },

    clientInfo: function()
    {
      let obj = this.obj,
          self = this

      obj.node.find('.forminput.ClientId').change(function()
      {
        let clientid = $(this).val()

        if (clientid)
        {
          self.ajax({
            method: 'getClientInfo',
            data: {
              clientid: $(this).val()
            },
            success: function(data)
            {
              data.map(item => 
              {
                let name = item.name,
                    value = item.data

                if (name == 'ClientAddress')
                {
                  value = 
                    '<a href="#" class="show-address" style="color:#237888;cursor:pointer;">'+value+'</a>'+
                    '<div id="map"></div>'
                }

                obj.node.find('.'+name).html(value)
              })
            }
          })
        }
      })

      // this.map()
    }

    // map: function()
    // {
    //   let obj = this.obj,
    //       self = this

    //   obj.node.on('click', '.show-address', function(e)
    //   {
    //     e.preventDefault()

    //     let address = $(this).text()

    //     function init() 
    //     {
    //           // Строка с адресом, который необходимо геокодировать
    //       let // address = ''.$info['District'].','.$info['Street'].','.$info['HouseNumber'].'',
    //           // Ищем координаты указанного адреса
    //           // https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/geocode-docpage/
    //           geocoder = ymaps.geocode(address)

    //       // После того, как поиск вернул результат, вызывается callback-функция
    //       geocoder.then(
    //         function (res) 
    //         {
    //             let myMap,
    //                 placemark,
    //                 // координаты объекта
    //                 coordinates = res.geoObjects.get(0).geometry.getCoordinates()

    //           // Добавление метки (Placemark) на карту
    //           placemark = new ymaps.Placemark(
    //             coordinates, {
    //               // 'hintContent': address,
    //               'balloonContent': address
    //             }, {
    //               'preset': 'islands#redDotIcon'
    //             }
    //           )

    //           // Создание карты.
    //           $('#map').css({width:'100%', height:'400px'})
    //           myMap = new ymaps.Map('map', {
    //             center: coordinates,
    //             zoom: 12,
    //           })

    //           myMap.geoObjects.add(placemark)
    //         }
    //       )
    //     }
    //     ymaps.ready(init)
    //   })
    // }
  })
})