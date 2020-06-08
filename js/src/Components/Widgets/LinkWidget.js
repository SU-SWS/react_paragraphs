import React, {useState} from 'react';
import TextField from '@material-ui/core/TextField';
import Button from '@material-ui/core/Button';
import FormGroup from '@material-ui/core/FormGroup';
import FormControl from '@material-ui/core/FormControl';
import FormLabel from '@material-ui/core/FormLabel';
import {FormHelperText} from "@material-ui/core";
import Autocomplete from '@material-ui/lab/Autocomplete';

export const LinkWidget = ({fieldId, defaultValue, onFieldChange, settings}) => {
  let timeout;
  let initialCondition = defaultValue;
  const [urlSuggestions, setSuggestions] = useState([]);
  const [fieldValues, setValues] = useState(initialCondition);
  const emptyLinkValue = {
      uri: '',
      title: '',
      options: [],
      target_uuid: false
  };

  if (initialCondition === undefined){
    initialCondition = [];
    initialCondition.push(emptyLinkValue);
  }

  const alterValues = (values) => {
    const newState = [...fieldValues];
    newState[values.delta].title = values.title;
    newState[values.delta].uri = values.uri;
    onFieldChange(newState);
  }

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
        newUri.search(/[a-z0-9]:\/\//) >= 0
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
  const suggestionPicked = (e, selectedValue, delta) => {
    alterValues({
      title: fieldValues[delta].title,
      uri: getUriFromString(selectedValue === null ? '' : selectedValue.value),
      delta: delta
    });
  };

  /**
   * When the textfield on the URI blurs, that's when we want to trigger the field change and pass it up to the manager.
   */
  const onUriBlur = (e, delta) => {
    alterValues({
      title: fieldValues[delta].title,
      uri: getUriFromString(e.target.value),
      delta: delta
    });
  }

  /**
   * To support cardinality, we will need an "Add another" button.
   */
  const addAnotherButton = () => {
    if ((settings.cardinality == -1) || (settings.cardinality > fieldValues.length )) {
      return (
      <div>
        <Button variant="outlined" style={{marginBottom: '20px'}} onClick={addAnother}>
          Add Another Link
        </Button>
      </div>
      );
    }
  }

  /**
   * Handler for the addAnotherButton
   */
  const addAnother = () => {
    const newState = fieldValues.concat(emptyLinkValue);
    setValues(newState);
  }

  /**
   * If we add more links, we need a way of removing them, but never remove link 0.
   */
  const removeLinkButton = (delta) => {
    if (delta > 0){
      return (
        <Button variant="outlined" style={{margin: '10px'}} onClick={() => removeLink(delta)} >
          Remove
        </Button>
      );
    }
  }

  /**
   * Handler for removing a link.
   */
  const removeLink = (delta) => {
    if (fieldValues[delta] !== undefined){
      let removed = fieldValues.splice(delta, 1);
      onFieldChange(fieldValues);
    }
  }

  /**
   * To support cardinality, we may need to display more than one link field.
   */
  const linkFields = fieldValues.map((link, delta) =>
    <div style={{marginBottom: '20px'}} key={delta}>
        <FormControl style={{marginBottom: '20px'}}>
        <Autocomplete
          freeSolo
          id={`${fieldId}-uri-${delta}`}
          options={urlSuggestions}
          renderOption={option => <div>{option.label}</div>}
          getOptionLabel={option => typeof option.label !== 'undefined' ? option.label : getUriAsDisplayableString(option)}
          onChange={(e, newValue) => suggestionPicked(e, newValue, delta)}
          value={getUriAsDisplayableString(fieldValues[delta].uri)}
          renderInput={params => (
            <TextField
              {...params}
              fullWidth
              label="URL"
              variant="outlined"
              helperText="Start typing the title of a piece of content to select it. You can also enter an internal path such as /foo/bar or an external URL such as http://example.com. Enter <front> to link to the front page."
              onChange={(e) => uriChanged(e.target.value)}
              required={settings.required}
              onBlur={(e) => onUriBlur(e, delta)}
            />
          )}
        />
      </FormControl>

      {settings.title !== 0 &&
      <FormControl style={{paddingBottom: '10px'}}>
        <TextField
          id={`${fieldId}-title-${delta}`}
          label="Link text"
          value={fieldValues[delta].title}
          onChange={e => alterValues({title: e.target.value, uri: fieldValues[delta].uri, delta: delta})}
          variant="outlined"
          required={typeof fieldValues[delta].uri !== 'undefined' && fieldValues[delta].uri.length >= 1}
          fullWidth
        />
      </FormControl>
      }

    {removeLinkButton(delta)}

    </div>
  );

  return (
    <FormGroup>
      <FormLabel component="legend">
        {settings.label}
      </FormLabel>

      {linkFields}

      {addAnotherButton(settings.cardinality)}

      {settings.help.length > 1 &&
      <FormHelperText>{settings.help}</FormHelperText>
      }
    </FormGroup>
  )
};
