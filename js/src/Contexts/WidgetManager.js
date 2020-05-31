import React, {Component} from "react";
import {v4 as uuidv4} from 'uuid';

export const WidgetContext = React.createContext({});

export class WidgetManager extends Component {

  functions = {
    removeRowItem: this.removeRowItem.bind(this),
    onBeforeCapture: this.onBeforeCapture.bind(this),
    onDragEnd: this.onDragEnd.bind(this),
    addRow: this.addRow.bind(this),
    removeRow: this.removeRow.bind(this),
    setItemWidth: this.setItemWidth.bind(this),
    onAdminTitleChange: this.onAdminTitleChange.bind(this),
    addToolToBottom: this.addToolToBottom.bind(this),
    onDragStart: this.onDragStart.bind(this),
    getToolInformation: this.getToolInformation.bind(this),
    getEntityForm: this.getEntityForm.bind(this),
    updateRowEntity: this.updateRowEntity.bind(this),
    updateRowItemEntity: this.updateRowItemEntity.bind(this),
    loadRow: this.loadRow.bind(this),
    loadRowItem: this.loadRowItem.bind(this)
  };

  apiUrls = {
    baseDomain: location.origin + drupalSettings.path.baseUrl,
    formApi: 'api/react-paragraphs/{entity_type_id}/{bundle}',
    entityApi: 'entity/{entity_type_id}/{entity_id}',
  };

  constructor(props) {
    super(props);

    // Local development url.
    if (typeof window.drupalSettings.user === 'undefined') {
      this.apiUrls.baseDomain = localBaseDomain;
    }
    let rows = {};
    let rowOrder = [];

    this.props.items.map((row, rowIndex) => {
      const rowId = 'row-' + rowIndex;
      rowOrder[rowIndex] = rowId;
      rows[rowId] = {
        id: rowId,
        items: {},
        itemsOrder: [],
        isDropDisabled: true,
        entity: row.row.entity,
        target_id: row.row.target_id,
        loadedEntity: false,
      }

      row.rowItems.map((rowItem, itemIndex) => {
        const itemId = 'item-' + rowItem.target_id;
        rows[rowId].itemsOrder[itemIndex] = itemId;
        rows[rowId].items[itemId] = {
          id: itemId,
          target_id: rowItem.target_id,
          index: itemIndex,
          width: rowItem.settings.width,
          admin_title: rowItem.settings.admin_title,
          entity: rowItem.entity,
          loadedEntity: false,
        };
      })
    })

    if (rowOrder.length === 0) {
      rowOrder.push('row-0');
      rows['row-0'] = {
        id: 'row-0',
        items: {},
        itemsOrder: [],
        isDropDisabled: true,
        entity: {"type": [{"target_id": this.props.rowBundle}]},
        target_id: null
      }
    }

    this.state = {
      rowCount: rowOrder.length,
      rows: rows,
      rowOrder: rowOrder.filter(rowId => rowId != null),
      loadedItems: 0,
      cachedForms: {}
    };
    this.componentDidUpdate();
  }

  /**
   * Trigger form updated event for drupal confirm leave module.
   *
   * We trigger this at various steps to make this more specific about when to
   * trigger the update. We don't want to do this in componentDidUpdate because
   * if the user just loads the page we don't need to trigger the event until
   * actual changes have been made.
   */
  triggerFormUpdated() {
    const formItemsField = document.getElementById(this.props.inputId);
    jQuery(formItemsField).closest('.form-item').trigger('formUpdated');
  }

  componentDidUpdate(prevProps, prevState, snapshot) {
    const formItemsField = document.getElementById(this.props.inputId);
    if (formItemsField) {
      const returnValue = {
        rows: this.state.rows,
        rowOrder: this.state.rowOrder,
      };
      formItemsField.value = encodeURI(JSON.stringify(returnValue));
    }
  }

  getToolInformation(toolId) {
    return this.props.tools.find(tool => tool.id === toolId);
  }

  onAdminTitleChange(itemId, title) {
    const newState = {...this.state};
    newState.rowOrder.map(rowId => {
      if (typeof newState.rows[rowId].items[itemId] !== 'undefined') {
        newState.rows[rowId].items[itemId].admin_title = title;
      }
    });
    this.setState(newState);
    this.triggerFormUpdated();
  }

