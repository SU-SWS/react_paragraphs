import React, {Component} from 'react';
import {Draggable, Droppable} from "react-beautiful-dnd";
import styled from 'styled-components'
import {DrupalContext} from "../WidgetManager";
import {Paragraph} from "./Paragraph";
import {FlexDiv} from "./Atoms/FlexDiv";
import {ConfirmDialog} from "./Atoms/ConfirmDialog";
import moveIcon from '../icons/move.svg';

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

export class Row extends Component {

  constructor(props) {
    super(props);
    this.state = {
      rowWidth: 0,
      deleteModalOpen: false
    };
    this.rowRef = React.createRef();
  }

  render() {
    let rowIsDragging;

    return (
      <Draggable
        draggableId={this.props.id}
        index={this.props.index}
      >
        {(provided, snapshot) => (
          <RowWrapper
            ref={provided.innerRef}
            {...provided.draggableProps}
            isDragging={snapshot.isDragging}
          >
            {rowIsDragging = snapshot.isDragging}
            <FlexDiv className="inner-row-wrapper" id={this.props.id}>
              <div className="move-row-handle" {...provided.dragHandleProps}>
              <span className="visually-hidden">
                Move Row
              </span>
              </div>

              <div ref={this.rowRef} style={{flex: 1}}>
                <Droppable
                  droppableId={this.props.id}
                  direction="horizontal"
                  type="item"
                  isDropDisabled={this.props.isDropDisabled}
                >
                  {(provided, snapshot) => (
                    <ItemsContainer
                      ref={provided.innerRef}
                      {...provided.droppableProps}
                      isDraggingOver={snapshot.isDraggingOver}
                      rowIsDragging={rowIsDragging}
                      hasItems={this.props.itemsOrder.length > 0}
                    >
                      <DrupalContext.Consumer>
                        {drupalContext =>
                          <React.Fragment>
                            {this.props.itemsOrder.map((itemId, index) => (
                              <Paragraph
                                key={itemId}
                                id={itemId}
                                index={index}
                                item={this.props.items[itemId]}
                                isDraggable={this.props.itemsPerRow > 1}
                                isDraggingOverRow={snapshot.isDraggingOver}
                                typeLabel={drupalContext.tools[this.props.items[itemId].entity.type[0].target_id].label}
                              />
                            ))}

                            {this.props.itemsOrder.length === 0 &&
                            <HelpTextPlaceholder
                              allowedNumber={this.props.itemsPerRow}/>
                            }
                            {provided.placeholder}
                          </React.Fragment>
                        }
                      </DrupalContext.Consumer>
                    </ItemsContainer>
                  )}
                </Droppable>
              </div>
            </FlexDiv>
            <div className="row-actions">
              <button
                type="button"
                className="button"
                disabled={this.props.itemsOrder.length === 0 && this.props.onlyRow}
                onClick={() => this.setState({deleteModalOpen: true})}
                style={{marginLeft: '10px', whiteSpace: 'nowrap'}}
              >
                Delete Row
              </button>

              <ConfirmDialog
                open={this.state.deleteModalOpen}
                title="Delete this row?"
                dialog="This action can not be undone."
                onCancel={() => this.setState({deleteModalOpen: false})}
                onConfirm={() => {
                  this.setState({deleteModalOpen: false});
                  this.props.onRemoveRow(this.props.id)
                }}
              />
            </div>
          </RowWrapper>
        )}
      </Draggable>
    )
  }
}

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
