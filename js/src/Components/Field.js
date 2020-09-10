import React from 'react';
import {DragDropContext, Droppable} from 'react-beautiful-dnd';
import {Row} from './Row';
import {Toolbox} from "./Toolbox";
import {WidgetManager, WidgetContext} from "../Contexts/WidgetManager";
import {FlexDiv} from "./Atoms/FlexDiv";

export const Field = ({inputId, fieldName, items, tools, itemsPerRow, resizableItems, rowBundle, existingData}) => {

  return (
    <WidgetManager
      items={items}
      inputId={inputId}
      fieldName={fieldName}
      tools={tools}
      maxItemsPerRow={itemsPerRow}
      rowBundle={rowBundle}
      existingData={existingData}
    >
      <FlexDiv alignItems={'flex-start'}>
        <WidgetContext.Consumer>
          {widgetContext => (
            <DragDropContext
              onBeforeCapture={widgetContext.onBeforeCapture}
              onDragStart={widgetContext.onDragStart}
              onDragEnd={widgetContext.onDragEnd}
              onDragUpdate={widgetContext.onDragUpdate}
            >
              <div style={{
                width: 'calc(100% - 200px)',
                minHeight: '300px',
                marginTop: '20px'
              }}>
                <Droppable
                  droppableId="rows"
                  type="row"
                >
                  {provided => (
                    <div
                      ref={provided.innerRef}
                      {...provided.droppableProps}
                    >
                      {widgetContext.state.rowOrder.map((rowId, index) => (
                        <Row
                          key={rowId}
                          id={rowId}
                          index={index}
                          itemsPerRow={itemsPerRow}
                          resizableItems={resizableItems}
                          onRemoveRow={widgetContext.removeRow}
                          onlyRow={widgetContext.state.rowOrder.length === 1}
                          {...widgetContext.state.rows[rowId]}
                        />
                      ))}
                      {provided.placeholder}
                    </div>
                  )}
                </Droppable>

                <button
                  type="button"
                  className="button"
                  onClick={widgetContext.addRow}
                  style={{marginTop: '30px'}}
                >
                  Add New Row
                </button>
              </div>

              <Toolbox tools={tools}/>
            </DragDropContext>
          )}
        </WidgetContext.Consumer>
      </FlexDiv>
    </WidgetManager>
  )
};
