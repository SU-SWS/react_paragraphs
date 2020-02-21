import React, {Component} from 'react';
import FormGroup from '@material-ui/core/FormGroup';
import InputLabel from '@material-ui/core/InputLabel';
import FormHelperText from '@material-ui/core/FormHelperText';
import TextField from '@material-ui/core/TextField';
import styled from 'styled-components';

// Disable eslint error on global Drupal //
/* global Drupal b:writable */

export class CkeditorWidget extends Component {

  constructor(props) {
    super(props);
    this.widgetRef = React.createRef();

    this.state = {
      showSummary: false,
      summaryText: 'Edit Summary',
      value: this.props.defaultValue && this.props.defaultValue.length ? this.props.defaultValue[0].value : '',
      format: this.props.defaultValue && this.props.defaultValue.length ? this.props.defaultValue[0].format : Object.keys(this.props.settings.allowed_formats)[0],
      summary: this.props.defaultValue && this.props.defaultValue.length ? this.props.defaultValue[0].summary : '',
    };

    this.onEditorChange = this.onEditorChange.bind(this);
    this.onTextAreaChange = this.onTextAreaChange.bind(this);
    this.onSummaryChange = this.onSummaryChange.bind(this);
    this.onFormatChange = this.onFormatChange.bind(this);
    this.showHideSummary = this.showHideSummary.bind(this);
    this.unlockSnapshot = this.unlockSnapshot.bind(this);
    this.getSummaryInput = this.getSummaryInput.bind(this);
    this.addListeners = this.addListeners.bind(this);
  }

  componentDidMount() {
    try {
      CKEDITOR.on('instanceReady', this.addListeners);
      Drupal.behaviors.editor.attach(this.widgetRef.current, window.drupalSettings);

      Object.keys(Drupal.editors).map(editorId => {
        Drupal.editors[editorId].onChange(this.textareaRef, this.onEditorChange.bind(undefined, this.textareaRef));
      })
    }
    catch (error) {
    }
  }

  addListeners() {
    CKEDITOR.instances[`${this.props.fieldId}-text-area`].on('unlockSnapshot', this.unlockSnapshot.bind(undefined, this.textareaRef));
    CKEDITOR.instances[`${this.props.fieldId}-text-area`].on("key", this.unlockSnapshot.bind(undefined, this.textareaRef));
  }

  unlockSnapshot(element, snapshot) {
    // Timeout to let the keyed in value to be added to the data.
    setTimeout(() => {
      if (this.state.value != snapshot.editor.getData()) {
        this.onEditorChange(element, snapshot.editor.getData());
      }
    }, 250);
  }

  componentWillUnmount() {
    try {
      Drupal.behaviors.editor.detach(this.widgetRef.current, window.drupalSettings, 'destroy');
      CKEDITOR.removeListener('instanceReady', this.addListeners);
    }
    catch (error) {
    }
  }

  onFieldChange(value, format, summary) {
    const newValue = [{
      value: value,
      format: format
    }];
    if (this.props.settings.summary) {
      newValue[0].summary = summary;
    }
    this.props.onFieldChange(newValue);
  }

  onEditorChange(element, newValue) {
    this.onFieldChange(newValue, this.state.format, this.state.summary);
    this.setState({value: newValue});
  }

  onTextAreaChange(event) {
    this.onEditorChange(event, event.target.value);
  }

  onSummaryChange(event) {
    this.onFieldChange(this.state.value, this.state.format, event.target.value);
    this.setState({summary: event.target.value});
  }

  onFormatChange(event) {
    this.onFieldChange(this.state.value, event.target.value, this.state.summary);
    this.setState({format: event.target.value});
  }

  showHideSummary() {
    this.setState(prevState => ({
      showSummary: !prevState.showSummary,
      summaryText: prevState.showSummary ? 'Edit Summary' : 'Hide summary'
    }));
  }

  getSummaryInput() {
    return (
      <div style={{display: this.state.showSummary ? 'block' : 'none'}}>
        <TextField
          label={`${this.props.settings.label} Summary`}
          id={`${this.props.fieldId}-summary`}
          defaultValue={this.state.summary}
          onChange={this.onSummaryChange}
          fullWidth
          variant="outlined"
        />
        <FormHelperText>
          Leave blank to use trimmed value of full text as the summary.
        </FormHelperText>
      </div>
    )
  }

  render() {
    return (
      <FormGroup ref={this.widgetRef}>
        {this.props.settings.summary && this.getSummaryInput()}

        <CkeditorWrapper>
          <InputLabel htmlFor={`${this.props.fieldId}-text-area`}>
            {this.props.settings.label}
            {this.props.settings.summary &&
            <button
              type="button"
              className="link"
              onClick={() => this.showHideSummary()}
            >
              {this.state.summaryText}
            </button>
            }
          </InputLabel>

          <textarea
            ref={elem => this.textareaRef = elem}
            id={`${this.props.fieldId}-text-area`}
            defaultValue={this.state.value}
            onChange={this.onTextAreaChange}
            rows="8"
            style={{width: "100%"}}
          />
          <div className="filter-wrapper">
            <div className="form-item">
              <label htmlFor={`${this.props.fieldId}-text-area`}>Text
                Format</label>
              <select
                data-editor-for={`${this.props.fieldId}-text-area`}
                defaultValue={this.state.format}
                onChange={this.onFormatChange}
                style={{marginLeft: '10px'}}
              >
                {Object.keys(this.props.settings.allowed_formats).map(formatId =>
                  <option
                    key={`${this.props.fieldId}-${formatId}`}
                    value={formatId}
                  >
                    {this.props.settings.allowed_formats[formatId]}
                  </option>
                )}
              </select>
            </div>
          </div>
        </CkeditorWrapper>
      </FormGroup>
    )
  }
}

const CkeditorWrapper = styled.div`
  .cke_top {
    position: sticky;
    top: 0 !important;
  }
`;
