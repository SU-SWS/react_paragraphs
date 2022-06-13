import React from 'react';
import {Droppable, Draggable} from "react-beautiful-dnd";
import {WidgetContext} from "../Contexts/WidgetManager";
import {FlexDiv} from "./Atoms/FlexDiv";

export const Toolbox = ({tools}) => {
  return (
    <div className="border border-solid border-[#C0C0C0] bg-[#F0F0EE] p-5 min-h-[calc(100vh-120px)] sticky t-[80px] -mt-8 -mr-5 -mb-5 ml-5 w-52 max-w-52 overflow-auto">
      <h3 className="fieldset-legend">Drag and drop Paragraphs into Rows</h3>
      <Droppable
        droppableId="toolbox"
        type="item"
        direction="horizontal"
        isDropDisabled={true}
      >
        {provided => (
          <div
            {...provided.droppableProps}
            ref={provided.innerRef}
          >
            <FlexDiv column={true}>
              {tools.map((tool, weight) => (
                <Tool
                  key={`tool-${tool.id}`}
                  weight={weight}
                  {...tool}
                />
              ))}
              {provided.placeholder}
            </FlexDiv>
          </div>
        )}
      </Droppable>
    </div>
  )
};

const Tool = ({weight, id, label, icon}) => {

  if (!icon) {
    icon = 'data:image/png;base64,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';
  }

  return (
    <WidgetContext.Consumer>
      {widgetContext =>

        <Draggable
          draggableId={id}
          index={weight}
        >
          {provided => (
            <div
              ref={provided.innerRef}
              {...provided.draggableProps}
              {...provided.dragHandleProps}
              onDoubleClick={(e) => widgetContext.addToolToBottom(id, e)}
              id={`tool-${id}`}
              className="bg-white m-2.5 p-2.5 border border-solid border-[#ccc] w-[100px] min-h-[75px] text-center text-slate-600 text-base font-semibold shadow-md"
            >
              <img
                className="max-w-[80px] max-h-[40px] mx-auto mt-1"
                src={icon}
                alt=""
              />

              <div className="label">{label}</div>
            </div>
          )}
        </Draggable>

      }
    </WidgetContext.Consumer>
  )

};
