import React, {useState} from 'react';
import {FormHelperText} from "@mui/material";
import TextField from '@mui/material/TextField';
import FormGroup from '@mui/material/FormGroup';
import FormControl from '@mui/material/FormControl';
import FormLabel from '@mui/material/FormLabel';
import Autocomplete from '@mui/material/Autocomplete';

import {UrlFix} from "../../utils/UrlFix";

export const LinkWidget = ({
                             fieldId,
                             defaultValue,
                             onFieldChange,
                             settings
                           }) => {
  let timeout;
  let initialCondition = defaultValue;

  try {
    // Checking for the length of 0 will catch any null, non-arrays, or empty
    // arrays. This ensures the initial state is always in a good structure.
    if (initialCondition.length === 0) {
      throw 'Default condition failed';
    }
  }
  catch (e) {
    initialCondition = [{uri: '', title: ''}];
  }

  const [urlSuggestions, setSuggestions] = useState([]);
  const [fieldValues, setValues] = useState(initialCondition)

  const alterValues = (values) => {
    const newState = [...fieldValues];
    newState[values.delta].title = values.title;
    newState[values.delta].uri = values.uri;
    newState[values.delta].options = values.options;
    onFieldChange(newState);
  }

  /**
   * When the uri changes, use a timer like a debounce and fetch some
   * suggestions from the linkit module.
   */
  const uriChanged = (newUri) => {
    clearTimeout(timeout);

    // Make a new timeout set to go off in 800ms
    timeout = setTimeout(() => {

      // If the user enters a url that starts with some characters, we dont
      // want to fetch suggestions that will just be empty anyways. This
      // includes absolute urls, <front> and relative urls.
      if (
        newUri.substr(0, 1) === '/' ||
        newUri.substr(0, 1) === '<' ||
        parseUrl(newUri, 'PHP_URL_HOST')
      ) {
        setSuggestions([]);
        return;
      }
      fetch(UrlFix(`${settings.autocomplete}?q=${newUri}`))
        .then(response => response.json())
        .then(suggestionResults => setSuggestions(suggestionResults));

    }, 600);
  };

  /**
   * Similar to php parse_url function, split up the url into its parts.
   *
   * @see https://locutus.io/php/url/parse_url/
   *
   * @param url
   * @param component
   * @returns object|string
   */
  const parseUrl = (url, component) => {
    let query

    let mode = 'php'

    let key = [
      'source',
      'scheme',
      'authority',
      'userInfo',
      'user',
      'pass',
      'host',
      'port',
      'relative',
      'path',
      'directory',
      'file',
      'query',
      'fragment'
    ]

    // For loose we added one optional slash to post-scheme to catch file:///
    // (should restrict this)
    let parser = {
      php: new RegExp([
        '(?:([^:\\/?#]+):)?',
        '(?:\\/\\/()(?:(?:()(?:([^:@\\/]*):?([^:@\\/]*))?@)?([^:\\/?#]*)(?::(\\d*))?))?',
        '()',
        '(?:(()(?:(?:[^?#\\/]*\\/)*)()(?:[^?#]*))(?:\\?([^#]*))?(?:#(.*))?)'
      ].join('')),
      strict: new RegExp([
        '(?:([^:\\/?#]+):)?',
        '(?:\\/\\/((?:(([^:@\\/]*):?([^:@\\/]*))?@)?([^:\\/?#]*)(?::(\\d*))?))?',
        '((((?:[^?#\\/]*\\/)*)([^?#]*))(?:\\?([^#]*))?(?:#(.*))?)'
      ].join('')),
      loose: new RegExp([
        '(?:(?![^:@]+:[^:@\\/]*@)([^:\\/?#.]+):)?',
        '(?:\\/\\/\\/?)?',
        '((?:(([^:@\\/]*):?([^:@\\/]*))?@)?([^:\\/?#]*)(?::(\\d*))?)',
        '(((\\/(?:[^?#](?![^?#\\/]*\\.[^?#\\/.]+(?:[?#]|$)))*\\/?)?([^?#\\/]*))',
        '(?:\\?([^#]*))?(?:#(.*))?)'
      ].join(''))
    }

    let m = parser[mode].exec(url)
    let uri = {}
    let i = 14

    while (i--) {
      if (m[i]) {
        uri[key[i]] = m[i]
      }
    }

    if (component) {
      return uri[component.replace('PHP_URL_', '').toLowerCase()]
    }

    if (mode !== 'php') {
      let name = 'queryKey'

      parser = /(?:^|&)([^&=]*)=?([^&]*)/g
      uri[name] = {}
      query = uri[key[12]] || ''
      query.replace(parser, function ($0, $1, $2) {
        if ($1) {
          uri[name][$1] = $2
        }
      })
    }

    delete uri.source
    return uri
  };

  /**
   * Similar to the link field widget function in PHP.
   *
   * @see \Drupal\link\Plugin\Field\FieldWidget\LinkWidget::getUserEnteredStringAsUri()
   *
   * @param string
   * @returns {*}
   */
  const getUserEnteredStringAsUri = (string) => {
    // By default, assume the entered string is an URI.
    let uri = string.trim();

    // Detect entity autocomplete string, map to 'entity:' URI.
    const entity_id = extractEntityIdFromAutocompleteInput(uri);
    if (entity_id !== null) {
      // @todo Support entity types other than 'node'. Will be fixed in
      //    https://www.drupal.org/node/2423093.
      uri = 'entity:node/' + entity_id;
    }
    // Support linking to nothing.
    else if (['<nolink>', '<none>'].includes(string)) {
      uri = 'route:' + string;
    }
    // Detect a schemeless string, map to 'internal:' URI.
    else if (string.length > 0 && parseUrl(string, 'PHP_URL_SCHEME') === undefined) {
      // @todo '<front>' is valid input for BC reasons, may be removed by
      //   https://www.drupal.org/node/2421941
      // - '<front>' -> '/'
      // - '<front>#foo' -> '/#foo'
      if (string.indexOf('<front>') === 0) {
        string = '/' + string.substr(7);
      }

      // This validation in the normal link widget occurs on submit. We'll just
      // force all user entered links to have the first character be valid.
      if (!['/', '?', '#'].includes(string.substr(0, 1))) {
        string = '/' + string;
      }
      uri = 'internal:' + string;
    }
    else if (string.length > 0 && parseUrl(string, 'PHP_URL_HOST') === window.location.host) {
      // Drupal core does not do this. To prevent unwanted domain change issues,
      // force all entered urls of the same domain to be relative links.
      uri = 'internal:' + uri.substr(uri.indexOf(window.location.host) + window.location.host.length);
    }

    return uri;
  };

  /**
   * Similar to what the link field widget calls in the entity autocomplete.
   *
   * @see \Drupal\Core\Entity\Element\EntityAutocomplete::extractEntityIdFromAutocompleteInput()
   *
   * @param input
   * @returns {null}
   */
  const extractEntityIdFromAutocompleteInput = (input) => {
    let match = null;
    if (input.match(/.+\s\(([^\)]+)\)/)) {
      match = input.match(/.+\s\(([^\)]+)\)/)[1];
    }
    return match;
  }

  /**
   * Similar to the link field widget function in php.
   *
   * @see \Drupal\link\Plugin\Field\FieldWidget\LinkWidget::getUriAsDisplayableString()
   *
   * @param uri
   * @returns {*}
   */
  const getUriAsDisplayableString = (uri) => {
    const scheme = parseUrl(uri, 'PHP_URL_SCHEME');

    // By default, the displayable string is the URI.
    let displayable_string = uri;

    // A different displayable string may be chosen in case of the 'internal:'
    // or 'entity:' built-in schemes.
    if (scheme === 'internal') {
      let uri_reference = uri.split(':', 2)[1];

      // @todo '<front>' is valid input for BC reasons, may be removed by
      //   https://www.drupal.org/node/2421941
      let path = parseUrl(uri, 'PHP_URL_PATH')
      if (path === '/') {
        uri_reference = '<front>' + uri_reference.substr(1);
      }

      displayable_string = uri_reference;
    }
    else if (scheme === 'entity') {
      const [entity_type, entity_id] = uri.substr(7).split('/', 2);


      // Since we can't load the entity directly here, we'll just display the
      // entity type and id.
      displayable_string = `/${entity_type}/${entity_id}`
    }
    else if (scheme === 'route' && displayable_string.indexOf('route:') === 0) {
      displayable_string = displayable_string.replace('route:', '');
    }
    return displayable_string;
  };

  /**
   * A suggestion was picked, pass that up to the manager to store the value.
   */
  const suggestionPicked = (e, selectedValue, delta) => {
    alterValues({
      title: fieldValues[delta].title,
      uri: getUserEnteredStringAsUri(selectedValue === null ? '' : selectedValue.value),
      options: fieldValues[delta].options,
      delta: delta
    });
  };

  /**
   * When the textfield on the URI blurs, that's when we want to trigger the
   * field change and pass it up to the manager.
   */
  const onUriBlur = (e, delta) => {
    alterValues({
      title: fieldValues[delta].title,
      uri: getUserEnteredStringAsUri(e.target.value),
      options: fieldValues[delta].options,
      delta: delta
    });
  }

  /**
   * To support cardinality, we will need an "Add another" button.
   */
  const addAnotherButton = () => {
    if ((settings.cardinality === -1) || (settings.cardinality > fieldValues.length)) {
      return (
        <div>
          <button
            type="button"
            className="button"
            onClick={addAnother}
            style={{margin: "10px"}}>
            Add Another Link
          </button>
        </div>
      );
    }
  }

  /**
   * Handler for the addAnotherButton
   */
  const addAnother = () => {
    const newState = fieldValues.concat({uri: '', title: ''});
    setValues(newState);
  }

  /**
   * If we add more links, we need a way of removing them.
   */
  const removeLinkButton = (delta) => {
    if (fieldValues.length > 1) {
      return (
        <button
          type="button"
          className="button"
          onClick={() => removeLink(delta)}
          style={{margin: "10px"}}>
          Remove
        </button>
      );
    }
  }

  /**
   * Handler for removing a link.
   */
  const removeLink = (delta) => {
    if (typeof fieldValues[delta] !== undefined) {
      fieldValues.splice(delta, 1);
      if (fieldValues.length < 1) {
        addAnother();
      }
      onFieldChange(fieldValues);
    }
  }

