import React, {useEffect, useState} from "react";
import styled from 'styled-components';
import {FlexDiv} from "../../Atoms/FlexDiv";
import {XButton} from "../../Atoms/XButton";
import {Loader} from "../../Atoms/Loader";
import {ErrorBoundary} from "../../Atoms/ErrorBoundary";
import {SortableContainer, SortableElement} from "react-sortable-hoc";

const arrayMove = require('array-move');

const PreviewContainer = styled.div`
  border: 1px solid #dbdbdb;
  position: relative;
  max-width: 200px;
  margin: 0 10px 10px 0;
  min-height: 100px;
  min-width: 100px;
  z-index: 999;
`;

const PreviewImageTag = styled.img`
  height: 180px;
  object-fit: contain;
  object-position: center center;
  max-width: 100%;
`;

const SortableItem = SortableElement(({item, delta, onRemove}) => (
  <MediaItem
    mid={item.target_id}
    onRemove={onRemove}
    delta={delta}
  />
));

const SortableList = SortableContainer(({items, onRemoveItem}) => {
  return (
    <FlexDiv style={{flexWrap: 'wrap'}}>
      {items.map((item, delta) => (
        <SortableItem
          key={item.target_uuid}
          index={delta}
          item={item}
          delta={delta}
          onRemove={onRemoveItem}
        />
      ))}
    </FlexDiv>
  );
});

/**
 * List of media items for media widget.
 */
export const MediaList = ({selectedItems, onRemove, updateOrder}) => {

  const onSortEnd = ({oldIndex, newIndex}) => {
    updateOrder(arrayMove(selectedItems, oldIndex, newIndex));
  }

  return (
    <SortableList
      axis="xy"
      transitionDuration={0}
      items={selectedItems}
      onSortEnd={onSortEnd}
      onRemoveItem={onRemove}
    />
  )
}

/**
 * Individual media item.
 */
const MediaItem = ({mid, delta, onRemove}) => {
  const [mediaData, setMediaData] = useState(false);

  useEffect(() => {
    if (!mediaData) {
      // Fetch the media data. If the content is not json or the page fails, it
      // will just bail.
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
      <ErrorBoundary
        errorMessage={
          <div style={{padding: '20px'}}>
            Unable to provide a preview of media.
          </div>
        }
      >
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
