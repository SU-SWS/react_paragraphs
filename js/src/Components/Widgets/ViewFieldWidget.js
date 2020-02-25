import React, {Component} from 'react';
import InputLabel from '@material-ui/core/InputLabel';
import FormGroup from '@material-ui/core/FormGroup';
import FormHelperText from '@material-ui/core/FormHelperText';
import Select from '@material-ui/core/Select';
import MenuItem from "@material-ui/core/MenuItem";
import TextField from "@material-ui/core/TextField";
import styled from 'styled-components';

export class ViewFieldWidget extends Component {

  constructor(props) {
    super(props);
    this.state = {
      target_id: this.getDefaultValue('target_id'),
      display_id: this.getDefaultValue('display_id'),
      arguments: this.getDefaultValue('arguments'),
      items_to_display: this.getDefaultValue('items_to_display')
    }
  }

  getDefaultValue(column) {
    return this.props.defaultValue && this.props.defaultValue[0] ? this.props.defaultValue[0][column] : '';
  }

  valueChanged(column, newValue) {
    const newState = {...this.state};
    newState[column] = newValue;

    if (column === 'target_id' && newValue === '_none') {
      newState['display_id'] = newValue;
      newState['arguments'] = newValue;
      newState['items_to_display'] = newValue;
    }

    this.setState(newState);

    // Don't change the field value if the view or the display are not selected.
    if (newState.target_id !== '_none' && newState.display_id !== '_none') {
      this.props.onFieldChange([newState]);
    }
    else {
      this.props.onFieldChange([]);
    }
  }

  getDisplayOptions(viewId) {
    if (viewId) {
      return this.props.settings.displays[viewId];
    }
    return [];
  }

  render() {
    return (
      <FormGroup>
        <InputLabel htmlFor={this.props.fieldId + '-target-id'}>
          {this.props.settings.label}
        </InputLabel>

        <FormHelperText>
          {this.props.settings.help}
        </FormHelperText>

        <Select
          id={this.props.fieldId + '-target-id'}
          value={this.state.target_id ? this.state.target_id : '_none'}
          multiple={this.props.settings.cardinality !== 1}
          onChange={e => this.valueChanged('target_id', e.target.value)}
          variant="outlined"
          required={this.props.settings.required}
          style={{maxWidth: "400px", marginTop: "10px"}}
        >
          {this.props.settings.required === false && this.props.settings.cardinality === 1 &&
          <MenuItem value="_none">
            -- Choose a view --
          </MenuItem>
          }

          {this.props.settings.views.map(view =>
            <MenuItem value={view.value} key={'view-' + view.value}>
              {view.label}
            </MenuItem>
          )}
        </Select>

        <InputWrapper style={{display: this.state.target_id ? 'block' : 'none'}}>

          <InputLabel htmlFor={this.props.fieldId + '-display-id'}>
            Display
          </InputLabel>

          <Select
            id={this.props.fieldId + '-display-id'}
            value={this.state.display_id ? this.state.display_id : '_none'}
            multiple={this.props.settings.cardinality !== 1}
            onChange={e => this.valueChanged('display_id', e.target.value)}
            variant="outlined"
            required={this.props.settings.required}
            style={{maxWidth: "400px", marginTop: "10px"}}
          >
            {this.props.settings.required === false && this.props.settings.cardinality === 1 &&
            <MenuItem value="_none">
              -- Choose a display --
            </MenuItem>
            }

            {this.getDisplayOptions(this.state.target_id).map(display =>
              <MenuItem value={display.value} key={'display-' + display.value}>
                {display.label}
              </MenuItem>
            )}
          </Select>

          <InputWrapper>
            <TextField
              id={this.props.fieldId + '-arguments'}
              label="Arguments"
              variant="outlined"
              defaultValue={this.state.arguments}
              required={this.props.settings.required}
              onChange={e => this.valueChanged('arguments', e.target.value)}
              inputProps={{maxLength: 254}}
              fullWidth
            />
          </InputWrapper>
          <InputWrapper>
            <TextField
              id={this.props.fieldId + '-num-items'}
              label="Number of items"
              inputProps={{
                min: 0,
                step: 1
              }}
              defaultValue={this.state.items_to_display}
              onChange={e => this.valueChanged('items_to_display', e.target.value)}
              type='number'
            />
          </InputWrapper>
        </InputWrapper>
      </FormGroup>
    )
  }

}

const InputWrapper = styled.div`
  margin-top: 10px;
`;
