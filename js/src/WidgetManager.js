import React, {Component} from "react";
import {v4 as uuidv4} from 'uuid';

export const DrupalContext = React.createContext({});

export class WidgetManager extends Component {

  functions = {
    updateParagraph: this.updateParagraph.bind(this),
    removeParagraph: this.removeParagraph.bind(this),
    onBeforeCapture: this.onBeforeCapture.bind(this),
    onDragEnd: this.onDragEnd.bind(this),
    addRow: this.addRow.bind(this),
    removeRow: this.removeRow.bind(this),
    setItemWidth: this.setItemWidth.bind(this),
    onAdminTitleChange: this.onAdminTitleChange.bind(this),
    addToolToBottom: this.addToolToBottom.bind(this),
    onDragStart: this.onDragStart.bind(this)
  };

  apiUrls = {
    baseDomain: location.origin + drupalSettings.path.baseUrl,
    formApi: 'api/react-paragraphs/{entity_type_id}/{bundle}',
    paragraphApi: 'entity/paragraph/{entity_id}'
  };

  constructor(props) {
    super(props);

    // Local development url.
    if (typeof window.drupalSettings.user === 'undefined') {
      this.apiUrls.baseDomain = 'http://docroot.cardinalsites.loc/';
    }

    let rows = {};
    let rowOrder = [];

    // Build the initial state from the given list of items.
    this.props.items.forEach(item => {
      const rowNumber = item.settings.row;
      const rowId = 'row-' + rowNumber;

      // Build each row data set if it hasn't already been created.
      if (typeof (rows[rowId]) === 'undefined') {
        rows[rowId] = {
          id: rowId,
          items: {},
          itemsOrder: [],
          isDropDisabled: true
        };
        rowOrder[rowNumber] = rowId;
      }

      // Add the current item into the appropriate row.
      const itemId = 'item-' + item.target_id;

      rows[rowId].items[itemId] = {
        id: itemId,
        target_id: item.target_id,
        index: item.settings.index,
        width: item.settings.width,
        admin_title: item.settings.admin_title,
        entity: {}
      };
      // todo: find a way to handle if multiple items in the same row have the
      // same index.
      rows[rowId].itemsOrder[item.settings.index] = itemId;
    });

    if (rowOrder.length === 0) {
      rowOrder.push('row-0');
      rows['row-0'] = {
        id: 'row-0',
        items: {},
        itemsOrder: [],
        isDropDisabled: true
      }
    }

    this.state = {
      rowCount: rowOrder.length,
      rows: rows,
      rowOrder: rowOrder,
      loadedItems: 0
    };

    window.addEventListener('beforeunload', this.handleBeforeunload.bind(this));
  }

  handleBeforeunload() {
    if (this.state.rowOrder.length === 0) {
      localStorage.removeItem(`react-data-${this.props.fieldName}`);
      return;
    }

    const storage = {...this.state};
    storage.expire = new Date().getTime() + 1000 * 15;
    localStorage.setItem(`react-data-${this.props.fieldName}`, JSON.stringify(storage));
  }

  checkLocalStorage() {
    let previousData = localStorage.getItem(`react-data-${this.props.fieldName}`);
    if (previousData) {
      // previousData = JSON.parse(previousData);
      // if (parseInt(previousData.expire) >= new Date().getTime()) {
      //   this.setState(previousData);
      //   // alert("Previous edits have been restored");
      //   return true;
      // }
    }
    return false;
  }

  componentDidMount() {
    if (this.checkLocalStorage()) {
      return;
    }

    const url = this.apiUrls.baseDomain + this.apiUrls.paragraphApi;
    this.state.rowOrder.map(rowId => {
      this.state.rows[rowId].itemsOrder.map(itemId => {

        const item = this.state.rows[rowId].items[itemId];

        fetch(url.replace('{entity_id}', item.target_id))
          .then(response => response.json())
          .then(entityData => {
            item.entity = entityData;

            const newState = {...this.state};
            newState.rows[rowId].items[itemId] = item;
            newState.loadedItems++;
            this.setState(newState);
          })
      })
    });
  }

