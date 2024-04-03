define(function(require) 
{
  let Window = require('lib/bs/window'),
      dispatchers = {
        main: require('lib/bs/event/dispatcher'),
        component: require('lib/bs/event/dispatcherComponent')
      }

  return new Class(
  {
    dep: {},
    window: null,

    clientPath: null,
    item: null,

    selector: '#app',

    objectCounter: 0,

    objs: [],
    modules: {},
    plugins: {},
    pluginsAfterRender: [],

    pages: [],
    pagePluginManager: [],

    reqData: {},

    service: {},

    dataStream: {},

    dispatchers: {},

    initialize: function() {},

    paramsTabCounter: 0,

    plineTO: null,
    plineNode: null,

    ismobile: null,
    ip: null,

    log: false,

    tree: {
      objects: [],
      childs: []
    },

    run: function()
    {
      let path = window.location.pathname

      this.window = new Window()
      this.window.open(path, {isrun: true})
    },

    ajax: function(obj)
    {
      let self = this,
          urlArgs = new URLSearchParams(window.location.search),
          reqData = {}

      reqData = {}
      reqData = $.extend(obj.data, reqData)

      if (reqData.isrun)
        urlArgs.forEach((key, val)=> reqData[val]=key)

      if (self.item && !reqData.isWindow)
        reqData.itemId = self.item.id

      this.objs = []
      this.pluginsAfterRender = []

      if (this.getPagePluginManager())
        this.getPagePluginManager().run('beforeSend', [reqData])

      reqData.stream = this.dataStream
      this.dataStream = {}

      this.pline(true, 0, reqData)

      // __log__
      if (this.log && reqData.option)
        console.log('beforeAjax - '+reqData.option+'.'+reqData.branch+':'+reqData.task);

      $.ajax({
        url: '/bootstrap.php',
        method: get(obj, 'GET', 'sendmethod'),
        data: reqData,
        async: get(obj, true, 'async'),
        success: function(response) 
        {
          let resData, newDep

          try 
          {
            resData = JSON.parse(response)
          }
          catch (e) 
          {
            if (response.indexOf('%%__dump__%%') === 0)
            {
              response = response.replace('%%__dump__%%', '')
              reqData.isWindow ? self.dump(response) : console.log(response)
            }
            else
            {
              reqData.isWindow ? self.syntaxServerError(response) : console.log(response)
            }

            self.pline(false, 0, reqData)
            return
          }

          if (resData.error)
          {
            self.error(resData)
            self.pline(false, 0, reqData)
          }
          else if (resData.redirect)
          {
            window.location.replace(resData.redirect)
          }
          else
          {
            newDep = self.getNewDep(resData.dep)

            self.clientPath = resData.clientPath
            self.ismobile = resData.ismobile
            self.ip = resData.ip

            if (self.ismobile)
              $('#app').addClass('mobile')

            if (reqData.isWindow)
              self.item = resData.item

            if (newDep.length)
              self.require(newDep, ()=>{ self.ajaxSuccess(obj, resData, reqData) })
            else
              self.ajaxSuccess(obj, resData, reqData)
          }
        }
      })
    },

    ajaxSuccess: function(obj, resData, reqData)
    {
      let self = this

      // __log__
      if (this.log && reqData.option)
        console.log('ajaxSuccess - '+reqData.option+'.'+reqData.branch+':'+reqData.task);

      $.each(requirejs.s.contexts._.defined, (name, dep) => App.setDep(name, dep))

      if (reqData.isWindow)
        this.getPage().opts = resData.data.opts

      if (obj.success)
        obj.success(resData.data)

      resData.updateModules.map(mData => this.modules[mData.id].updateRender(mData.data))
      this.afterRender()

      this.pluginsAfterRender.map(plugin =>
      {
        plugin.onAfterObjsRender(resData, reqData)
      }) 

      if (obj.afterSuccess)
        obj.afterSuccess(resData.data)

      this.getPagePluginManager().run('afterSuccess', [resData, reqData])

      self.pline(false, 0, reqData)
    },

    afterRender: function()
    {
      this.objs.map(function(object) 
      {
        if (!object.isrender)
        {
          let node = $('#'+object.key)

          if (node[0])
            object.node = node

          object.onAfterRender()
          object.isrender = true   
        }
      })
    },

    setDataStream: function(key, data)
    {
      let self = this,
          keyArr = key.split('.'),
          dataStream = this.dataStream,
          lastKey

      keyArr.map(function(key, i)
      {
        if (!dataStream[key])
        {
          if (keyArr.length != i+1)
            dataStream[key] = {}
          else
            dataStream[key] = null
        }

        if (keyArr.length != i+1)
          dataStream = dataStream[key]
        else
          lastKey = key
      })

      dataStream[lastKey] = data
    },

    getNewDep: function(dep)
    {
      var newDep = []

      dep.map((name)=>
      {
        if (!this.dep[name])
          newDep.push(name)
      })

      return newDep      
    },

    require: function(dep, callback)
    {
      let self = this

      require(dep, function() 
      {
        $.each(arguments, (i, arg) => self.setDep(dep[i], arg))

        if (callback)
          callback()
      })
    },

    setDep: function(name, dep)
    {
      if (!this.dep[name])
        this.dep[name] = dep
    },

    render: function(object, isupd, notstack)
    {
      let html = object.render()

      if (!notstack)
        this.objs.push(object)

      if (object.afterRender)
        html = object.afterRender(html)

      return html
    },

    getActor: function(data, actorData)
    {
      let path = 'components/'+data.group+'/actors/'+data.name,
          obj

      data.data = $.extend(data.data, get(actorData, {}))
      obj = new this.dep[path](data)

      return obj
    },

    getPluginManager: function()
    {
      return this.getDispatcher()
    },

    getDispatcher: function(type)
    {
      type = get(type, 'main')

      if (!this.dispatchers[type])
        this.dispatchers[type] = new dispatchers[type]()

      return this.dispatchers[type]
    },

    getService: function(component, name)
    {
      let key = component+'.'+name

      if (!this.service[key])
        this.service[key] = new this.dep['components/'+component+'/service/'+name]()

      return this.service[key]
    },

    getPage: function()
    {
      let bid = this.item.builderid

      if (!this.pages[bid])
      {
        this.pages[bid] = new this.dep['components/builder/actors/page']({
          id: this.item.builderid, 
          group: 'builder', name:'page', 
          data: {tablename: this.item.builderalias}
        })
      }

      return this.pages[bid]
    },

    getPagePluginManager: function()
    {
      let pagePluginManager = null

      if (this.item)
      {
        let bid = this.item.builderid

        if (!this.pagePluginManager[bid])
        {
          let page = this.getPage()
          this.pagePluginManager[bid] = page.getPluginManager()
        }

        pagePluginManager = this.pagePluginManager[bid]
      }

      return pagePluginManager
    },

    syntaxClientError: function(fullMsg, file, line)
    {
      let type = fullMsg.match(/(.*?:)/)[0].slice(0, -1)
          msg = fullMsg.replace(type, '').slice(1)

      this.syntaxError({side: 'client', code: 500, type: type, msg: msg, file: file, line: line})
    },

    syntaxServerError: function(text)
    {
      let i = document.createElement('i'),
          arr,
          type, msg, file, line

      i.innerHTML = text
      arr = i.childNodes

      if (arr.length > 5)
      {
        type = arr[2].innerText
        msg = arr[3].data.slice(1)
        file = arr[4].innerText
        line = arr[6].innerText

        this.syntaxError({side: 'server', code: 500, type: type, msg: msg, file: file, line: line})
      }

      console.log(text) 
    },

    syntaxError: function(data)
    {
      $(this.selector).html(data.side+data.code+'. <b>'+data.type+':</b>'+data.msg+' <b>'+data.file+'</b> on line <b>'+data.line+'</b>')
    },

    error: function(data)
    {
      let html = ''

      html += 
        '<div id="error">'+
          '<div class="msg">'+data.code+'. '+data.msg+'</div>'

      if (data.isdev)
      {
        html += 
          '<div class="system_msg">'+
            '<span class="bb">'+data.system_msg+'</span>'+
            '<br>'+
            data.backtrace.map(function(row)
            {
              let html = '<br>'

              $.each(row, function(key, val)
              {
                html += '<br>['+key+'] => '+val
              })

              return html
            }).join('')+
          '</div>'

      }

      html += 
        '<i class="fas fa-times cclose"></i>'+
          '</div>'
             
      $(this.selector).append(html)
      $(this.selector+' #error .cclose').click(() => $(this.selector+' #error').remove())
    },

    dump: function(data)
    {
      $(this.selector).html('<pre style="margin:10px;font-size:12px;height:100%;">'+data+'</pre>')
    },

    pline: function(show, dur, reqData)
    {
      let self = this

      if (show)
      {
        if (!this.plineTO)
        {
          if (reqData.isrun)
          {
            self.plineNode = $('<i class="far fa-spinner-third spin pageloader"></i>')
            $(self.selector).append(self.plineNode)
          }
          else
          {
            this.plineTO = setTimeout(function() 
            {
              self.plineNode = $('<div id="progressline"><div class="wrapper"></div></div><div class="blocker"></div>')
              $(self.selector).append(self.plineNode)
            }, get(dur, 0))
          }
        }
      }
      else
      {
        if (!reqData.isrun)
        {
          if (this.plineTO)
            clearTimeout(this.plineTO)

          this.plineTO = null
        }

        if (this.plineNode)
        {
          this.plineNode.remove()
          this.plineNode = null
        }
      }
    }
  })
})
