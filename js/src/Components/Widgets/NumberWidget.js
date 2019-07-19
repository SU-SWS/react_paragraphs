import React from 'react';
import TextField from '@material-ui/core/TextField';
import {FormGroup} from "@material-ui/core";

export const NumberWidget = ({fieldId, defaultValue, onFieldChange, settings}) => {

  return (
    <FormGroup>
      <TextField
        id={fieldId}
        label={settings.label}
        helperText={settings.help}
        inputProps={{
          min: settings.min,
          max: settings.max,
          step: settings.scale ? Math.pow(10, -settings.scale) : 1
        }}
        defaultValue={defaultValue && defaultValue.length ? defaultValue[0].value : ''}
        onChange={e => onFieldChange([{value: e.target.value}])}
        required={settings.required}
        type='number'
      />
    </FormGroup>
  )
};
