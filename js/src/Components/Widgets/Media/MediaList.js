import React, {useEffect, useState} from "react";
import styled from 'styled-components';
import {FlexDiv} from "../../Atoms/FlexDiv";
import {XButton} from "../../Atoms/XButton";
import {Loader} from "../../Atoms/Loader";

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

const MediaItem = ({mid, delta, onRemove}) => {
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
      <XButton
        title="Remove Media Item"
        onClick={() => onRemove(delta)}
        greyIcon
      />

      {typeof mediaData.message === 'undefined' &&
      <>
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
      </>
      }

      {typeof mediaData.message !== 'undefined' &&
      // When the media entity doesn't exist or some communication error occurs.
      <div style={{padding: '20px'}}>Unable to provide a preview of media.</div>
      }

    </PreviewContainer>
  )
}
