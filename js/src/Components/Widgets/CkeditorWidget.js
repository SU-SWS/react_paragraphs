import React, {Component} from 'react';
import FormGroup from '@mui/material/FormGroup';
import InputLabel from '@mui/material/InputLabel';
import FormHelperText from '@mui/material/FormHelperText';
import TextField from '@mui/material/TextField';

// Disable eslint error on global Drupal //
/* global Drupal b:writable */

export class CkeditorWidget extends Component {

  /**
   * Constructor.
   */
  constructor(props) {
    super(props);
    this.widgetRef = React.createRef();

    this.state = {
      showSummary: false,
      summaryText: 'Edit Summary',
      value: this.getDefaultValue('value'),
      format: this.getDefaultValue('format', Object.keys(this.props.settings.allowed_formats)[0]),
      summary: this.getDefaultValue('summary'),
    };

    this.onEditorChange = this.onEditorChange.bind(this);
    this.getSummaryInput = this.getSummaryInput.bind(this);
    this.addListeners = this.addListeners.bind(this);
    this.updateState = this.updateState.bind(this);
  }

  /**
   * Get the default value without the value being null or undefined.
   *
   * @param key
   *   Key of the default value to obtain.
   * @param defaultValue
   *   The fallback value to use.
   *
   * @returns {string|*}
   */
  getDefaultValue(key, defaultValue = '') {
    const value = this.props.defaultValue && this.props.defaultValue.length ? this.props.defaultValue[0][key] : defaultValue;
    return (value === null || value === undefined) ? defaultValue : value;
  }

  /**
   * After the component mounts, apply the ckeditor.
   */
  componentDidMount() {
    try {
      // Apply listeners to the ckeditor text area field. When the ckeditor
      // format is changed, `instanceReady` is called again.
      CKEDITOR.on('instanceReady', this.addListeners);
      Drupal.behaviors.editor.attach(this.widgetRef.current, window.drupalSettings);

      // Add a scroll listener to allow us to fix the ckeditor toolbar.
      document.getElementsByClassName('MuiPaper-root')[0].addEventListener('scroll', this.modalScroll);
    }
    catch (error) {
    }
  }

  /**
   * Remove listeners before the component is about the be removed.
   */
  componentWillUnmount() {
    try {
      document.getElementsByClassName('MuiPaper-root')[0].removeEventListener('scroll', this.modalScroll);

      Drupal.behaviors.editor.detach(this.widgetRef.current, window.drupalSettings, 'destroy');
      CKEDITOR.removeListener('instanceReady', this.addListeners);
    }
    catch (error) {
    }
  }

  /**
   * Add the ckeditor listeners for the various actions that occur when editing.
   */
  addListeners() {
    // UnlockSnapshot is an event that is triggered when a button in the toolbar
    // or media is added to the wysiwyg. Key is the only event triggered when
    // the ckeditor is in "view source" mode.
    CKEDITOR.instances[`${this.props.fieldId}-text-area`].on('unlockSnapshot', this.onEditorChange);
    CKEDITOR.instances[`${this.props.fieldId}-text-area`].on('key', this.onEditorChange);
    CKEDITOR.instances[`${this.props.fieldId}-text-area`].on('change', this.onEditorChange);
  }

  /**
   * After a user changes something in the UI, pass it up to the widget manager.
   *
   * @param key
   *   The key value to change.
   * @param value
   *   Changed value.
   */
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

  /**
   * After the ckeditor has changed, use timeout to reduce the activity.
   *
   * @param snapshot
   *   Ckeditor event snapshot.
   */
  onEditorChange(snapshot) {
    clearTimeout(this.editorTimeout);

    // Make a new timeout set to go off in 200ms.
    // This reduces the unnecessary calls since there are multiple event
    // listeners on the ckeditor area.
    this.editorTimeout = setTimeout(() => {
      // Make sure there the data in the wysiwyg is different than what we
      // already have stored.
      if (snapshot.editor.getData() !== this.state.value) {
        this.updateState('value', snapshot.editor.getData());
      }
    }, 200);
  }

  /**
   * If the settings allow a summary, provide a textfield for that.
   */
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

  /**
   * When the user scrolls within the modal dialog, fix the ckeditor toolbar.
   *
   * @param e
   */
  modalScroll = (e) => {
    const dialogTitleBounding = e.target.getElementsByClassName('MuiDialogTitle-root')[0].getBoundingClientRect();
    const editor = this.widgetRef.current.getElementsByClassName('cke')[0];

    const toolbar = editor.getElementsByClassName('cke_top')[0];
    const editorBounding = editor.getBoundingClientRect();

    toolbar.style.width = null;
    toolbar.style.position = null;
    toolbar.style.top = null;
    editor.getElementsByClassName('cke_contents')[0].style['margin-top'] = null;

    // The top of the ckeditor container is above the bottom of the dialog
    // title and the bottom of the editor is still within view. We don't want
    // to do anything if the bottom of the editor is too far up on the scroll.
    if (
      editor.getBoundingClientRect().top < dialogTitleBounding.bottom &&
      editorBounding.bottom - 100 > dialogTitleBounding.bottom
    ) {
      toolbar.style.width = (editorBounding.width - 18) + 'px';
      toolbar.style.position = 'fixed';
      toolbar.style.top = dialogTitleBounding.bottom + 'px';

      // Add some margin top to the contents to avoid the input area jumping.
      editor.getElementsByClassName('cke_contents')[0].style['margin-top'] = toolbar.getBoundingClientRect().height + 'px';
    }
  }

  /**
   * Rendering the component.
   */
  render() {
    return (
      <FormGroup ref={this.widgetRef}>
        {this.getSummaryInput()}

        <div className="ckeditor-react">
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
        </div>

        {this.props.settings.help.length > 1 &&
          <FormHelperText dangerouslySetInnerHTML={{__html: this.props.settings.help}}/>
        }
      </FormGroup>
    )
  }
}
