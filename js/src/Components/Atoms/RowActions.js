import React from 'react';
import IconButton from '@material-ui/core/IconButton';
import MoreVertIcon from '@material-ui/icons/MoreVert';
import {WidgetContext} from "../../Contexts/WidgetManager";
import {RowForm} from "../RowForm";
import {ConfirmDialog} from "./ConfirmDialog";
import styled from 'styled-components';

export const RowActions = ({onlyRow, rowId, entity, loadedEntity, onRemoveRow}) => {

  const [formDialogOpen, setFormDialogOpen] = React.useState(false);
  const [deleteModalOpen, setDeleteModalOpen] = React.useState(false);
  const [actionsOpen, setActionsOpen] = React.useState(false);

  return (
    <div
      className="row-actions"
      style={{position: 'relative'}}
      onMouseLeave={() => setActionsOpen(false)}
    >
      <Kabob onClick={() => setActionsOpen(!actionsOpen)}/>
      <ActionsContainer style={{display: actionsOpen ? 'block' : 'none'}}>
        <div>
          <button
            type="button"
            className="button"
            onClick={() => {
              setActionsOpen(false)
              setFormDialogOpen(true)
            }}
          >
            Edit Row
          </button>
        </div>
        <div>
          <button
            type="button"
            className="button"
            disabled={onlyRow}
            onClick={() => {
              setActionsOpen(false);
              setDeleteModalOpen(true)
            }}
          >
            Delete Row
          </button>
        </div>
      </ActionsContainer>

      <WidgetContext.Consumer>
        {widgetContext =>
          <RowForm
            open={formDialogOpen}
            onClose={() => setFormDialogOpen(false)}
            rowId={rowId}
            entity={entity}
            loadedEntity={loadedEntity}
            widgetContext={widgetContext}
          />
        }
      </WidgetContext.Consumer>

      <ConfirmDialog
        open={deleteModalOpen}
        title="Delete this row?"
        dialog="This action can not be undone."
        onCancel={() => setDeleteModalOpen(false)}
        onConfirm={() => {
          setDeleteModalOpen(false);
          onRemoveRow(rowId)
        }}
      />
    </div>
  )
}

const ActionsContainer = styled.div`
  position: absolute;
  background: #fff;
  border: 1px solid #000;
  box-shadow: 3px 4px 4px #ccc;
  top: 0;
  right: 0;
  padding: 10px;
  border-radius: 5px;

  button {
    display:block;
    white-space: nowrap;
  }
`

const Kabob = ({onClick}) => {
  return (
    <IconButton
      color="inherit"
      aria-label="Open row actions"
      edge="end"
      onClick={onClick}
    >
      <MoreVertIcon/>
    </IconButton>
  )
}
