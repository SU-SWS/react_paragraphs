import React from 'react';
import styled from 'styled-components';
import whiteIcon from '../../icons/ex.svg';
import greyIcon from '../../icons/grey-ex.svg';

const CloseButton = styled.button`
  position: absolute;
  top: 10px;
  right: 12px;
  width: 30px;
  height: 30px;
  margin: 0;
  padding: 0;
  -webkit-transition: all 0.1s;
  transition: all 0.1s;
  border: ${props => (props.greyIcon ? "2px solid #ccc" : "none")};
  border-radius: 20px;
  background: ${props => (props.greyIcon ? "#fff" : "transparent")};;
  
  .ui-icon {
    background: url(${props => (props.greyIcon ? greyIcon : whiteIcon)}) no-repeat center;
    display: block;
    cursor: pointer;
    width: 100%;
    height: 100%;
  }
`;

export const XButton = (props) => {
  return (
    <CloseButton type="button" {...props}>
      <span className="ui-icon"></span>
      <span className="visually-hidden">{props.title}</span>
    </CloseButton>
  )
};
