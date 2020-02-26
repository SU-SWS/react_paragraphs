import React, {useEffect, useState} from 'react';
import {useDropzone} from 'react-dropzone';
import styled from "styled-components";
import exIcon from '../../icons/ex.svg';

const ThumbsContainer = styled.aside`
  display: flex;
  flexDirection: row;
  flexWrap: wrap;
  marginTop: 16;
`;

const RemoveButton = styled.button`
  background: url(${exIcon});
  height: 25px;
  width: 25px;
  cursor: pointer;
  position: absolute;
  right: -10px;
  top: -10px;
  border: 0;
  border-radius: 18px;
  background-color: rgba(0, 0, 0, .5);
  background-repeat: no-repeat;
  background-position: center;
`;

const thumb = {
  display: 'inline-flex',
  borderRadius: 2,
  border: '1px solid #eaeaea',
  marginBottom: 8,
  marginRight: 8,
  width: 100,
  height: 100,
  padding: 4,
  boxSizing: 'border-box'
};

const thumbInner = {
  display: 'flex',
  minWidth: 0,
  position: 'relative'
};

const img = {
  display: 'block',
  objectFit: "cover",
  maxWidth: "100%"
};

const Container = styled.section`
  padding: 16px;
  border: 1px solid #bfbfbf;
  border-radius: 2px;
  background: #fcfcfa;
`;

const DropArea = styled.div`
  margin: .5em;
  background: #f5f5f2;
  border: 3px dashed hsla(0, 0%, 42%, 0.65);
  border-radius: 5px;
  text-align: center;
  min-height: 100px;
  padding: 20px 20px;
`;

export const DropzoneJs = (props) => {
  const [files, setFiles] = useState([]);
  const [csrfToken, setToken] = useState('');

  const {getRootProps, getInputProps} = useDropzone({
    accept: 'image/*',
    onDrop: acceptedFiles => {
      setFiles(acceptedFiles.map(file => Object.assign(file, {
        preview: URL.createObjectURL(file)
      })));
    }
  });

  const thumbs = files.map((file, position) => (
    <div style={thumb} key={file.name}>
      <div style={thumbInner}>
        <img
          src={file.preview}
          style={img}
        />
        <RemoveButton
          type="button"
          onClick={() => {
            const newFiles = [...files];
            newFiles.splice(position, 1);
            setFiles(newFiles);
          }}
        >
          <span className="visually-hidden">Remove Item</span>
        </RemoveButton>
      </div>
    </div>
  ));

  const uploadFiles = () => {
    files.map(file => {
      file.arrayBuffer().then(buffer => {

        fetch(`/jsonapi/media/${props.fileType}/field_media_image`, {
          method: 'POST',
          headers: {
            'Accept': 'application/vnd.api+json',
            'Content-Type': 'application/octet-stream',
            'Content-Disposition': `file; filename="${files[0].name}"`,
            'X-CSRF-Token': csrfToken,
          },
          body: buffer
        })
          .then(response => response.json())
          .then(fileData => {
            createMediaEntity(props.fileType, fileData);
          })
      });
    });
  };

  const createMediaEntity = (bundle, fileJsonData) => {
    const postData = {
      data: {
        type: `media--${bundle}`,
        attributes: {name: fileJsonData.data.attributes.filename},
        relationships: {
          field_media_image: {
            data: {
              type: "file--file",
              id: fileJsonData.data.id
            }
          }
        }
      }
    };

    fetch(`/jsonapi/media/${props.fileType}`, {
      method: 'POST',
      headers: {
        'Accept': 'application/vnd.api+json',
        'Content-Type': 'application/vnd.api+json',
        'X-CSRF-Token': csrfToken,
      },
      body: JSON.stringify(postData)
    })
      .then(response => response.json())
      .then(mediaData => {
        return [mediaData.data.id];
      })
  };

  useEffect(() => () => {
    // Make sure to revoke the data uris to avoid memory leaks
    files.forEach(file => URL.revokeObjectURL(file.preview));
  }, [files]);

  if (csrfToken.length === 0) {
    fetch('/rest/session/token')
      .then(response => response.text())
      .then(token => setToken(token))
  }

  return (
    <Container className="container">
      <label className="label">Add File</label>
      <DropArea {...getRootProps()}>
        <input {...getInputProps()} />

        {files.length < 1 &&
        <p>Drop files here to upload them</p>
        }
      </DropArea>

      <ThumbsContainer>
        {thumbs}
      </ThumbsContainer>

      <button
        type="button"
        className="button"
        onClick={uploadFiles}
        disabled={files.length <= 0}
      >
        Upload and Continue
      </button>
    </Container>
  );


};
