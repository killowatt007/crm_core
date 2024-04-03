define(function(require) 
{
  return( 
  {
    inputsToObject: function(inputs)
    {
      let self = this,
          data = {}

      inputs.each(function() 
      {
        let name = $(this).attr('name'),
            value = $(this).val()

        if ($(this).attr('type') == 'checkbox')
          value = $(this).is(':checked') ? 1 : 0

        data = self.formigInputData(name, value, data)
      })

      return data
    },

    formigInputData: function(name, value, data)
    {
      let nameArr = name.split('['),
          length = nameArr.length,
          _data = data,
          getpart = part=> part.substring(0, part.length-1)

      nameArr.map(function(part, i)
      {
        if (i)
          part = getpart(part)

        if (i == (length-1))
        {
          _data[part] = ($.isNumeric(value) ? parseInt(value) : value)
        }
        else
        {
          part = (!part) ? _data.length : part

          if (!_data[part])
          {
            if (nameArr[i+1]!=undefined && getpart(nameArr[i+1]) == '0')
              _data[part] = []
            else
              _data[part] = {}
          }

          _data = _data[part]
        }
      })

      return data
    } 
  })
})
