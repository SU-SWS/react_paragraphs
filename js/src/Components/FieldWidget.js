import React from 'react';
import {DragDropContext, Droppable} from 'react-beautiful-dnd';
import {Row} from './Row';
import {Toolbox} from "./Toolbox";
import {WidgetManager, DrupalContext} from "../WidgetManager";
import {FlexDiv} from "./Atoms/FlexDiv";

export const FieldWidget = ({inputId, fieldName, items, tools, itemsPerRow, resizableItems}) => {

  return (
    <WidgetManager
      items={items}
      inputId={inputId}
      fieldName={fieldName}
      tools={tools}
    >
      <FlexDiv alignItems={'flex-start'}>
        <DrupalContext.Consumer>
          {drupalContext => (
            <DragDropContext onDragEnd={drupalContext.onDragEnd}>
              <div style={{width:'calc(100% - 200px)', minHeight:'300px', marginTop: '20px'}}>
                <Droppable
                  droppableId="rows"
                  type="row"
                >
                  {provided => (
                    <div
                      ref={provided.innerRef}
                      {...provided.droppableProps}
                    >
                      {drupalContext.state.rowOrder.map((rowId, index) => (
                        <Row
                          key={rowId}
                          id={rowId}
                          index={index}
                          items={drupalContext.state.rows[rowId].items}
                          itemsOrder={drupalContext.state.rows[rowId].itemsOrder}
                          onRemoveRow={drupalContext.removeRow}
                          itemsPerRow={itemsPerRow}
                          resizableItems={resizableItems}
                          onlyRow={drupalContext.state.rowOrder.length === 1}
                        />
                      ))}
                      {provided.placeholder}
                    </div>
                  )}
                </Droppable>

                <button
                  type="button"
                  className="button"
                  onClick={drupalContext.addRow}
                  style={{marginTop: '30px'}}
                >
                  Add New Row
                </button>
              </div>
              <Toolbox tools={tools}/>
            </DragDropContext>
          )}
        </DrupalContext.Consumer>
      </FlexDiv>
    </WidgetManager>
  )
};
