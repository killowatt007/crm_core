define(function(require) 
{
  /**
   * $version 1.1
   * $deps c:components/builder/actors/popup v1.1
   * $style self 1.1
   *        core 1.1
   */

  let Popup = require('components/builder/actors/popup')

  return new Class(
  {
    Extends: Popup,

    opts: {
      label: null,
      width: 'litle',
      btnClose: false,
      labelAlign: 'center',
      label_info: ''
    },

    items: [],
    status: 'draft',

    counter: 0,

    initialize: function(data)
    {
      data.opts.content = 
        '<div class="progress-popup">'+
          '<div class="data">'+
            '<div class="info">'+get(data.opts, '', 'label_info')+'</div>'+
            '<div class="scrol">'+
              '<div class="history"></div>'+
              '<div class="end"></div>'+
            '</div>'+
            '<div class="actions">'+
              '<button class="b b-s bs-r b-primary cclose dis action">Закрыть</button>'+
            '</div>'+
          '</div>'+
        '</div>'

      this.parent(data)
    },

    open: function()
    {
      let self = this

      this.parent()
      this.node.find('.actions .action.cclose').click(function()
      {
        if (self.status == 'end')
          self.close()
      })
    },

    setInfo: function(data)
    {
      this.node.find('.info').html(data)
    },

    addItem: function(item)
    {
      if (this.status == 'draft')
        this.status = 'process'

      this.clearLoadingItem()

      this.node.find('.history').append(
        '<div class="item loading">'+
          '<span class="text">'+item.label+'</span>'+
          '<i class="far fa-spinner-third spin status"></i>'+
        '</div>'
      )

      $('.progress-popup .scrol').scrollTop($('.progress-popup .scrol')[0].scrollHeight);
      this.items.push(item)
      this.counter++
    },

    clearLoadingItem: function()
    {
      let loadingItem = this.node.find('.history .item.loading')

      if (loadingItem[0])
      {
        loadingItem.removeClass('loading')
        loadingItem.find('.spin.status').remove()
        loadingItem.append('<i class="far fa-check ok status"></i>')
      }
    },

    end: function(items)
    {
      this.clearLoadingItem()
      this.addEnd(items)

      if (this.status == 'process')
        this.status = 'end'

      this.acticeActions()
    },

    acticeActions: function()
    {
      this.node.find('.actions .action').removeClass('dis')
    },

    addEnd: function(items)
    {
      let html = ''

      html +=
        '<div class="l">'+
          '<div class="endtop">'+
            'Загрузка завершена'+
          '</div>'

      if (items && items.length)
      {
        html +=
          '<div class="enditems">'

        items.map(item =>
        {
          html +=
            '<div class="enditem">'+
              item.label

          if (item.list)
          {
            html +=
              '<div class="subitems">'

            item.list.map(li =>
            {
              html +=
                '<div class="sub">'+
                  '- '+li.label+
                '</div>'
            })

            html +=
              '</div>'
          }

          html +=
            '</div>'
        })

        html +=
          '</div>'
      }

      html +=
        '</div>'

      this.node.find('.end').append(html)
      $('.progress-popup .scrol').scrollTop( (this.counter*21)-50)
    }
  })
})
