import React from 'react';
import {FormDialog} from "./Atoms/FormDialog";
import {Loader} from "./Atoms/Loader";
import {EntityForm} from "./EntityForm";


export const RowForm = ({open, drupalContext, rowId, onClose, entity}) => {
  const formFields = drupalContext.getEntityForm('paragraphs_row', drupalContext.props.rowBundle);

  if (formFields === undefined) {
    return <Loader/>
  }

  return (
    <FormDialog open={open} onClose={onClose} title={'Edit Row'}>
      <EntityForm
        entityType="paragraphs_row"
        bundle={drupalContext.props.rowBundle}
        onFieldChange={(fieldName, newValue) => drupalContext.updateRow(rowId, fieldName, newValue)}
        drupalContext={drupalContext}
        entity={entity}
      />
    </FormDialog>
  )

}
