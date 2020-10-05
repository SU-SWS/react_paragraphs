import React from 'react';
import {FormHelperText} from "@material-ui/core";
import TextField from '@material-ui/core/TextField';
import FormGroup from '@material-ui/core/FormGroup';
import Autocomplete from '@material-ui/lab/Autocomplete';

export const AutocompleteReferenceWidget = ({defaultValue, fieldId, fieldName, onFieldChange, settings}) => {

  const options = settings.options.map((option) => {
    const firstLetter = option.label[0].toUpperCase();
    return {
      firstLetter: /[0-9]/.test(firstLetter) ? '0-9' : firstLetter,
      ...option,
    };
  });

  const getValue = () => {
    if (defaultValue.length > 0) {
      return options.find(option => option.entityId === defaultValue[0].target_id);
    }
  }

  const onChange = (e, newValue) => {
    if (newValue) {
      return onFieldChange([{target_id: newValue.entityId}])
    }

    onFieldChange([]);
  }

  return (
    <FormGroup>
      <Autocomplete
        id={fieldId}
        multiple={false}
        options={options.sort((a, b) => -b.firstLetter.localeCompare(a.firstLetter))}
        getOptionLabel={(option) => option.label + ' (' + option.entityId + ')'}
        groupBy={(option) => option.firstLetter}
        onChange={onChange}
        value={getValue()}
        renderInput={(params) => (
          <TextField {...params} label={settings.label} variant="outlined"/>
        )}
      />
      {settings.help.length > 0 &&
      <FormHelperText>{settings.help}</FormHelperText>}
    </FormGroup>
  )
}
