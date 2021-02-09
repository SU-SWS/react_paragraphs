import React, {useEffect, useState} from "react";
import styled from 'styled-components';
import {FlexDiv} from "../../Atoms/FlexDiv";
import {XButton} from "../../Atoms/XButton";
import {Loader} from "../../Atoms/Loader";
import {ErrorBoundary} from "../../Atoms/ErrorBoundary";

const PreviewContainer = styled.div`
  border: 1px solid #dbdbdb;
  position: relative;
  max-width: 200px;
  margin: 0 10px 10px 0;
  min-height: 100px;
  min-width: 100px;
`;

const PreviewImageTag = styled.img`
  height: 180px;
  object-fit: contain;
  object-position: center center;
  max-width: 100%;
`;

/**
 * List of media items for media widget.
 */
export const MediaList = ({selectedItems, onRemove, updateOrder}) => {

  return (
    <FlexDiv
      style={{flexWrap: 'wrap'}}
    >
      {selectedItems.map((item, delta) =>
        <MediaItem
          key={`media-${delta}-${item.target_id}`}
          mid={item.target_id}
          delta={delta}
          onRemove={onRemove}
        />
      )}
    </FlexDiv>
  )
}

/**
 * Individual media item.
 */
const MediaItem = ({mid, delta, onRemove}) => {
  const [mediaData, setMediaData] = useState(false);

  useEffect(() => {
    if (!mediaData) {
      // Fetch the media data. If the content is not json or the page fails, it will just bail.
      fetch(`/media/${mid}/edit?_format=json`)
        .then(response => response.json())
        .then(jsonData => {
          if (typeof jsonData.message !== 'undefined') {
            console.error(jsonData.message);
          }
          // Always set the media data so that it will remove the loader tag.
          setMediaData(jsonData);
        });
    }
  }, []);

  if (!mediaData) {
    return <PreviewContainer><Loader/></PreviewContainer>
  }

  return (
    <PreviewContainer>
      <XButton
        title="Remove Media Item"
        onClick={() => onRemove(delta)}
        greyIcon
      />
      {/* Wrap the preview image in an error handler. */}
      <ErrorBoundary errorMessage={<div style={{padding: '20px'}}>Unable to provide a preview of media.</div>}>
        <PreviewImage mediaData={mediaData}/>
      </ErrorBoundary>
    </PreviewContainer>
  )
}

/**
 * The preview image tag and name.
 */
const PreviewImage = ({mediaData}) => {
  return (
    <>
      <div style={{background: "#ebebeb", padding: "0 20px"}}>
        <PreviewImageTag
          src={mediaData.thumbnail[0].url}
          alt=""
          role="presentation"
        />
      </div>
      <div style={{padding: "5px"}}>
        {mediaData.name[0].value}
      </div>
    </>
  )
}
