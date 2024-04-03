define(function(require) 
{
  let Plugin = require('components/fabrik/event/plugin')

  return new Class(
  {
    Extends: Plugin,

    onBeforeSubmit: function()
    {
      let result = true,
          text = 
            "Имя Товара:"+" "+$('.forminput.ItemName').val()+"\n"+
            "Сумма:"+" "+$('.forminput.Amount').val()+"\n"+
            "Печать чека:"+" "+($('.forminput.Electronically').is(':checked') ? 'Да' : 'Нет')+"\n"+
            "Метод:"+" "+$('.forminput.MethodId option:selected').text()

      return confirm(text)
    }
  })
})