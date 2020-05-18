import React, {useState} from 'react';
import {Draggable} from "react-beautiful-dnd";
import styled from 'styled-components';
import {ParagraphForm} from "./ParagraphForm";
import {WidgetContext} from "../Contexts/WidgetManager";
import {DropButtons} from "./DropButtons"
import {ConfirmDialog} from "./Atoms/ConfirmDialog";

const ParagraphWrapper = styled.div`
  margin: 5px;
  border: 1px solid #BDBDBD;
  width: 0;
  height: 80px;
  line-height: 80px;
  padding: 0px 85px 0px 20px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  position: relative;
  flex: ${props => props.itemWidth} 1 0;
  background: ${props => props.isDragging ? '#dafcdf' : '#fff'};

  .img-helper {
    display: inline-block;
    height: 100%;
    vertical-align: middle;
  }

  img {
    vertical-align: middle;
  }

  .dropbutton-wrapper {
    position: absolute;
    top: calc(50% - 13px);
    right: 5px;
  }
`;

const ItemIcon = styled.img`
  max-height: 35px;
  max-width: 35px;
  margin-right: 2px;
`;


export const RowItem = ({item,id, isDraggable, index}) => {

  const [modalOpen, setModalOpen] = useState(typeof item.isNew === 'undefined' ? false : true);
  const [deleteConfirmOpen, setDeleteConfirmOpen] = useState(false);

  const getItemIcon = (availableTools) => {
    let iconSrc = 'data:image/png;base64,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';

    const tool = availableTools.find(tool => tool.id === item.entity.type[0].target_id);
    if (tool.icon) {
      iconSrc = tool.icon;
    }

    return <ItemIcon
      className="item-icon"
      role="presentation"
      alt=""
      src={iconSrc}
    />;
  };


  return (
    <Draggable
      draggableId={id}
      index={index}
      isDragDisabled={!isDraggable}
    >
      {(provided, snapshot) =>
        <WidgetContext.Consumer>
          {widgetContext =>
            <ParagraphWrapper
              ref={provided.innerRef}
              {...provided.draggableProps}
              {...provided.dragHandleProps}
              isDragging={snapshot.isDragging}
              id={id}
              itemWidth={item.width}
            >
              <span className="img-helper"></span>
              {getItemIcon(widgetContext.tools)}
              {item.admin_title}

              <DropButtons
                style={{marginRight: '6px'}}
                buttons={[
                  <button
                    type="button"
                    className="button"
                    onClick={() => setModalOpen(!modalOpen)}
                  >
                    Edit<span
                    className="visually-hidden">{item.admin_title}</span>
                  </button>,
                  <button
                    type="button"
                    className="button"
                    onClick={() => setDeleteConfirmOpen(true)}
                  >
                    Delete
                  </button>,
                ]}
              />

              <ParagraphForm
                open={modalOpen}
                item={item}
                onClose={setModalOpen}
                widgetContext={widgetContext}
              />

              <ConfirmDialog
                open={deleteConfirmOpen}
                title={`Delete ${item.admin_title}?`}
                dialog="This action can not be undone."
                onCancel={() => setDeleteConfirmOpen(false)}
                onConfirm={() => {
                  setDeleteConfirmOpen(false);
                  widgetContext.removeRowItem(id)
                }}
              />

            </ParagraphWrapper>
          }
        </WidgetContext.Consumer>
      }
    </Draggable>
  )
};
