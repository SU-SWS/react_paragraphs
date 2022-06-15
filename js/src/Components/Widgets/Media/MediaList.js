import React, {useEffect, useState} from "react";
import {FlexDiv} from "../../Atoms/FlexDiv";
import {XButton} from "../../Atoms/XButton";
import {Loader} from "../../Atoms/Loader";
import {ErrorBoundary} from "../../Atoms/ErrorBoundary";
import {SortableContainer, SortableElement} from "react-sortable-hoc";
import {UrlFix} from "../../../utils/UrlFix";

const {arrayMoveImmutable} = require('array-move');

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
    updateOrder(arrayMoveImmutable(selectedItems, oldIndex, newIndex));
  }

  return (
    <SortableList
      axis="xy"
      transitionDuration={250}
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
      fetch(UrlFix(`/media/${mid}/edit?_format=json`))
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
    return (
      <div
        className="relative max-w-[200px] mr-2.5 mb-2.5 min-h-[100px] min-w-[100px] z-[999] cursor-move">
        <Loader/>
      </div>
    )
  }

  return (
    <div
      className="relative max-w-[200px] mr-2.5 mb-2.5 min-h-[100px] min-w-[100px] z-[999] cursor-move">
      <XButton
        title="Remove Media Item"
        onClick={() => onRemove(delta)}
        greyIcon
        style={{zIndex: 1000}}
      />
      {/* Wrap the preview image in an error handler. */}
      <ErrorBoundary
        errorMessage={
          <div className="p-5">
            Unable to provide a preview of media.
          </div>
        }
      >
        <PreviewImage mediaData={mediaData}/>
      </ErrorBoundary>
    </div>
  )
}

/**
 * The preview image tag and name.
 */
const PreviewImage = ({mediaData}) => {
  return (
    <>
      <div className="bg-[#ebebeb] px-5">
        <img
          src={mediaData.thumbnail[0].url}
          alt=""
          role="presentation"
          className="h-44 object-contain object-center max-w-full"
        />
      </div>
      <div className="p-1">
        {mediaData.name[0].value}
      </div>
    </>
  )
}
