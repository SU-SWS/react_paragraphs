import React, {useState} from 'react';
import TextField from '@material-ui/core/TextField';
import FormGroup from '@material-ui/core/FormGroup';
import FormControl from '@material-ui/core/FormControl';
import FormLabel from '@material-ui/core/FormLabel';
import {FormHelperText} from "@material-ui/core";
import Autocomplete from '@material-ui/lab/Autocomplete';

export const LinkWidget = ({fieldId, defaultValue, onFieldChange, settings}) => {

  let timeout;
  const [urlSuggestions, setSuggestions] = useState([]);

  const defaultFieldValue = {
    uri: defaultValue && defaultValue.length ? defaultValue[0].uri : '',
    title: defaultValue && defaultValue.length ? defaultValue[0].title : ''
  };

  const uriChanged = (newUri) => {
    clearTimeout(timeout);

    // Make a new timeout set to go off in 800ms
    timeout = setTimeout(() => {

      onFieldChange([{
        title: defaultFieldValue.title,
        uri: getUriFromString(newUri)
      }]);

      if (newUri.substr(0, 1) === '/' || newUri.substr(0, 1) === '<') {
        setSuggestions([]);
        return;
      }
      fetch(`${settings.autocomplete}?q=${newUri}`)
        .then(response => response.json())
        .then(suggestionResults => setSuggestions(suggestionResults));

    }, 800);
  };

  const parseUrl = (url) => {
    const parse_url = /^(?:([A-Za-z]+):)?(\/{0,3})([0-9.\-A-Za-z\/]+)(?::(\d+))?(?:\/([^?#]*))?(?:\?([^#]*))?(?:#(.*))?$/;
    return parse_url.exec(url);
  };

  const getUriFromString = (userString) => {
    var uri = userString.trim();
    if (uri.length === 0) {
      return uri;
    }
    const parsedValue = userString.match(/.+\s\(([^\)]+)\)/);

    if (parsedValue && parsedValue.length === 2) {
      uri = `entity:node/${parsedValue[1]}`
    }
    else if (!parseUrl(userString) || !parseUrl(userString)[1]) {
      if (userString.trim().indexOf('<front>') === 0) {
        userString = '/' + userString.substr(7);
      }
      uri = `internal:${userString}`;
    }

    return uri;
  };

  const getUriAsDisplayableString = (uri) => {
    const parsedUri = parseUrl(uri);
    var displayString = uri;

    if (parsedUri && parsedUri[1] === 'internal') {
      displayString = uri.split(':', 2)[1];

      if (parsedUri[3] === '/') {
        displayString = '<front>' + uri.substr(uri.indexOf('/') + 1);
      }
    }
    else if (parsedUri && parsedUri[1] === 'entity') {
      // todo: get the entity label somehow.
      displayString = '/' + uri.split(':', 2)[1];
    }
    return displayString;
  };

  const suggestionPicked = (e, selectedValue) => {
    onFieldChange([{
      title: defaultFieldValue.title,
      uri: getUriFromString(selectedValue === null ? '' : selectedValue.value)
    }]);
  };

  const titleChanged = (newTitle) => {
    onFieldChange([{title: newTitle, uri: defaultFieldValue.uri}]);
  };

  return (
    <FormGroup>
      <FormLabel component="legend">
        {settings.label}
      </FormLabel>

      <FormControl style={{marginBottom: '20px'}}>
        <Autocomplete
          id={`${fieldId}-uri`}
          freeSolo
          options={urlSuggestions}
          renderOption={option => <div>{option.label}</div>}
          getOptionLabel={option => typeof option.label !== 'undefined' ? option.label : getUriAsDisplayableString(option)}
          onChange={suggestionPicked}
          defaultValue={defaultFieldValue.uri}
          renderInput={params => (
            <TextField
              {...params}
              label="URL"
              onChange={e => uriChanged(e.target.value)}
              variant="outlined"
              required={settings.required}
              helperText="Start typing the title of a piece of content to select it. You can also enter an internal path such as /foo/bar or an external URL such as http://example.com. Enter <front> to link to the front page."
              fullWidth
            />
          )}
        />
      </FormControl>

      {settings.title !== 0 &&
      <FormControl style={{paddingBottom: '10px'}}>
        <TextField
          id={`${fieldId}-title`}
          label="Link text"
          value={defaultFieldValue.title}
          onChange={e => titleChanged(e.target.value)}
          variant="outlined"
          required={typeof defaultFieldValue.uri !== 'undefined' && defaultFieldValue.uri.length >= 1}
          fullWidth
        />
      </FormControl>
      }

      {settings.help.length > 1 &&
      <FormHelperText>{settings.help}</FormHelperText>
      }
    </FormGroup>
  )
};
