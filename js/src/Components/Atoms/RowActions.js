import React, {useState} from 'react';
import IconButton from '@material-ui/core/IconButton';
import MoreVertIcon from '@material-ui/icons/MoreVert';
import Menu from '@material-ui/core/Menu';
import MenuItem from '@material-ui/core/MenuItem';
import {WidgetContext} from "../../Contexts/WidgetManager";
import {RowForm} from "../RowForm";
import {ConfirmDialog} from "./ConfirmDialog";

export const RowActions = ({onlyRow, rowId, entity, loadedEntity, onRemoveRow}) => {

  const [formDialogOpen, setFormDialogOpen] = useState(false);
  const [deleteModalOpen, setDeleteModalOpen] = useState(false);
  const [actionsOpen, setActionsOpen] = useState(false);
  const [anchorEl, setAnchorEl] = useState(null);

  const openActions = (e) => {
    setActionsOpen(true)
    setAnchorEl(e.currentTarget);
  }

  const closeActions = () => {
    setActionsOpen(false);
    setAnchorEl(null);
  }

  return (
    <div
      className="row-actions"
      style={{position: 'relative'}}
    >
      <IconButton
        aria-label="more"
        aria-controls="long-menu"
        aria-haspopup="true"
        onClick={openActions}
      >
        <MoreVertIcon/>
      </IconButton>

      <Menu
        anchorEl={anchorEl}
        open={actionsOpen}
        onClose={closeActions}
      >
        <MenuItem onClick={() => {setFormDialogOpen(true); closeActions()}}>
          Edit Row
        </MenuItem>
        <MenuItem onClick={() => {setDeleteModalOpen(true); closeActions()}}>
          Delete Row
        </MenuItem>
      </Menu>

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
