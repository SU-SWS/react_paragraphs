import React from 'react';
import Checkbox from '@mui/material/Checkbox';
import FormControl from '@mui/material/FormControl';
import FormControlLabel from '@mui/material/FormControlLabel';

export const BooleanWidget = ({fieldId, defaultValue, onFieldChange, settings}) => {

  const value = defaultValue && defaultValue.length ? defaultValue[0].value : 0;

  return (
    <FormControl component="fieldset">
      <FormControlLabel
        control={
          <Checkbox
            id={fieldId}
            checked={value === 1 || value === true}
            value="1"
            onChange={(e, checked) => onFieldChange([{value: checked ? 1 : 0}])}
            required={settings.required}
          />
        }
        label={settings.label}
      />
    </FormControl>
  )
};
