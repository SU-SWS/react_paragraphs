import React from 'react';
import {RowForm} from "../RowForm";
import {ConfirmDialog} from "./ConfirmDialog";
import IconButton from '@material-ui/core/IconButton';
import MenuIcon from '@material-ui/icons/Menu';
import styled from 'styled-components';

export const RowActions = (props) => {

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

        <button
          type="button"
          className="button"
          disabled={props.onlyRow}
          onClick={() => {
            setActionsOpen(false);
            setDeleteModalOpen(true)
          }}
        >
          Delete Row
        </button>
      </ActionsContainer>

      <RowForm
        open={formDialogOpen}
        onClose={() => setFormDialogOpen(false)}
        rowId={props.rowId}
        entity={props.entity}
      />

      <ConfirmDialog
        open={deleteModalOpen}
        title="Delete this row?"
        dialog="This action can not be undone."
        onCancel={() => setDeleteModalOpen(false)}
        onConfirm={() => {
          setDeleteModalOpen(false);
          props.onRemoveRow(props.id)
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
      <MenuIcon/>
    </IconButton>
  )
}
