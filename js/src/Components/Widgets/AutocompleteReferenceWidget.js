import React, {useState} from 'react';
import {FormHelperText} from "@material-ui/core";
import TextField from '@material-ui/core/TextField';
import FormGroup from '@material-ui/core/FormGroup';
import FormControl from '@material-ui/core/FormControl';
import FormLabel from '@material-ui/core/FormLabel';
import Autocomplete from '@material-ui/lab/Autocomplete';

export const AutocompleteReferenceWidget = ({defaultValue, fieldId, fieldName, onFieldChange, settings}) => {
  console.log(settings);

  return (
    <Autocomplete
      id="combo-box-demo"
      options={settings.options}
      getOptionLabel={(option) => option.label}
      style={{ width: 300 }}
      renderInput={(params) => <TextField {...params} label="Combo box" variant="outlined" />}
    />
  )
}
