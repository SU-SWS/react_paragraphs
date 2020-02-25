import React, {Component} from 'react';
import InputLabel from '@material-ui/core/InputLabel';
import FormGroup from '@material-ui/core/FormGroup';
import FormHelperText from '@material-ui/core/FormHelperText';
import Select from '@material-ui/core/Select';
import MenuItem from "@material-ui/core/MenuItem";
import TextField from "@material-ui/core/TextField";

export class ViewFieldWidget extends Component {

  getTargetId() {
    return this.props.defaultValue && this.props.defaultValue[0] ? this.props.defaultValue[0].target_id : '';
  }

  getDisplayId() {
    return this.props.defaultValue && this.props.defaultValue[0] ? this.props.defaultValue[0].display_id : '';
  }

  getArguments() {
    return this.props.defaultValue && this.props.defaultValue[0] ? this.props.defaultValue[0].arguments : '';
  }

  getNumItems() {
    return this.props.defaultValue && this.props.defaultValue[0] ? this.props.defaultValue[0].items_to_display : '';
  }

  valueChanged(column, newValue) {
    const newFieldValue = {
      target_id: this.getTargetId(),
      display_id: this.getDisplayId(),
      arguments: this.getArguments(),
      items_to_display: this.getNumItems()
    };
    newFieldValue[column] = newValue;
    this.props.onFieldChange([newFieldValue]);
  }

  getDisplayOptions(viewId) {
    if (viewId) {
      return this.props.settings.displays[viewId];
    }
    return [];
  }

  render() {
    const target_id = this.getTargetId();
    const display_id = this.getDisplayId();
    const display_arguments = this.getArguments();
    const items_to_display = this.getNumItems();

    return (
      <FormGroup>
        <InputLabel>
          {this.props.settings.label}
        </InputLabel>

        <FormHelperText>
          {this.props.settings.help}
        </FormHelperText>

        <Select
          id={this.props.fieldId + '-target-id'}
          value={target_id}
          multiple={this.props.settings.cardinality !== 1}
          onChange={e => this.valueChanged('target_id', e.target.value)}
          variant="outlined"
          required={this.props.settings.required}
          style={{maxWidth: "400px", marginTop: "10px"}}
        >
          {this.props.settings.required === false && this.props.settings.cardinality === 1 &&
          <MenuItem value="">
            -- None --
          </MenuItem>
          }

          {this.props.settings.views.map(view =>
            <MenuItem value={view.value} key={'view-' + view.value}>
              {view.label}
            </MenuItem>
          )}

        </Select>

        <Select
          id={this.props.fieldId + '-display-id'}
          value={display_id}
          multiple={this.props.settings.cardinality !== 1}
          onChange={e => this.valueChanged('display_id', e.target.value)}
          variant="outlined"
          required={this.props.settings.required}
          style={{
            maxWidth: "400px",
            marginTop: "10px",
            display: target_id ? 'block' : 'none'
          }}
        >
          {this.props.settings.required === false && this.props.settings.cardinality === 1 &&
          <MenuItem value="">
            -- None --
          </MenuItem>
          }

          {this.getDisplayOptions(target_id).map(display =>
            <MenuItem value={display.value} key={'display-' + display.value}>
              {display.label}
            </MenuItem>
          )}
        </Select>

        <TextField
          id={this.props.fieldId + '-arguments'}
          label="Arguments"
          variant="outlined"
          defaultValue={display_arguments}
          required={this.props.settings.required}
          onChange={e => this.valueChanged('arguments', e.target.value)}
          inputProps={{maxLength: 254}}
          fullWidth
          style={{
            display: target_id ? 'block' : 'none'
          }}
        />

        <TextField
          id={this.props.fieldId + '-num-items'}
          label="Number of items"
          inputProps={{
            min: 0,
            step: 1
          }}
          defaultValue={items_to_display}
          onChange={e => this.valueChanged('items_to_display', e.target.value)}
          type='number'
          style={{
            display: target_id ? 'block' : 'none'
          }}
        />

      </FormGroup>
    )
  }

}
