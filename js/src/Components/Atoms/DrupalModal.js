import React from "react";
import Modal from "react-modal";
import styled from "styled-components";
import {XButton} from "./XButton";

const ModalHeader = styled.div`
  padding: 15px 49px 15px 15px;
  border-top-left-radius: 5px;
  border-top-right-radius: 5px;
  background: #6b6b6b;
  position: relative;

  span {
    margin: 0;
    color: #fff;
    font-size: 1.231em;
    font-weight: 600;
  }
`;

const ModalContents = styled.div`
  overflow-y: scroll;
  height: 100%;
`;


Modal.defaultStyles.overlay.zIndex = 99;
Modal.defaultStyles.overlay.backgroundColor = 'rgba(0, 0, 0, .7)';

export const DrupalModal = (props) => {
  const modalStyles = {
    content: {
      top: "10%",
      left: "20%",
      overflow: "auto",
      width: "60%",
      height: "80%",
      padding: "0",
      display: "flex",
      flexDirection: "column"
    }
  };

  const smallModalStyles = {content: {...modalStyles.content}};
  smallModalStyles.content.width = "400px";
  smallModalStyles.content.height = "250px";

  return (
    <Modal
      style={props.smallModal ? smallModalStyles : modalStyles}
      isOpen={props.isOpen}
      onRequestClose={props.onRequestClose}
    >
      <ModalHeader>
        <span>
          {props.contentLabel}
        </span>
        <XButton onClick={props.onRequestClose} title="Close"/>
      </ModalHeader>
      <ModalContents className="modal-contents" {...props.wrapperProps}>
        {props.children}
      </ModalContents>
    </Modal>
  )
};
