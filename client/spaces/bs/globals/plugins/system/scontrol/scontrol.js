define(function(require) 
{
  let Plugin = require('plugin')

  return new Class(
  {
    Extends: Plugin,

    timeout: null,

    onTest: function()
    {
      // let tree = App.tree

      // if (!this.timeout)
      // {
      //   this.timeout = setTimeout(() => 
      //   {
      //     let start = false,
      //         html =
      //          '<div class="swccontrol">'+
      //             '<div class="label exp"><i class="fas fa-cogs"></i></div>'+
      //             '<div class="layer">'+
      //               '<div class="tree3">'+
      //                 '<ul>'+
      //                   '<li class="open">'+
      //                     '<span class="folder">'+
      //                       App.tree.objects[0].type+'.'+App.tree.objects[0].group+'.'+App.tree.objects[0].name+
      //                       '<i class="fa fa-sort-up arrow"></i>'+
      //                     '</span>'+
      //                     '<ul>'+
      //                       this.renderTree(0)+
      //                     '</ul>'+
      //                   '</li>'+
      //                 '</ul>'+
      //               '</div>'+
      //             '</div>'+
      //           '</div>'

      //     $('#app').append(html)

      //     $('.tree3').on('click', '.folder', function(e)
      //     {
      //       e.preventDefault()

      //       let li = $(this).parents('li:first')

      //       li.toggleClass('open')
      //     })

      //     $('.exp').click(function()
      //     {
      //       $('.tree3').toggleClass('open')
      //     })

      //     $(document).on('mousemove mouseup', null, function(e)
      //     {
      //       let data = self.resizedata

      //       if (start && e.type == 'mouseup')
      //       {
      //         start = false
      //       }

      //       if (start)
      //       {
      //         let left = parseInt($('.item.root').css('left')),
      //             step = 18

      //         if (e.pageX-start.offset > 0)
      //         {
      //           $('.item.root').css('left', (left+step)+'px')
      //         }
      //         else
      //         {
      //           $('.item.root').css('left', (left-step)+'px')
      //         }

      //         start.offset = e.pageX
      //       }
      //     })

      //     $('#app').on('mousedown', '.item.root', function(e)
      //     {
      //       start = {
      //         offset: e.pageX
      //       }
      //     })

      //     this.timeout = null
      //   }, 1000)
      // }
    },

    renderTree: function(n)
    {
      let html = '',
          childs = App.tree.childs[n]

      childs.map(child => 
      {
        let n = App.tree.objects.indexOf(child),
            ischilds = App.tree.childs[n],
            classesA = ischilds ? 'folder' : '',
            classesLi = ischilds ? 'open' : '',
            label = ''

        html +=
          '<li class="'+classesLi+'">'+
            '<span class="'+classesA+'">'+
              child.type+'.'+child.group+'.'+child.name

        if (ischilds)
        {
          html +=
              '<i class="fa fa-sort-up arrow"></i>'
        }

        html +=
            '</span>'

        if (ischilds)
        {
          html +=
            '<ul>'+
              this.renderTree(n)+
            '</ul>'
        }

        html +=
          '</li>'
      })

      return html
    }
  })
})