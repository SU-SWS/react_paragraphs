import React from "react";
import styled from "styled-components";

const ModalFooter = styled.footer`
  position:absolute;
  bottom:0;
  padding: 15px 20px;
  background: #f5f5f2;
  width: calc(100% - 40px);
  z-index: 10;
`;


export const DrupalModalFooter = (props) => {
  return <ModalFooter {...props}/>
};
