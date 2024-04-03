define(function(require) 
{
	let Plugin = require('components/fabrik/event/plugin')

	return new Class(
	{
		Extends: Plugin,

    onBeforeUpdate: function() {this.streamData()},

    streamData: function()
    {
    	let analytics = $('.analytics_dash')

    	if (analytics[0])
    	{
				let debt = analytics.find('.forminput.debt').val(),
						status = analytics.find('.forminput.status').val(),
						base = analytics.find('.forminput.base').val()

				App.setDataStream('analytics', {
					islist: 1,
					debt: debt,
					status: status,
					base: base
				})
    	}
    },

		onElementGetValue: function()
		{
			let obj = this.obj, sobj = this.sobj

			// id
			if (sobj.opts.name == 'id')
			{    
				sobj.value =
					'<a style="font-size:12px;margin-right:10px;" href="/operator-clients?_ffilter[31][12]='+sobj.value+'&_ffilter[31][13]='+sobj.value+'" target="_blank" class="redirect_icon"><i class="fal fa-external-link"></i></a>'+
					'<a href="#" class="link_to_client" client-id="'+sobj.value+'">'+sobj.value+'</a>'
			}
		},

		onAfterObjsRender: function()
		{
			$('.link_to_client').click(function(e)
			{
				e.preventDefault()
				let id = $(this).attr('client-id')

				App.window.open(75, { 
					args: {
						_ffilter: {31: {12:id,13:id}}
					},
					callback: ()=>
					{
						$(window).scrollTop(0);
						App.modules[5].setActive(75)
					}
				})
			})
		}
	})
})