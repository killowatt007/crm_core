define(function(require) 
{
  /**
   * $version 1.1
   */

  let Plugin = require('components/fabrik/event/plugin')

  require('components/domofon/actors/invoice/receipt')

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

          module.getField('district').placeholder = 'Район'
          module.getField('street').placeholder = 'Улица'
          module.getField('housenumber').placeholder = 'Дом'

          if (App.ismobile)
          {
            module.html =
              '<div class="row">'+
                '<label class="col-24">Номер</label>'+
                '<div class="col-24 control">'+
                  module.renderField(module.getField('id'), true)+
                '</div>'+
              '</div>'+
              '<div class="row">'+
                '<label class="col-24">Адрес</label>'+
                '<div class="col-24 control">'+
                  '<div class="l-district">'+
                    module.renderField(module.getField('district'), true)+
                  '</div>'+
                  '<div class="l-street">'+
                    module.renderField(module.getField('street'), true)+
                  '</div>'+
                  '<div class="l-housenumber">'+
                    module.renderField(module.getField('housenumber'), true)+
                  '</div>'+
                '</div>'+
              '</div>'+
              '<div class="row">'+
                '<div class="col-24 clr">'+
                  module.renderClearButton()+
                '</div>'+
              '</div>'
          }
          else
          {
            module.html = 
              '<div class="bl bl-left">'+
                '<div class="lab">Номер:</div>'+
                module.renderField(module.getField('id'), true)+
              '</div>'+
              '<div class="bl bl-right">'+
                '<div class="lab">Адрес:</div>'+
                module.renderField(module.getField('district'), true)+
                module.renderField(module.getField('street'), true)+
                module.renderField(module.getField('housenumber'), true)+
              '</div>'+
              '<div class="clr">'+
                module.renderClearButton()+
              '</div>'
          }
        }
      }
    },

    renderActions: function(page, addon)
    {
      if (addon.name == 'title')
      {
        let invoice_receipt = this.getActor(this.obj.opts.invoice.receipt, {
          districts_options: this.obj.opts.invoice.districts_options
        })

        addon.opts.title += 
          '<div class="header-actions">'+
            App.render(invoice_receipt)
          '<div>'
      }
    }
  })
})