  componentDidUpdate(prevProps, prevState, snapshot) {
    const formItemsField = document.getElementById(this.props.inputId);
    if (formItemsField) {
      const returnValue = {
        rows: this.state.rows,
        rowOrder: this.state.rowOrder,
      };
      formItemsField.value = encodeURI(JSON.stringify(returnValue));
      jQuery(formItemsField).closest('.form-item').trigger('formUpdated');
    }
  }

  onAdminTitleChange(itemId, title) {
    const newState = {...this.state};
    newState.rowOrder.map(rowId => {
      if (typeof newState.rows[rowId].items[itemId] !== 'undefined') {
        newState.rows[rowId].items[itemId].admin_title = title;
      }
    });
    this.setState(newState);
  }

  updateParagraph(item, fieldName, newValues) {
    const newRows = {...this.state.rows};

    this.state.rowOrder.map(rowId => {
      if (typeof newRows[rowId].items[item.id] !== 'undefined') {

        newRows[rowId].items[item.id].entity[fieldName] = newValues;
        this.setState({rows: newRows});
        return;
      }
    });
  }

  removeParagraph() {

  }

  addToolToBottom(item_name, e) {
    const rowOrder = this.state.rowOrder.filter(item => item != null);

    let lastRow = rowOrder[rowOrder.length - 1];

    if (this.state.rows[lastRow].itemsOrder.length > 0) {
      this.addRow(() => {
        const simulated_drag = {
          draggableId: item_name,
          destination: {
            index: 0,
            droppableId: this.state.rowOrder[this.state.rowOrder.length - 1]
          }
        };

        this.moveNewItemIntoRow(simulated_drag);
      });
    }
    else {
      const simulated_drag = {
        draggableId: item_name,
        destination: {
          index: 0,
          droppableId: this.state.rowOrder[this.state.rowOrder.length - 1]
        }
      };

      this.moveNewItemIntoRow(simulated_drag);
    }

    function moveItem() {

    }
  };

  addRow(callback) {
    const newState = {...this.state};
    newState.rowCount++;
    const newRowId = 'row-' + (newState.rowCount);
    newState.rowOrder.push(newRowId);
    newState.rows[newRowId] = {
      id: newRowId,
      items: {},
      itemsOrder: [],
      isDropDisabled: true
    };
    if (typeof callback === 'function') {
      this.setState(newState, callback);
    }
    else {
      this.setState(newState);
    }
  }

  removeRow(rowId) {
    const newState = {...this.state};
    delete newState.rows[rowId];
    newState.rowOrder.splice(newState.rowOrder.findIndex(k => k === rowId), 1);

    if (newState.rowOrder.length === 0) {
      newState.rowOrder.push('row-' + newState.rowCount);
      newState.rows['row-' + newState.rowCount] = {
        id: 'row-' + newState.rowCount,
        items: {},
        itemsOrder: [],
        isDropDisabled: true
      }
    }

    this.setState(newState);
  }

  /**
   * Before dragging an tool, create a new row if the last one is full.
   */
  onBeforeCapture(item) {
    // When dragging rows, we don't want to add another row. Only when dragging
    // items around.
    if (item.draggableId.indexOf('row') === 0) {
      return;
    }
    const lastRowId = this.state.rowOrder[this.state.rowOrder.length - 1];
    if (this.state.rows[lastRowId].itemsOrder.length >= this.props.maxItemsPerRow) {
      this.addRow();
    }
  }

  onDragStart(dragItem) {

    if (dragItem.type !== 'item') {
      return;
    }

    let itemBundle = dragItem.draggableId;
    if (dragItem.source.droppableId !== 'toolbox') {
      const item = this.state.rows[dragItem.source.droppableId].items[itemBundle];
      itemBundle = item.entity.type[0].target_id;
    }

    const newState = {...this.state};
    this.state.rowOrder.map(rowId => {
      newState.rows[rowId].isDropDisabled = !this.canDropInRow(itemBundle, rowId, dragItem.source.droppableId);
    });
    this.setState(newState);
  }

  canDropInRow(itemBundle, destinationRow, sourceRow) {
    if (destinationRow === sourceRow) {
      return true;
    }
    if (this.state.rows[destinationRow].itemsOrder.length >= this.props.maxItemsPerRow) {
      return false;
    }
    // TODO add logic to check for available columns.
    return true;
  }

