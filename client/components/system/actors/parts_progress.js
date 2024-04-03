define(function(require) 
{
  /**
   * $version 1.1
   * $deps c:components/builder/actors/popup v1.1
   * $style _progress_popup 1.1
   *        _core 1.1
   */
  
  let Object = require('lib/bs/object/object')

  require('components/builder/actors/popup_progress')

  return new Class(
  {
    Extends: Object,

    pp: null,

    props: null,

    part_l: 0,
    part_i: 0,
    partname: null,
    pr_partname: null,
    part: null,
    partnames: [],

    sub: null,

    ajaxData: null,

    counter: 0,
    partcounter: {},

    info: [],

    init: function()
    {
      this.sub = this.po.subconst(this.opts.subname)
      this.sub.prog = this

      this.props = this.sub.props()

      this.pp = this.getActor({
        group: 'builder',
        name: 'popup_progress',
        opts: {
          label: get(this.props, '', 'pp.label'),
          label_info: get(this.props, '', 'pp.label_info')
        }
      })

      $.each(this.props.parts, (pname, p) => this.partnames.push(pname))
      this.part_l = this.partnames.length
    },

    start: function()
    {
      let self = this

      if (this.pr_partname)
      {
        this.partname = this.pr_partname
        self.pr_partname = null
        this.part_i--
      }
      else
      {
        this.partname = this.partnames[this.part_i]
      }

      this.part = this.props.parts[this.partname]
      this.setCounter()

      if (this.getCounter() == 1)
        this.pp.open()

      if (this.partname)
      {
        let ppdata = get(this.part, {}, 'pp')

        if (ppdata.info) 
          this.pp.setInfo(ppdata.info)
        else
          this.pp.setInfo('Действие '+(this.part_i+1)+' из '+this.part_l)

        if (ppdata.label) 
          this.pp.addItem({label: ppdata.label})

        if (this.part.type == 'ajax')
        {
          this.sub.beforeAjax()

          this.ajax({
            data: this.part.ajax.data,
            success: function(data)
            {
              self.ppdata(data)
              self.ajaxData = data

              self.sub.step()

              self.part_i++
              self.start()
            }
          })
        }
      }
      else
      {
        this.pp.end(this.info)
      }
    },

    ppdata: function(data)
    {
      let ppdata = get(data, {}, 'pp')

      if (ppdata.info)
        this.info = this.info.concat(ppdata.info)
    },

    addInfo: function(info)
    {
      this.info = this.info.concat(info)
    },

    setCounter: function()
    {
      if (!this.partcounter[this.partname])
        this.partcounter[this.partname] = 0

      this.partcounter[this.partname]++
      this.counter++
    },

    getCounter: function(ispart)
    {
      ispart = get(ispart, false)

      return ispart ? this.partcounter[this.partname] : this.counter
    },

    next: function(name)
    {
      if (this.partnames[this.part_i+1] != name)
        this.pr_partname = name
    }
  })
})