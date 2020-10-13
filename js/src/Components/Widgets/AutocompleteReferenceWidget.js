import React from 'react';
import {FormHelperText} from "@material-ui/core";
import TextField from '@material-ui/core/TextField';
import FormGroup from '@material-ui/core/FormGroup';
import Autocomplete from '@material-ui/lab/Autocomplete';

export const AutocompleteReferenceWidget = ({defaultValue, fieldId, onFieldChange, settings}) => {

  /**
   * Grab the first character of the label for grouping.
   */
  const options = settings.options.map((option) => {
    const firstLetter = option.label[0].toUpperCase();
    return {
      firstLetter: /[0-9]/.test(firstLetter) ? '0-9' : firstLetter,
      ...option,
    };
  });

  /**
   * Find the option to be displayed to the user from the default values.
   * @returns {*|[]}
   */
  const getValue = () => {
    const values = [];
    if (defaultValue && defaultValue.length > 0) {
      defaultValue.map(value => {
        const foundOption = options.find(option => parseInt(option.entityId) === parseInt(value.target_id));
        if (foundOption) {
          values.push(foundOption);
        }
      })
    }
    return settings.cardinality === 1 ? values[0] : values;
  }

  /**
   * On changing of the options, pass the values up to the widget context.
   *
   * @param e
   * @param newValue
   * @returns {*}
   */
  const onChange = (e, newValue) => {
    if (settings.cardinality === 1) {
      if (newValue) {
        return onFieldChange([{target_id: newValue.entityId}])
      }
      return onFieldChange([]);
    }

    const newValues = [];
    newValue.map(newValue => {
      const chosenOption = options.find(option => parseInt(option.entityId) === parseInt(newValue.entityId))
      newValues.push({target_id: chosenOption.entityId})
    });
    onFieldChange(newValues);
  }

  return (
    <FormGroup>
      <Autocomplete
        id={fieldId}
        multiple={settings.cardinality !== 1}
        limitTags={2}
        getOptionDisabled={() => (defaultValue && defaultValue.length === settings.cardinality)}
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
      <FormHelperText>{settings.help}</FormHelperText>
      }
    </FormGroup>
  )
}