console.log(urlSuggestions);
  return (
    <FormGroup className="clearfix">
      <FormLabel component="legend">
        {settings.label}
      </FormLabel>

      {fieldValues.map((link, delta) =>
        <div style={{marginBottom: '20px'}} key={delta}>
          <FormControl style={{marginBottom: '20px'}}>
            <Autocomplete
              freeSolo
              id={`${fieldId}-uri-${delta}`}
              options={urlSuggestions}
              renderOption={option => option.label}
              getOptionLabel={option => typeof option.label !== 'undefined' ? option.label : getUriAsDisplayableString(option)}
              onChange={(e, newValue) => suggestionPicked(e, newValue, delta)}
              value={getUriAsDisplayableString(fieldValues[delta].uri)}
              renderInput={params => (
                <TextField
                  {...params}
                  fullWidth
                  label="URL"
                  variant="outlined"
                  helperText={"Start typing the title of a piece of content to select it. You can also enter an internal path such as /foo/bar or an external URL such as http://example.com. Enter <front> to link to the front page."}
                  onChange={(e) => uriChanged(e.target.value)}
                  required={settings.required}
                  onBlur={(e) => onUriBlur(e, delta)}
                  inputProps={{maxlength: 2048}}
                  defaultValue={getUriAsDisplayableString(fieldValues[delta].uri)}
                />
              )}
            />
          </FormControl>

          {settings.title !== 0 &&
            <>
              <FormControl style={{paddingBottom: '10px', width: '100%'}}>
                <TextField
                  id={`${fieldId}-title-${delta}`}
                  label="Link text"
                  value={fieldValues[delta].title}
                  inputProps={{maxlength: 255}}
                  onChange={e => alterValues({
                    title: e.target.value,
                    uri: fieldValues[delta].uri,
                    delta: delta
                  })}
                  variant="outlined"
                  required={typeof fieldValues[delta].uri !== 'undefined' && fieldValues[delta].uri.length >= 1}
                  fullWidth
                />
              </FormControl>
              {settings.help.length > 1 &&
                <FormHelperText style={{paddingBottom:'10px'}}
                  dangerouslySetInnerHTML={{__html: settings.help}}/>
              }

              <FormControl style={{paddingBottom: '10px', width: '100%'}}>
                <TextField
                  id={`${fieldId}-aria-label-${delta}`}
                  label="Aria-Label Text"
                  value={fieldValues[delta]?.options?.attributes?.['aria-label']}
                  inputProps={{maxlength: 255}}
                  onChange={e => alterValues({
                    title: fieldValues[delta].title,
                    uri: fieldValues[delta].uri,
                    delta: delta,
                    options: {attributes: {'aria-label': e.target.value}}
                  })}
                  variant="outlined"
                  fullWidth
                />
                <FormHelperText>Provide more descriptive text for the link if using common repeated phrases like "Read More"</FormHelperText>
              </FormControl>
            </>
          }

          {removeLinkButton(delta)}

        </div>
      )}

      {addAnotherButton(settings.cardinality)}
    </FormGroup>
  )
};
