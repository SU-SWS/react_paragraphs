import React from 'react';
import styled from "styled-components";

const StyledDiv = styled.div`
  display: flex;
  flex-wrap: nowrap;
  width: 100%;
  justify-content: ${props => (props.justifyContent ? props.justifyContent : 'initial')};
  align-items: ${props => (props.alignItems ? props.alignItems : 'initial')};
  flex-direction: ${props => (props.column ? 'column' : 'row')};
`;

export const FlexDiv = ({justifyContents, alignItems, column, ...props}) => {
  return (
    <StyledDiv
      justifyContent={justifyContents}
      alignItems={alignItems}
      column={column}
      {...props}
    />
  )
};
