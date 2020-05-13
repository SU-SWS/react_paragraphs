import React, {Component, useEffect, useState} from "react";
import {FormHelperText} from "@material-ui/core";
import {FlexDiv} from "../Atoms/FlexDiv";
import {XButton} from "../Atoms/XButton";
import styled from 'styled-components';
import {Loader} from "../Atoms/Loader";

export class MediaLibrary extends Component {

  constructor(props) {
    super(props);

    this.openMediaLibrary = this.openMediaLibrary.bind(this);
    this.updateWidget = this.updateWidget.bind(this);
    this.removeItem = this.removeItem.bind(this);

    // Make sure the existing default value is valid. Make sure the ids are more than 0.
    this.state = {
      selectedMedia: this.props.defaultValue === null ? [] : this.props.defaultValue.filter(item => item.target_id > 0)
    };

    this.inputRef = React.createRef();
  }

  updateWidget() {
    const newState = {...this.state};
    if (this.inputRef.current.value) {
      this.inputRef.current.value.split(',').forEach(item => newState.selectedMedia.push({target_id: item}));
      this.setState(newState);
      this.updateField();
    }
  }

  updateField() {
    this.props.onFieldChange(this.state.selectedMedia);
  }

  componentDidMount() {
    jQuery(`#${this.props.fieldName}-library-update`).on('mousedown', this.updateWidget);
  }

  componentWillUnmount() {
    jQuery(`#${this.props.fieldName}-library-update`).off('mousedown');
  }

  openMediaLibrary(e) {
    e.preventDefault();

    const allowed_types = Object.keys(this.props.settings.target_bundles);
    const params = {
      hash: '',
      media_library_opener_id: 'media_library.opener.field_widget',
      media_library_allowed_types: allowed_types,
      media_library_selected_type: allowed_types[0],
      media_library_remaining: this.props.settings.cardinality - this.state.selectedMedia.length,
      media_library_opener_parameters: {
        field_widget_id: this.props.fieldName,
        entity_type_id: 'paragraph',
        bundle: this.props.bundle,
        field_name: this.props.fieldName
      }
    };

    fetch(`/react-media-library?${jQuery.param(params)}`)
      .then(response => response.json())
      .then(jsonData => {
        // The ajax response assumes the views button is the only one on the
        // page, so we have to change that because it will conflict with the
        // original button on the entity form.
        jsonData[0].settings.ajax['edit-submit-modal'] = jsonData[0].settings.ajax['edit-submit'];
        delete jsonData[0].settings.ajax['edit-submit'];

        // The ajax response includes alot of js files that we dont need. Some
        // even reset alot of functionality going on during th first merge of
        // the settings above.
        jsonData[2].data = '';
        var ajaxObject = Drupal.ajax({
          url: '',
          base: false,
          element: false,
          progress: false
        });

        ajaxObject.success(jsonData, 'success');
        document.getElementsByClassName('media-library-view')[0].getElementsByClassName('media-library-select button')[0].setAttribute("id", "edit-submit-modal");

        setTimeout(function () {
          Drupal.attachBehaviors(jQuery('#drupal-modal').parent());
        }, 200);

      })
  }

  removeItem(mid) {
    const itemIndex = this.state.selectedMedia.findIndex(item => item.target_id === mid);
    const newState = {...this.state};
    this.state.selectedMedia.splice(itemIndex, 1);
    this.setState(newState);
    this.updateField();
  }

  render() {
    return (
      <fieldset className="form-item">
        <div className="label">
          {this.props.settings.label}
        </div>

        <FlexDiv>
          {this.state.selectedMedia.map(item =>
            <MediaItemPreview
              key={item.target_id}
              mid={item.target_id}
              onRemove={this.removeItem}
            />
          )}
        </FlexDiv>

        {this.props.settings.cardinality > this.state.selectedMedia.length &&
        <input
          type="submit"
          className="button"
          onClick={this.openMediaLibrary}
          value="Add media"
          style={{margin: "10px 0"}}
        />
        }

        <FormHelperText>{this.props.settings.help}</FormHelperText>
        <input
          data-media-library-widget-value={this.props.fieldName}
          type="hidden"
          name={`${this.props.fieldName}[media_library_selection]`}
          ref={this.inputRef}
        />
        <input
          id={`${this.props.fieldName}-library-update`}
          type="submit"
          data-media-library-widget-update={this.props.fieldName}
          value="Update Media"
          name={`${this.props.fieldName}-library-update`}
          style={{display: "none"}}
        />
      </fieldset>
    )
  }
}

const MediaItemPreview = ({mid, onRemove}) => {
  const [mediaData, setMediaData] = useState(false);

  useEffect(() => {
    if (!mediaData) {
      fetch(`/media/${mid}/edit?_format=json`)
        .then(response => response.json())
        .then(jsonData => {
          if (typeof jsonData.message !== 'undefined') {
            console.error(jsonData.message);
          }
          setMediaData(jsonData);
        });
    }
  }, []);

  if (!mediaData) {
    return <Loader/>
  }

  return (
    <PreviewContainer>
      <XButton title="Remove Media Item" onClick={() => onRemove(mid)}
               greyIcon/>

      {typeof mediaData.message === 'undefined' &&
      <React.Fragment>
        <div style={{background: "#ebebeb", padding: "0 20px"}}>
          <PreviewImage
            src={mediaData.thumbnail[0].url}
            alt=""
            role="presentation"
          />
        </div>
        <div style={{padding: "5px"}}>
          {mediaData.name[0].value}
        </div>
      </React.Fragment>
      }

      {typeof mediaData.message !== 'undefined' &&
      // When the media entity doesn't exist or some communication error occurs.
      <div style={{padding: '20px'}}>Unable to provide a preview of media.</div>
      }

    </PreviewContainer>
  )
};

const PreviewContainer = styled.div`
  border: 1px solid #dbdbdb;
  position: relative;
  max-width: 200px;
  margin: 0 10px 10px 0;
`;

const PreviewImage = styled.img`
  height: 180px;
  object-fit: contain;
  object-position: center center;
  max-width: 100%;
`;
