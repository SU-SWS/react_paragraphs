import React, {useState} from 'react';
import {Draggable} from "react-beautiful-dnd";
import {Resizable} from "re-resizable";
import styled from 'styled-components'
import {ParagraphForm} from "./ParagraphForm";
import {DrupalContext} from "../WidgetManager";
import {AdminTitleField} from "./Widgets/AdminTitleField";
import {FlexDiv} from "./Atoms/FlexDiv";
import {DrupalModal} from "./Atoms/DrupalModal";
import {DrupalModalFooter} from "./Atoms/DrupalModalFooter";

const ParagraphWrapper = styled.div`
  background: ${props => props.isDragging ? 'lightgreen' : '#fff'};
`;

const ResizehandleWrapper = styled.div`
  width: 5px;
  border-right: 1px solid #000;
  height: 100%;
`;

const ItemIcon = styled.img`
  max-height:35px;
  max-width:35px;
  margin-right: 30px;
`;

const ResizeHandle = () => {
  return (
    <ResizehandleWrapper>
      <span className="visually-hidden">Resize Item</span>
    </ResizehandleWrapper>
  )
};

export const Paragraph = ({item, ...props}) => {

  const [collapsed, setCollapsed] = useState(Object.keys(item.entity).length > 1);

  const getItemIcon = (availableTools) => {
    let iconSrc = 'data:image/png;base64,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';
    if (availableTools[item.entity.type[0].target_id].icon) {
      iconSrc = availableTools[item.entity.type[0].target_id].icon;
    }

    return <ItemIcon
      className="item-icon"
      role="presentation"
      alt=""
      src={iconSrc}
    />;
  };

  const onItemResize = () => {

  };

  const openCloseModal = () => {
    setCollapsed(!collapsed);
  };

  return (
    <Draggable
      draggableId={props.id}
      index={props.index}
      isDragDisabled={!props.isDraggable}
    >
      {(provided, snapshot) => (
        <DrupalContext.Consumer>
          {drupalContext =>
            <ParagraphWrapper
              ref={provided.innerRef}
              {...provided.draggableProps}
              isDragging={snapshot.isDragging}
              id={props.id}
            >
              <Resizable
                style={{padding: "10px"}}
                enable={{
                  top: false,
                  right: props.resizableItems,
                  bottom: false,
                  left: false,
                  topRight: false,
                  bottomRight: false,
                  bottomLeft: false,
                  topLeft: false
                }}
                defaultSize={{width: '100%', height: 'auto'}}
                size={{
                  width: item.width * (props.rowWidth / 12)
                }}
                onResizeStop={onItemResize}
                handleComponent={{right: <ResizeHandle/>}}
                grid={[props.rowWidth / 12, 1]}
                minWidth={props.rowWidth / 12 * 2}
                maxWidth={(12 - props.rowItemsWidthTotal + item.width) * (props.rowWidth / 12)}
              >
                <FlexDiv
                  className="move-item-handle"
                  alignItems="center"
                  justifyContent="left"
                  {...provided.dragHandleProps}
                >

                  <FlexDiv
                    justifyContents="left"
                    alignItems="center"
                    style={{
                      color: "#4d4f53",
                      border: "1px solid #BDBDBD",
                      padding: "20px 50px",
                      lineHeight: "35px",
                      maxWidth: "150px",
                      whiteSpace: "nowrap",
                      overflow: "hidden",
                      textOverflow: "ellipsis"
                    }}
                  >
                    {getItemIcon(drupalContext.tools)}

                    {item.admin_title}

                  </FlexDiv>
                  <div>
                    <button
                      type="button"
                      className="button"
                      onClick={openCloseModal}
                      style={{marginLeft: '10px'}}
                    >
                      Edit
                      <span className="visually-hidden">
                          {item.admin_title}
                        </span>
                    </button>
                  </div>
                </FlexDiv>
              </Resizable>

              <DrupalModal
                isOpen={!collapsed}
                onRequestClose={openCloseModal}
                contentLabel={`Edit ${item.admin_title}`}
                wrapperProps={{style: {height: "calc(100% - 109px)"}}}
              >
                <form onSubmit={e => e.preventDefault()}>
                  <div style={{padding: "20px"}}>
                    <AdminTitleField
                      textField={!collapsed}
                      item={item}
                      onChange={drupalContext.onAdminTitleChange.bind(undefined, item.id)}
                    />
                    <ParagraphForm
                      apiUrls={drupalContext.apiUrls}
                      item={item}
                    />
                  </div>
                  <DrupalModalFooter>
                    <input
                      type="submit"
                      className="button button--primary"
                      onClick={(e) => {
                        e.preventDefault();
                        openCloseModal()
                      }}
                      value="Continue"
                    />

                  </DrupalModalFooter>
                </form>
              </DrupalModal>
            </ParagraphWrapper>
          }
        </DrupalContext.Consumer>
      )}
    </Draggable>
  )
};
