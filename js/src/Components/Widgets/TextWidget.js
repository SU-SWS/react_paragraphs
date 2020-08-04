import React from 'react';
import TextField from '@material-ui/core/TextField';
import FormGroup from '@material-ui/core/FormGroup';

export const TextWidget = ({fieldId, defaultValue, onFieldChange, settings}) => {

  let maxLength = 254
  if (typeof settings.max_length !== 'undefined' && settings.max_length > 0) {
    maxLength = settings.max_length;
  }

  return (
    <FormGroup>
      <TextField
        id={fieldId}
        label={settings.label}
        helperText={settings.help}
        variant="outlined"
        rows={settings.text_type === 'textarea' ? 8 : 1}
        multiline={settings.text_type === 'textarea'}
        defaultValue={defaultValue && defaultValue.length ? defaultValue[0].value : ''}
        required={settings.required}
        type={settings.text_type}
        onChange={e => onFieldChange([{value: e.target.value}])}
        inputProps={{maxLength: settings.text_type !== 'textarea' ? maxLength : null}}
        fullWidth
      />
    </FormGroup>
  )
};
