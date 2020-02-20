import React, {Component} from 'react';
import {Draggable, Droppable} from "react-beautiful-dnd";
import styled from 'styled-components'
import {DrupalContext} from "../WidgetManager";
import {Paragraph} from "./Paragraph";
import moveIcon from '../icons/move.svg';
import {FlexDiv} from "./Atoms/FlexDiv";
import {DrupalModal} from "./Atoms/DrupalModal";
import {DrupalModalFooter} from "./Atoms/DrupalModalFooter";

const ItemsContainer = styled.div`
  display: flex;
  align-items: center;
  flex-wrap: no-wrap;
  min-height: 80px;
  background: ${props => props.isDraggingOver ? '#add8e6' : 'transparent'};
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
    display: flex;
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

  /**
   * When the component mounts, add an event listener so we can measure the
   * container.
   */
  componentDidMount() {
    this.setState({rowWidth: this.rowRef.current.offsetWidth});
    window.addEventListener("resize", this.onWindowResize.bind(this));
  }

  /**
   * If the window is resized, measure and save the new row container width.
   */
  onWindowResize(e) {
    // Set row width very small so that the individual items in the row don't
    // prevent the row itself from shrinking. Set to a multiple of 12 since
    // the grids use 12 columns.
    this.setState({rowWidth: 12 * 2});
    this.setState({rowWidth: this.rowRef.current.offsetWidth});
  }

  render() {
    let rowItemsWidthTotal = 0;
    this.props.itemsOrder.forEach(itemId => {
      rowItemsWidthTotal += this.props.items[itemId].width;
    });

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
                  isDropDisabled={this.props.itemsOrder.length >= this.props.itemsPerRow}
                >
                  {(provided, snapshot) => (
                    <ItemsContainer
                      ref={provided.innerRef}
                      {...provided.droppableProps}
                      isDraggingOver={snapshot.isDraggingOver}
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
                                rowWidth={this.state.rowWidth}
                                rowItemsWidthTotal={rowItemsWidthTotal}
                                setItemWidth={drupalContext.setItemWidth}
                                resizableItems={this.props.resizableItems}
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

              <DrupalModal
                isOpen={this.state.deleteModalOpen}
                onRequestClose={() => this.setState({deleteModalOpen: false})}
                contentLabel={`Delete the Row?`}
                wrapperProps={{style: {height: "calc(100% - 109px)"}}}
                smallModal
              >
                <div style={{padding: "15px", textAlign: "center"}}>
                  Are you sure you wish to delete this row? This can not be
                  undone.
                </div>
                <DrupalModalFooter>
                  <button
                    type="button"
                    className="button button--primary"
                    onClick={() => {
                      this.setState({deleteModalOpen: false});
                      this.props.onRemoveRow(this.props.id)
                    }}
                  >
                    Delete
                  </button>
                  <button
                    type="button"
                    className="button"
                    onClick={() => this.setState({deleteModalOpen: false})}
                  >
                    Cancel
                  </button>
                </DrupalModalFooter>
              </DrupalModal>
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
