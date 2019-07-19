import React from 'react';
import {Droppable, Draggable} from "react-beautiful-dnd";
import styled from 'styled-components'
import {DrupalContext} from "../WidgetManager";
import {FlexDiv} from "./Atoms/FlexDiv";

const ToolboxWrapper = styled.div`
  border: 1px solid #C0C0C0;
  background: #F0F0EE;
  padding: 20px;
  max-height: calc(100vh - 120px);
  position: sticky;
  top: 80px;
  margin: -31px -19px -19px 20px;
  width: 200px;
  max-width: 200px;
  overflow: auto;
`;

const Toolwrapper = styled.div`
  background: #F9F9F9;
  margin: 10px;
  padding: 10px;
  border: 1px solid #ccc;
  box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.13);
  width: 100px;
  min-height: 75px;
  text-align: center;
  font-weight: 600;
  font-size: 15px;
  line-height:23px;
  color: #8A8D92;

  img {
    max-width: 80px
    max-height: 40px;
    margin: 0 auto 3px;
    display: block;
  }
`;

export const Toolbox = ({tools}) => {
  return (
    <ToolboxWrapper>
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
              {Object.keys(tools).map((tool_key, weight) => (
                <Tool
                  key={`tool-${tool_key}`}
                  weight={weight}
                  machine_name={tool_key}
                  label={tools[tool_key].label}
                  icon={tools[tool_key].icon}
                />
              ))}
              {provided.placeholder}
            </FlexDiv>
          </div>
        )}
      </Droppable>
    </ToolboxWrapper>
  )
};

const Tool = ({weight, machine_name, label, icon}) => {

  if (!icon) {
    icon = 'data:image/png;base64,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';
  }

  return (
    <DrupalContext.Consumer>
      {drupalContext =>

        <Draggable
          draggableId={machine_name}
          index={weight}
        >
          {provided => (
            <Toolwrapper
              ref={provided.innerRef}
              {...provided.draggableProps}
              {...provided.dragHandleProps}
              onDoubleClick={(e) => drupalContext.addToolToBottom(machine_name, e)}
              id={`tool-${machine_name}`}
            >
              <img src={icon} alt="" role="presentation"/>

              <div className="label">{label}</div>
            </Toolwrapper>
          )}
        </Draggable>

      }
    </DrupalContext.Consumer>
  )

};
