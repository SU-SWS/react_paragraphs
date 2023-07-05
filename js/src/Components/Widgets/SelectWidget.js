import React from 'react';
import InputLabel from '@mui/material/InputLabel';
import FormHelperText from '@mui/material/FormHelperText';
import Select from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';
import FormControl from "@mui/material/FormControl";

export const SelectWidget = ({fieldId, defaultValue, onFieldChange, settings}) => {

  let defaultFieldValue = settings.default_value;
  if (defaultValue && defaultValue.length >= 1) {
    defaultFieldValue = settings.cardinality === 1 ? defaultValue[0][settings.column_key] : defaultValue.map(item => item[settings.column_key]);
  }

  if (defaultFieldValue === undefined && settings.cardinality !== 1) {
    defaultFieldValue = [];
  }

  const valueChanged = (newValue) => {
    if (newValue.length === 0) {
      onFieldChange([]);
      return;
    }

    if (settings.cardinality === 1) {
      onFieldChange([{[settings.column_key]: newValue}]);
      return;
    }

    const newFieldValue = [];

    newValue.map((deltaValue, delta) => {
      newFieldValue[delta] = {[settings.column_key]: deltaValue};
    });

    onFieldChange(newFieldValue);
  };

  return (
    <FormControl required={settings.required} variant="outlined">
      <InputLabel id={'label-'+ fieldId} htmlFor={fieldId}>{settings.label}</InputLabel>
      <Select
        id={fieldId}
        labelId={'label-'+ fieldId}
        value={defaultFieldValue}
        multiple={settings.cardinality !== 1}
        onChange={e => valueChanged(e.target.value)}
        classes={{select:'max-w-md mt-2.5'}}
        inputProps={{required: settings.required}}
      >
        {settings.required === false && settings.cardinality === 1 &&
        <MenuItem value="">
          -- None --
        </MenuItem>
        }

        {Object.keys(settings.options).map(key =>
          <MenuItem key={`${fieldId}-${key}`} value={key}>
            {settings.options[key]}
          </MenuItem>
        )}

      </Select>
      <FormHelperText dangerouslySetInnerHTML={{__html: settings.help}}/>
    </FormControl>
  )
};
