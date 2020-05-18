import React from 'react';
import {Draggable, Droppable} from "react-beautiful-dnd";
import styled from 'styled-components'
import {RowItem} from "./RowItem";
import {FlexDiv} from "./Atoms/FlexDiv";
import moveIcon from '../icons/move.svg';
import {RowActions} from "./Atoms/RowActions";

const ItemsContainer = styled.div`
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
  min-height: 80px;
  background: ${props =>
  props.isDraggingOver ? '#add8e6' :
    props.rowIsDragging ? '#dafcdf' :
      props.hasItems ? '#edede8' : 'transparent'};
`;

const RowWrapper = styled.div`
  display: flex;
  background: ${props => props.isDragging ? '#dafcdf' : '#fff'};

  .move-row-handle {
    background: url(${moveIcon}) no-repeat center;
    width: 40px;
    background-color: #edede8;
    flex-shrink: 0;
    border-right: 1px solid #ccc;
  }

  .inner-row-wrapper {
    border: 1px solid #CCCCCC;
  }

  .row-actions {
    display: ${props => props.isDragging ? 'none' : 'flex'};
    align-items: center;
    justify-content: left;
  }
`;

export const Row = (props) => {
  let rowIsDragging;

  return (
    <Draggable
      draggableId={props.id}
      index={props.index}
    >
      {(provided, snapshot) => (
        <RowWrapper
          ref={provided.innerRef}
          {...provided.draggableProps}
          isDragging={snapshot.isDragging}
        >
          {rowIsDragging = snapshot.isDragging}
          <FlexDiv className="inner-row-wrapper" id={props.id}>
            <div className="move-row-handle" {...provided.dragHandleProps}>
              <span className="visually-hidden">
                Move Row
              </span>
            </div>

            <div style={{flex: 1}}>
              <Droppable
                droppableId={props.id}
                direction="horizontal"
                type="item"
                isDropDisabled={props.isDropDisabled}
              >
                {(provided, snapshot) => (
                  <ItemsContainer
                    ref={provided.innerRef}
                    {...provided.droppableProps}
                    isDraggingOver={snapshot.isDraggingOver}
                    rowIsDragging={rowIsDragging}
                    hasItems={props.itemsOrder.length > 0}
                  >

                    <React.Fragment>
                      {props.itemsOrder.map((itemId, index) => (
                        <RowItem
                          key={itemId}
                          id={itemId}
                          index={index}
                          item={props.items[itemId]}
                          isDraggable={props.itemsPerRow > 1}
                          isDraggingOverRow={snapshot.isDraggingOver}
                        />
                      ))}

                      {props.itemsOrder.length === 0 &&
                      <HelpTextPlaceholder allowedNumber={props.itemsPerRow}/>
                      }
                      {provided.placeholder}
                    </React.Fragment>

                  </ItemsContainer>
                )}
              </Droppable>
            </div>
          </FlexDiv>
          <RowActions
            onlyRow={props.onlyRow}
            onRemoveRow={props.onRemoveRow}
            rowId={props.id}
            entity={props.entity}
          />
        </RowWrapper>
      )}
    </Draggable>
  )
};

const HelpTextPlaceholder = ({allowedNumber = 1}) => {
  return (
    <div style={{
      width: '100%',
      textAlign: 'center',
      padding: '15px',
      margin: '0 15px',
      border: '2px dashed #ccc'
    }}>
      Drag and drop Paragraph into Row. {allowedNumber} per Row.
    </div>
  )
};
