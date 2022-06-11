import React, {Component} from 'react';
import InputLabel from '@mui/material/InputLabel';
import FormGroup from '@mui/material/FormGroup';
import FormHelperText from '@mui/material/FormHelperText';
import Select from '@mui/material/Select';
import MenuItem from "@mui/material/MenuItem";
import TextField from "@mui/material/TextField";
import styled from 'styled-components';

export class ViewFieldWidget extends Component {

  constructor(props) {
    super(props);
    this.state = {
      target_id: this.getDefaultValue('target_id') ? this.getDefaultValue('target_id') : '_none',
      display_id: this.getDefaultValue('display_id') ? this.getDefaultValue('display_id') : '_none',
      arguments: this.getDefaultValue('arguments'),
      items_to_display: this.getDefaultValue('items_to_display'),
      advancedOpen: false
    };
  }

  getDefaultValue(column) {
    return this.props.defaultValue && this.props.defaultValue[0] ? this.props.defaultValue[0][column] : '';
  }

  valueChanged(column, newValue) {
    const newState = {...this.state};
    newState[column] = newValue;

    if (column === 'target_id' && newValue === '_none') {
      newState['target_id'] = '_none';
      newState['display_id'] = '_none';
      newState['arguments'] = '';
      newState['items_to_display'] = '';
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
    if (viewId !== '_none') {
      if (typeof this.props.settings.displays[viewId] === 'object') {
        return Object.keys(this.props.settings.displays[viewId]).map(key => this.props.settings.displays[viewId][key])
      }
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

        <FormHelperText dangerouslySetInnerHTML={{__html: this.props.settings.help}}/>

        <Select
          id={this.props.fieldId + '-target-id'}
          value={this.state.target_id}
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

        <InputGroupWrapper
          style={{display: this.state.target_id !== '_none' ? 'block' : 'none'}}>

          <InputLabel htmlFor={this.props.fieldId + '-display-id'}>
            Display
          </InputLabel>

          <Select
            id={this.props.fieldId + '-display-id'}
            value={this.state.display_id}
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


          <AdvancedOptions>
            <a href="#"
               onClick={(e) => {
                 e.preventDefault();
                 this.setState({advancedOpen: !this.state.advancedOpen})
               }}
            >
              Advanced Options
            </a>
            <InputWrapper
              style={{display: this.state.advancedOpen ? 'block' : 'none'}}>
              <InputWrapper>
                <TextField
                  id={this.props.fieldId + '-arguments'}
                  label="Arguments"
                  helperText={`Separate contextual filters with a "/". Each filter may use "+" or "," for multi-value arguments. This field supports tokens.`}
                  variant="outlined"
                  defaultValue={this.state['arguments']}
                  onChange={e => this.valueChanged('arguments', e.target.value)}
                  inputProps={{maxLength: 254}}
                  fullWidth
                />
              </InputWrapper>
              <InputWrapper>
                <TextField
                  id={this.props.fieldId + '-num-items'}
                  label="Number of items"
                  helperText="Override the number of items to display. This also disables the pager if one is configured. Leave empty for default limit."
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
          </AdvancedOptions>
        </InputGroupWrapper>
      </FormGroup>
    )
  }

}

const AdvancedOptions = styled.div`
  border: 1px solid #ccc;
  padding: 20px;
  margin-top: 20px;
`;

const InputGroupWrapper = styled.div`
  border: 1px solid #ccc;
  padding: 20px;
  margin-top: 10px;
`

const InputWrapper = styled.div`
  margin-top: 10px;
`;
