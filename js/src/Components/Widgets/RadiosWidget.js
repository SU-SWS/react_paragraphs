import React from 'react';
import Radio from '@material-ui/core/Radio';
import FormControl from '@material-ui/core/FormControl';
import FormLabel from '@material-ui/core/FormLabel';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import RadioGroup from '@material-ui/core/RadioGroup';

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
