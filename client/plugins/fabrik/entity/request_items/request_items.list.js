define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin'),
      Builders = {
        table: require('components/builder/back/table'),
        page: require('components/builder/back/page')
      }

  return new Class(
  {
    Extends: Plugin,

    onRenderBody: function(args)
    {
      if (App.ismobile)
      {
        let obj = this.obj,
            odd = true

        args.html +=
          '<div class="mlist">'+
            '<div class="actions">'+obj.renderButtons()+'</div>'+
            '<div class="data">'
        if (obj.opts.rows.length)
        {
          obj.opts.fieldsgroup.map(function(fields, i)
          {
            let classname = odd ? 'odd' : ''
            let row = obj.opts.rows[i]
            let isTypeIsset = row.TypeId_join

            args.html += 
              '<div class="item data-row '+classname+'" key="'+i+'" rowid="'+obj.opts.rows[i].id+'" rowalias="main">'+
                '<div>'+App.render(obj.getElement(i, 'Number'))+'</div>'+
                '<div class="description">'+
                  (isTypeIsset ? App.render(obj.getElement(i, 'TypeId'))+'<br>' : '')+
                  App.render(obj.getElement(i, 'Description'))+
                '</div>'+
                '<div><i class="far fa-map-marker-alt"></i> <span class="addrval">'+App.render(obj.getElement(i, 'ClientAddress'))+'</span></div>'+
                '<div><span class="lab">Статус:</span> '+App.render(obj.getElement(i, 'StatusId'))+'</div>'+
                '<div><span class="lab">Приоритет:</span> '+App.render(obj.getElement(i, 'PriorityId'))+'</div>'+
                '<div>'+App.render(obj.getElement(i, 'DateCreate'))+'</div>'+
              '</div>'

            odd = !odd
          })
        }
        else
        {
          args.html +=
            '<div class="norecord">Нет записей</div>'
        }

        args.html +=
          '</div></div>'
      }
    }
  })
})