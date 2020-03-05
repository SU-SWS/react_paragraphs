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
    this.getSummaryInput = this.getSummaryInput.bind(this);
    this.addListeners = this.addListeners.bind(this);
    this.updateState = this.updateState.bind(this);
  }

  componentDidMount() {
    try {
      // Apply listeners to the ckeditor text area field. When the ckeditor
      // format is changed, `instanceReady` is called again.
      CKEDITOR.on('instanceReady', this.addListeners);
      Drupal.behaviors.editor.attach(this.widgetRef.current, window.drupalSettings);
    }
    catch (error) {
    }
  }

  componentWillUnmount() {
    try {
      Drupal.behaviors.editor.detach(this.widgetRef.current, window.drupalSettings, 'destroy');
      CKEDITOR.removeListener('instanceReady', this.addListeners);
    }
    catch (error) {
    }
  }

  addListeners() {
    // UnlockSnapshot is an event that is triggered when a button in the toolbar
    // or media is added to the wysiwyg. Key is the only event triggered when
    // the ckeditor is in "view source" mode.
    CKEDITOR.instances[`${this.props.fieldId}-text-area`].on('unlockSnapshot', this.onEditorChange);
    CKEDITOR.instances[`${this.props.fieldId}-text-area`].on("key", this.onEditorChange);
  }

  updateState(key, value) {
    const newState = {...this.state};
    newState[key] = value;
    this.setState(newState);

    // If the ckeditor area or the summary is empty, clear the field value.
    if (newState.value.length === 0 && newState.summary.length === 0) {
      this.props.onFieldChange([]);
      return;
    }

    // Construct the new field value to bubble up to the widget manager.
    const newValue = [{
      value: newState.value,
      format: newState.format
    }];

    if (this.props.settings.summary) {
      newValue[0].summary = newState.summary;
    }
    this.props.onFieldChange(newValue);
  }

  onEditorChange(snapshot) {
    clearTimeout(this.editorTimeout);

    // Make a new timeout set to go off in 200ms.
    // This reduces the unnecessary calls since there are multiple event
    // listeners on the ckeditor area.
    this.editorTimeout = setTimeout(() => {

      // Make sure there is some data in the wysiwyg and its different than what
      // we already have stored.
      if (snapshot.editor.getData().length && snapshot.editor.getData() !== this.state.value) {
        this.updateState('value', snapshot.editor.getData());
      }
    }, 200);
  }

  getSummaryInput() {
    // The field is not configured to accept a summary.
    if (!this.props.settings.summary) {
      return;
    }
    return (
      <div style={{display: this.state.showSummary ? 'block' : 'none'}}>
        <TextField
          label={`${this.props.settings.label} Summary`}
          id={`${this.props.fieldId}-summary`}
          defaultValue={this.state.summary}
          onChange={e => this.updateState('summary', e.target.value)}
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
        {this.getSummaryInput()}

        <CkeditorWrapper>
          <InputLabel htmlFor={`${this.props.fieldId}-text-area`}>
            {this.props.settings.label}
            {this.props.settings.summary &&
            <button
              type="button"
              className="link"
              onClick={() => this.setState({showSummary: !this.state.showSummary})}
            >
              {this.state.showSummary ? 'Hide summary' : 'Edit Summary'}
            </button>
            }
          </InputLabel>

          <textarea
            ref={elem => this.textareaRef = elem}
            id={`${this.props.fieldId}-text-area`}
            defaultValue={this.state.value}
            onChange={e => this.updateState('value', e.target.value)}
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
                onChange={e => this.updateState('format', e.target.value)}
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
