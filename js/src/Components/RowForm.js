import React from 'react';
import {FormDialog} from "./Atoms/FormDialog";
import {EntityForm} from "./EntityForm";
import {Loader} from "./Atoms/Loader";

export const RowForm = ({widgetContext, open, onClose, rowId, entity, loadedEntity}) => {

  if (open && typeof loadedEntity !== 'undefined') {
    widgetContext.loadRow(rowId);
  }

  const getEntityForm = () => {
    if (typeof loadedEntity !== 'undefined') {
      return <Loader/>;
    }

    return <EntityForm
      entityType="paragraphs_row"
      bundle={widgetContext.props.rowBundle}
      onFieldChange={(fieldName, newValue) => widgetContext.updateRowEntity(rowId, fieldName, newValue)}
      widgetContext={widgetContext}
      entity={entity}
    />
  }

  return (
    <FormDialog
      open={open}
      onClose={onClose}
      title={'Edit Row'}
    >
      {getEntityForm()}
    </FormDialog>
  )

}
