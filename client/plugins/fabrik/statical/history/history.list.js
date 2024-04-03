define(function(require) 
{
	let Plugin = require('components/fabrik/event/plugin')

	require('components/fabrik/actors/list/rows')

	return new Class(
	{
		Extends: Plugin,

    onBeforeOpenPopup: function() 
    {
    	if (this.obj.opts.modulealias == 'history')
    	{
	    	let obj = this.obj,
	    			row = obj.rows[obj.currentRAlias].opts.rows[obj.currentKey],
	    			isdeleted = row.HRowId ? 1 : 0

	      App.setDataStream('history.isdeleted', isdeleted)
    	}
    },

		onElementBasicBeforeGetValue: function()
		{
			let obj = this.obj, sobj = this.sobj,
					row

			if (obj.opts.modulealias == 'history')
			{
				// id
				if (sobj.opts.name == 'id')
				{
					if (sobj.opts.rowsalias == 'history')
					{
						sobj.value = ''
					}
					else
					{
						row = this.getRow()

						if (row.HRowId)
							sobj.value = row.HRowId
					}
				}
			}
		},

		onRowsAfterRowRender: function(rowobj, i, args)
		{
			let obj = this.obj

			// if (obj.opts.modulealias == 'history' && rowobj.opts.alias == 'main')
			// {
			// 	let row = rowobj.opts.rows[rowi],
			// 			classes = row.HIsDelete ? 'red' : 'green'
 
			// 	args.actions[1].classes = classes
			// }

			if (obj.opts.modulealias == 'history' && rowobj.opts.alias == 'main')
			{
        let div = $('<div>').append(args.html),
        		row = rowobj.opts.rows[i],
        		classes = row.HIsDelete ? 'red' : 'green'

        div.find('td:last').css('position', 'relative')
        div.find('td:last').append('<div class="delstatus right '+classes+'"></div>')
        // div.find('td:first').css('position', 'relative')
        // div.find('td:first').append('<div class="delstatus left '+classes+'"></div>')

        args.html = div[0].innerHTML
			}


			




		},

		onAfterObjsRender: function()
		{
			let self = this, obj = this.obj

      if (obj.opts.modulealias == 'history')
      {
				if (!$('body')[0].test)
				{
					$('body').on('click', '.actions .history', function()
					{
						let btn = this,
								row = $(btn).parents('tr:first'),
								color = (row.hasClass('odd') ? 'grey' : 'white'),
								rowid = row.attr('rowid'),
								isrender = $(btn).hasClass('render'),
								entity = $('.fabrik.filter').find('.entityid')

						$(btn).toggleClass('open')

						if (!isrender)
						{
					    self.ajax({
					      method: 'getHistoryItems',
					      format: 'list',
					      data: {
					      	rowid: rowid,
					      	moduleid: entity.val()
					      },
					      success: function(data)
					      {
					      	rows = self.obj.initRowGroup({
										rows: data.rows,
										fieldsgroup: data.fgroup,
										left_colspan: 3,
										tr_class: 'sub-rws data-row history-items '+color+' link-'+rowid
									}, 'history')

					      	$(row).addClass('notborder')
					      	$(btn).addClass('render')
									row.after(App.render(rows))
					      }
					    })
						}
						else
						{
							$(row).toggleClass('notborder')
							$('body').find('.sub-rws.history-items.link-'+rowid).toggleClass('hide')
						}
					})

					$('body')[0].test = true
				}
      }
		}
	})
})