  /**
   * When an item or row if finished dragging, rebuild the state.
   *
   * @param result
   */
  onDragEnd(result) {
    if (!result.destination) {
      return;
    }

    if (result.source.droppableId === 'toolbox') {
      return this.moveNewItemIntoRow(result);
    }

    if (result.source.droppableId === result.destination.droppableId) {
      if (result.type === 'item') {
        return this.moveItemWithinRow(result);
      }
      return this.moveRow(result);
    }
    this.moveItemToNewRow(result);
  }

  /**
   * Rows were sorted.
   *
   * @param result
   */
  moveRow(result) {
    const rowOrder = [...this.state.rowOrder];
    rowOrder.splice(result.source.index, 1);
    rowOrder.splice(result.destination.index, 0, result.draggableId);
    this.setState({rowOrder: rowOrder});
  }

  /**
   * An item was moved within its own row.
   *
   * @param result
   */
  moveItemWithinRow(result) {
    const newState = {...this.state};
    const itemsOrder = newState.rows[result.source.droppableId].itemsOrder;
    itemsOrder.splice(result.source.index, 1);
    itemsOrder.splice(result.destination.index, 0, result.draggableId);
    this.setState(newState);
  }

  // todo: Improve these two methods!
  /**
   * An item was moved into a new row.
   *
   * @param result
   */
  moveItemToNewRow(result) {
    const newState = {...this.state};
    newState.rows[result.source.droppableId].itemsOrder.splice(result.source.index, 1);
    newState.rows[result.destination.droppableId].itemsOrder.splice(result.destination.index, 0, result.draggableId);

    newState.rows[result.destination.droppableId].items[result.draggableId] = newState.rows[result.source.droppableId].items[result.draggableId];
    delete newState.rows[result.source.droppableId].items[result.draggableId];

    // Set the widths of all items in the destination row to equal columns.
    let equalWidths = 12 / newState.rows[result.destination.droppableId].itemsOrder.length;
    Object.keys(newState.rows[result.destination.droppableId].items).forEach(itemId => {
      newState.rows[result.destination.droppableId].items[itemId].width = equalWidths;
    });

    // Set the widths of all items in the source row to equal columns.
    equalWidths = 12 / newState.rows[result.source.droppableId].itemsOrder.length;
    Object.keys(newState.rows[result.source.droppableId].items).forEach(itemId => {
      newState.rows[result.source.droppableId].items[itemId].width = equalWidths;
    });
    this.setState(newState);
  }

  // todo: Improve these two methods!
  /**
   * An item from the toolbox was dragged into a row.
   *
   * @param result
   */
  moveNewItemIntoRow(result) {
    const newItem = this.createNewItem(result.draggableId, result.destination.index, 12);

    const newState = {...this.state};
    newState.rows[result.destination.droppableId].itemsOrder.splice(newItem.index, 0, newItem.id);
    newState.rows[result.destination.droppableId].items[newItem.id] = newItem;

    const equalWidths = 12 / newState.rows[result.destination.droppableId].itemsOrder.length;
    Object.keys(newState.rows[result.destination.droppableId].items).forEach(itemId => {
      newState.rows[result.destination.droppableId].items[itemId].width = equalWidths;
    });

    this.setState(newState);
  }

  /**
   * Create a subbed out item.
   *
   * @param machine_name
   * @param index
   * @param width
   * @returns {{width: *, index: *, id: string}}
   */
  createNewItem(machine_name, index, width) {
    const uuid = uuidv4();
    return {
      id: "new-" + uuid,
      index: index,
      width: width,
      admin_title: this.props.tools[machine_name].label,
      entity: {
        type: [{target_id: machine_name}],
      }
    }
  }

  setItemWidth(itemId, newWidth) {
    const newState = {...this.state};
    newState.rowOrder.map(rowId => {
      if (typeof newState.rows[rowId].items[itemId] !== 'undefined') {
        newState.rows[rowId].items[itemId].width = newWidth;
      }
    });
    this.setState(newState);
  }

  render() {
    if (this.state.loadedItems >= this.props.items.length) {
      return (
        <DrupalContext.Provider
          value={{
            state: this.state,
            apiUrls: this.apiUrls,
            tools: this.props.tools,
            ...this.functions
          }}>
          {this.props.children}
        </DrupalContext.Provider>
      )
    }

    return (
      <div className="loading">Loading...</div>
    )
  }

}
