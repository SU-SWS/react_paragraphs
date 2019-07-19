import React from 'react';
import Checkbox from '@material-ui/core/Checkbox';
import FormControl from '@material-ui/core/FormControl';
import FormControlLabel from '@material-ui/core/FormControlLabel';

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