  removeRowItem(itemId) {
    const rowId = this.state.rowOrder.find(rowId => this.state.rows[rowId].itemsOrder.includes(itemId));
    const newState = {...this.state};

    delete newState.rows[rowId].items[itemId];
    newState.rows[rowId].itemsOrder.splice(newState.rows[rowId].itemsOrder.indexOf(itemId), 1);
    this.resetRowItemWidths(rowId, newState);
    this.setState(newState);
    this.triggerFormUpdated();
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
    this.triggerFormUpdated();
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
      isDropDisabled: true,
      entity: {"type": [{"target_id": this.props.rowBundle}]},
      target_id: null
    };
    if (typeof callback === 'function') {
      this.setState(newState, callback);
    }
    else {
      this.setState(newState);
    }
    this.triggerFormUpdated();
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
    this.triggerFormUpdated();
  }

  /**
   * Before dragging an tool, create a new row if the last one is full.
   *
   * @link https://github.com/atlassian/react-beautiful-dnd/blob/master/docs/guides/responders.md#ondragstart
   */
  onBeforeCapture(item) {
    // When dragging rows, we don't want to add another row. Only when dragging
    // items around.
    if (item.draggableId.indexOf('row') === 0) {
      return;
    }
    const lastRowId = this.state.rowOrder[this.state.rowOrder.length - 1];
    let needsNewRow = false;

    // The last row is maxed out with items, a new row is needed.
    if (this.state.rows[lastRowId].itemsOrder.length >= this.props.maxItemsPerRow) {
      needsNewRow = true;
    }
    else {
      try {
        // The last row is not full of items, but the items in the row require
        // all columns for that row. We need a new row.
        needsNewRow = !this.canDropInRow(item.draggableId, lastRowId, null);
      }
      catch (e) {
        // Nothing to do here.
      }
    }

    if (needsNewRow) {
      this.addRow();
    }
  }

  /**
   * When the drag is initiated.
   *
   * @param dragItem
   *
   * @link https://github.com/atlassian/react-beautiful-dnd/blob/master/docs/guides/responders.md#ondragstart
   */
  onDragStart(dragItem) {

    // We don't need to do anything here if the user is dragging a row.
    if (dragItem.type !== 'item') {
      return;
    }

    // Find the tool machine name if the dragging item is a tool or an existing
    // item.
    let itemBundle = dragItem.draggableId;
    if (dragItem.source.droppableId !== 'toolbox') {
      const item = this.state.rows[dragItem.source.droppableId].items[itemBundle];
      itemBundle = item.entity.type[0].target_id;
    }

    // Go through all the rows and mark them as disabled if they are full.
    const newState = {...this.state};
    this.state.rowOrder.map(rowId => {
      newState.rows[rowId].isDropDisabled = !this.canDropInRow(itemBundle, rowId, dragItem.source.droppableId);
    });
    this.setState(newState);
  }

  /**
   * Find out if the given tool can be dropped in the destination row.
   *
   * @param itemBundle
   * @param destinationRow
   * @param sourceRow
   * @returns {boolean}
   */
  canDropInRow(itemBundle, destinationRow, sourceRow) {
    // Dragging within the same row is always allowed.
    if (destinationRow === sourceRow) {
      return true;
    }
    // The destination row is already full of items.
    if (this.state.rows[destinationRow].itemsOrder.length >= this.props.maxItemsPerRow) {
      return false;
    }

    // Find out how many columns are required as minimums of the existing items.
    // If the minimum required columns is full, mark the row as full.
    const requiredColumns = this.getToolInformation(itemBundle).minWidth;
    let columnsTaken = 0;
    this.state.rows[destinationRow].itemsOrder.map(itemId => {
      const rowItemBundle = this.state.rows[destinationRow].items[itemId].entity.type[0].target_id;

      columnsTaken += this.getRequiredMinColumns(rowItemBundle);
    });
    return (12 - columnsTaken) >= requiredColumns;
  }

  /**
   * Get the number of columns required for the current tool.
   *
   * @param toolId
   * @returns {number}
   */
  getRequiredMinColumns(toolId) {
    return parseInt(this.getToolInformation(toolId).minWidth);
  }

  /**
   * When an item or row if finished dragging, rebuild the state.
   *
   * @param result
   */
  onDragEnd(result) {
    // The item wasn't dragged anywhere, bail.
    if (!result.destination) {
      return;
    }

    // When an item was dragged from the toolbox, create a new item and add it.
    if (result.source.droppableId === 'toolbox') {
      return this.moveNewItemIntoRow(result);
    }

    // When a item was dragged in the same row, or a row was dragged to reorder.
    if (result.source.droppableId === result.destination.droppableId) {
      if (result.type === 'item') {
        return this.moveItemWithinRow(result);
      }
      return this.moveRow(result);
    }

    // An item was dragged into a new row.
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

    this.resetRowItemWidths(result.destination.droppableId, newState);
    this.resetRowItemWidths(result.source.droppableId, newState);
    this.setState(newState);
  }

  // todo: Improve these two methods!
  /**
   * An item from the toolbox was dragged into a row.
   *
   * @param result
   */
  moveNewItemIntoRow(result) {
    const newItem = this.createNewRowItem(result.draggableId, result.destination.index, 12);

    const newState = {...this.state};
    newState.rows[result.destination.droppableId].itemsOrder.splice(newItem.index, 0, newItem.id);
    newState.rows[result.destination.droppableId].items[newItem.id] = newItem;

    this.resetRowItemWidths(result.destination.droppableId, newState);
    // After settings the state, the children will re-render. We can then
    // remove the indicator that the item is new.
    this.setState(newState, () => {
      const newState = {...this.state};
      delete newState.rows[result.destination.droppableId].items[newItem.id].isNew;
      this.setState(newState);
    });
  }

  resetRowItemWidths(rowId, state) {
    const equalWidths = 12 / state.rows[rowId].itemsOrder.length;

    let totalColumns = 0;
    state.rows[rowId].itemsOrder.map(itemId => {
      const rowItemBundle = state.rows[rowId].items[itemId].entity.type[0].target_id;
      state.rows[rowId].items[itemId].width = Math.max(equalWidths, this.getRequiredMinColumns(rowItemBundle));
      totalColumns += state.rows[rowId].items[itemId].width;
    });

    while (totalColumns > 12) {
      state.rows[rowId].itemsOrder.map(itemId => {
        const rowItemBundle = state.rows[rowId].items[itemId].entity.type[0].target_id;

        if (state.rows[rowId].items[itemId].width > this.getRequiredMinColumns(rowItemBundle)) {
          state.rows[rowId].items[itemId].width--;
          totalColumns--;
        }
      })
    }

  }

  /**
   * Create a subbed out item.
   *
   * @param machine_name
   * @param index
   * @param width
   * @returns {{width: *, index: *, id: string}}
   */
  createNewRowItem(machine_name, index, width) {
    const uuid = uuidv4();
    return {
      id: "new-" + uuid,
      index: index,
      width: width,
      admin_title: this.getToolInformation(machine_name).label,
      isNew: true,
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

  getEntityForm(entityType, bundle) {
    let url = this.apiUrls.baseDomain + this.apiUrls.formApi;
    url = url.replace('{entity_type_id}', entityType).replace('{bundle}', bundle);
    if (typeof this.state.cachedForms[`${entityType}-${bundle}`] !== 'undefined') {
      return this.state.cachedForms[`${entityType}-${bundle}`];
    }

    fetch(url)
      .then(response => response.json())
      .then(jsonData => this.setState(prevState => ({
        ...prevState,
        cachedForms: {
          ...prevState.cachedForms,
          [`${entityType}-${bundle}`]: jsonData
        }
      })))
      .catch(e => console.error(e));
  }

  updateRowEntity(rowId, fieldName, newValues) {
    const newState = {...this.state}
    newState.rows[rowId].entity[fieldName] = newValues;
    this.setState(newState);
  }

  updateRowItemEntity(item, fieldName, newValues) {
    const newRows = {...this.state.rows};

    this.state.rowOrder.map(rowId => {
      if (typeof newRows[rowId].items[item.id] !== 'undefined') {

        newRows[rowId].items[item.id].entity[fieldName] = newValues;
        this.setState({rows: newRows});
        return;
      }
    });
    this.triggerFormUpdated();
  }

  loadRow(rowId) {
    this.loadEntity('paragraph_row', this.state.rows[rowId].target_id)
      .then(entityData => {
        const newState = {...this.state}
        this.state.rows[rowId].entity = entityData;
        delete this.state.rows[rowId].loadedEntity;
        this.setState(newState);
      });
  }

  loadRowItem(itemId) {
    const rowId = this.state.rowOrder.find(rowId => this.state.rows[rowId].itemsOrder.find(rowItemId => this.state.rows[rowId].items[rowItemId].target_id === itemId));
    const rowItemId = this.state.rows[rowId].itemsOrder.find(rowItemId => this.state.rows[rowId].items[rowItemId].target_id === itemId)

    const promise = this.loadEntity('paragraph', itemId);
    promise.then(entityData => {
      const newState = {...this.state}
      newState.rows[rowId].items[rowItemId].entity = entityData;
      delete newState.rows[rowId].items[rowItemId].loadedEntity;
      this.setState(newState);
    });
  }

  loadEntity(entityType, entityId) {
    const url = this.apiUrls.baseDomain + this.apiUrls.entityApi;
    return fetch(url.replace('{entity_type_id}', entityType).replace('{entity_id}', entityId))
      .then(response => response.json());
  }

  render() {
    return (
      <WidgetContext.Provider
        value={{
          props: this.props,
          state: this.state,
          apiUrls: this.apiUrls,
          tools: this.props.tools,
          ...this.functions
        }}>
        {this.props.children}
      </WidgetContext.Provider>
    )

  }

}
