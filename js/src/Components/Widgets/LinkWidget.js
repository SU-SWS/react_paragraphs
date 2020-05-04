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

  /**
   * When the uri changes, use a timer like a debounce and fetch some suggestions from the linkit module.
   */
  const uriChanged = (newUri) => {
    clearTimeout(timeout);

    // Make a new timeout set to go off in 800ms
    timeout = setTimeout(() => {

      // If the user enters a url that starts with some characters, we dont want to fetch suggestions that will just
      // be empty anyways. This includes absolute urls, <front> and relative urls.
      if (
        newUri.substr(0, 1) === '/' ||
        newUri.substr(0, 1) === '<' ||
        newUri.substr(0, 4) === 'http'
      ) {
        setSuggestions([]);
        return;
      }
      fetch(`${settings.autocomplete}?q=${newUri}`)
        .then(response => response.json())
        .then(suggestionResults => setSuggestions(suggestionResults));

    }, 600);
  };

  const parseUrl = (url) => {
    let parser = document.createElement('a');
    // Let the browser do the work
    parser.href = url;

    // If the user is linking to the same site they are on, force the link to
    // be relative.
    if (parser.protocol === window.location.protocol && parser.host === window.location.host) {
      if (url.substr(0, 1) !== '/') {
        parser.href = '/' + url;
      }
      return {
        protocol: null,
        pathname: decodeURIComponent(parser.pathname + parser.search + parser.hash)
      };
    }
    return {
      protocol: parser.protocol,
      pathname: parser.pathname + parser.search + parser.hash
    };
  };

  const getUriFromString = (userString) => {
    var uri = userString.trim();
    if (uri.length === 0) {
      return uri;
    }
    const matchedEntity = userString.match(/.+\s\(([^\)]+)\)/);
    const parsedUrl = parseUrl(userString);

    if (matchedEntity && matchedEntity.length === 2) {
      uri = `entity:node/${matchedEntity[1]}`
    }
    else if (parsedUrl.protocol === null) {
      userString = parsedUrl.pathname;
      if (userString.trim().indexOf('<front>') === 0) {
        userString = '/' + userString.substr(7);
      }
      userString = userString.toLowerCase();

      if (!['/', '?', '#'].includes(userString.substr(0, 1))) {
        userString = `/${userString}`
      }
      uri = `internal:${userString}`;
    }

    return uri;
  };

  const getUriAsDisplayableString = (uri) => {
    const parsedUri = parseUrl(uri);
    var displayString = uri;

    if (parsedUri.protocol === 'internal:') {
      displayString = uri.split(':', 2)[1];

      if (parsedUri.pathname === '/') {
        displayString = '<front>' + uri.substr(uri.indexOf('/') + 1);
      }
      else if (parsedUri.pathname.indexOf('/<front>') >= 0) {
        // If the user inputs `<front>#some-anchor`, the saved data on the entity is saved as
        // `internal:/<front>#some-anchor` and the pathname is `/<front>#some-anchor`. We want
        // to clean that up for users.
        displayString = parsedUri.pathname.substr(1);
      }
    }
    else if (parsedUri.protocol === 'entity:') {
      // todo: get the entity label somehow.
      displayString = '/' + uri.split(':', 2)[1];
    }
    return displayString;
  };

  /**
   * A suggestion was picked, pass that up to the manager to store the value.
   */
  const suggestionPicked = (e, selectedValue) => {
    onFieldChange([{
      title: defaultFieldValue.title,
      uri: getUriFromString(selectedValue === null ? '' : selectedValue.value)
    }]);
  };

  /**
   * When the textfield on the URI blurs, that's when we want to trigger the field change and pass it up to the manager.
   */
  const onUriBlur = (e) => {
    onFieldChange([{
      title: defaultFieldValue.title,
      uri: getUriFromString(e.target.value)
    }]);
  }

  return (
    <FormGroup>
      <FormLabel component="legend">
        {settings.label}
      </FormLabel>

      <FormControl style={{marginBottom: '20px'}}>
        <Autocomplete
          freeSolo
          id={`${fieldId}-uri`}
          options={urlSuggestions}
          renderOption={option => <div>{option.label}</div>}
          getOptionLabel={option => typeof option.label !== 'undefined' ? option.label : getUriAsDisplayableString(option)}
          onChange={suggestionPicked}
          value={getUriAsDisplayableString(defaultFieldValue.uri)}
          renderInput={params => (
            <TextField
              {...params}
              fullWidth
              label="URL"
              variant="outlined"
              helperText="Start typing the title of a piece of content to select it. You can also enter an internal path such as /foo/bar or an external URL such as http://example.com. Enter <front> to link to the front page."
              onChange={e => uriChanged(e.target.value)}
              required={settings.required}
              onBlur={onUriBlur}
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
          onChange={e => onFieldChange([{title: e.target.value, uri: defaultFieldValue.uri}])}
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
