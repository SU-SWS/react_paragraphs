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

export const Row = ({id, index, isDropDisabled, itemsOrder, items, itemsPerRow, onlyRow, onRemoveRow, entity, loadedEntity}) => {
  let rowIsDragging;

  return (
    <Draggable
      draggableId={id}
      index={index}
    >
      {(provided, snapshot) => (
        <RowWrapper
          ref={provided.innerRef}
          {...provided.draggableProps}
          isDragging={snapshot.isDragging}
        >
          {rowIsDragging = snapshot.isDragging}
          <FlexDiv className="inner-row-wrapper" id={id}>
            <div className="move-row-handle" {...provided.dragHandleProps}>
              <span className="visually-hidden">
                Move Row
              </span>
            </div>

            <div className="flex-1">
              <Droppable
                droppableId={id}
                direction="horizontal"
                type="item"
                isDropDisabled={isDropDisabled}
              >
                {(provided, snapshot) => (
                  <ItemsContainer
                    ref={provided.innerRef}
                    {...provided.droppableProps}
                    isDraggingOver={snapshot.isDraggingOver}
                    rowIsDragging={rowIsDragging}
                    hasItems={itemsOrder.length > 0}
                  >

                    <React.Fragment>
                      {itemsOrder.map((itemId, index) => (
                        <RowItem
                          key={itemId}
                          id={itemId}
                          index={index}
                          item={items[itemId]}
                          isDraggable={itemsPerRow > 1}
                          isDraggingOverRow={snapshot.isDraggingOver}
                        />
                      ))}

                      {itemsOrder.length === 0 &&
                      <HelpTextPlaceholder allowedNumber={itemsPerRow}/>
                      }
                      {provided.placeholder}
                    </React.Fragment>

                  </ItemsContainer>
                )}
              </Droppable>
            </div>
          </FlexDiv>
          <RowActions
            onlyRow={onlyRow}
            onRemoveRow={onRemoveRow}
            rowId={id}
            entity={entity}
            loadedEntity={loadedEntity}
          />
        </RowWrapper>
      )}
    </Draggable>
  )
};

const HelpTextPlaceholder = ({allowedNumber = 1}) => {
  return (
    <div className="w-full text-center p-4 mx-4 border-1 border-dashed border-slate-400/50">
      Drag and drop Paragraph into Row. {allowedNumber} per Row.
    </div>
  )
};
