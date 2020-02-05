import React from 'react';
import {TextField} from "@material-ui/core";

export const AdminTitleField = ({textField, item, onChange}) => {
  return (
    <TextField
      label="Administration Label"
      disabled={!textField}
      onChange={e => onChange(e.target.value)}
      defaultValue={item.admin_title && item.admin_title.length >= 1 ? item.admin_title : 'Unknown'}
      InputLabelProps={{className: textField ? '' : 'visually-hidden'}}
      helperText="Provide a simple label for easy organization among other items."
      FormHelperTextProps={{className: textField ? '' : 'visually-hidden'}}
      variant="outlined"
    />
  )
};
