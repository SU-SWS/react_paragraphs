import React from 'react';
import InputLabel from '@material-ui/core/InputLabel';
import FormGroup from '@material-ui/core/FormGroup';
import FormHelperText from '@material-ui/core/FormHelperText';
import Select from '@material-ui/core/Select';
import MenuItem from '@material-ui/core/MenuItem';

export const SelectWidget = ({fieldId, defaultValue, onFieldChange, settings}) => {

  let defaultFieldValue = settings.default_value;
  if (defaultValue && defaultValue.length >= 1) {
    defaultFieldValue = settings.cardinality === 1 ? defaultValue[0][settings.column_key] : defaultValue.map(item => item[settings.column_key]);
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
    <FormGroup>
      <InputLabel htmlFor={fieldId}>{settings.label}</InputLabel>
      <Select
        id={fieldId}
        value={defaultFieldValue}
        multiple={settings.cardinality !== 1}
        onChange={e => valueChanged(e.target.value)}
        variant="outlined"
        required={settings.required}
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

        {settings.help.length &&
        <FormHelperText>
          {settings.help}
        </FormHelperText>
        }
      </Select>
    </FormGroup>
  )
};
