import React from 'react';
import TextField from '@mui/material/TextField';
import {FormGroup} from "@mui/material";

export const DateWidget = ({fieldId, defaultValue, onFieldChange, settings}) => {

  const toDatetimeLocal = (timestamp) => {
    const date = new Date(timestamp),
      ten = function (i) {
        return (i < 10 ? '0' : '') + i;
      },
      YYYY = date.getFullYear(),
      MM = ten(date.getMonth() + 1),
      DD = ten(date.getDate()),
      HH = ten(date.getHours()),
      II = ten(date.getMinutes()),
      SS = ten(date.getSeconds());
    return YYYY + '-' + MM + '-' + DD + 'T' +
      HH + ':' + II + ':' + SS;
  };

  const toDatetimeUtc = (timestamp) => {
    const date = new Date(timestamp),
      ten = function (i) {
        return (i < 10 ? '0' : '') + i;
      },
      YYYY = date.getUTCFullYear(),
      MM = ten(date.getUTCMonth() + 1),
      DD = ten(date.getUTCDate()),
      HH = ten(date.getUTCHours()),
      II = ten(date.getUTCMinutes()),
      SS = ten(date.getUTCSeconds());
    return YYYY + '-' + MM + '-' + DD + 'T' +
      HH + ':' + II + ':' + SS;
  };

  defaultValue = defaultValue && defaultValue.length ? defaultValue[0].value : '';
  if (defaultValue && settings.type === 'datetime') {
    const newDefaultValue = toDatetimeLocal(defaultValue);
    if (newDefaultValue !== defaultValue) {
      defaultValue = newDefaultValue;
      onFieldChange([{value: defaultValue}]);
    }
  }

  const dateChanged = (e) => {
    onFieldChange([{value: settings.type === 'date' ? e.target.value : toDatetimeUtc(e.target.value)}]);
  };

  return (
    <FormGroup>
      <TextField
        id={fieldId}
        InputLabelProps={{shrink: true}}
        label={settings.label}
        helperText={settings.help}
        type={settings.type === 'date' ? 'date' : 'datetime-local'}
        required={settings.required}
        onChange={dateChanged}
        defaultValue={defaultValue}
        variant="outlined"
      />
    </FormGroup>
  )
};
