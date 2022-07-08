import React from 'react';
import Radio from '@mui/material/Radio';
import FormControl from '@mui/material/FormControl';
import FormLabel from '@mui/material/FormLabel';
import FormControlLabel from '@mui/material/FormControlLabel';
import RadioGroup from '@mui/material/RadioGroup';

export const RadiosWidget = ({fieldId, defaultValue, onFieldChange, settings}) => {

  return (
    <FormControl component="fieldset">
      <FormLabel component="legend">{settings.label}</FormLabel>
      <RadioGroup
        aria-label={settings.label}
        onChange={e => onFieldChange([{value: e.target.value}])}
        defaultValue={defaultValue && defaultValue.length ? defaultValue[0].value : null}
      >
        {Object.keys(settings.options).map(key =>
          <FormControlLabel
            key={`${fieldId}-${key}`}
            value={key}
            htmlFor={`${fieldId}-key`}
            control={<Radio id={`${fieldId}-key`}/>}
            label={settings.options[key]}
          />
        )}
      </RadioGroup>
    </FormControl>
  )
};
